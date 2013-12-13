<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * FUEL CMS
 * http://www.getfuelcms.com
 *
 * An open source Content Management System based on the 
 * Codeigniter framework (http://codeigniter.com)
 *
 * @package		FUEL CMS
 * @author		David McReynolds @ Daylight Studio
 * @copyright	Copyright (c) 2013, Run for Daylight LLC.
 * @license		http://docs.getfuelcms.com/general/license
 * @link		http://www.getfuelcms.com
 */

// ------------------------------------------------------------------------

/**
 * Extends Base_module_model
 *
 * <strong>Fuel_pages_model</strong> is used for managing FUEL users in the CMS
 * 
 * @package		FUEL CMS
 * @subpackage	Models
 * @category	Models
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/models/fuel_pages_model
 */

require_once('base_module_model.php');

class Fuel_pages_model extends Base_module_model {

	public $required = array('location'); // The location field is required
	public $unique_fields = array('location'); // The location field is unique
	public $hidden_fields = array('last_modified', 'last_modified_by'); // The Last modified and Last modified by are hidden fields
	public $ignore_replacement = array('location'); // The location value will be ignored upon replacement
	
	// --------------------------------------------------------------------
	
	/**
	 * Constructor.
	 *
	 * @access	public
	 * @return	void
	 */	
	public function __construct()
	{
		parent::__construct('fuel_pages');
	}

	// --------------------------------------------------------------------
	
	/**
	 * Displays related items on the right side
	 *
	 * @access	public
	 * @param	array View variable data (optional)
	 * @return	mixed Can be an array of items or a string value
	 */	
	public function related_items($values = array())
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
		$view = $this->load->module_view(FUEL_FOLDER, '_blocks/related_items_array', array('related_items' => $return), TRUE);
		$layout = $CI->fuel->layouts->get($values['layout']);
		if (!empty($layout->preview_image))
		{
			$img_path = (is_http_path($layout->preview_image) OR substr($layout->preview_image, 0, 1) == '/') ? $layout->preview_image : img_path($layout->preview_image);
			$view = '<img src="'.$img_path.'" alt="'.$layout->name().'" class="layout_preview" />'.$view;
		}

		return $view;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Tree view that puts pages in a hierarchy based on their location value
	 *
	 * @access	public
	 * @param	boolean Determines whether to return just published pages or not (optional... and ignored in the admin)
	 * @return	array An array that can be used by the Menu class to create a hierachical structure
	 */	
	public function tree($just_published = FALSE)
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
		// can cause memory issues because it will mess with the array keys
		//$return = array_sorter($return, 'label', 'asc');
		return $return;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns a key/value array list of the page locations
	 *
	 * @access	public
	 * @param	boolean Determines whether to included unpublished or not (optional)
	 * @return	array
	 */	
	public function list_locations($include_unpublished = FALSE)
	{
		$where = (!$include_unpublished) ? array('published' => 'yes') : NULL;
		return array_keys($this->options_list('location', 'location', $where));
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns an array of page information based on the location
	 *
	 * @access	public
	 * @param	string The location of the page
	 * @param	boolean Determines whether to included unpublished or not (optional)
	 * @return	array
	 */	
	public function find_by_location($location, $just_published = 'yes')
	{
		
		if (substr($location, 0, 4) == 'http')
		{
			$location = substr($location, strlen(site_url()));
		}
		
		if (empty($location))
		{
			return NULL;
		}

		$segs = explode('/', $location);
		if (count($segs) > 1)
		{
			$last_seg = array_pop($segs);

			$wildcard_location = implode('/', $segs);
			$where = 'location="'.$location.'" OR location="'.$wildcard_location.'/:any"';

			if (is_numeric($last_seg))
			{
				$where .= ' OR location="'.$wildcard_location.'/:num"';
			}
			$where = '('.$where.')';
			if ($just_published === TRUE || $just_published == 'yes')
			{
				$where .= ' AND published = "yes"';
			}

		}
		else
		{
			$where['location'] = $location;
			if ($just_published === TRUE || $just_published == 'yes') $where['published'] = 'yes';
		}
		$data = $this->find_one_array($where, 'location desc');
		return $data;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Page form fields
	 *
	 * @access	public
	 * @param	array Values of the form fields (optional)
	 * @param	array An array of related fields. This has been deprecated in favor of using has_many and belongs to relationships (deprecated)
	 * @return	array An array to be used with the Form_builder class
	 */	
	public function form_fields($values = array(), $related = array())
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
			$fields['language'] = array('type' => 'select', 'options' => $this->fuel->language->options(), 'order' => 4);
		}
		else
		{
			$fields['language'] = array('type' => 'hidden', 'value' => $this->fuel->language->default_option());
		}
		
		
		// easy add for navigation
		if (empty($values['id']))
		{
			$fields['navigation_label'] = array('comment' => lang('navigation_quick_add'));
		}
		
		return $fields;
	}
	
		// --------------------------------------------------------------------
	
	/**
	 * Model hook right before the data is cleaned
	 *
	 * @access	public
	 * @param	array The values to be saved right the clean method is run
	 * @return	array Returns the values to be cleaned
	 */	
	public function on_before_clean($values)
	{
		if (!empty($values['location']))
		{
			if ($values['location'] == lang('pages_default_location'))
			{
				$values['location'] = '';
			}
			
			$values['location'] = str_replace(array('/', '.', ':any', ':num'), array('___', '_X_', '__ANY__', '__NUM__'), $values['location']);
			$values['location'] = url_title($values['location']);
			$values['location'] = str_replace(array('___', '_X_', '__ANY__', '__NUM__'), array('/', '.', ':any', ':num'), $values['location']);
			
			$segments = array_filter(explode('/', $values['location']));
			$values['location'] = implode('/', $segments);
			
		}
		return $values;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Model hook executed right before validation is run
	 *
	 * @access	public
	 * @param	array The values to be saved right before validation
	 * @return	array Returns the values to be validated right before saving
	 */	
	public function on_before_validate($values)
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
	
	// --------------------------------------------------------------------
	
	/**
	 * Model hook executed right before saving
	 *
	 * @access	public
	 * @param	array The values to be saved right before saving
	 * @return	array Returns the values to be saved
	 */	
	public function on_before_save($values)
	{
		$CI = get_instance();
		$user = $CI->fuel->auth->user_data();
		$values['last_modified_by'] = $user['id'];
		return $values;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Model hook executed right after deleting
	 *
	 * @access	public
	 * @param	mixed The where condition to be applied to the delete (e.g. array('user_name' => 'darth'))
	 * @return	void
	 */	
	public function on_after_delete($where)
	{
		$this->delete_related(array(FUEL_FOLDER => 'fuel_pagevariables_model'), 'page_id', $where);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Overwrites parent method. Used to restore page data from the archive
	 *
	 * @access	public
	 * @param	int The record ID associated with the archive
	 * @param	int The version of the archive to retrieve (optional)
	 * @return	boolean
	 */	
	public function restore($ref_id, $version = NULL)
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
	
	// --------------------------------------------------------------------
	
	/**
	 * Overwrite parent method to replace variable data as well
	 *
	 * @access	public
	 * @param	int The old record id of data that will be replaced
	 * @param	int The new record id of data that will be used for the replacement
	 * @param	boolean Determines whether to delete the old record (optional)
	 * @return	boolean Whether it was saved properly or not
	 */	
	// 
	public function replace($replace_id, $id, $delete = TRUE)
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
	
	// --------------------------------------------------------------------
	
	/**
	 * Common query that joins user created/modified information to the page
	 *
	 * @access	public
	 * @param mixed parameter to pass to common query (optional)
	 * @return	void
	 */	
	public function _common_query($params = NULL)
	{
		$this->db->join($this->_tables['fuel_users'], $this->_tables['fuel_users'].'.id = '.$this->_tables['fuel_pages'].'.last_modified_by', 'left');
		$this->db->select($this->_tables['fuel_pages'].'.*, '.$this->_tables['fuel_users'].'.user_name, '.$this->_tables['fuel_users'].'.first_name, '.$this->_tables['fuel_users'].'.last_name, '.$this->_tables['fuel_users'].'.email, CONCAT('.$this->_tables['fuel_users'].'.first_name, '.$this->_tables['fuel_users'].'.last_name) AS full_name', FALSE);
	}
}

class Fuel_page_model extends Base_module_record {
}
