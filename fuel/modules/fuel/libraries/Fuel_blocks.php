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
 * @copyright	Copyright (c) 2012, Run for Daylight LLC.
 * @license		http://www.getfuelcms.com/user_guide/general/license
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
 * @link		http://www.getfuelcms.com/user_guide/libraries/fuel_blocks
 */

// --------------------------------------------------------------------
class Fuel_blocks extends Fuel_module {
	
	protected $module = 'blocks';
	
	// --------------------------------------------------------------------

	/**
	 * Allows you to load a view and pass data to it
	 *
	 * @access	public
	 * @param	mixed	Array of parameters
	 * @param	array	Array of variables
	 * @param	boolean	Determines whether to check the CMS for the block or not (alternative to using the "mode" parameter)
	 * @return	string
	 */
	function render($params, $vars = array(), $check_db = TRUE)
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
						'cache' => FALSE,
						'mode' => 'AUTO',
						'module' => '',
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
			$module_info = $this->CI->fuel_modules->info($p['model']);
			if (!empty($module_info))
			{
				$var_name = $this->CI->$module_info['model_name']->short_name(TRUE, FALSE);
				$vars[$var_name] = $data;
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
				$view_path = MODULES_PATH.$p['module'].'/'.$view_path;
				$is_module_block = TRUE;
			}
			else
			{
				$view_path = APPPATH.$view_path;
			}
			$view_file = $view_path.$p['view'].EXT;
			
			$p['mode'] = strtolower($p['mode']);
			
			// only check database if the fuel_mode does NOT equal 'views, the "only_views" parameter is set to FALSE and the view name does not begin with an underscore'
			if ($check_db AND (($p['mode'] == 'auto' AND $this->CI->fuel->config('fuel_mode') != 'views') OR $p['mode'] == 'cms') AND substr($p['view'], 0, 1) != '_')
			{
				$this->fuel->load_model('blocks');

				// find the block in FUEL db
				$block = $this->CI->blocks_model->find_one_by_name($p['view']);
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
					$view = ($is_module_block) ? $this->CI->load->module_view($p['module'], '_blocks/'.$p['view'], $vars, TRUE) : $this->CI->load->view('_blocks/'.$p['view'], $vars, TRUE);
				}
			}
			else if (file_exists($view_file))
			{
				// pass in reference to global CI object
				$vars['CI'] =& $this->CI;

				// pass along these since we know them... perhaps the view can use them
				$view = ($is_module_block) ? $this->CI->load->module_view($p['module'], '_blocks/'.$p['view'], $vars, TRUE) : $this->CI->load->view('_blocks/'.$p['view'], $vars, TRUE);
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
	 * Uploads a block view file into the database
	 *
	 * @access	public
	 * @param	string	The name of the block file to upload to the CMS
	 * @param	boolean	Determines whether to sanitize the block by applying the php to template syntax function before uploading
	 * @return	string
	 */
	function upload($block, $sanitize = TRUE)
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
	 * @return	array
	 */
	function options_list($where = array(), $dir_folder = '', $dir_filter = '^_(.*)|\.html$', $order = TRUE)
	{
		$model = $this->model();
		return $model->options_list_with_views($where, $dir_folder, $dir_filter, $order);
	}
	
}

/* End of file Fuel_blocks.php */
/* Location: ./modules/fuel/libraries/Fuel_blocks.php */