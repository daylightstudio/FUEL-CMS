<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH.'models/base_module_model.php');

class Fuel_tags_model extends Base_module_model {

	public $filters = array('name', 'slug');
	public $unique_fields = array('slug');
	public $linked_fields = array('name' => 'slug');

	protected $friendly_name = 'Tags';
	protected $singular_name = 'Tag';
	
	
	// --------------------------------------------------------------------
	
	/**
	 * Constructor. Automatically assigns belongs_to 
	 *
	 * @access	public
	 * @return	void
	 */	
	function __construct()
	{
		parent::__construct('fuel_tags'); // table name

		$this->init_relationships();
	}

	// --------------------------------------------------------------------
	
	/**
	 * Constructor. Automatically assigns belongs_to 
	 *
	 * @access	public
	 * @return	void
	 */	
	function init_relationships()
	{
		$CI =& get_instance();

		$modules = $CI->fuel->modules->get(NULL, FALSE);

		$belongs_to = array();

		// loop through all the modules to check for has_many relationships
		unset($modules['categories'], $modules['tags']);
		foreach($modules as $module)
		{
			// grab each model
			$model = $module->model();
			if (!empty($model->has_many))
			{

				// loop through the has_many relationships to see if any have a "tags" relationship
				foreach($model->has_many as $key => $rel)
				{
					$mod_name = $module->name();
					if (is_array($rel))
					{
						if (isset($rel['model']) AND (($rel['model'] == 'tags' OR $rel['model'] == array(FUEL_FOLDER => 'tags')) 
							OR ($rel['model'] == 'fuel_tags_model' OR $rel['model'] == array(FUEL_FOLDER => 'fuel_tags_model'))))
						{
							$belongs_to[$mod_name] = $mod_name;
						}
						else if (current($rel) == 'tags' OR current($rel) == 'fuel_tags_model')
						{
							$belongs_to[$mod_name] = $mod_name;	
						}
					}
					else if (is_string($rel) AND ($rel == 'tags' OR $rel == 'fuel_tags_model'))
					{
						$belongs_to[$mod_name] = $mod_name;
					}
				}
			}
		}

		// set the belongs_to
		$this->belongs_to = $belongs_to;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Initializes the class with the parent model and field names
	 *
	 * @access	public
	 * @param	int	the number in which to limit the returned data results (optional)
	 * @param	int	the number in which to offset the returned data results (optional)
	 * @param	string	the column name to sort on (optional)
	 * @param	string	the order in which to return the results (optional)
	 * @return	array
	 */	
	function list_items($limit = null, $offset = null, $col = 'precedence', $order = 'desc')
	{
		$data = parent::list_items($limit, $offset, $col, $order);
		return $data;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Creates the form_fields array to be used with Form_builder
	 *
	 * @access	public
	 * @param	array	an array of values to pass to the form fields
	 * @param	array	related field information
	 * @return	array
	 */	
	function form_fields($values = array(), $related = array())
	{	
		$fields = parent::form_fields($values, $related);
		$CI =& get_instance();
		$fields['category_id'] = array('type' => 'select', 'label' => 'Category', 'module' => 'categories', 'model' => array(FUEL_FOLDER => 'fuel_categories'), 'first_option' => 'Select a category...');
		return $fields;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Overwrites the parent option_list method 
	 *
	 * @access	public
	 * @param	string	the name of the field to be used as the key (optional)
	 * @param	string	the name of the filed to be used as the value (optional)
	 * @param	mixed	the where condition to apply (optional)
	 * @param	mixed	the order in which to return the results (optional)
	 * @return	array 	
	 */	
	function options_list($key = 'id', $val = 'name', $where = array(), $order = TRUE)
	{
		$this->db->join($this->_tables['fuel_categories'], $this->_tables['fuel_categories'].'.id = '.$this->_tables['fuel_tags'].'.category_id', 'LEFT');

		if (empty($key)) $key = 'id';
		if (empty($val)) $val = 'name';

		// needed to prevent ambiguity
		if (strpos($key, '.') === FALSE)
		{
			$key = $this->_tables['fuel_tags'].'.'.$key;
		}

		// needed to prevent ambiguity
		if (strpos($val, '.') === FALSE)
		{
			$val = $this->_tables['fuel_tags'].'.'.$val;
		}
	
		$options = parent::options_list($key, $val, $where, $order);
		return $options;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Common query to automatically join the categories table
	 *
	 * @access	public
	 * @return	void 	
	 */		
	function _common_query()
	{
		parent::_common_query();
		$this->db->join($this->_tables['fuel_categories'], $this->_tables['fuel_categories'].'.id = '.$this->_tables['fuel_tags'].'.category_id', 'LEFT');
	}

}

class Fuel_tag_model extends Base_module_record {
	
	// put your record model code here
}