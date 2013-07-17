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
 * <strong>Fuel_sitevariables_model</strong> is used for managing site-wide variables
 * 
 * @package		FUEL CMS
 * @subpackage	Models
 * @category	Models
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/models/fuel_sitevariables_model
 */

require_once('base_module_model.php');

class Fuel_sitevariables_model extends Base_module_model {

	public $required = array('name'); // The Name field is required
	public $unique_fields = array('name'); // The Name should be unique
	
	// --------------------------------------------------------------------
	
	/**
	 * Constructor.
	 *
	 * @access	public
	 * @return	void
	 */	
	public function __construct()
	{
		parent::__construct('fuel_site_variables');
	}

	
	// --------------------------------------------------------------------
	
	/**
	 * Lists the site variable items
	 *
	 * @access	public
	 * @param	int The limit value for the list data (optional)
	 * @param	int The offset value for the list data (optional)
	 * @param	string The field name to order by (optional)
	 * @param	string The sorting order (optional)
	 * @param	boolean Determines whether the result is just an integer of the number of records or an array of data (optional)
	 * @return	mixed If $just_count is true it will return an integer value. Otherwise it will return an array of data (optional)
	 */	
	public function list_items($limit = NULL, $offset = NULL, $col = 'name', $order = 'desc', $just_count = FALSE)
	{

		$this->db->select('id, name, SUBSTRING(value, 1, 50) as value, scope, active', FALSE);	
		$data = parent::list_items($limit, $offset, $col, $order, $just_count);
		if (empty($just_count))
		{
			foreach($data as $key => $val)
			{
				$data[$key]['value'] = htmlentities($val['value'], ENT_QUOTES, 'UTF-8');
			}
		}
		return $data;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Lists the module items
	 *
	 * @access	public
	 * @return	Key/value option list with the names of the variables and the values as the value
	 */	
	public function retrieve_all()
	{
		$vars = $this->options_list('name', 'value', array('active' => 'yes'));
		return $vars;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns a single value based on the site variable name
	 *
	 * @access	public
	 * @return	string
	 */	
	public function retrieve_one($name = null)
	{
		$vars = $this->find_one_array(array('active' => 'yes', 'name' => $name));
		return $vars['value'];
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Site variable form fields array
	 *
	 * @access	public
	 * @param	array Values of the form fields (optional)
	 * @param	array An array of related fields. This has been deprecated in favor of using has_many and belongs to relationships (deprecated)
	 * @return	array An array to be used with the Form_builder class
	 */	
	public function form_fields($values = array(), $related = array()){
		$fields = parent::form_fields($values, $related);
		$fields['value']['class'] = 'markitup';
		return $fields;
	}

}

class Fuel_sitevariable_model extends Base_module_record {

}
