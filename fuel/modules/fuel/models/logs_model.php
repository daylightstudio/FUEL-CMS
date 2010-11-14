<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once('base_module_model.php');

class Logs_model extends Base_module_model {

	public $id;
	
	function __construct()
	{
		parent::__construct('logs');
	}
	
	function list_items($limit = NULL, $offset = NULL, $col = 'entry_date', $order = 'desc')
	{
		$this->db->select('entry_date, CONCAT('.$this->_tables['users'].'.first_name, " ", '.$this->_tables['users'].'.last_name) as name, message', FALSE);
		$this->db->join($this->_tables['users'], $this->_tables['logs'].'.user_id = '.$this->_tables['users'].'.id', 'left');
		
		$data = array();
		
		if (is_array($this->filters)){
			foreach($this->filters as $key => $val){
				if (!empty($val)) $this->db->or_like('LOWER('.$key.')', strtolower($val), 'both');
			}
		}
		
		$this->db->order_by($col, $order);
		$this->db->limit($limit, $offset);
		$query = $this->get();
		//$data = $query->result_array();
		$data = $query->result();
	//	$this->db->debug_query();
		return $data;
	}
	
	
	function logit($msg, $user = null){
		$CI =& get_instance();
		if (empty($user)) 
		{
			$user = $CI->fuel_auth->user_data();
		}
		$save['user_id'] = $user['id'];
		$save['message'] = $msg;
		$save['entry_date'] = datetime_now();
		$this->save($save);
	}
	
}