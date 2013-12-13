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
 * @copyright	Copyright (c) 2010, Run for Daylight LLC.
 * @license		http://docs.getfuelcms.com/general/license
 * @link		http://www.getfuelcms.com
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Simplepie Helper
 *
 * @package		FUEL CMS
 * @subpackage	Helpers
 * @category	Helpers
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/helpers/simplepie_helper
 */


// --------------------------------------------------------------------

/**
 * Uses <a href="[user_guide_url]libraries/simplepie">SimplePie</a> class to retrieve an RSS Feed
 *
 * @access	public
 * @param	string	Feed URL
 * @param	string	The number of results to return
 * @param	string	Additional parameters which include, cache_duration, enable_order_by_date, enable_cache and cache_location
 * @return	object
 */
function simplepie($feed, $limit = 5, $params = array())
{
	$CI =& get_instance();
	$defaults = array(
		'cache_duration' => 600,
		'enable_cache' => TRUE,
		'cache_location' => $CI->config->item('cache_path'),
		'enable_order_by_date' => TRUE,
		'item_class' => NULL,
		);
	
	$p = array();
	foreach($defaults as $key => $val)
	{
		if (isset($params[$key]))
		{
			$p[$key] = $params[$key];
		}
		else
		{
			$p[$key] = $val;
		}
	}
	$CI->load->module_library(FUEL_FOLDER, 'simplepie');
	$CI->simplepie->set_feed_url($feed);
	$CI->simplepie->set_cache_duration($p['cache_duration']);
	$CI->simplepie->enable_order_by_date($p['enable_order_by_date']);
	$CI->simplepie->enable_cache($p['enable_cache']);
	$CI->simplepie->set_cache_location($p['cache_location']);

	// load in any custom classes
	if (!empty($p['item_class']))
	{
		if (!class_exists($p['item_class']))
		{
			if (!empty($p['item_class_path']))
			{
				$class_path = $p['item_class_path'];
			}
			else
			{
				$class_path = APPPATH.'libraries/'.$p['item_class'].'.php';
			}
			require_once($class_path);
		}
		$CI->simplepie->set_item_class($p['item_class']);
	}
	@$CI->simplepie->init();
	$CI->simplepie->handle_content_type();
	$feed_data = $CI->simplepie->get_items(0, $limit);
	$latest_fuel_version = $CI->simplepie->get_channel_tags('', 'latestFuelVersion');
	if ( ! is_null($latest_fuel_version[0]))
	{
		$feed_data['latest_fuel_version'] = $latest_fuel_version[0]['data'];
	}
	return $feed_data;
}


/* End of file simplepie_helper.php */
/* Location: ./modules/fuel/helpers/simplepie_helper.php */