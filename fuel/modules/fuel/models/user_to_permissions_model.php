<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class User_to_permissions_model extends MY_Model {
	
	private $_tables;
	
	function __construct()
	{
		$CI =& get_instance();
		$CI->config->module_load(FUEL_FOLDER, 'fuel', TRUE);
		$this->_tables = $CI->config->item('tables', 'fuel');
		parent::__construct($this->_tables['user_to_permissions']);
	}
	
	function get_permissions($user_id, $name_as_key = TRUE)
	{
		$this->_common_query();
		$where = array('user_id' => $user_id, 'active' => 'yes');
		$this->db->where($where);
		$this->db->from($this->table_name);
		$query = $this->db->get();
		if ($name_as_key)
		{
			return $query->result_assoc_array('perm_name');
		}
		return $query->result_assoc_array('perm_id');
	}
	
	function _common_query()
	{
		$this->db->select($this->_tables['user_to_permissions'].'.permission_id AS perm_id, '.$this->_tables['permissions'].'.name AS perm_name, '.$this->_tables['permissions'].'.active AS perm_active');
		$this->db->join($this->_tables['permissions'], $this->_tables['user_to_permissions'].'.permission_id = '.$this->_tables['permissions'].'.id', 'left');
	}
	
	function has_permission($user_id, $perm_name)
	{
		$where = array('user_id' => $user_id, 'LOWER('.$this->_tables['permisisons'].'.name)' => strtolower($perm_name));
		return $this->record_exists($where);
	}
	
}