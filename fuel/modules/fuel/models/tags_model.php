<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH.'models/base_module_model.php');

class Tags_model extends Base_module_model {

	public $record_class = 'Tag';
	public $filters = array('name', 'slug');
	public $unique_fields = array('name', 'slug');
	public $linked_fields = array('name' => 'slug');
	
	function __construct()
	{
		parent::__construct('tags'); // table name
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
						if (isset($rel['model']) AND ($rel['model'] == 'tags' OR $rel['model'] == array(FUEL_FOLDER => 'tags')))
						{
							$belongs_to[$mod_name] = $mod_name;
						}
						else if (current($rel) == 'tags')
						{
							$belongs_to[$mod_name] = $mod_name;	
						}
					}
					else if (is_string($rel) AND $rel == 'tags')
					{
						$belongs_to[$mod_name] = $mod_name;
					}
				}
			}
		}

		// set the belongs_to
		$this->belongs_to = $belongs_to;
	}

	function list_items($limit = null, $offset = null, $col = 'precedence', $order = 'desc')
	{
		$data = parent::list_items($limit, $offset, $col, $order);
		return $data;
	}

	function form_fields($values = array(), $related = array())
	{	
		$fields = parent::form_fields($values, $related);
		$CI =& get_instance();
		$fields['category_id'] = array('type' => 'select', 'label' => 'Category', 'module' => 'categories', 'model' => array(FUEL_FOLDER => 'categories'), 'first_option' => 'Select a category...');
		return $fields;
	}

	function options_list($key = 'id', $val = 'name', $where = array(), $order = TRUE)
	{
		$this->db->join($this->_tables['categories'], $this->_tables['categories'].'.id = '.$this->_tables['tags'].'.category_id', 'LEFT');

		// needed to prevent ambiguity
		if (strpos($key, '.') === FALSE)
		{
			$key = $this->_tables['tags'].'.id';
		}

		// needed to prevent ambiguity
		if (strpos($val, '.') === FALSE)
		{
			$val = $this->_tables['tags'].'.name';
		}
		$options = parent::options_list($key, $val, $where, $order);
		return $options;
	}
	
	function _common_query()
	{
		parent::_common_query();
		$this->db->join($this->_tables['categories'], $this->_tables['categories'].'.id = '.$this->_tables['tags'].'.category_id', 'LEFT');
	}

	function on_after_save($values)
	{
		parent::on_after_save($values);
		return $values;
	}
}

class Tag_model extends Base_module_record {
	
	// put your record model code here
}