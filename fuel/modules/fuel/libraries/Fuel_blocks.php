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
	
	// --------------------------------------------------------------------

	/**
	 * Allows you to load a view and pass data to it
	 *
	 * @access	public
	 * @param	mixed
	 * @return	string
	 */
	function render($params)
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
		$vars = (array) $p['vars'];
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
			$view_file = APPPATH.'views/_blocks/'.$p['view'].EXT;
			
			$p['mode'] = strtolower($p['mode']);
			
			// only check database if the fuel_mode does NOT equal 'views, the "only_views" parameter is set to FALSE and the view name does not begin with an underscore'
			if ((($p['mode'] == 'auto' AND $this->CI->fuel->config('fuel_mode') != 'views') OR $p['mode'] == 'cms') AND substr($p['view'], 0, 1) != '_')
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
				else if (file_exists(APPPATH.'views/_blocks/'.$p['view'].EXT))
				{
					// pass in reference to global CI object
					$vars['CI'] =& $this->CI;

					// pass along these since we know them... perhaps the view can use them
					$view = $this->CI->load->view("_blocks/".$p['view'], $vars, TRUE);
				}
			}
			else if (file_exists($view_file))
			{
				// pass in reference to global CI object
				$vars['CI'] =& $this->CI;

				// pass along these since we know them... perhaps the view can use them
				$view = $this->CI->load->view("_blocks/".$p['view'], $vars, TRUE);
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
	 * @param	string
	 * @param	boolean
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
	
	function get($where = array(), $dir_filter = '^_(.*)|\.html$', $order = TRUE)
	{
		$model = $this->model();
		return $model->options_list_with_views($where, $dir_filter, $order);
	}

}

/* End of file Fuel_blocks.php */
/* Location: ./modules/fuel/libraries/Fuel_blocks.php */