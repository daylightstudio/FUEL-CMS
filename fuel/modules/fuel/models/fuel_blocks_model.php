<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once('base_module_model.php');

class Fuel_blocks_model extends Base_module_model {
	
	public $required = array('name');
	public $filters = array('description');
	public $ignore_replacement = array('name');
		
	function __construct()
	{
		parent::__construct('fuel_blocks');
	}
		
	function list_items($limit = NULL, $offset = NULL, $col = 'name', $order = 'desc')
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
		foreach($data as $key => $val)
		{
			$data[$key]['view'] = htmlentities($val['view'], ENT_QUOTES, 'UTF-8');
		}
		return $data;
	}
	
	function options_list_with_views($where = array(), $dir_folder = '', $dir_filter = '^_(.*)|\.html$', $order = TRUE, $recursive = TRUE)
	{
		$CI =& get_instance();
		$CI->load->helper('directory');
		$blocks_path = APPPATH.'views/_blocks/'.$dir_folder;

		// don't display blocks with preceding underscores or .html files'
		$block_files = directory_to_array($blocks_path, $recursive, '#'.$dir_filter.'#', FALSE, TRUE);
		$view_blocks = array();
		foreach($block_files as $block)
		{
			$view_blocks[$block] = $block;
		}

		$blocks = parent::options_list('name', 'name', $where, $order);
		$blocks = array_merge($view_blocks, $blocks);
		if ($order)
		{
			ksort($blocks);	
		}
		return $blocks;
	}
	
	function form_fields()
	{
		$CI =& get_instance();
		$fields = parent::form_fields();

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

	function on_before_validate($values)
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

	function is_new_block($name, $lang)
	{
		if (empty($name)) return FALSE;
		$data = $this->find_one_array(array('name' => $name, 'language' => $lang));
		if (!empty($data)) return FALSE;
		return TRUE;
	}

	// validation method
	function is_editable_block($name, $id, $lang)
	{
		$data = $this->find_one_array(array('name' => $name, 'language' => $lang));
		if (empty($data) || (!empty($data) && $data['id'] == $id)) return TRUE;
		return FALSE;
	}
}

class Fuel_block_model extends Base_module_record {
}
