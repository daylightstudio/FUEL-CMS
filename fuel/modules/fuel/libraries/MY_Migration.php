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

// Includes change for a subset version (e.g. for modules)

class MY_Migration extends CI_Migration{

	protected $_module = '';

	/**
	 * Initialize Migration Class
	 * Overwritten to create module field in the database
	 *
	 * @param	array	$config
	 * @return	void
	 */
	public function __construct($config = array())
	{
		// Only run this constructor on main library load
		if ( ! in_array(get_class($this), array('CI_Migration', config_item('subclass_prefix').'Migration'), TRUE))
		{
			return;
		}

		foreach ($config as $key => $val)
		{
			$this->{'_'.$key} = $val;
		}

		log_message('info', 'Migrations Class Initialized');

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

		// Make sure the migration table name was set.
		if (empty($this->_migration_table))
		{
			show_error('Migrations configuration file (migration.php) must have "migration_table" set.');
		}

		// Migration basename regex
		$this->_migration_regex = ($this->_migration_type === 'timestamp')
			? '/^\d{14}_(\w+)$/'
			: '/^\d{3}_(\w+)$/';

		// Make sure a valid migration numbering type was set.
		if ( ! in_array($this->_migration_type, array('sequential', 'timestamp')))
		{
			show_error('An invalid migration numbering type was specified: '.$this->_migration_type);
		}

		// If the migrations table is missing, make it
		if ( ! $this->db->table_exists($this->_migration_table))
		{
			$this->dbforge->add_field(array(
				'version' => array('type' => 'BIGINT', 'constraint' => 20),
				'module' => array('type' => 'VARCHAR', 'constraint' => 50),
			));

			$this->dbforge->create_table($this->_migration_table, TRUE);

			$this->db->insert($this->_migration_table, array('version' => 0));
		}

		// Do we auto migrate to the latest migration?
		if ($this->_migration_auto_latest === TRUE && ! $this->latest())
		{
			show_error($this->error_string());
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
}

/* End of file Migration.php */
/* Location: ./system/libraries/Migration.php */