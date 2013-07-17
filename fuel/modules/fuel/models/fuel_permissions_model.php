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
 * Extends Base_module_model
 *
 * <strong>Fuel_permissions_model</strong> is used for managing FUEL users in the CMS
 * 
 * @package		FUEL CMS
 * @subpackage	Models
 * @category	Models
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/models/fuel_permissions_model
 */

require_once('base_module_model.php');

class Fuel_permissions_model extends Base_module_model {
	
	public $required = array('name', 'description'); // The name and description value are required
	public $unique_fields = array('name'); // The name needs to be a unique value
	public $belongs_to = array('users' => array('model' => array(FUEL_FOLDER => 'fuel_users_model'), 'where' => array('super_admin' => 'no')));	// Permissions have a "belong_to" relationship with users

	// --------------------------------------------------------------------
	
	/**
	 * Constructor.
	 *
	 * @access	public
	 * @return	void
	 */	
	public function __construct()
	{
		parent::__construct('fuel_permissions');
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Lists the permission items
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
		$this->db->select('id, name, description, active');
		$data = parent::list_items($limit, $offset, $col, $order, $just_count);
		return $data;
	}
	
	public function tree()
	{
		$CI =& get_instance();
		// first get the permissions
		$perms_list = $CI->fuel_permissions_model->find_all_array_assoc('name', array(), 'name asc');
		$perms = array();

		foreach($perms_list as $perm => $perm_val)
		{
			$sub = explode('/', $perm);

			$parent_id = (isset($sub[1])) ? $sub[0] : 0;
			$sub_attributes = ($perm_val['active'] == 'no') ? array('class' => 'unpublished', 'title' => 'unpublished') : NULL;
			$perms[$perm] = array('label' => $perm_val['description'], 'parent_id' => $parent_id, 'location' => fuel_url('permissions/edit/'.$perm_val['id']), 'attributes' => $sub_attributes);
		}
		return $perms;
	}
	// --------------------------------------------------------------------
	
	/**
	 * User form fields
	 *
	 * @access	public
	 * @param	array Values of the form fields (optional)
	 * @param	array An array of related fields. This has been deprecated in favor of using has_many and belongs to relationships (deprecated)
	 * @return	array An array to be used with the Form_builder class
	 */	
	public function form_fields($values = array(), $related = array())
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
	
	// --------------------------------------------------------------------
	
	/**
	 * Model hook executed at the end of the post cycle (after saving). Used to avoid recursion of save
	 *
	 * @access	public
	 * @param	array The values that were just saved
	 * @return	array Returns the values that were saved
	 */	
	// 
	public function on_after_post($values)
	{
		$values = parent::on_after_save($values);
		$data = $this->normalized_save_data;
		
		if (isset($data['other_perms']) AND is_array($data['other_perms']) AND !empty($values['name']))
		{
			$module = $values['name'];
			$CI =& get_instance();
			$other_perms = $CI->fuel->permissions->create_simple_module_permissions($module, $data['other_perms']);

			$users = $CI->input->post('users');
			if (!empty($users))
			{
				// get the IDS of the other perms
				$perm_ids = array();
				foreach($other_perms as $op)
				{
					$perm = $this->find_one_array(array('name' => $op['name']));
					$perm_ids[] = $perm['id'];
				}

				// now associate the other users to those perms
				$CI->load->module_model(FUEL_FOLDER, 'fuel_users_model');
				foreach($users as $user_id)
				{
					$user = $CI->fuel_users_model->find_by_key($user_id);
					if (isset($user->id))
					{
						$model = $user->get_permissions(TRUE);
						$user_perms = $model->find_all_array_assoc('id');
						$user_perm_ids = array_keys($user_perms);

						$user->permissions = array_merge($user_perm_ids, $perm_ids);
						$user->save();
					}
				}
			}
		}
		return $values;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Displays related items on the right side
	 *
	 * @access	public
	 * @param	array View variable data (optional)
	 * @return	mixed Can be an array of items or a string value
	 */	
	public function related_items($values = array())
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