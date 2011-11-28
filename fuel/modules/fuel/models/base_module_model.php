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
 * @copyright	Copyright (c) 2011, Run for Daylight LLC.
 * @license		http://www.getfuelcms.com/user_guide/general/license
 * @link		http://www.getfuelcms.com
 */

// ------------------------------------------------------------------------

/**
 * Extends MY_Model to be specific to FUEL modules
 * 
 * @package		FUEL CMS
 * @subpackage	Models
 * @category	Models
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/libraries/base_module_model
 */

require_once(APPPATH.'core/MY_Model.php');

class Base_module_model extends MY_Model {
	
	public $filters = array(); // filters to apply to when searching for items
	public $filter_value = NULL; // the values of the filters
	public $filter_join = 'or'; // how to combine the filters in the query (and or or)
	public $parsed_fields = array(); // fields to automatically parse
	public $upload_data = array(); // data about all uploaded files
	protected $_tables = array(); // fuel tables
	
	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	string	the table name
	 * @param	string	the module name to 
	 * @return	void
	 */
	function __construct($table, $params = NULL)
	{
		$CI = & get_instance();
		
		// initialize parameters to pass to parent model
		// if it is a string, then we assume it's a module name, if it is an array, then we extract the module name from it'
		if (is_array($params))
		{
			if (isset($params['module']))
			{
				$module = $params['module'];
			}
		}
		else
		{
			$module = $params;
			$params = array();
		}
		
		if (!isset($module)) $module = FUEL_FOLDER;
		
		$fuel_tables = array();
		$module_tables = array();
		$config_tables = array();
		
		// first load the FUEL config so that we can get the tables
		$CI->config->module_load(FUEL_FOLDER, 'fuel', TRUE);
		$fuel_tables = $CI->config->item('tables', 'fuel');
		
		// load in the module configuration file
		if (!empty($module) && $module != FUEL_FOLDER)
		{
			$CI->config->module_load($module, $module, FALSE);
			if ($CI->config->item('tables'))
			{
				$module_tables = $CI->config->item('tables');
			}
		}
		
		// look in the generic configuration space
		if ($CI->config->item('tables')) 
		{
			$config_tables = $CI->config->item('tables');
		}

		// create master list of tables
		$this->_tables = array_merge($config_tables, $module_tables, $fuel_tables);
		
		// set the table to the configuration mapping if it is in array
		if (isset($this->_tables[$table])) 
		{
			$table = $this->_tables[$table];
		}
		
		$CI->load->module_library(FUEL_FOLDER, 'fuel_auth'); // must do this for the cron job
		
		// if no configuration mapping is found then we will assume it is just the straight up table name
		parent::__construct($table, $params); // table name and params
	}
	

	// --------------------------------------------------------------------
	
