<?php 

// convenience function to easily create user guide urls
function user_guide_url($uri = '')
{
	$CI =& get_instance();
	$url_base = $CI->config->item('user_guide_root_url');
	return site_url($url_base.$uri);
}
 ?>