<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once('base_module_model.php');

class Pages_model extends Base_module_model {

	public $id;
	public $required = array('location');
	public $hidden_fields = array('last_modified', 'last_modified_by');
	
	function __construct()
	{
		parent::__construct('pages');
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
		return array_keys($this->pages_model->options_list('location', 'location', $where));
	}

	// include id if set to true will screw up Menu class
	function export($export_unpublished = FALSE)
	{
		$CI =& get_instance();
		$CI->load->helper('array');
		$return = array();
		
		$sql['select'] = $this->_tables['pages'].'.*';
		$where = array();
		if (!$export_unpublished) $where = array('published' => 'yes');
		$pages = $this->find_all_array_assoc('location', $where, 'location asc');
		
		foreach($pages as $key => $val){
			$parts = explode('/', $val['location']);
			$parent = implode('/', $parts);
			
			if (!empty($pages[$parent]) || strrpos($val['location'], '/') === FALSE)
			{
				$pages[$key]['parent_id'] = $parent;
			}
			else
			{
				// if orphaned... then put them in the _orphans folder
				if (empty($return['_orphans']))
				{
					$pages['_orphans'] = array('parent_id' => null, 'location' => '_orphans');
				}
				$pages[$key]['parent_id'] = '_orphans';
			}
		}
		$pages = array_sorter($pages, 'location');
		return $pages;
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

	function form_fields()
	{
		$CI =& get_instance();
		
		$fields = parent::form_fields();
		$fields['location']['placeholder'] = lang('pages_default_location');
		$fields['date_added']['type'] = 'hidden';
		$fields['layout']['type'] = 'select';
		$fields['layout']['options'] = $CI->fuel->layouts->options_list();
		
		$yes = lang('form_enum_option_yes');
		$no = lang('form_enum_option_no');
		$fields['cache']['options'] = array('yes' => $yes, 'no' => $no);
		return $fields;
	}
	
	function clean($values = array())
	{
		$cleaned = parent::clean($values);
		if (!empty($cleaned['location']))
		{
			$segments = explode('/', $cleaned['location']);
			$cleaned_segments = array();
			foreach($segments as $val)
			{
				if (!empty($val)) $cleaned_segments[] = $val;
			}
			$cleaned['location'] = implode('/', $cleaned_segments);
		}
		return $cleaned;
	}
	
	function on_before_clean($values)
	{
		if (!empty($values['location']))
		{
			if ($values['location'] == lang('pages_default_location'))
			{
				$values['location'] = '';
			}
			
			$values['location'] = str_replace('/', '___', $values['location']);
			$values['location'] = url_title($values['location']);
			$values['location'] = str_replace('___', '/', $values['location']);
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
		$this->delete_related(array(FUEL_FOLDER => 'pagevariables_model'), 'page_id', $where);
	}
	
	// overwrite parent
	function restore($ref_id, $version = null)
	{
		$CI =& get_instance();
		$CI->load->module_model(FUEL_FOLDER, 'pagevariables_model');
		$archive = $this->get_archive($ref_id, $version);
		if (empty($archive)) return true;
		$pages_saved = $this->save($archive, array('id' => $ref_id));
		
		// delete page variables before saving
		$CI->pagevariables_model->delete(array('page_id' => $ref_id));
		$page_variables_saved = $CI->pagevariables_model->save($archive['variables']);
		return ($pages_saved && $page_variables_saved);
	}
	
	function _common_query()
	{
		$this->db->join($this->_tables['users'], $this->_tables['users'].'.id = '.$this->_tables['pages'].'.last_modified_by', 'left');
		$this->db->select($this->_tables['pages'].'.*, '.$this->_tables['users'].'.user_name, '.$this->_tables['users'].'.first_name, '.$this->_tables['users'].'.last_name, '.$this->_tables['users'].'.email, CONCAT('.$this->_tables['users'].'.first_name, '.$this->_tables['users'].'.last_name) AS full_name', FALSE);
	}
}

class Page_model extends Base_module_record {
}
