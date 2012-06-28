<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once('base_module_model.php');

class Blocks_model extends Base_module_model {
	
	public $required = array('name');
	public $filters = array('description');
	public $ignore_replacement = array('name');
		
	public function __construct()
	{
		parent::__construct('blocks');
	}
	
	public function list_items($limit = NULL, $offset = NULL, $col = 'name', $order = 'desc')
	{
		$this->db->select('id, name, SUBSTRING(description, 1, 50) as description, SUBSTRING(view, 1, 150) as view, published', FALSE);
		$data = parent::list_items($limit, $offset, $col, $order);
		foreach($data as $key => $val)
		{
			$data[$key]['view'] = htmlentities($val['view'], ENT_QUOTES, 'UTF-8');
		}
		return $data;
	}
	
	public function options_list_with_views($where = array(), $dir_folder = '', $dir_filter = '^_(.*)|\.html$', $order = TRUE)
	{
		$CI =& get_instance();
		$CI->load->helper('directory');
		$blocks_path = APPPATH.'views/_blocks/'.$dir_folder;

		// don't display blocks with preceding underscores or .html files'
		$block_files = directory_to_array($blocks_path, TRUE, '#'.$dir_filter.'#', FALSE, TRUE);
		$view_blocks = array();
		foreach($block_files as $block)
		{
			$view_blocks[$block] = $block;
		}

		$blocks = parent::options_list('name', 'name', $where, $order);
		$blocks = array_merge($view_blocks, $blocks);
		return $blocks;
	}
	
	public function form_fields()
	{
		$fields = parent::form_fields();
		return $fields;
	}

}

class Block_model extends Base_module_record {
}
