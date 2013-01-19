<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

require_once('base_module_model.php');

class Fuel_relationships_model extends Base_module_model {

	function __construct()
	{
		parent::__construct('fuel_relationships');
	}
	
	function find_by_candidate($candidate_table, $foreign_table, $candidate_id = NULL, $return_method = NULL)
	{
		$where['candidate_table'] = $candidate_table;
		$where['foreign_table'] = $foreign_table;
		if (!empty($candidate_id))
		{
			$where['candidate_id'] = $candidate_id;
		}
		$this->_common_select_and_joins($candidate_table, $foreign_table);
		return $this->find_all($where, NULL, NULL, NULL, NULL, $return_method);
	}

	function find_by_foreign($candidate_table, $foreign_table, $foreign_id = NULL, $return_method = NULL)
	{
		$where['candidate_table'] = $candidate_table;
		$where['foreign_table'] = $foreign_table;
		if (!empty($foreign_id))
		{
			$where['foreign_id'] = $foreign_id;
		}
		$this->_common_select_and_joins($candidate_table, $foreign_table);
		return $this->find_all($where, NULL, NULL, NULL, NULL, $return_method);
	}
	
	function _common_select_and_joins($candidate_table, $foreign_table)
	{
		$candidate_select = $this->db->safe_select($candidate_table, NULL, 'candidate_');
		$foreign_select = $this->db->safe_select($foreign_table, NULL, 'foreign_');
		$this->db->select($candidate_select);
		$this->db->select($foreign_select);
		$this->db->join($candidate_table, $candidate_table.'.id = '.$this->table_name.'.candidate_key AND '.$this->table_name.'.candidate_table = "'.$candidate_table.'"', 'LEFT');
		$this->db->join($foreign_table, $foreign_table.'.id = '.$this->table_name.'.foreign_key AND '.$this->table_name.'.foreign_table = "'.$foreign_table.'"', 'LEFT');
	}

}

class Fuel_relationship_model extends Base_module_record {
}