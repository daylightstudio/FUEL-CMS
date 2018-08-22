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
 * @copyright	Copyright (c) 2018, Daylight Studio LLC.
 * @license		http://docs.getfuelcms.com/general/license
 * @link		http://www.getfuelcms.com
 */

// ------------------------------------------------------------------------

/**
 * Extends Base_module_model
 *
 * <strong>Fuel_categories_model</strong> is used for managing FUEL categories in the CMS
 * 
 * @package		FUEL CMS
 * @subpackage	Models
 * @category	Models
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/models/fuel_categories_model
 */

require_once('Base_module_model.php');

class Fuel_categories_model extends Base_module_model {

	public $required = array('name', 'slug'); // name and slug are required
	public $filters = array('name', 'slug', 'context'); // allows for the description field to be searchable as well as the name field
	public $linked_fields = array('slug' => 'name'); // the slug value should be the name field's value with the url_title function applied to it if there is no value specified
	public $unique_fields = array('slug'); // the slug field needs to be unique

	protected $friendly_name = 'Categories';
	protected $singular_name = 'Category';
	
	
	// --------------------------------------------------------------------
	
	/**
	 * Constructor.
	 *
	 * @access	public
	 * @return	void
	 */	
	public function __construct()
	{
		parent::__construct('fuel_categories'); // table name
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
	public function list_items($limit = NULL, $offset = NULL, $col = 'name', $order = 'asc', $just_count = FALSE)
	{
		$table = $this->table_name();
		$CI =& get_instance();
		if ($CI->fuel->language->has_multiple())
		{
			$this->db->select($table.'.id, '.$table.'.name, '.$table.'.slug, SUBSTRING('.$table.'.description, 1, 50) as description, '.$table.'.context, p.name as parent_id, '.$table.'.language, '.$table.'.precedence, '.$table.'.published', FALSE);
		}
		else
		{
			$this->db->select($table.'.id, '.$table.'.name, '.$table.'.slug, SUBSTRING('.$table.'.description, 1, 50) as description, '.$table.'.context, p.name as parent_id, '.$table.'.precedence, '.$table.'.published', FALSE);
		}
		$this->db->join($table.' AS p', $this->tables('fuel_categories').'.parent_id = p.id', 'left');
		$data = parent::list_items($limit, $offset, $col, $order, $just_count);
		if (empty($just_count))
		{
			foreach($data as $key => $val)
			{
				$data[$key]['description'] = htmlentities($val['description'], ENT_QUOTES, 'UTF-8');
			}
		}
		return $data;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Initializes the class with the parent model and field names
	 *
	 * @access	public
	 * @return	array
	 */	
	public function context_options_list()
	{
		$this->db->group_by('context');
		$this->db->where('context != ""');
		return parent::options_list('context', 'context');
	}

	// --------------------------------------------------------------------
	
	/**
	 * Tree view that puts categories in a hierarchy based on their parent value
	 *
	 * @access	public
	 * @param	boolean Determines whether to return just published pages or not (optional... and ignored in the admin)
	 * @return	array An array that can be used by the Menu class to create a hierarchical structure
	 */	
	public function tree($just_published = FALSE)
	{
		$return = array(); 
		$where = ($just_published) ? array('published' => 'yes') : array();
		$categories = $this->find_all_array($where); 
		foreach($categories as $category) 
		{ 
			$attributes = ((isset($category['published']) AND $category['published'] == 'no')) ? array('class' => 'unpublished', 'title' => 'unpublished') : NULL;
			$return[] = array('id' => $category['id'], 'label' => $category['name'], 'parent_id' => $category['parent_id'], 'location' => fuel_url('categories/edit/'.$category['id']), 'attributes' => $attributes); 
		}
		return $return;
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
	public function form_fields($values = array(), $related = array())
	{	
		$fields = parent::form_fields($values, $related);
		$fields['parent_id'] = array('type' => 'select', 'model' => 'fuel_categories', 'first_option' => lang('label_select_one'));

		// magically sets the options list view to remove the current category
		if (!empty($values['id']))
		{
			$this->db->where(array('id != ' => $values['id']));
		}

		$fields['language'] = array('type' => 'select', 'options' => $this->fuel->language->options(), 'value' => $this->fuel->language->default_option(), 'hide_if_one' => TRUE, 'first_option' => lang('label_select_one'));
		return $fields;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Overwrites the _common_query parent method to automatically sort by precedence value
	 *
	 * @access	public
	 * @param mixed parameter to pass to common query (optional)
	 * @return	array
	 */	
	public function _common_query($params = NULL)
	{
		parent::_common_query();
		$this->db->order_by('precedence asc');
	}

}


class Fuel_category_model extends Base_module_record {

	public $_belongs_to = array(); // contains all the modules that have a foreign key relationship
	
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
		unset($modules['categories']);
		foreach($modules as $module)
		{
			//grab each model
			$model = $module->model();
			if (!empty($model->foreign_keys))
			{
				// loop through the has_many relationships to see if any have a "categories" relationship
				foreach($model->foreign_keys as $key => $mod)
				{
					$mod_name = $module->name();
					if (is_array($mod) AND isset($mod[FUEL_FOLDER]) AND ($mod[FUEL_FOLDER] == 'fuel_categories_model' OR $mod[FUEL_FOLDER] == 'categories'))
					{
						$mod['model'] = $module->model();
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
	 * Find the children
	 *
	 * @access	public
	 * @param	mixed where conditions
	 * @param	string	the order by of the query (optional)
	 * @param	int		the number of records to limit in the results (optional)
	 * @param	int		the offset value for the results (optional)
	 * @return	mixed
	 */	
	public function get_children($where = array(), $order = NULL, $limit = NULL, $offset = NULL)
	{
		$where['parent_id'] = $this->id;
		$children = $this->_parent_model->find_all($where, $order, $limit, $offset);
		return $children;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Magic method that will allow you to return the model object by doing something like $category->get_products(TRUE);
	 *
	 * @access	public
	 * @param	string	field name
	 * @param	array	arguments
	 * @return	mixed
	 */	
	public function __call($method, $args)
	{
		if (preg_match("/^get_(.*)/", $method, $found))
		{
			$model = $this->get($found[1]);
			if ($model)
			{
				$data = (isset($args[0]) AND $args[0] === TRUE) ? $model : $model->find_all();
				return $data;
			}
		}
		return parent::__call($method, $args);
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