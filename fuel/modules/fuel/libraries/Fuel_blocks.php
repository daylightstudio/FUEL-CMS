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
 * FUEL blocks object
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/libraries/fuel_blocks
 */

// --------------------------------------------------------------------
class Fuel_blocks extends Fuel_module {
	
	public $blocks_folder = '_blocks';
	protected $module = 'blocks';
	
	// --------------------------------------------------------------------

	/**
	 * Allows you to load a view and pass data to it
	 *
	 * Renders a navigation structure using the <a href="[user_guide_url]libraries/menu">Menu class</a>.
	 *
	 * The <a href="[user_guide_url]helpers/fuel_helper">fuel_block helper</a> function is an alias to this method.
	 *
	<ul>
		<li><strong>view</strong> - the name of the view block file. Also can be the first parameter of the method</li>
		<li><strong>vars</strong>: an array of variables to pass to the block. Also can be the second parameter of the method.</li>
		<li><strong>scope</strong>: a string value used for placing the variables into a certain scope to prevent conflict with other loaded variables. Default behavior will load variables in to a global context. The value of TRUE will generate one for you.</li>
		<li><strong>view_string</strong> - a string variable that represents the block</li>
		<li><strong>model</strong> - the name of a model to automatically load for the block</li>
		<li><strong>find</strong> - the name of the find method to run on the loaded model</li>
		<li><strong>select</strong> - select parameters to run for the find method</li>
		<li><strong>where</strong> - where parameters to run for the find method</li>
		<li><strong>order</strong> - the order the find method should return</li>
		<li><strong>limit</strong> - the limit number of results to be returned by the find method</li>
		<li><strong>offset</strong> - the find results returned offset value</li>
		<li><strong>return_method</strong>: the return method the find query should use</li>
		<li><strong>assoc_key</strong>: the column name to be used as the associative key in the find method</li>
		<li><strong>data</strong>: the data values to be passed to the block. This variable get's automatically set if you specify the model and find method</li>
		<li><strong>editable</strong>: css class styles to apply to menu items... can be a nested array</li>
		<li><strong>parse</strong>: determines whether to parse the contents of the block. The default is set to 'auto'</li>
		<li><strong>cache</strong>: determines whether to cache the block. Default is false</li>
		<li><strong>mode</strong>: explicitly will look in either the CMS or the views/_blocks folder</li>
		<li><strong>module</strong>: the name of the module to look in for the block</li>
		<li><strong>language</strong>: the language version to use for the block. Must be a value specified found in the 'languages' options in the FUEL configuration</li>
		<li><strong>use_default</strong>: determines whether to find a non-language specified version of a block with the same name if the specified language version is not available in the CMS</li>
	</ul>
	* @access	public
	 * @param	mixed	Array of parameters
	 * @param	array	Array of variables
	 * @param	boolean	Determines whether to check the CMS for the block or not (alternative to using the "mode" parameter)
	 * @param	boolean	Determines whether to scope the variables. A string can also be passed otherwise the scope value will be created for you
	 * @return	string
	 */
	public function render($params, $vars = array(), $check_db = TRUE, $scope = NULL)
	{
		$this->CI->load->library('parser');

		$valid = array( 'view' => '',
						'view_string' => FALSE,
						'model' => '', 
						'find' => 'all',
						'select' => NULL,
						'where' => '', 
						'order' => '', 
						'limit' => NULL, 
						'offset' => 0, 
						'return_method' => 'auto', 
						'assoc_key' => '',
						'data' => array(),
						'editable' => TRUE,
						'parse' => 'auto',
						'vars' => array(),
						'scope' => $scope,
						'cache' => FALSE,
						'mode' => 'auto',
						'module' => '',
						'language' => NULL,
						'use_default' => TRUE,
						);

		// for convenience
		if (!is_array($params))
		{
			$new_params = array();
			if (strpos($params, '=') === FALSE)
			{
				$new_params['view'] = $params;
			}
			else
			{
				$this->CI->load->helper('array');
				$new_params = parse_string_to_array($params);
			}
			$params = $new_params;
		}

		$p = array();
		foreach($valid as $param => $default)
		{
			$p[$param] = (isset($params[$param])) ? $params[$param] : $default;
		}
		
		// pull from cache if cache is TRUE and it exists
		if ($p['cache'] === TRUE)
		{
			$this->CI->load->library('cache');
			$cache_group = $this->CI->fuel->config('page_cache_group');
			$cache_id = (!empty($p['view_string'])) ? $p['view_string'] : $p['view'];
			$cache_id = md5($cache_id);
			$cache = $this->CI->cache->get($cache_id, $cache_group);
			if (!empty($cache))
			{
				return $cache;
			}
		}

		// load the model and data
		$p['vars'] = (array) $p['vars'];
		$vars = (is_array($vars) AND ! empty($vars)) ? array_merge($p['vars'], $vars) : $p['vars'];
		
		if (!empty($p['model']))
		{
			$data = fuel_model($p['model'], $p);
			$module = $this->CI->fuel->modules->get($p['model']);
			if ($module)
			{
				$model_name = $module->model()->friendly_name(TRUE);
				if (!empty($model_name))
				{
					$var_name = $module->model()->friendly_name(TRUE, FALSE);
					$vars[$var_name] =& $data; // for convenience
					$vars['data'] =& $data;
				}
			}
		}
		else
		{
			$vars['data'] = $p['data'];
		}

		$output = '';

		// load proper view to parse. If a view is given then we first look up the name in the DB
		$view = '';
		if (!empty($p['view_string']))
		{
			$view = $p['view_string'];
		}
		else if (!empty($p['view']))
		{
			$is_module_block = FALSE;
			$view_path = 'views/_blocks/';

			if ( ! empty($p['module']) AND defined('MODULES_PATH'))
			{
				if ($p['module'] == 'app' OR $p['module'] == 'application')
				{
					$view_path = APPPATH.$view_path;
				}
				else
				{
					$view_path = MODULES_PATH.$p['module'].'/'.$view_path;	
				}
				
				$is_module_block = TRUE;
			}
			else
			{
				$view_path = APPPATH.$view_path;
			}

			// get language value
			if ($this->fuel->language->has_multiple())
			{
				$language = (!empty($p['language'])) ? $p['language'] : $this->fuel->language->detect();
			}
			else
			{
				$language = $this->fuel->language->default_option();
			}

			// test that the file exists in the associated language
			if (!empty($language) AND !$this->fuel->language->is_default($language))
			{
				$view_tmp = 'language/'.$language.'/'.$p['view'];
				if (file_exists($view_path . $view_tmp.EXT))
				{
					$view_file = $view_path.$view_tmp.EXT;
				}
			}

			if (empty($view_file))
			{
				$view_file = $view_path.$p['view'].EXT;	
			}

			$p['mode'] = strtolower($p['mode']);

			// only check database if the fuel_mode does NOT equal 'views, the "only_views" parameter is set to FALSE and the view name does not begin with an underscore'
			if ($check_db AND (($p['mode'] == 'auto' AND $this->mode() != 'views') OR $p['mode'] == 'cms') AND substr($p['view'], 0, 1) != '_')
			{
				$this->fuel->load_model('fuel_blocks');

				// find the block in FUEL db
				$block = $this->CI->fuel_blocks_model->find_one_by_name_and_language($p['view'], $language);

				// if there is no block found with that language we will try to find one that may not have a language associated with it
				if (!isset($block->id) AND $p['use_default'])	
				{
					$block = $this->CI->fuel_blocks_model->find_one_by_name($p['view']);
				}
				
				if (isset($block->id))
				{
					if (strtolower($p['parse']) == 'auto')
					{
						$p['parse'] = TRUE;
					}

					$view = $block->view;

					if ($p['editable'] === TRUE)
					{
						$view = fuel_edit($block->id, 'Edit Block: '.$block->name, 'blocks').$view;
					}
				}
				else if (file_exists($view_file))
				{
					// pass in reference to global CI object
					$vars['CI'] =& $this->CI;

					// pass along these since we know them... perhaps the view can use them
					$view = ($is_module_block) ? $this->CI->load->module_view($p['module'], '_blocks/'.$p['view'], $vars, TRUE, $p['scope']) : $this->CI->load->view('_blocks/'.$p['view'], $vars, TRUE, $p['scope']);
				}
			}
			else if (file_exists($view_file))
			{

				// pass in reference to global CI object
				$vars['CI'] =& $this->CI;

				// pass along these since we know them... perhaps the view can use them
				$view = ($is_module_block) ? $this->CI->load->module_view($p['module'], '_blocks/'.$p['view'], $vars, TRUE, $p['scope']) : $this->CI->load->view('_blocks/'.$p['view'], $vars, TRUE, $p['scope']);

			}
		}

		// parse the view again to apply any variables from previous parse
		$output = ($p['parse'] === TRUE) ? $this->CI->parser->parse_string($view, $vars, TRUE) : $view;

		if ($p['cache'] === TRUE)
		{
			$this->CI->cache->save($cache_id, $output, $cache_group, $this->CI->fuel->config('page_cache_ttl'));
		}

		return $output;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Imports a block view file into the database
	 *
	 * @access	public
	 * @param	string	The name of the block file to import to the CMS
	 * @param	boolean	Determines whether to sanitize the block by applying the php to template syntax function before uploading
	 * @return	string
	 */
	public function import($block, $sanitize = TRUE)
	{
		$this->CI->load->helper('file');
		
		$model = $this->model();
		if (!is_numeric($block))
		{
			$block_data = $model->find_by_name($block, 'array');
		}
		else
		{
			$block_data = $model->find_by_key($block, 'array');
		}
		
		$view_twin = APPPATH.'views/_blocks/'.$block_data['name'].EXT;

		$output = '';
		if (file_exists($view_twin))
		{
			$view_twin_info = get_file_info($view_twin);
			
			$tz = date('T');
			if ($view_twin_info['date'] > strtotime($block_data['last_modified'].' '.$tz) OR
				$block_data['last_modified'] == $block_data['date_added'])
			{
				// must have content in order to not return error
				$output = file_get_contents($view_twin);

				// replace PHP tags with template tags... comments are replaced because of xss_clean()
				if ($sanitize)
				{
					$output = php_to_template_syntax($output);
				}
			}
		}
		return $output;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Returns an associative array of all blocks with from both the CMS and static views in the views/_blocks/ folder
	 *
	 * @access	public
	 * @param	array 	Where condition to apply to blocks in the CMS (optional)
	 * @param	string	The subfolder within the views/_blocks folder (optional)
	 * @param	string	Filter condition for those blocks found in the views/_blocks folder (optional)
	 * @param	mixed	The ordering condition to apply for the views (applies to those fond in the CMS... optional)
	 * @param	boolean	Determines whether to recursively look for files (optional)
	 * @return	array
	 */
	public function options_list($where = array(), $dir_folder = '', $dir_filter = '^_(.*)|\.html$', $order = TRUE, $recursive = TRUE)
	{
		$model = $this->model();
		return $model->options_list_with_views($where, $dir_folder, $dir_filter, $order, $recursive);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the rendering mode for the blocks module (e.g. views or cms)
	 *
	 * @access	public
	 * @return	boolean
	 */	
	public function mode()
	{
		$fuel_mode = $this->fuel->config('fuel_mode');
		if (is_array($fuel_mode))
		{
			if (isset($fuel_mode['blocks']))
			{
				return $fuel_mode['blocks'];
			}
			else
			{
				return 'auto';
			}
		}
		return $fuel_mode;
	}

	
}

/* End of file Fuel_blocks.php */
/* Location: ./modules/fuel/libraries/Fuel_blocks.php */