<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH.'models/base_module_model.php');

class Categories_model extends Base_module_model {

	public $filters = array('name', 'slug', 'context');
	public $required = array('name', 'slug');
	public $linked_fields = array('slug' => 'name');
	public $unique_fields = array('slug');

	public $boolean_fields = array();
	public $belongs_to = array();
	public $has_many = array();
	public $serialized_fields = array();
	
	
	function __construct()
	{
		parent::__construct('categories'); // table name
	}

	function list_items($limit = null, $offset = null, $col = 'precedence', $order = 'desc')
	{
		$data = parent::list_items($limit, $offset, $col, $order);
		return $data;
	}

	function context_options_list()
	{
		$this->db->group_by('context');
		return parent::options_list('context', 'context');
	}

	function form_fields($values = array(), $related = array())
	{	
		$fields = parent::form_fields($values, $related);
		return $fields;
	}
	
	function on_after_save($values)
	{
		return $values;
	}
}

class Category_model extends Base_module_record {

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
					if (is_array($mod) AND isset($mod[FUEL_FOLDER]) AND $mod[FUEL_FOLDER] == 'categories_model')
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
	 * Returns a related category item
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
			$where[$model->table_name().'.'.$key] = $this->_fields['id'];
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