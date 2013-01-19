<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once('base_module_model.php');

class Fuel_pages_model extends Base_module_model {

	public $id;
	public $required = array('location');
	public $hidden_fields = array('last_modified', 'last_modified_by');
	public $ignore_replacement = array('location');
	
	function __construct()
	{
		parent::__construct('fuel_pages');
	}
	
	// displays related items on the right side
	function related_items($values = array())
	{
		$CI =& get_instance();
		$CI->load->module_model(FUEL_FOLDER, 'fuel_navigation_model');
		$where['location'] = $values['location'];
		$related_items = $CI->fuel_navigation_model->find_all_array_assoc('id', $where);
		$return = array();
		if (!empty($related_items))
		{
			$return['navigation'] = array();

			foreach($related_items as $key => $item)
			{
				$label = $item['label'];
				if (!empty($item['group_name']))
				{
					$label .= ' ('.$item['group_name'].')';
				}
				$return['navigation']['inline_edit/'.$key] = $label;
			}
		}
		else if (!empty($values['location']) AND $this->fuel->auth->has_permission('navigation', 'create'))
		{

			$return['navigation'] = array();
			$label = (!empty($values['page_vars']['page_title'])) ? $values['page_vars']['page_title'] : '';
			$parent_id = 0;
			$group_id = $CI->fuel->config('auto_page_navigation_group_id');

			// determine parent based off of location
			$location_arr = explode('/', $values['location']);
			$parent_location = implode('/', array_slice($location_arr, 0, (count($location_arr) -1)));
		
			if (!empty($parent_location)) $parent = $this->fuel_navigation_model->find_by_location($parent_location);
			if (!empty($parent))
			{
				$parent_id = $parent['id'];
			}
			$return['navigation']['inline_create?location='.urlencode($values['location']).'&label='.$label.'&group_id='.$group_id.'&parent_id='.$parent_id] = lang('navigation_related');
		}
		
		return $return;
	}
	
	function tree($just_published = FALSE)
	{
		$CI =& get_instance();
		$CI->load->helper('array');
		$return = array();
		
		$where = array();
		if ($just_published) $sql['where'] = array('published' => 'yes');
		$pages = $this->find_all_array_assoc('location', $where, 'location asc');
		foreach($pages as $key => $val)
		{
			$parts = explode('/', $val['location']);
			$label = array_pop($parts);
			$parent = implode('/', $parts);
			
			if (!empty($pages[$parent]) || strrpos($val['location'], '/') === FALSE)
			{
				$return[$key]['label'] = $label;
				$return[$key]['parent_id'] = (empty($parent)) ? 0 : $parent;
			}
			else
			{
				// if orphaned... then put them in the _orphans folder
				if (empty($return['_orphans']))
				{
					$return['_orphans'] = array('label' => '_orphans', 'parent_id' => 0, 'location' => null);
				}
				$return[$key]['label'] = $key;
				$return[$key]['parent_id'] = '_orphans';
			}
			if ($val['published'] == 'no') {
				$return[$key]['attributes'] = array('class' => 'unpublished', 'title' => 'unpublished');
			}
			$return[$key]['location'] = fuel_url('pages/edit/'.$val['id']);
		}
		$return = array_sorter($return, 'label', 'asc');
		return $return;
	}
	
	function list_locations($include_unpublished = FALSE)
	{
		$where = ($include_unpublished) ? array('published' => 'no') : null;
		return array_keys($this->fuel_pages_model->options_list('location', 'location', $where));
	}

	function get_root_pages()
	{
		$return = array();
		$data = $this->find_all('location');
		foreach($data as $key => $val){
			$parts = explode('/', $val['location']);
			if (isset($parts[0])){
				$return[] = array('name' => $parts[0], 'value' => $parts[0]);
			}
		}
		return $return;
	}
	
	function find_by_location($location, $just_published = 'yes')
	{
		
		if (substr($location, 0, 4) == 'http')
		{
			$location = substr($location, strlen(site_url()));
		}
		$where['location'] = $location;
		if ($just_published === TRUE || $just_published == 'yes') $where['published'] = 'yes';
		$data = $this->find_one_array($where);
		return $data;
	}

