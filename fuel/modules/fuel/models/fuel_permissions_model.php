<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Fuel_permissions_model extends Base_module_model {
	
	public $required = array('name', 'description');
	public $unique_fields = array('name');
	public $belongs_to = array('users' => array('model' => array(FUEL_FOLDER => 'fuel_users_model'), 'where' => array('super_admin' => 'no')));	

	function __construct()
	{
		parent::__construct('fuel_permissions');
	}
	
	function list_items($limit = NULL, $offset = NULL, $col = 'name', $order = 'asc')
	{
		$this->db->select('id, name, description, active');
		$data = parent::list_items($limit, $offset, $col, $order);
		return $data;
	}
	
	function form_fields($values = array(), $related = array())
	{
		$fields = parent::form_fields($values, $related);
		$other_perms_options = array('create', 'edit', 'publish', 'delete', 'export');
		if (empty($values['id']))
		{
			$values = $other_perms_options;
			array_pop($values);
			$fields['other_perms'] = array('type' => 'multi', 'options' => array_combine($other_perms_options, $other_perms_options), 'value' => $values);
		}
		return $fields;
	}
	
	// must use on_after_post to avoid recursion of save
	function on_after_post($values)
	{
		$values = parent::on_after_save($values);
		$data = $this->normalized_save_data;
		
		if (isset($data['other_perms']) AND is_array($data['other_perms']) AND !empty($values['name']))
		{
			$module = $values['name'];
			$CI =& get_instance();
			$CI->fuel->permissions->create_simple_module_permissions($module, $data['other_perms']);
		}
		return $values;
	}

	// displays related items on the right side
	function related_items($values = array())
	{
		$CI =& get_instance();
		$return = array();

		if (!empty($values['name']))
		{
			$name = current(explode('/', $values['name']));
			$this->db->where('(name LIKE "'.$name.'/%" OR name ="'.$name.'") AND name != "'.$values['name'].'"');
			$related_items = $this->find_all_array_assoc('id');
			if (!empty($related_items))
			{
				$return['permissions'] = array();

				foreach($related_items as $key => $item)
				{
					$label = $item['description'];
					$return['permissions']['edit/'.$key] = $label;
				}
			}
		}
		return $return;
	}
}


class Fuel_permission_model extends Data_record {
}