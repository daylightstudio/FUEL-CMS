<?php 

// convenience function to easily create user guide urls
function user_guide_url($uri = '')
{
	$CI =& get_instance();
	$url_base = $CI->fuel->user_guide->config('root_url');
	return site_url($url_base.$uri);
}
 ?>