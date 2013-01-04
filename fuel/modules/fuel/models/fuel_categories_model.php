<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH.'models/base_module_model.php');

class Fuel_categories_model extends Base_module_model {

	public $filters = array('name', 'slug', 'context');
	public $required = array('name', 'slug');
	public $linked_fields = array('slug' => 'name');
	public $unique_fields = array('slug');

	public $boolean_fields = array();
	public $belongs_to = array();
	public $has_many = array();
	public $serialized_fields = array();

	protected $friendly_name = 'Categories';
	protected $singular_name = 'Category';
	
	
	// --------------------------------------------------------------------
	
	/**
	 * Constructor.
	 *
	 * @access	public
	 * @return	void
	 */	
	function __construct()
	{
		parent::__construct('fuel_categories'); // table name
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
	function list_items($limit = NULL, $offset = NULL, $col = 'precedence', $order = 'desc')
	{
		$data = parent::list_items($limit, $offset, $col, $order);
		return $data;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Initializes the class with the parent model and field names
	 *
	 * @access	public
	 * @return	array
	 */	
	function context_options_list()
	{
		$this->db->group_by('context');
		return parent::options_list('context', 'context');
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
		$fields['parent_id'] = array('type' => 'select', 'model' => 'fuel_categories', 'first_option' => lang('label_select_one'));

		// magically sets the options list view to remove the current category
		if (!empty($values['id']))
		{
			$this->db->where(array('id != ' => $values['id']));
		}
		return $fields;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Overwrites the on_after_save parent method and doesn't call the parent
	 *
	 * @access	public
	 * @param	array	values
	 * @return	array
	 */	
	function on_after_save($values)
	{
		return $values;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Overwrites the _common_query parent method to automatically sort by precedence value
	 *
	 * @access	public
	 * @param	array	values
	 * @return	array
	 */	
	function _common_query()
	{
		parent::_common_query();
		$this->db->order_by('precedence asc');
	}

}

class Fuel_category_model extends Base_module_record {

	// contains all the modules that have a foreign key relationship
	public $_belongs_to = array();
	
	// --------------------------------------------------------------------
	
	/**
	 * Initializes the class with the parent model and field names
	 *
	 * @access	public
	 * @param	object	parent model object
	 * @param	array	field names
	 * @return	array
	 */	
	public function initialize(&$parent, $fields = array())
	{
		parent::initialize($parent, $fields);

		$modules = $this->_CI->fuel->modules->get(NULL, FALSE);

		$belongs_to = array();

		// loop through all the modules to check for foreign_key relationships
		unset($modules['categories'], $modules['tags']);
		foreach($modules as $module)
		{
			//grab each model
			$model = $module->model();
			if (!empty($model->foreign_keys))
			{
				// loop through the has_many relationships to see if any have a "tags" relationship
				foreach($model->foreign_keys as $key => $mod)
				{
					$mod_name = $module->name();
					if (is_array($mod) AND isset($mod[FUEL_FOLDER]) AND ($mod[FUEL_FOLDER] == 'fuel_categories_model' OR $mod[FUEL_FOLDER] == 'categories'))
					{
						$mod['model'] =& $module->model();
						$mod['key'] = $key;
						$belongs_to[$mod_name] = $mod;
					}
				}
			}
		}

		// set the belongs_to
		$this->_belongs_to = $belongs_to;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns a related category model with the active record query already applied
	 *
	 * @access	public
	 * @param	string	related slug value
	 * @return	array
	 */	
	public function get($var)
	{
		if (isset($this->_belongs_to[$var]))
		{
			if (!empty($this->_belongs_to['where']))
			{
				$where = $this->_belongs_to['where'];	
			}
			$model =& $this->_belongs_to[$var]['model'];
			$key = $this->_belongs_to[$var]['key'];
			$key_field = $this->_parent_model->key_field();
			$where[$model->table_name().'.'.$key] = $this->_fields[$key_field];
			$model->db()->where($where);
			return $model;
		}
		return FALSE;
	}
	// --------------------------------------------------------------------
	
	/**
	 * Magic method to return first property, method, then field values 
	 *
	 * @access	public
	 * @param	string	field name
	 * @return	mixed
	 */	
	public function __get($var)
	{
		$model = $this->get($var);
		if ($model)
		{
			$data = $model->find_all();
			return $data;
		}
		return parent::__get($var);
	}
}