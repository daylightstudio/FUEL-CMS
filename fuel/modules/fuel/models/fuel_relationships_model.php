<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/**
 * FUEL CMS
 * http://www.getfuelcms.com
 *
 * An open source Content Management System based on the 
 * Codeigniter framework (http://codeigniter.com)
 *
 * @package		FUEL CMS
 * @author		David McReynolds @ Daylight Studio
 * @copyright	Copyright (c) 2012, Run for Daylight LLC.
 * @license		http://www.getfuelcms.com/user_guide/general/license
 * @link		http://www.getfuelcms.com
 */

// ------------------------------------------------------------------------

/**
 * Extends Base_module_model
 *
 * <strong>Fuel_relationships_model</strong> is used for managing FUEL users in the CMS
 * 
 * @package		FUEL CMS
 * @subpackage	Models
 * @category	Models
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/models/fuel_relationships_model
 */

require_once('base_module_model.php');

class Fuel_relationships_model extends Base_module_model {

	// --------------------------------------------------------------------
	
	/**
	 * Constructor.
	 *
	 * @access	public
	 * @return	void
	 */	
	public function __construct()
	{
		parent::__construct('fuel_relationships');
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Finds "candidate" relationship information
	 *
	 * @access	public
	 * @param	string The candidate table name
	 * @param	string The foreign table name
	 * @param	int The candidate ID (optional)
	 * @param	string Values can be object, array, query, auto (optional)
	 * @return	array
	 */	
	public function find_by_candidate($candidate_table, $foreign_table, $candidate_id = NULL, $return_method = NULL)
	{
		$where['candidate_table'] = $candidate_table;
		$where['foreign_table'] = $foreign_table;
		if (!empty($candidate_id))
		{
			$where['candidate_id'] = $candidate_id;
		}
		$this->_common_select_and_joins($candidate_table, $foreign_table);
		return $this->find_all($where, NULL, NULL, NULL, $return_method);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Finds "foreign" relationship information 
	 *
	 * @param	string The candidate table name
	 * @param	string The foreign table name
	 * @param	int The foreign ID (optional)
	 * @param	string Values can be object, array, query, auto
	 * @return	array
	 */	
	public function find_by_foreign($candidate_table, $foreign_table, $foreign_id = NULL, $return_method = NULL)
	{
		$where['candidate_table'] = $candidate_table;
		$where['foreign_table'] = $foreign_table;
		if (!empty($foreign_id))
		{
			$where['foreign_id'] = $foreign_id;
		}
		$this->_common_select_and_joins($candidate_table, $foreign_table);
		return $this->find_all($where, NULL, NULL, NULL, $return_method);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Sets common active record selects and joins for the relationships
	 *
	 * @access	protected
	 * @param	string The candidate table name
	 * @param	string The foreign table name
	 * @return	void
	 */	
	protected function _common_select_and_joins($candidate_table, $foreign_table)
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