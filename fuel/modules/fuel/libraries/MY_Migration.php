<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2006 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Migration Class
 *
 * All migrations should implement this, forces up() and down() and gives
 * access to the CI super-global.
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		Reactor Engineers
 * @link
 */

// includes this fix
// https://github.com/bubbafoley/CodeIgniter/commit/dae42fa65fc65e43d704f1a6c139e985e93486f4
// as well as the ability to save a subset version (e.g. for modules)

class MY_Migration extends CI_Migration{

	protected $_migration_enabled = FALSE;
	protected $_migration_path = NULL;
	protected $_migration_version = 0;
	protected $_module = '';

	protected $_error_string = '';

	public function __construct($config = array())
	{
		// Only run this constructor on main library load
		if (get_parent_class($this) !== 'CI_Migration')
		{
			return;
		}

		foreach ($config as $key => $val)
		{
			$this->{'_' . $key} = $val;
		}

		log_message('debug', 'Migrations class initialized');

		// Are they trying to use migrations while it is disabled?
		if ($this->_migration_enabled !== TRUE)
		{
			show_error('Migrations has been loaded but is disabled or set up incorrectly.');
		}

		// If not set, set it
		if (empty($this->_migration_path))
		{
			// take into account the module that's set
			if (!empty($this->_module))
			{

				$this->_migration_path = MODULES_PATH . $this->_module.'/migrations/';
			}
			else
			{
				$this->_migration_path = APPPATH . 'migrations/';
			}
		}

		// Add trailing slash if not set
		$this->set_migration_path($this->_migration_path);

		// Load migration language
		$this->lang->load('migration');

		// They'll probably be using dbforge
		$this->load->dbforge();

		// If the migrations table is missing, make it
		if ( ! $this->db->table_exists('migrations'))
		{
			$this->dbforge->add_field(array(
				'version' => array('type' => 'INT', 'constraint' => 3),
				'module' => array('type' => 'VARCHAR', 'constraint' => 50),
			));

			$this->dbforge->create_table('migrations', TRUE);

			$this->db->insert('migrations', array('version' => 0));
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Sets the migration path
	 *
	 * @access	public
	 * @param  string	The path to the migration folder
	 * @return	void
	 */
	public function set_migration_path($path)
	{
		// Add trailing slash if not set
		$this->_migration_path = rtrim($path, '/').'/';
	}

	// --------------------------------------------------------------------

	/**
	 * Returns the migration path
	 *
	 * @access	public
	 * @return	string
	 */
	public function migration_path()
	{
		return $this->_migration_path;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Sets the module
	 *
	 * @access	public
	 * @param  string	The name of the module
	 * @return	void
	 */
	public function set_module($module)
	{
		$this->_module = $module;
	}

	// --------------------------------------------------------------------

	/**
	 * Returns the module
	 *
	 * @access	public
	 * @return	string
	 */
	public function module()
	{
		return $this->_module;
	}

	// --------------------------------------------------------------------

	/**
	 * Stores the current schema version
	 *
	 * @access	protected
	 * @param $migrations integer	Migration reached
	 * @return	void					Outputs a report of the migration
	 */
	protected function _update_version($migrations)
	{
		return $this->db->update('migrations', array(
			'version' => $migrations,
			'module' => $this->_module
		));
	}


	// THIS IS A FIX FOR A BUG IN CI --------------------------------------------------------------------

	/**
	 * Set's the schema to the latest migration
	 *
	 * @return	mixed	true if already latest, false if failed, int if upgraded
	 */
	public function latest()
	{
		if ( ! $migrations = $this->find_migrations())
		{
			$this->_error_string = $this->lang->line('migration_none_found');
			return false;
		}

		$last_migration = basename(end($migrations));

		// Calculate the last migration step from existing migration
		// filenames and procceed to the standard version migration
		return $this->version((int) substr($last_migration, 0, 3));
	}

}

/* End of file Migration.php */
/* Location: ./system/libraries/Migration.php */