	/**
	 * Gets the table name based on the configuration
	 *
	 * @access	public
	 * @param	string	the table name
	 * @return	string
	 */	
	function get_table($table)
	{
		if (isset($this->_tables[$table]))
		{
			return $this->_tables[$table];
		}
		return NULL;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Adds a filter for searching
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	void
	 */	
	function add_filter($filter, $key = NULL)
	{
		if (!empty($key))
		{
			$this->filters[$key] = $filter;
		}
		else
		{
			$this->filters[] = $filter;
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Adds multiple filters for searching
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	void
	 */	
	function add_filters($filters)
	{
		if (empty($this->filters))
		{
			$this->filters = $filters;
		}
		else
		{
			$this->filters = array_merge($this->filters, $filters);
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Lists the module items
	 *
	 * @access	public
	 * @param	int
	 * @param	int
	 * @param	string
	 * @param	string
	 * @return	void
	 */	
	function list_items($limit = NULL, $offset = 0, $col = 'id', $order = 'asc')
	{
		if (empty($this->db->ar_select))
		{
			$this->db->select($this->table_name.'.*'); // make select table specific
		}
		
		$data = array();
		
		if (is_array($this->filters))
		{
			foreach($this->filters as $key => $val)
			{
				if (is_int($key))
				{
					$key = $val;
					$val = $this->filter_value;
				}

				if (!empty($val)) 
				{
					if (strpos($key, '.') === FALSE) $key = $this->table_name.'.'.$key;
					
					if (strtolower($this->filter_join) == 'and') 
					{
						// do a direct match if the values are integers and have _id in them
						if (preg_match('#_id#', $key) AND is_numeric($val))
						{
							$this->db->where(array($key => $val));
						}
						else
						{
							$this->db->like('LOWER('.$key.')', strtolower($val), 'both');
						}
					}
					else
					{
						// do a direct match if the values are integers and have _id in them
						if (preg_match('#_id#', $key) AND is_numeric($val))
						{
							$this->db->or_where(array($key => $val));
						}
						else
						{
							$this->db->or_like('LOWER('.$key.')', strtolower($val), 'both');
						}
					}
				}
			}
		}
		
		if (!empty($col) && !empty($order)) $this->db->order_by($col, $order);
		if (!empty($limit)) $this->db->limit($limit);
		$this->db->offset($offset);
		$query = $this->db->get($this->table_name);
		$data = $query->result_array();
		return $data;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Lists the total number of module items
	 *
	 * @access	public
	 * @return	int
	 */	
	function list_items_total()
	{
		return count($this->list_items());
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Saves data to the archive
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 * @return	void
	 */	
	function archive($ref_id, $data)
	{
		$CI =& get_instance();
		$CI->load->module_model(FUEL_FOLDER, 'archives_model');
		
		// grab archive to compare it to current data values... don't want to save if it isn't different
		$last_archive = $this->get_last_archive($ref_id, TRUE);
		$last_archive_data = (!empty($last_archive['data'])) ?  $last_archive['data'] : array();
		$last_archive_version = (!empty($last_archive['version'])) ? $last_archive['version'] : 0;
		
		// just return true if it's the same as the last version'
		$tmp_data = $data;
		
		// remove unimportant data from check
		$remove_from_check = array('last_modified', 'published');
		foreach($remove_from_check as $val)
		{
			if (!empty($last_archive_data[$val])) unset($last_archive_data[$val]);
			if (!empty($tmp_data[$val])) unset($tmp_data[$val]);
		}
		
		if (!empty($last_archive_data) && $last_archive_data == $tmp_data) {
			return true;
		}
		
		$user = $CI->fuel_auth->user_data();
		$save['ref_id'] = $ref_id;
		$save['table_name'] = $this->table_name;
		$save['archived_user_id'] = $user['id'];
		$save['version'] = $last_archive_version + 1;
		$save['data'] = serialize($data);
		if ($saved = $this->archives_model->save($save))
		{
			$num_versions = $this->archives_model->record_count(array('table_name' => $this->table_name, 'ref_id' => $ref_id));
			if ($num_versions > $CI->config->item('max_number_archived', 'fuel') )
			{
				$delete_version = ($last_archive_version - $CI->config->item('max_number_archived', 'fuel')) + 1;
				$where = array('table_name' => $this->table_name, 'ref_id' => $ref_id, 'version' => $delete_version);
				$this->archives_model->delete($where);
			}
		}
		return $saved;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Retrieves the last archived value
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 * @return	void
	 */	
	function get_last_archive($ref_id, $all_data = FALSE)
	{
		$CI =& get_instance();
		$CI->load->module_model(FUEL_FOLDER, 'archives_model');
		$archive = $this->archives_model->find_one_array(array('table_name' => $this->table_name, 'ref_id' => $ref_id), 'version_timestamp desc');
		if (!empty($archive['data']))
		{
			$archive['data'] = unserialize($archive['data']);
			return ($all_data) ? $archive : $archive['data'];
		}
		return array();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Retrieves an archived value
	 *
	 * @access	public
	 * @param	string
	 * @param	int
	 * @param	boolean
	 * @return	array
	 */	
	function get_archive($ref_id, $version = NULL, $all_data = FALSE)
	{
		$CI =& get_instance();
		$CI->load->module_model(FUEL_FOLDER, 'archives_model');
		$CI->load->helper('date');
		
		// best to use ref_id and version because it is more secure
		$where = array('table_name' => $this->table_name, 'ref_id' => $ref_id, 'version' => $version);
		$archive = $CI->archives_model->find_one_array($where);
		$return = $archive;
		$return['data'] = array();
		if (!empty($archive))
		{
			$data = unserialize($archive['data']);
			foreach($data as $key => $val)
			{
				// reformat dates
				if (is_date_format($val))
				{
					$date_ts = strtotime($val);
					$return['data'][$key] = english_date($val);
					$return['data'][$key.'_hour'] = date('h', $date_ts);
					$return['data'][$key.'_min'] = date('i', $date_ts);
					$return['data'][$key.'_ampm'] = date('a', $date_ts);
				}
				else
				{
					$return['data'][$key] = $val;
				}
			}
		}
		return ($all_data) ? $return : $return['data'];
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Restores module item from an archived value
	 *
	 * @access	public
	 * @param	string
	 * @param	int
	 * @return	boolean
	 */	
	function restore($ref_id, $version = NULL)
	{
		$archive = $this->get_archive($ref_id, $version);
		return $this->save($archive);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Get other listed module items excluding the currently displayed
	 *
	 * @access	public
	 * @param	string
	 * @param	int
	 * @return	boolean
	 */	
	function get_others($display_field, $id, $val_field = NULL)
	{
		if (empty($val_field)) $val_field = $this->key_field;
		$others = $this->options_list($val_field, $display_field);
		if (isset($others[$id])) unset($others[$id]);
		return $others;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Add FUEL specific changes to the form_fields method
	 *
	 * @access	public
	 * @param	string
	 * @param	int
	 * @return	boolean
	 */	
	function form_fields($values = array(), $related = array())
	{
		$fields = parent::form_fields($values, $related);
		$order = 1;
		
		
		// create default images
		$upload_path = assets_server_path('', 'images');
		$order = 1;
		foreach($fields as $key => $field)
		{
			$fields[$key]['order'] = $order;
			
			// get field names that end with _image
			if ($fields[$key]['type'] == 'string' AND substr($key, -5) == 'image' OR substr($key, -3) == 'img')
			{
				$img = '';
				if (!empty($values['id']))
				{
					if (!empty($values[$key])) $img = '<div class="img_display"><img src="'.img_path($values[$key]).'" style="float: right;"/></div>';
				}
			
				$fields[$key]['class'] = 'asset_select';
				$order++;
				$fields[$key.'_upload'] = array('order' => $order, 'before_html' => $img, 'label' => '... OR upload an image', 'upload_path' => $upload_path, 'type' => 'file', 'overwrite' => TRUE);
			}
			$order++;
		}
		
		$yes = lang('form_enum_option_yes');
		$no = lang('form_enum_option_no');
		if (isset($fields['published']))
		{
			$fields['published']['order'] = 9999;
			$fields['published']['options'] = array('yes' => $yes, 'no' => $no);
		}
		if (isset($fields['active']))
		{
			$fields['active']['order'] = 9999;
			$fields['active']['options'] = array('yes' => $yes, 'no' => $no);
		}

		return $fields;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Common query that will automatically hide non-published/active items from view on the front end
	 *
	 * @access	public
	 * @return	string
	 */	
	function _common_query()
	{
		if (!defined('FUEL_ADMIN'))
		{
			$fields = $this->fields();
			if (in_array('published', $fields)) $this->db->where(array($this->table_name.'.published' => 'yes'));
			if (in_array('active', $fields)) $this->db->where(array($this->table_name.'.active' => 'yes'));
		}
	}
	
}


// ------------------------------------------------------------------------

/**
 * Module record class
 *
 * @package		FUEL CMS
 * @subpackage	Models
 * @category	Models
 * @author		David McReynolds @ Daylight Studio
 */
class Base_module_record extends Data_record {

	protected $_parsed_fields = NULL;
	
	
	// --------------------------------------------------------------------
	
	/**
	 * Sets the fields to parse
	 *
	 * @access	public
	 * @param	string
	 * @return	array
	 */	
	function set_parsed_fields($fields)
	{
		$this->_parsed_fields = $fields;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns any fields that should be automatically parsed from the $this->_parsed_fields array
	 *
	 * @access	public
	 * @param	string
	 * @return	array
	 */	
	function get_parsed_fields()
	{
		$parsed = NULL;
		if (isset($this->_parsed_fields))
		{
			$parsed = $this->_parsed_fields;
		}
		else if (isset($this->_parent_model->parsed_fields))
		{
			$parsed = $this->_parent_model->parsed_fields;
		}
		return $parsed;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * After get hook that will parse fields automatically
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	string
	 */	
	function after_get($output, $var)
	{
		if (is_string($output))
		{
			$parsed_fields = $this->get_parsed_fields();
			if ($parsed_fields === TRUE OR 
				(is_array($parsed_fields) && in_array($var, $parsed_fields)))
			{
				$output = $this->_parse($output);
			}
		}
		return $output;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Get other listed module items excluding the currently displayed
	 *
	 * @access	protected
	 * @param	string
	 * @return	string
	 */	
	protected function _parse($output)
	{
		$CI =& get_instance();
		$CI->load->library('parser');
		$vars = $this->values();
		$output = $CI->parser->parse_string($output, $vars, TRUE);
		return $output;
	}
	
}