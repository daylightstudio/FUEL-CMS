<?php 

function simplepie($feed, $limit = 5, $params = array())
{
	$CI =& get_instance();
	$defaults = array(
		'cache_duration' => 600,
		'enable_cache' => TRUE,
		'cache_location' => $CI->config->item('cache_path'),
		'enable_order_by_date' => TRUE,
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
	@$CI->simplepie->init();
	$CI->simplepie->handle_content_type();
	$feed_data = $CI->simplepie->get_items(0, $limit);
	$latest_fuel_version = (float)$CI->simplepie->get_channel_tags('', 'latestFuelVersion');
	if ( ! is_null($latest_fuel_version))
	{
		$feed_data['latest_fuel_version'] = $latest_fuel_version;
	}
	return $feed_data;
}