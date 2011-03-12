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
 * @copyright	Copyright (c) 2011, Run for Daylight LLC.
 * @license		http://www.getfuelcms.com/user_guide/general/license
 * @link		http://www.getfuelcms.com
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * The base class Test classes should inherit from
 *
 * @package		Tester Module
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/modules/tester/tester_base
 */

abstract class Tester_base 
{
	protected $CI;
	protected $loaded_page;
	
	private $_is_db_created;
	
	// --------------------------------------------------------------------

	/**
	 * Constructor sets up the CI instance
	 *
	 * @access	public
	 * @return	array
	 */
	public function __construct()
	{
		$this->CI =& get_instance();
		
		// set testing constant if not already
		if (!defined('TESTING')) define('TESTING', TRUE);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Returns where condition based on the users logged in state
	 *
	 * @access	public
	 * @param	mixed
	 * @param	mixed
	 * @param	string
	 * @return	string
	 */
	public function run($test, $expected, $name = '')
	{
		$name = $this->format_test_name($name, $test, $expected);
		return $this->CI->unit->run($test, $expected, $name);
	}
	
	// --------------------------------------------------------------------

	/**
	 *  Placeholder to be overwritten by child classes for test setup (like database table etc)
	 *
	 * @access	public
	 * @return	void
	 */
	public function setup()
	{
		
	}
	
	// --------------------------------------------------------------------

	/**
	 *  Is called at the end of the test and will remove any test database that has been created
	 *
	 * @access	public
	 * @return	void
	 */
	public function tear_down()
	{
		if ($this->_is_db_created)
		{
			$this->remove_db();
			$this->CI->db->close();
		}
		
		// remove the cookie file
		if (file_exists($this->config_item('session_cookiejar_file')))
		{
			@unlink($this->config_item('session_cookiejar_file'));
		}
	}
	
	// --------------------------------------------------------------------

	/**
	 *  Formats the test name to include the test and expected results
	 *
	 * @access	public
	 * @param	string
	 * @param	mixed
	 * @param	mixed
	 * @return	string
	 */
	protected function format_test_name($name, $test, $expected)
	{
		$str = '<strong>'.$name.'</strong><br />';
		// $str .= '<strong>Test:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong> <pre>'.htmlentities($test).'</pre><br />';
		// $str .= '<strong>Expected:</strong> <pre>'.htmlentities($expected).'</pre>';
		return $str;
	}
	
	// --------------------------------------------------------------------

	/**
	 *  Return Tester specific configuration items
	 *
	 * @access	public
	 * @param	string
	 * @return	mixed
	 */
	protected function config_item($key)
	{
		$tester_config = $this->CI->config->item('tester');
		return $tester_config[$key];
	}

	// --------------------------------------------------------------------

	/**
	 *  Connects to the testing database
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */
	protected function db_connect($dsn = '')
	{
		// check config if $dsn is empty
		if (empty($dsn))
		{
			$tester_config = $this->CI->config->item('tester');
			$dsn = $this->config_item('dsn_group');
		}
		$this->CI->load->database($dsn);
	}
	
	// --------------------------------------------------------------------

	/**
	 *  Checks to see if the database exists or not
	 *
	 * @access	public
	 * @return	void
	 */
	protected function db_exists($test)
	{
		$result = $this->CI->db->query('SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = "'.$this->config_item('db_name').'"');
		$table = $result->row_array();
		return (!empty($table['SCHEMA_NAME']) && strtoupper($table['SCHEMA_NAME']) == strtoupper($this->config_item('db_name')));
	}
	
	// --------------------------------------------------------------------

	/**
	 *  Creates the database.
	 *
	 * @access	public
	 * @param	boolean
	 * @return	void
	 */
	protected function create_db()
	{
		$this->db_connect();
		
		$this->CI->load->dbforge();
		
		// create the database if it doesn't exist'
		if (!$this->db_exists('created'))
		{
			$this->CI->dbforge->create_database($this->config_item('db_name'));
		}

		// create database
		$this->_is_db_created = TRUE;
	}
	
	// --------------------------------------------------------------------

	/**
	 *  Removes the test database.
	 *
	 * @access	public
	 * @param	boolean
	 * @return	void
	 */
	protected function remove_db()
	{
		$this->db_connect();
		
		$this->CI->load->dbforge();
		
		// drop the database if it exists
		if ($this->db_exists('remove'))
		{
			$this->CI->dbforge->drop_database($this->config_item('db_name'));
		}
		$this->_is_db_created = FALSE;
	}
	
	// --------------------------------------------------------------------

	/**
	 *  Loads the sql from a file in the {module}/test/sql folder
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	void
	 */
	protected function load_sql($file = NULL, $module = 'tester')
	{
		if (!$this->_is_db_created) $this->create_db();
		if (empty($module) OR $module == 'app')
		{
			$sql_path = APPPATH.'tests/sql/'.$file;
		}
		else
		{
			$sql_path = MODULES_PATH.$module.'/tests/sql/'.$file;
		}
		
		// select the database
		$sql = 'USE '.$this->config_item('db_name');
		
		$this->CI->db->query($sql);
		if (file_exists($sql_path))
		{
			$sql = file_get_contents($sql_path);
			$sql = str_replace('`', '', $sql);
			$sql = preg_replace('#^/\*(.+)\*/$#U', '', $sql);
			$sql = preg_replace('/^#(.+)$/U', '', $sql);
		}
		$sql_arr = explode(";\n", $sql);
		
		foreach($sql_arr as $s)
		{
			$s = trim($s);
			if (!empty($s))
			{
				$this->CI->db->query($s);
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 *  Loads the results of call to a controller. Additionally creates pq function to query dom nodes
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 * @return	string
	 */
	protected function load_page($page, $post = array())
	{
		if (!is_array($post))
		{
			return FALSE;
		}
		
		$this->CI->load->library('user_agent');
		
		// NO LONGER USED... OPTED FOR CURL INSTEAD!... kept for reference just in case though
		// $_SERVER['PATH_INFO'] = $page;
		// $_SERVER['REQUEST_URI'] = $page;

		// must suppres warnings to remove constant warnings
		// $exec = TESTER_PATH.'libraries/Controller_runner.php --run='.$page.' -CI='.FCPATH.' -D='.$_SERVER['SERVER_NAME'].' -P='.$_SERVER['SERVER_PORT'].' -X='.base64_encode(serialize($post));
		// $output = shell_exec($exec);
		
		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_URL, site_url($page));
		curl_setopt($ch, CURLOPT_HEADER, 0); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		// set cookie jar for sessions and to tell system we are running tests
		$tester_config = $this->CI->config->item('tester');
		curl_setopt($ch, CURLOPT_COOKIEFILE, $tester_config['session_cookiejar_file']); 
		curl_setopt($ch, CURLOPT_COOKIEJAR, $tester_config['session_cookiejar_file']); 
		curl_setopt($ch, CURLOPT_COOKIE, 'tester_dsn='.$this->config_item('dsn_group')); 
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		
		if (!empty($post))
		{
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		}
		curl_setopt($ch, CURLOPT_USERAGENT, $this->CI->agent->agent_string());
		
		$output = curl_exec($ch);
		curl_close($ch); 
		
		//http://code.google.com/p/phpquery/wiki/Manual
		require_once(TESTER_PATH.'libraries/phpQuery.php');
		phpQuery::newDocumentHTML($output, strtolower($this->CI->config->item('charset')));
		$this->loaded_page = $output;
		return $output;
	}
	
	// --------------------------------------------------------------------

	/**
	 *  Convenience method to test if something exists on a page
	 *
	 * @access	public
	 * @param	string
	 * @param	boolean
	 * @return	void
	 */
	public function page_contains($match, $use_jquery = TRUE)
	{
		if ($use_jquery)
		{
			return pq($match)->size();
		}
		else
		{
			return (preg_match('#'.$match.'#', $this->loaded_page));
		}
	}
	
	
	// --------------------------------------------------------------------

	/**
	 *  Class desctruct magic method which will run teardown
	 *
	 * @access	public
	 * @return	void
	 */
	public function __destruct()
	{
		$this->tear_down();
	}
}

/* End of file Tester_base.php */
/* Location: ./modules/tester/libraries/Tester_base.php */