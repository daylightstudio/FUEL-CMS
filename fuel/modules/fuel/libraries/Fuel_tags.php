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
 * @copyright	Copyright (c) 2012, Run for Daylight LLC.
 * @license		http://www.getfuelcms.com/user_guide/general/license
 * @link		http://www.getfuelcms.com
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * FUEL tags object
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/libraries/fuel_tags
 */

// --------------------------------------------------------------------
class Fuel_tags extends Fuel_module {
	
	protected $module = 'tags';
	
	
	function find_by_tag($tag, $type = NULL)
	{
		$this->CI->load->module_model(FUEL_FOLDER, 'relationships_model');
		$this->CI->relationships_model->find_by_candidate($candidate_table, $foreign_table, $candidate_id = NULL, $return_method = NULL);
		// find_by_foreign($candidate_table, $foreign_table, $foreign_id = NULL, $return_method = NULL)
		$model = $this->model();
	}

	function find_all_by_group($group, $type = NULL)
	{
		$this->CI->load->module_model(FUEL_FOLDER, 'relationships_model');
		$tag_table  = $this->model()->table_name();

		$group_module = $this->fuel->modules->get($group, FALSE);
		$group_table = $group_module->model()->table_name();

		$this->CI->relationships_model->find_by_candidate($group_table, $tag_table);
		// find_by_foreign($candidate_table, $foreign_table, $foreign_id = NULL, $return_method = NULL)
	}

	function list_options()
	{
		return $this->model()->list_options('slug', 'name');
	}

}

/* End of file Fuel_tags.php */
/* Location: ./modules/fuel/libraries/Fuel_tags.php */