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
 * <strong>Fuel_blocks_model</strong> is used for managing CMS administrable blocks
 * 
 * @package		FUEL CMS
 * @subpackage	Models
 * @category	Models
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/models/fuel_blocks_model
 */

require_once('base_module_model.php');

class Fuel_blocks_model extends Base_module_model {
	
	public $required = array('name'); // name is required
	public $filters = array('description'); // allows for the description field to be searchable as well as the name field
	public $ignore_replacement = array('name'); // the name value will be ignored when one record replaces another

	/**
	 * Constructor
	 *
	 * @access	public
	 * @return	void
	 */
	public function __construct()
	{
		parent::__construct('fuel_blocks');
	}
		
	// --------------------------------------------------------------------
	
	/**
	 * Lists the module items
	 *
	 * @access	public
	 * @param	int The limit value for the list data
	 * @param	int The offset value for the list data
	 * @param	string The field name to order by
	 * @param	string The sorting order
	 * @param	boolean Determines whether the result is just an integer of the number of records or an array of data
	 * @return	mixed If $just_count is true it will return an integer value. Otherwise it will return an array of data
	 */	
	public function list_items($limit = NULL, $offset = NULL, $col = 'name', $order = 'desc', $just_count = FALSE)
	{
		$CI =& get_instance();
		if ($CI->fuel->language->has_multiple())
		{
			$this->db->select('id, name, SUBSTRING(description, 1, 50) as description, SUBSTRING(view, 1, 150) as view, language, published', FALSE);
		}
		else
		{
			$this->db->select('id, name, SUBSTRING(description, 1, 50) as description, SUBSTRING(view, 1, 150) as view, published', FALSE);	
		}
		
		$data = parent::list_items($limit, $offset, $col, $order);
		if (empty($just_count))
		{
			foreach($data as $key => $val)
			{
				$data[$key]['view'] = htmlentities($val['view'], ENT_QUOTES, 'UTF-8');
			}
		}
		return $data;
	}
	
	public function options_list_with_views($where = array(), $dir_folder = '', $dir_filter = '^_(.*)|\.html$', $order = TRUE, $recursive = TRUE)
	{
		$CI =& get_instance();
		$CI->load->helper('directory');

		$module_path = APPPATH;
		if (is_array($dir_folder))
		{
			$module = key($dir_folder);
			$dir_folder = current($dir_folder);
			if (is_string($module))
			{
				$module_path = MODULES_PATH.$module;
			}
		}
		$dir_folder = trim($dir_folder, '/');
		$blocks_path = $module_path.'/views/_blocks/'.$dir_folder;

		// don't display blocks with preceding underscores or .html files'
		$block_files = directory_to_array($blocks_path, $recursive, '#'.$dir_filter.'#', FALSE, TRUE);
		$view_blocks = array();
		foreach($block_files as $block)
		{
			$view_blocks[$block] = $block;
		}

		// if a dir_folder exists, then we will look for any CMS blocks that may be prefixed with that dir_folder 
		// (e.g. sections/left_block becomes just left_block) 
		if (!empty($dir_folder) AND empty($where))
		{
			$where = 'name LIKE "'.$dir_folder.'/%"';
		}
		$blocks = parent::options_list('name', 'name', $where, $order);

		// continue filter of cms blocks dir_folder is specified
		$cms_blocks = array();
		if (!empty($dir_folder))
		{
			$cms_blocks = array();
			foreach($blocks as $key => $val)
			{
				$key = preg_replace('#^'.$dir_folder.'/(.+)#', '$1', $key);
				$cms_blocks[$key] = $key;
			}
		}
		else
		{
			$cms_blocks = $blocks;
		}

		$blocks = array_merge($view_blocks, $cms_blocks);
		if ($order)
		{
			ksort($blocks);	
		}
		return $blocks;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Block form fields
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

		// set language field
		if ($CI->fuel->language->has_multiple())
		{
			$fields['language'] = array('type' => 'select', 'options' => $CI->fuel->language->options());
		}
		else
		{
			$fields['language'] = array('type' => 'hidden', 'value' => $CI->fuel->language->default_option());
		}

		// to prevent <p> tags from cropping up
		$fields['view']['ckeditor_options']['enter-mode'] = 2;
		return $fields;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Model hook ran before validation that will check to make sure it's not a block that already exists
	 *
	 * @access	public
	 * @param	array An array of values to be saved
	 * @return	array An array of values that will be sent to the validate method before saving
	 */	
	public function on_before_validate($values)
	{
		$this->add_validation('parent_id', array(&$this, 'no_location_and_parent_match'), lang('error_location_parents_match'));
	//	$this->add_validation('id', array(&$this, 'no_id_and_parent_match'), lang('error_location_parents_match'), $values['parent_id']);
		
		if (!empty($values['id']))
		{
			$this->add_validation('name', array(&$this, 'is_editable_block'), lang('error_val_empty_or_already_exists', lang('form_label_name')), array($values['id'], $values['language']));
		}
		else
		{
			$this->add_validation('name', array(&$this, 'is_new_block'), lang('error_val_empty_or_already_exists', lang('form_label_name')), array($values['language']));
		}
		return $values;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Method used with on_before_valid model hook
	 *
	 * @access	public
	 * @param	string The name of the block
	 * @param	string The language associated with the block
	 * @return	boolean
	 */	
	public function is_new_block($name, $lang)
	{
		if (empty($name)) return FALSE;
		$data = $this->find_one_array(array('name' => $name, 'language' => $lang));
		if (!empty($data)) return FALSE;
		return TRUE;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Method used with on_before_valid model hook
	 *
	 * @access	public
	 * @param	string The name of the block
	 * @param	int The records ID
	 * @param	string The language associated with the block
	 * @return	boolean
	 */	
	public function is_editable_block($name, $id, $lang)
	{
		$data = $this->find_one_array(array('name' => $name, 'language' => $lang));
		if (empty($data) || (!empty($data) && $data['id'] == $id)) return TRUE;
		return FALSE;
	}
}

class Fuel_block_model extends Base_module_record {
}
