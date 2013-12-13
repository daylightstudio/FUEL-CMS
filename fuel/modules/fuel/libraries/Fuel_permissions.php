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
 * @copyright	Copyright (c) 2013, Run for Daylight LLC.
 * @license		http://docs.getfuelcms.com/general/license
 * @link		http://www.getfuelcms.com
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * FUEL permissions object
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/libraries/fuel_permissions
 */

// --------------------------------------------------------------------

class Fuel_permissions extends Fuel_module {
	
	protected $module = 'permissions';
	protected $_perms = array();
	
	// --------------------------------------------------------------------
	
	/**
	 * Constructor
	 *
	 * Accepts an associative array as input, containing preferences (optional)
	 *
	 * @access	public
	 * @param	array	config preferences
	 * @return	void
	 */	
	public function __construct($params = array())
	{
		parent::__construct();
		$this->initialize($params);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Initialize the object and set object parameters
	 *
	 * Accepts an associative array as input, containing object preferences.
	 *
	 * @access	public
	 * @param	array	Array of initalization parameters  (optional)
	 * @return	void
	 */	
	public function initialize($params = array())
	{
		parent::initialize($params);
		
		// can't use because contstructor hasn't initialized
		$this->_perms = $this->model()->find_all_array_assoc('name');
	}

	// --------------------------------------------------------------------

	/**
	 * Returns whether a permission exists or not
	 *
	 * @access	public
	 * @param	string
	 * @return	boolean
	 */
	public function exists($perm)
	{
		return (isset($this->_perms[$perm]));
	}


	// --------------------------------------------------------------------

	/**
	 * Returns all permissions
	 *
	 * @access	public
	 * @return	array
	 */
	public function list_all()
	{
		return $this->_perms;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Returns a permission
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @return	string
	 */
	public function get($perm_id, $return_type = NULL)
	{
		if (is_int($perm_id))
		{
			$perm = $this->model()->find_by_key($perm_id, $return_type);
		}
		else
		{
			$perm = $this->model()->find_one('name = "'.$perm_id.'"', $return_type);
		}
		return $perm;
	}

	// --------------------------------------------------------------------

	/**
	 * Creates a permission
	 *
	 * @access	public
	 * @param	string	The name of the permission
	 * @param	array	The description of the permission
	 * @return	boolean
	 */
	public function create($name, $description = NULL)
	{
		$save = array();
		if (empty($description))
		{
			$description = humanize($name);
		}
		$save = array('name' => $name, 'description' => $description);
		if (!$this->model()->save($save))
		{
			return FALSE;
		}
		return $save;
	}

	// --------------------------------------------------------------------

	/**
	 * Creates permission(s) for a simple module
	 *
	 * @access	public
	 * @param	string	The name of the module to create permissions
	 * @param	array	An array of type of permissions to save with the module. If set to False then no extra permission types will be created
	 * @return	array
	 */
	public function create_simple_module_permissions($module, $types = array('create', 'edit', 'publish', 'delete'))
	{
		$save = array();
		$description = humanize(str_replace('/', ' ', $module));

		// check if the module permission already exists
		if (!$this->model()->record_exists(array('name' => $module)))
		{
			$save[] = array('name' => $module, 'description' => $description);
		}
		
		if (is_array($types))
		{
			foreach($types as $type)
			{
				$sub_description = humanize(str_replace('/', ' ', $module)).': '.ucfirst($type);
				$save[] = array('name' => $module.'/'.$type, 
								'description' => $sub_description
								);
			}
		}
		if (!$this->model()->save($save))
		{
			return FALSE;
		}
		return $save;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Creates permission(s) for a simple module
	 *
	 * @access	public
	 * @param	string	The name of the module to create permissions
	 * @param	array	An array of type of permissions to save with the module. If set to False then no extra permission types will be created
	 * @return	array
	 */
	public function delete_simple_module_permissions($module, $types = array('create', 'edit', 'publish', 'delete'))
	{
		$description = humanize(str_replace('/', ' ', $module));
		$delete[] = array('name' => $module, 'description' => $description);
		
		if (is_array($types))
		{
			foreach($types as $type)
			{
				$delete[] = array('name' => $module.'/'.$type);
			}
		}

		foreach($delete as $d)
		{
			if (!$this->model()->delete($d))
			{
				return FALSE;
			}
		}
		return TRUE;
	}

	
	
	// --------------------------------------------------------------------

	/**
	 * Assigns a permission to a user
	 *
	 * @access	public
	 * @param	int
	 * @param	int
	 * @return	boolean
	 */
/*	function assign_to_user($user_id, $perm_id)
	{
		$this->fuel->load_model('permissions');
		$user = $this->get($user_id);
		
		if (!isset($user->id)) return FALSE;

		$permission = $this->fuel->permissions->get($perm_id);
		if (!isset($permission->id)) return FALSE;
		
		$perm_to_user = $this->CI->user_to_permissions_model->create();
		$perm_to_user->permission_id = $user->id;
		$perm_to_user->user_id = $user->id;
		return $perm_to_user->save();
	}*/

	


}

/* End of file Fuel_permissions.php */
/* Location: ./modules/fuel/libraries/Fuel_permissions.php */