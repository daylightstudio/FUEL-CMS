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
 * @copyright	Copyright (c) 2013, Run for Daylight LLC.
 * @license		http://docs.getfuelcms.com/general/license
 * @link		http://www.getfuelcms.com
 */

// ------------------------------------------------------------------------

/**
 * Extends MY_Model to be specific to FUEL modules
 *
 * <strong>Base_module_model</strong> is the base class that should be extended when creating modules. 
 * The class should be required at the top of your module like so:
 *
 * 	<code>
 * 	&lt;?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
 * 	
 * 	require_once(FUEL_PATH.'models/base_module_model.php');
 * 	
 * 	class My_super_model extends Base_module_model {
 * 	...
 * 	</code>
 * 
 * @package		FUEL CMS
 * @subpackage	Models
 * @category	Models
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/models/base_module_model
 */

require_once(APPPATH.'core/MY_Model.php');

class Base_module_model extends MY_Model {
	
	public $filters = array(); // filters to apply to when searching for items
	public $filter_value = NULL; // the values of the filters
	public $filter_join = 'or'; // how to combine the filters in the query (and or or)
	public $parsed_fields = array(); // fields to automatically parse
	public $upload_data = array(); // data about all uploaded files
	public $ignore_replacement = array(); // the fields you wish to remain in tack when replacing (.e.g. location, slugs)
	public $display_unpublished_if_logged_in = FALSE; // determines whether to display unpublished content on the front end if you are logged in to the CMS
	public static $tables = array(); // cached array of table names that can be accessed statically
	protected $_formatters = array(
								'datetime'			=> array(
															'formatted' => 'date_formatter',
															'month', 
															'day', 
															'weekday', 
															'year', 
															'hour', 
															'minute', 
															'second',
															'pretty',
															'ts' => 'strtotime'),
								'date' 				=> array(
															'formatted' => 'date_formatter',
															'month', 
															'day', 
															'year', 
															'hour',
															'pretty',
															'ts' => 'strtotime'),
								'string'			=> array(
															'formatted'		=> 'auto_typography',
															'stripped' 		=> 'strip_tags', 
															'markdown',
															'parsed' 		=> 'parse_template_syntax',
															'excerpt'		=> 'character_limiter',
															'wordlimiter'  	=> 'word_limiter',
															'ellipsize',
															'censor'		=> 'word_censor',
															'highlight' 	=> 'highlight_phrase',
															'wrap' 			=> 'word_wrap',
															'entities' 		=> 'htmlentities',
															'specialchars'  => 'htmlspecialchars',
															'humanize',
															'underscore',
															'camelize',
															'upper'			=> 'strtoupper',
															'lower'			=> 'strtlower',
															),
								'number'			=> array(
															'currency',
															),
								'url|link|website'	=> array(
															'path' 			=> 'site_url',
															'prep'			=> 'prep_url',
															),
								'img|image|thumb'	=> array(
															'path' 			=> array('assets_path', 'images'),
															'serverpath' 	=> array('assets_server_path', 'images'),
															'filesize' 		=> array('asset_filesize', 'images', '', FALSE),
															'exists'		=> array('asset_exists', 'images', ''),
															),
								'pdf'				=> array(
															'path' 			=> 'pdf_path',
															'serverpath' 	=> array('assets_server_path', 'pdf'),
															'filesize' 		=> array('asset_filesize', 'pdf', '', FALSE),
															'exists'		=> array('asset_exists', 'pdf', ''),
															)
								); // default formatters which get merged into $formatters property array
	
	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	string	The table name
	 * @param	mixed	If an array, it will assume they are initialization properties. If a string, it will assume it's the name of the module the module exists in
	 * @return	void
	 */
	public function __construct($table = NULL, $params = NULL)
	{
		$CI = & get_instance();
		
		$CI->load->module_language(FUEL_FOLDER, 'fuel');
		
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
		if (!empty($module) AND $module != FUEL_FOLDER)
		{
			// fail gracefully is last parameter
			$CI->config->module_load($module, $module, FALSE, TRUE);
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
		self::$tables = array_merge(self::$tables, $config_tables, $module_tables, $fuel_tables);
		$this->set_tables(self::$tables);
		
		// set the table to the configuration mapping if it is in array
		if ($this->tables($table)) 
		{
			$table = $this->tables($table);
		}
		
		// if no configuration mapping is found then we will assume it is just the straight up table name
		parent::__construct($table, $params); // table name and params


		// load additional helpers here 
		$this->load->helper('typography');
		$this->load->helper('text');
		$this->load->helper('markdown');
		$this->load->helper('format');

		// set formatters
		if (!empty($this->_formatters) AND !empty($this->formatters))
		{
			$this->formatters = array_merge_recursive($this->_formatters, $this->formatters);	
		}
		else
		{
			$this->formatters = $this->_formatters;	
		}		
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Adds a filter for searching
	 *
	 * @access	public
	 * @param	string The name of the field to filter on
	 * @param	string A key to associate with the filter(optional)
	 * @return	void
	 */	
	public function add_filter($filter, $key = NULL)
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
	 * @param	array An array of fields to filter on
	 * @return	void
	 */	
	public function add_filters($filters)
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
	 * Adds a filter join such as "and" or "or" to a particular field
	 *
	 * @access	public
	 * @param	string The name of the field to filter on
	 * @param	string "and" or "or" (optional)
	 * @return	void
	 */	
	public function add_filter_join($field, $join_type = 'or')
	{
		if (!empty($this->filter_join) AND is_string($this->filter_join))
		{
			$this->filter_join = array($this->filter_join);
		}
		$this->filter_join[$field] = $join_type;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Lists the module's items
	 *
	 * @access	public
	 * @param	int The limit value for the list data (optional)
	 * @param	int The offset value for the list data (optional)
	 * @param	string The field name to order by (optional)
	 * @param	string The sorting order (optional)
	 * @param	boolean Determines whether the result is just an integer of the number of records or an array of data (optional)
	 * @return	mixed If $just_count is true it will return an integer value. Otherwise it will return an array of data (optional)
	 */	
	public function list_items($limit = NULL, $offset = 0, $col = 'id', $order = 'asc', $just_count = FALSE)
	{
		$this->_list_items_query();
		
		if ($just_count)
		{
			return $this->db->count_all_results();
		}

		if (empty($this->db->ar_select))
		{
			$this->db->select($this->table_name.'.*'); // make select table specific
		}
		
		if (!empty($col) AND !empty($order)) $this->db->order_by($col, $order);
		if (!empty($limit)) $this->db->limit($limit);
		$this->db->offset($offset);
		
		$query = $this->db->get();
		$data = $query->result_array();
		//$this->debug_query();
		return $data;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Creates the query logic for the list view using CI's active record. Separated out so that the count can use this method as well.
	 *
	 * @access	protected
	 * @return	void
	 */	
	protected function _list_items_query()
	{
		$this->filters = (array) $this->filters;
		$where_or = array();
		$where_and = array();
		foreach($this->filters as $key => $val)
		{
			if (is_int($key))
			{
				if (isset($this->filters[$val])) {
					continue;
				}
				$key = $val;
				$val = $this->filter_value;
			}
			else
			{
				// used for separating table names and fields since periods get translated to underscores
				$key = str_replace(':', '.', $key);
			}
			
			$joiner = $this->filter_join;
			
			if (is_array($joiner))
			{
				if (isset($joiner[$key]))
				{
					$joiner = $joiner[$key];
				}
				else
				{
					$joiner = 'or';
				}
			}

			if (!empty($val)) 
			{
				$joiner_arr = 'where_'.$joiner;
				
				if (strpos($key, '.') === FALSE) $key = $this->table_name.'.'.$key;
				
				//$method = ($joiner == 'or') ? 'or_where' : 'where';
				
				// do a direct match if the values are integers and have _id in them
				if (preg_match('#_id$#', $key) AND is_numeric($val))
				{
					//$this->db->where(array($key => $val));
					array_push($$joiner_arr, $key.'='.$val);
				}
				
				// from imknight https://github.com/daylightstudio/FUEL-CMS/pull/113#commits-pushed-57c156f
				//else if (preg_match('#_from#', $key) OR preg_match('#_to#', $key))
				else if (preg_match('#_from$#', $key) OR preg_match('#_fromequal$#', $key) OR preg_match('#_to$#', $key) OR preg_match('#_toequal$#', $key) OR preg_match('#_equal$#', $key))
				{
					//$key = strtr($key, array('_from' => ' >', '_fromequal' => ' >=', '_to' => ' <', '_toequal' => ' <='));
					$key_with_comparison_operator = preg_replace(array('#_from$#', '#_fromequal$#', '#_to$#', '#_toequal$#', '#_equal$#'), array(' >', ' >=', ' <', ' <=', ' ='), $key);
					//$this->db->where(array($key => $val));
					//$where_or[] = $key.'='.$this->db->escape($val);
					array_push($$joiner_arr, $key_with_comparison_operator.$this->db->escape($val));
				}
				else
				{
					//$method = ($joiner == 'or') ? 'or_like' : 'like';
					//$this->db->$method('LOWER('.$key.')', strtolower($val), 'both');
					array_push($$joiner_arr, 'LOWER('.$key.') LIKE "%'.strtolower($val).'%"');
				}
			}
		}
		
		// here we will group the AND and OR separately which should handle most cases me thinks... but if not, you can always overwrite
		$where = array();
		if (!empty($where_or))
		{
			$where[] = '('.implode(' OR ', $where_or).')';
		}
		if (!empty($where_and))
		{
			$where[] = '('.implode(' AND ', $where_and).')';
		}
		if (!empty($where))
		{
			$where_sql = implode(' AND ', $where);
			$this->db->where($where_sql);
		}
		
		
		// set the table here so that items total will work
		$this->db->from($this->table_name);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Lists the total number of module items
	 *
	 * @access	public
	 * @return	int The total number of items with filters applied
	 */	
	public function list_items_total()
	{
		$cnt = $this->list_items(NULL, NULL, NULL, NULL, TRUE);
		if (is_array($cnt))
		{
			return count($cnt);
		}
		return $cnt;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns a tree array structure that can be used by a public "tree" method on models inheriting from this class 
	 *
	 * @access	protected
	 * @param	string The name of the model's property to use to generate the tree. Options are 'foreign_fields', 'has_many' or 'belongs_to'
	 * @return	array An array that can be used by the Menu class to create a hierachical structure
	 */	
	protected function _tree($prop = NULL)
	{
		$CI =& get_instance();
		$return = array();


		if (!empty($this->foreign_keys) OR !empty($this->has_many) OR !empty($this->belongs_to))
		{
			if (empty($prop))
			{
				if (!empty($this->foreign_keys))
				{
					$p = $this->foreign_keys;
				}
				else if (!empty($this->has_many))
				{
					$p = $this->has_many;
				}
			}
			else if (property_exists($this, $prop))
			{
				$p = $this->$prop;
			}

			$key_field = key($p);

			// get related model info
			$rel_module = current($p);
			if (is_array($rel_module))
			{
				$rel_module =  current($rel_module);
			}
			$rel_module_obj =  $CI->fuel->modules->get($rel_module, FALSE);

			if (!$rel_module_obj)
			{
				return array();
			}
			$rel_model = $rel_module_obj->model();
			$rel_key_field = $rel_model->key_field();
			$rel_display_field = $rel_module_obj->info('display_field');


			$module = $this->table_name();
			$module_obj =  $CI->fuel->modules->get($module, FALSE);
			if (!$module_obj)
			{
				return array();
			}
			$model = $module_obj->model();
			$display_field = $module_obj->info('display_field');
			$rel_col = !empty($rel_module_obj->default_col) ? $rel_module_obj->default_col : $this->key_field();
			$rel_order = !empty($rel_module_obj->default_order) ? $rel_module_obj->default_order : 'asc';

			if ($prop == 'foreign_keys')
			{
				$groups = $rel_model->find_all_array(array(), $rel_model->key_field().' asc');
				$children = $this->find_all_array(array(), $key_field.' asc');
				$g_key_field = $key_field;
			}
			else if ($prop == 'has_many')
			{
				$CI->load->module_model(FUEL_FOLDER, 'fuel_relationships_model');
				$groups = $rel_model->find_all_array(array(), $rel_col.' '.$rel_order);
				$children = $CI->fuel_relationships_model->find_by_candidate($this->table_name(), $rel_model->table_name(), NULL, 'array');
				$key_field = 'foreign_id';
				$g_key_field = 'candidate_id';
				$display_field = 'candidate_'.$display_field;
			}
			else if ($prop == 'belongs_to')
			{
				$CI->load->module_model(FUEL_FOLDER, 'fuel_relationships_model');
				$groups = $rel_model->find_all_array(array(), $rel_col.' '.$rel_order);
				$children = $CI->fuel_relationships_model->find_by_candidate($rel_model->table_name(), $this->table_name(), NULL, 'array');
				$key_field = 'candidate_id';
				$g_key_field = 'foreign_id';
				$display_field = 'foreign_'.$display_field;
			}

			// now get this models records
			foreach($children as $child)
			{
				$used_groups[$child[$key_field]] = $child[$key_field];
				$attributes = ((isset($child['published']) AND $child['published'] == 'no') OR (isset($child['active']) AND $child['active'] == 'no')) ? array('class' => 'unpublished', 'title' => 'unpublished') : NULL;
				$return['g'.$child[$g_key_field].'_c_'.$child[$key_field]] = array('parent_id' => $child[$key_field], 'label' => $child[$display_field], 'location' => fuel_url($module_obj->name().'/edit/'.$child[$key_field]), 'attributes' => $attributes);
			}

			foreach($groups as $group)
			{
				if (isset($used_groups[$group[$rel_key_field]]))
				{
					$attributes = ((isset($group['published']) AND $group['published'] == 'no') OR (isset($group['active']) AND $group['active'] == 'no')) ? array('class' => 'unpublished', 'title' => 'unpublished') : NULL;
					$return[$group[$rel_key_field]] = array('id' => $group[$rel_key_field], 'parent_id' => 0, 'label' => $group[$rel_display_field], 'location' => fuel_url($rel_module_obj->name().'/edit/'.$group[$rel_key_field]), 'attributes' => $attributes);	
				}
				
			}
		}
		return $return;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Saves data to the archive
	 *
	 * @access	public
	 * @param	int The record ID associated with the archive
	 * @param	array The array of data to be archived
	 * @return	boolean Whether it was saved properly or not
	 */	
	public function archive($ref_id, $data)
	{
		$CI =& get_instance();
		$CI->load->module_model(FUEL_FOLDER, 'fuel_archives_model');
		
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
		
		if (!empty($last_archive_data) AND $last_archive_data == $tmp_data) {
			return true;
		}

		// save to archive
		$user = $CI->fuel->auth->user_data();
		$save['ref_id'] = $ref_id;
		$save['table_name'] = $this->table_name;
		$save['archived_user_id'] = $user['id'];
		$save['version'] = $last_archive_version + 1;
		$save['data'] = json_encode($data);
		if ($saved = $this->fuel_archives_model->save($save))
		{
			$num_versions = $this->fuel_archives_model->record_count(array('table_name' => $this->table_name, 'ref_id' => $ref_id));
			if ($num_versions > $CI->config->item('max_number_archived', 'fuel') )
			{
				$delete_version = ($last_archive_version - $CI->config->item('max_number_archived', 'fuel')) + 1;
				$where = array('table_name' => $this->table_name, 'ref_id' => $ref_id, 'version' => $delete_version);
				$this->fuel_archives_model->delete($where);
			}
		}
		return $saved;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Retrieves the last archived value
	 *
	 * @access	public
	 * @param	int The record ID associated with the archive
	 * @param	boolean Determines whether to return all of the archives fields or just the data field value (optional)
	 * @return	array
	 */	
	public function get_last_archive($ref_id, $all_data = FALSE)
	{
		$CI =& get_instance();
		$CI->load->module_model(FUEL_FOLDER, 'fuel_archives_model');
		$archive = $this->fuel_archives_model->find_one_array(array('table_name' => $this->table_name, 'ref_id' => $ref_id), 'version_timestamp desc');
		if (!empty($archive['data']))
		{
			$archive['data'] = (is_serialized_str($archive['data'])) ? @unserialize($archive['data']) : json_decode($archive['data'], TRUE);
			return ($all_data) ? $archive : $archive['data'];
		}
		return array();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Retrieves an archived value
	 *
	 * @access	public
	 * @param	int The record ID associated with the archive
	 * @param	int The version of the archive to retrieve (optional)
	 * @param	boolean Determines whether to return all of the archives fields or just the data field value (optional)
	 * @return	array
	 */	
	public function get_archive($ref_id, $version = NULL, $all_data = FALSE)
	{
		$CI =& get_instance();
		$CI->load->module_model(FUEL_FOLDER, 'fuel_archives_model');
		$CI->load->helper('date');
		
		// best to use ref_id and version because it is more secure
		$where = array('table_name' => $this->table_name, 'ref_id' => $ref_id, 'version' => $version);
		$archive = $CI->fuel_archives_model->find_one_array($where);
		$return = $archive;
		$return['data'] = array();
		if (!empty($archive))
		{
			// check for serialization for backwards compatibility
			$data = (is_serialized_str($archive['data'])) ? @unserialize($archive['data']) : json_decode($archive['data'], TRUE);
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
	 * @param	int The record ID associated with the archive
	 * @param	int The version of the archive to retrieve (optional)
	 * @return	boolean Whether it was saved properly or not
	 */	
	public function restore($ref_id, $version = NULL)
	{
		$archive = $this->get_archive($ref_id, $version);
		return $this->save($archive);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Get other listed module items excluding the currently displayed
	 *
	 * @access	public
	 * @param	string The field name used as the label
	 * @param	int The current value... and actually deprecated (optional)
	 * @param	string The value field (optional)
	 * @return	array Key/value array
	 */	
	public function get_others($display_field, $id = NULL, $val_field = NULL)
	{
		$orderby = TRUE;
		if (empty($val_field))
		{
			$CI =& get_instance();
			if (!empty($CI->language_col))
			{
				$fields = $this->fields();

				if (in_array($CI->language_col, $fields))
				{
					$display_field = 'CONCAT('.$display_field.', " - ", '.$CI->language_col.') AS val_field';
					$orderby = 'val_field ASC';
				}
			}
			else
			{
				$val_field = $this->table_name.'.'.$this->key_field;	
			}
		}
		$others = $this->options_list($val_field, $display_field, NULL, $orderby);

		// COMMENTED OUT BECAUSE WE DISABLE IT IN THE DROPDOWN INSTEAD
		//if (isset($others[$id])) unset($others[$id]);
		return $others;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns a key/value array of a distinct set of languages associated with records that have a language field
	 *
	 * @access	public
	 * @param	string The name of the field used to determine which language. if empty, it will default to 'language' (optional)
	 * @return	array
	 */	
	public function get_languages($field = NULL)
	{
		if (empty($field))
		{
			$field = 'language';
		}
		$fields = $this->fields();
		
		if (!in_array($field, $fields))
		{
			return array();
		}
		else
		{
			$this->db->distinct($field);
		}

		$where[$field.' !='] = '';
		$options = $this->options_list($field, $field, $where, TRUE, FALSE);
		return $options;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Replaces an existing record with another record
	 *
	 * @access	public
	 * @param	int The old record id of data that will be replaced
	 * @param	int The new record id of data that will be used for the replacement
	 * @param	boolean Determines whether to delete the old record (optional)
	 * @return	boolean Whether it was saved properly or not
	 */	
	public function replace($replace_id, $id, $delete = TRUE)
	{
		$replace_values = $this->find_by_key($replace_id, 'array');
		$new_values = $this->find_by_key($id, 'array');

		// set values to save based on the new $id
		$values = $new_values;
		
		if (!empty($this->ignore_replacement))
		{
			// ignore certain fields by setting them to their old values
			foreach($this->ignore_replacement as $field)
			{
				if (isset($values[$field]))
				{
					$values[$field] = $replace_values[$field];
					//unset($values[$field]);
				}
			}
		}
		
		// set the id to be that of the old replace_id
		$values[$this->key_field()] = $replace_id;
		
		// must delete before saving to prevent errors
		if ($delete)
		{
			$where[$this->key_field()] = $id;
			$this->delete($where);
		}

		// save values
		$saved = $this->save($values);

		// archive values saving
		$this->archive($replace_id, $values);
		
		return $saved;
		
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns CSV data that will be downloaded automatically. Overwrite this method for more specific output
	 *
	 * @access	public
	 * @param	array An array that contains "col", "order", "offset", "limit", "searh_term" to help with the formatting of the output. By default only the "col" and "order" parameters are used (optional)
	 * @return	string
	 */	
	public function export_data($params = array())
	{
		// normalize parameters
		$valid_params = array('col', 'order');
		foreach($valid_params as $p)
		{
			if (!isset($params[$p]))
			{
				$params[$p] = NULL;
			}
		}
		
		$items = $this->list_items(NULL, NULL, $params['col'], $params['order']);
		$data = $this->csv($items);
		return $data;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Placeholder for return data that appears in the right side when editing a record (e.g. Related Navigation in pages module )
	 *
	 * @access	public
	 * @param	array View variable data (optional)
	 * @return	mixed Can be an array of items or a string value
	 */	
	public function related_items($params = array())
	{
		return array();
	}

	// --------------------------------------------------------------------
	
	/**
	 * Add FUEL specific changes to the form_fields method
	 *
	 * @access	public
	 * @param	array Values of the form fields (optional)
	 * @param	array An array of related fields. This has been deprecated in favor of using has_many and belongs to relationships (deprecated)
	 * @return	array An array to be used with the Form_builder class
	 */	
	public function form_fields($values = array(), $related = array())
	{
		$fields = parent::form_fields($values, $related);
		$order = 1;
		
		
		// create default images
		$upload_path = assets_server_path('', 'images');
		$order = 1;
		foreach($fields as $key => $field)
		{
			$fields[$key]['order'] = $order;
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
	 * @param	boolean	whether to display unpublished content in the front end if logged in
	 * @return	void
	 */	
	public function _common_query($display_unpublished_if_logged_in = NULL)
	{
		if (!isset($display_unpublished_if_logged_in))
		{
			$display_unpublished_if_logged_in = $this->display_unpublished_if_logged_in;
		}
		
		if ((!defined('FUEL_ADMIN') AND $display_unpublished_if_logged_in === FALSE) OR ($display_unpublished_if_logged_in AND !is_fuelified()))
		{
			$this->_publish_status();
		}
	}
	
	
	// --------------------------------------------------------------------
	
	/**
	 * Used for displaying content that is published
	 *
	 * @access	protected
	 * @return	void
	 */	
	protected function _publish_status()
	{
		$fields = $this->fields();

		if (in_array('published', $fields))
		{
			if (in_array('published', $this->boolean_fields))
			{
				$this->db->where(array($this->table_name.'.published' => 1));
			}
			else
			{
				$this->db->where(array($this->table_name.'.published' => 'yes'));
			}
		}

		if (in_array('active', $fields))
		{
			if (in_array('active', $this->boolean_fields))
			{
				$this->db->where(array($this->table_name.'.active' => 1));
			}
			else
			{
				$this->db->where(array($this->table_name.'.active' => 'yes'));
			}
		}

		if (in_array('publish_date', $fields))
		{
			$this->db->where(array('publish_date <=' => datetime_now()));
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
	public function set_parsed_fields($fields)
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
	public function get_parsed_fields()
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
	public function after_get($output, $var)
	{
		if (is_string($output))
		{
			$parsed_fields = $this->get_parsed_fields();
			if ($parsed_fields === TRUE OR 
				(is_array($parsed_fields) AND in_array($var, $parsed_fields)))
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
		$this->_CI->load->library('parser');
		$vars = $this->values();
		$output = $this->_CI->parser->parse_string($output, $vars, TRUE);
		return $output;
	}
	
}
