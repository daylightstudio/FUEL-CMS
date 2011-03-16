<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Archives_model extends MY_Model {
	
	function __construct()
	{
		$CI =& get_instance();
		$CI->config->module_load(FUEL_FOLDER, 'fuel', TRUE);
		$tables = $CI->config->item('tables', 'fuel');
		parent::__construct($tables['archives']);
	}
	
	function options_list($ref_id, $table_name, $include_current = false)
	{
		$CI =& get_instance();
		$CI->load->helper('date');
		$options = $this->find_all_array(array('ref_id' => $ref_id, 'table_name' => $table_name), 'version_timestamp desc');
		$return = array();
		$i = 0;
		foreach($options as $val)
		{
			if ($i == 0 && $include_current)
			{
				$return[$val['version']] = 'Current Version';
			}
			else
			{
				$return[$val['version']] = 'Version '.$val['version'].' - '.english_date($val['version_timestamp'], true);
			}
			$i++;
		}
		return $return;
	}

}

class Archive_model extends Data_record {
}