	function form_fields($values = array(), $related = array())
	{
		$CI =& get_instance();
		$fields = parent::form_fields($values, $related);
		$fields['location']['placeholder'] = lang('pages_default_location');
		$fields['location']['size'] = 100;
		$fields['date_added']['type'] = 'hidden';
		$fields['layout']['type'] = 'select';
		$fields['layout']['options'] = $CI->fuel->layouts->options_list();
		
		$yes = lang('form_enum_option_yes');
		$no = lang('form_enum_option_no');
		$fields['cache']['options'] = array('yes' => $yes, 'no' => $no);
		
		// set language field
		if ($CI->fuel->language->has_multiple())
		{
			$fields['language'] = array('type' => 'select', 'options' => $this->fuel->language->options(), 'order' => 2);
		}
		else
		{
			$fields['language'] = array('type' => 'hidden', 'value' => $this->fuel->language->default_option());
		}
		
		
		// easy add for navigation
		if (empty($values['id']))
		{
			$fields['navigation_label'] = array('comment' => 'This field lets you quickly add a navigation item for this page. 
			It only allows you to create a navigation item during page creation. To edit the navigation item, you must click on the
			\'Navigation\' link on the left, find the navigation item you want to change and click on the edit link.');
		}
		
		return $fields;
	}
	
	function on_before_clean($values)
	{
		if (!empty($values['location']))
		{
			if ($values['location'] == lang('pages_default_location'))
			{
				$values['location'] = '';
			}
			
			$values['location'] = str_replace(array('/', '.'), array('___', '_X_'), $values['location']);
			$values['location'] = url_title($values['location']);
			$values['location'] = str_replace(array('___', '_X_'), array('/', '.'), $values['location']);
			
			$segments = array_filter(explode('/', $values['location']));
			$values['location'] = implode('/', $segments);
			
		}
		return $values;
	}
	
	function on_before_validate($values)
	{
		if (!empty($values['id']))
		{
			$this->add_validation('location', array(&$this, 'is_editable'), lang('error_val_empty_or_already_exists', lang('form_label_location')), array('location', $values['id']));
		}
		else
		{
			$this->add_validation('location', array(&$this, 'is_new'), lang('error_val_empty_or_already_exists', lang('form_label_location')), array('location'));
		}
		return $values;
	}
	
	function on_before_save($values)
	{
		$CI = get_instance();
		$user = $CI->fuel->auth->user_data();
		$values['last_modified_by'] = $user['id'];
		return $values;
	}
	
	function on_after_delete($where)
	{
		$this->delete_related(array(FUEL_FOLDER => 'fuel_pagevariables_model'), 'page_id', $where);
	}
	
	// overwrite parent
	function restore($ref_id, $version = NULL)
	{
		$CI =& get_instance();
		$CI->load->module_model(FUEL_FOLDER, 'fuel_pagevariables_model');
		$archive = $this->get_archive($ref_id, $version);
		if (empty($archive))
		{
			return TRUE;
		}
		$pages_saved = $this->save($archive, array('id' => $ref_id));
		
		// delete page variables before saving
		$CI->fuel_pagevariables_model->delete(array('page_id' => $ref_id));
		$page_variables_saved = $CI->fuel_pagevariables_model->save($archive['variables']);
		return ($pages_saved AND $page_variables_saved);
	}
	
	// overwrite parent to replace page variables
	function replace($replace_id, $id, $delete = TRUE)
	{
		$CI =& get_instance();
		$CI->load->module_model(FUEL_FOLDER, 'fuel_pagevariables_model');
		
		// start a transaction in case there are any errors
		$CI->fuel_pagevariables_model->db()->trans_begin();

		// retrieve new variables
		$new_values = $CI->fuel_pagevariables_model->find_all_array(array('page_id' => $id));

		// delete old variables
		$CI->fuel_pagevariables_model->delete(array('page_id' => $replace_id));
		$saved = TRUE;
		foreach($new_values as $var)
		{
			$var['page_id'] = $replace_id;
			if (!$CI->fuel_pagevariables_model->save($var))
			{
				$saved = FALSE;
			}
		}
		
		// check if there are any errors and if so we rollem back...
		if ($CI->fuel_pagevariables_model->db()->trans_status() === FALSE)
		{
			$saved = FALSE;
		    $CI->fuel_pagevariables_model->db()->trans_rollback();
		}
		else
		{
		    $CI->fuel_pagevariables_model->db()->trans_commit();
		}
		
		$saved = parent::replace($replace_id, $id, $delete);
		
		return $saved;
	}
	
	function _common_query()
	{
		$this->db->join($this->_tables['fuel_users'], $this->_tables['fuel_users'].'.id = '.$this->_tables['fuel_pages'].'.last_modified_by', 'left');
		$this->db->select($this->_tables['fuel_pages'].'.*, '.$this->_tables['fuel_users'].'.user_name, '.$this->_tables['fuel_users'].'.first_name, '.$this->_tables['fuel_users'].'.last_name, '.$this->_tables['fuel_users'].'.email, CONCAT('.$this->_tables['fuel_users'].'.first_name, '.$this->_tables['fuel_users'].'.last_name) AS full_name', FALSE);
	}
}

class Fuel_page_model extends Base_module_record {
}
