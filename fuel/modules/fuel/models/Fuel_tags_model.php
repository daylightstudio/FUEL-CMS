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
 * <strong>Fuel_tags_model</strong> is used for managing FUEL tags in the CMS
 * 
 * @package		FUEL CMS
 * @subpackage	Models
 * @category	Models
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/models/fuel_tags_model
 */

require_once('Base_module_model.php');

class Fuel_tags_model extends Base_module_model {

	public $filters = array('name', 'slug'); // Allows for filtering on both the name and the slug
	public $unique_fields = array('slug'); // The slug value must be unique
	public $linked_fields = array('name' => 'slug'); // the slug value should be the name field's value with the url_title function applied to it if there is no value specified
	public $foreign_keys = array('category_id' => array(FUEL_FOLDER => 'fuel_categories_model')); // to create the foreign key association with the fuel_categories model

	protected $friendly_name = 'Tags';
	protected $singular_name = 'Tag';
	
	
	// --------------------------------------------------------------------
	
	/**
	 * Constructor.
	 *
	 * @access	public
	 * @return	void
	 */	
	public function __construct()
	{
		parent::__construct('fuel_tags'); // table name

		$this->init_relationships();
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
		$categories_table = $this->_tables['fuel_categories'];
		$CI =& get_instance();
		$this->db->join($categories_table, $categories_table.'.id = '.$table.'.category_id', 'LEFT');

		if ($CI->fuel->language->has_multiple())
		{
			$this->db->select($table.'.id, '.$table.'.name, '.$table.'.slug, '.$categories_table.'.name as category, SUBSTRING('.$table.'.description, 1, 50) as description, '.$table.'.context, '.$table.'.language, '.$table.'.precedence, '.$table.'.published', FALSE);
		}
		else
		{
			$this->db->select($table.'.id, '.$table.'.name, '.$table.'.slug, '.$categories_table.'.name as category, SUBSTRING('.$table.'.description, 1, 50) as description, '.$table.'.context, '.$table.'.precedence, '.$table.'.published', FALSE);
		}
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
	 * Initializes belongs_to relationship
	 *
	 * @access	public
	 * @return	void
	 */	
	public function init_relationships()
	{
		$CI =& get_instance();

		$modules = $CI->fuel->modules->get(NULL, FALSE);

		$belongs_to = array();

		// loop through all the modules to check for has_many relationships
		unset($modules['tags']);
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
					$model_name = $module->info('model_name');
					$module_location = $module->info('model_location');

					if (is_array($rel))
					{
						if (isset($rel['model']) AND (($rel['model'] == 'tags' OR $rel['model'] == array(FUEL_FOLDER => 'tags')) 
							OR ($rel['model'] == 'fuel_tags_model' OR $rel['model'] == array(FUEL_FOLDER => 'fuel_tags_model'))))
						{
							$belongs_to[$mod_name] = array('model' => $model_name, 'module' => $module_location);
						}
						else if (current($rel) == 'tags' OR current($rel) == 'fuel_tags_model')
						{
							$belongs_to[$mod_name] = array('model' => $model_name, 'module' => $module_location);
						}
						else if (!empty($rel['model']))
						{
	
							$rel_model_name = $this->load_model($rel['model']);
							$rel_model = $this->CI->$rel_model_name;

							// test if the instantiated model uses the fuel_tags table or not
							if (method_exists($rel_model, 'table_name') AND $rel_model->table_name() == $rel_model->tables('fuel_tags'))
							{
								$belongs_to[$mod_name] = array('model' => $model_name, 'module' => $module_location);
							}
						}
					}
					else if (is_string($rel) AND ($rel == 'tags' OR $rel == 'fuel_tags_model'))
					{
						$belongs_to[$mod_name] = array('model' => $model_name, 'module' => $module_location);
					}
				}
			}
		}

		// set the belongs_to
		$this->belongs_to = $belongs_to;
	}

	/**
	 * Tree view that puts tags in a hierarchy based on their category
	 *
	 * @access	public
	 * @param	boolean 	Determines whether to return just published pages or not (optional... and ignored in the admin)
	 * @return	array 		An array that can be used by the Menu class to create a hierarchical structure
	 */	
	public function tree($just_published = FALSE)
	{
		$return = array(); 
		$where = ($just_published) ? array('published' => 'yes') : array();

		$categories = $this->fuel_categories_model->find_all_array($where); 

		foreach($categories as $category) 
		{ 
			$return[] = array('id' =>$category['id'], 'label' => $category['name'], 'parent_id' => $category['parent_id'], 'location' => ''); 
			$tags = $this->find_all_array( array('category_id'=>$category['id']), 'name asc' );
			foreach($tags as $tag){
				$return[] = array('id' => $tag['name'], 'label' => $tag['name'], 'parent_id' => $category['id'], 'location' => fuel_url('tags/edit/'.$tag['id'])); 
			}
			
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
		$fields['language'] = array('type' => 'select', 'options' => $this->fuel->language->options(), 'value' => $this->fuel->language->default_option(), 'hide_if_one' => TRUE, 'first_option' => lang('label_select_one'));
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
	public function options_list($key = 'id', $val = 'name', $where = array(), $order = TRUE)
	{
		$this->db->join($this->_tables['fuel_categories'], $this->_tables['fuel_categories'].'.id = '.$this->_tables['fuel_tags'].'.category_id', 'LEFT');

		// if (empty($key)) $key = $this->_tables['fuel_categories'].'.id';
		// if (empty($val)) $val = $this->_tables['fuel_categories'].'.name';
		if (empty($key)) $key = $this->_tables['fuel_tags'].'.id';
		if (empty($val)) $val = $this->_tables['fuel_tags'].'.name';

		// needed to prevent ambiguity
		if (strpos($key, '.') === FALSE AND strpos($key, '(') === FALSE)
		{
			$key = $this->_tables['fuel_tags'].'.'.$key;
		}

		// needed to prevent ambiguity
		if (strpos($val, '.') === FALSE AND strpos($val, '(') === FALSE)
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
	 * @param mixed parameter to pass to common query (optional)
	 * @return	void 	
	 */		
	public function _common_query($params = NULL)
	{
		parent::_common_query();
		$this->db->join($this->_tables['fuel_categories'], $this->_tables['fuel_categories'].'.id = '.$this->_tables['fuel_tags'].'.category_id', 'LEFT');
	}

}

class Fuel_tag_model extends Base_module_record {
	
}