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
		$this->db->select($this->_tables['logs'].'.id, entry_date, CONCAT('.$this->_tables['users'].'.first_name, " ", '.$this->_tables['users'].'.last_name) as name, message, type', FALSE);
		$this->db->join($this->_tables['users'], $this->_tables['logs'].'.user_id = '.$this->_tables['users'].'.id', 'left');
		$data = parent::list_items($limit, $offset, $col, $order);
		//$this->debug_query();
		return $data;
	}
	
	function logit($msg, $type = NULL, $user_id = NULL){
		$CI =& get_instance();
		if (!isset($user_id)) 
		{
			$user = $CI->fuel->auth->user_data();
			if (isset($user['id']))
			{
				$user_id = $user['id'];
			}
		}
		
		$save['message'] = $msg;
		$save['type'] = $type;
		$save['user_id'] = $user_id;
		$save['entry_date'] = datetime_now();
		$this->save($save);
	}
	
}

class Log_model extends Base_module_record {
}