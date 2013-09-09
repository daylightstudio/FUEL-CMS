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
 * FUEL Asset Helper
 *
 * This helper allows you to output css, js links and/or files as well as 
 * allows you to compress and cache them. Also has convenience methods for 
 * paths to assets. It is essentially an alias to the Asset Class.
 *
 * @package		FUEL CMS
 * @subpackage	Helpers
 * @category	Helpers
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/helpers/asset_helper
 */


// --------------------------------------------------------------------

/**
 * Returns an image asset path
 *
 * @access	public
 * @param	string	image file name including extension
 * @param	string	module folder if any
 * @param	boolean	whether to include http://... at beginning
 * @return	string
 */	
function img_path($file = NULL, $module = NULL, $absolute = NULL)
{
	$CI = _get_assets();
	return $CI->asset->img_path($file, $module, $absolute);
}

// --------------------------------------------------------------------

/**
 * Returns a css asset path
 *
 * @access	public
 * @param	string	css file name (extension not required)
 * @param	string	module folder if any
 * @param	boolean	whether to include http://... at beginning
 * @return	string
 */	
function css_path($file = NULL, $module = NULL, $absolute = NULL)
{
	$CI = _get_assets();
	return $CI->asset->css_path($file, $module, $absolute);
}

// --------------------------------------------------------------------

/**
 * Returns a js asset path
 *
 * @access	public
 * @param	string	javascript file name (extension not required)
 * @param	string	module folder if any
 * @param	boolean	whether to include http://... at beginning
 * @return	string
 */	
function js_path($file = NULL, $module = NULL, $absolute = NULL)
{
	$CI = _get_assets();
	return $CI->asset->js_path($file, $module, $absolute);
}

// --------------------------------------------------------------------

/**
 * Returns a swf asset path
 *
 * @access	public
 * @param	string	swf file name (extension not required)
 * @param	string	module folder if any
 * @param	boolean	whether to include http://... at beginning
 * @return	string
 */	
function swf_path($file = NULL, $module = NULL, $absolute = NULL)
{
	$CI = _get_assets();
	return $CI->asset->swf_path($file, $module, $absolute);
}

// --------------------------------------------------------------------

/**
 * Returns a pdf asset path
 *
 * @access	public
 * @param	string	pdf file name (extension not required)
 * @param	string	module folder if any
 * @param	boolean	whether to include http://... at beginning
 * @return	string
 */	
function pdf_path($file = NULL, $module = NULL, $absolute = NULL)
{
	$CI = _get_assets();
	return $CI->asset->pdf_path($file, $module, $absolute);
}

// --------------------------------------------------------------------

/**
 * Returns a media asset path (e.g. quicktime .mov)
 *
 * @access	public
 * @param	string	pdf file name including extension
 * @param	string	module folder if any
 * @param	boolean	whether to include http://... at beginning
 * @return	string
 */	
function media_path($file = NULL, $module = NULL, $absolute = NULL)
{
	$CI = _get_assets();
	return $CI->asset->media_path($file, $module, $absolute);
}

// --------------------------------------------------------------------

/**
 * Returns a document asset path (e.g. doc, docx)
 *
 * @access	public
 * @param	string	docs file name including extension
 * @param	string	module folder if any
 * @param	boolean	whether to include http://... at beginning
 * @return	string
 */	
function docs_path($file = NULL, $module = NULL, $absolute = NULL)
{
	$CI = _get_assets();
	return $CI->asset->docs_path($file, $module, $absolute);
}

// --------------------------------------------------------------------

/**
 * Returns a cache asset path
 *
 * @access	public
 * @param	string	cached file name including extension
 * @param	string	module folder if any
 * @param	boolean	whether to include http://... at beginning
 * @return	string
 */	
function cache_path($file = NULL, $module = NULL, $absolute = NULL)
{
	$CI = _get_assets();
	return $CI->asset->cache_path($file, 'assets_cache_folder', $module);
}

// --------------------------------------------------------------------

/**
 * Returns a captcha image path
 *
 * @access	public
 * @param	string	captcha file name including extension
 * @param	string	module folder if any
 * @param	boolean	whether to include http://... at beginning
 * @return	string
 */	
function captcha_path($file = NULL, $module = NULL, $absolute = NULL)
{
	$CI = _get_assets();
	return $CI->asset->captcha_path($file, $module);
}

// --------------------------------------------------------------------

/**
 * Returns an asset path and is what the others above use
 *
 * @access	public
 * @param	string	asset file name including extension
 * @param	string	subfolder to asset file (e.g. images, js, css... etc)
 * @param	string	module folder if any
 * @param	boolean	whether to include http://... at beginning
 * @return	string
 */	
function assets_path($file = NULL, $path = NULL, $module = NULL,  $absolute = NULL)
{
	$CI = _get_assets();
	return $CI->asset->assets_path($file, $path, $module, $absolute);
}

// --------------------------------------------------------------------

/**
 * Get the server path
 *
 * @access	public
 * @param	string	asset file name including extension
 * @param	string	subfolder to asset file (e.g. images, js, css... etc)
 * @param	string	module folder if any
 * @return	string
 */	
function assets_server_path($file = NULL, $path = NULL, $module = NULL)
{
	$CI = _get_assets();
	return $CI->asset->assets_server_path($file, $path, $module);
}

// --------------------------------------------------------------------

/**
 * Convert a server path to a web path
 *
 * @access	public
 * @param	string	server path to asset file
 * @return	string
 */	
function assets_server_to_web_path($file, $truncate_to_asset_folder = FALSE)
{
	$CI = _get_assets();
	return $CI->asset->assets_server_to_web_path($file, $truncate_to_asset_folder);
}

// --------------------------------------------------------------------

/**
 * Returns a boolean value of whether a file exists
 *
 * @access	public
 * @param	string	asset file name including extension
 * @param	string	subfolder to asset file (e.g. images, js, css... etc)
 * @param	string	module folder if any
 * @return	boolean
 */	
function asset_exists($file = NULL, $path = NULL, $module = NULL)
{
	$CI = _get_assets();
	return $CI->asset->asset_exists($file, $path, $module);
}

// --------------------------------------------------------------------

/**
 * Returns the file size of an asset
 *
 * @access	public
 * @param	string	asset file name including extension
 * @param	string	subfolder to asset file (e.g. images, js, css... etc)
 * @param	string	module folder if any
 * @param	boolean	format
 * @return	string
 */	
function asset_filesize($file = NULL, $path = NULL, $module = NULL, $format = FALSE)
{
	$CI = _get_assets();
	return $CI->asset->asset_filesize($file, $path, $module, $format);
}

// --------------------------------------------------------------------

/**
 * Creates javascript code that first tries to pull in jquery from the Google CDN, and if it doesn't exist, goes to the local backup version
 *
 * @access	public
 * @param	string	jQuery version number for Google CDN
 * @param	string	local asset path to default version
 * @return	string
 */	
function jquery($version = '1.7.1', $default = 'jquery')
{
	$CI = _get_assets();
	return $CI->asset->jquery($version, $default);
}

// --------------------------------------------------------------------

/**
 * Inserts <script ...></script> tags based on configuration settings for js file path
 *
 * @access	public
 * @param	string	file name of the swf file including extension
 * @param	string	module module folder if any
 * @param	array	additional parameter to include (attrs, ie_conditional, and output)
 * @return	string
 */	
function js($path, $module = '', $options = '')
{
	$CI = _get_assets();
	return $CI->asset->js($path, $module, $options);
}

// --------------------------------------------------------------------

/**
 * Inserts <link ... /> tags based on configuration settings for css file path
 *
 * @access	public
 * @param	string	file name of the swf file including extension
 * @param	string	module module folder if any
 * @param	array	additional parameter to include (attrs, ie_conditional, and output)
 * @return	string
 */	
function css($path, $module = '', $options = '')
{
	$CI = _get_assets();
	return $CI->asset->css($path, $module, $options);

}

// --------------------------------------------------------------------

/**
 * Returns an swf asset path
 *
 * @access	public
 * @param	string	file name of the swf file including extension
 * @param	string	module module folder if any
 * @param	array	additional parameter to include (attrs, ie_conditional, and output)
 * @return	string
 */	
function swf($flash, $id, $width, $height, $options = array()){
	$CI = _get_assets();
	return $CI->asset->swf($flash, $id, $width, $height, $options);
	
}

// --------------------------------------------------------------------

/**
 * Uses the "http://placehold.it" service to display images (good for mocking up sites)
 *
 * @access	public
 * @param	int		width of placeholder image (optional)
 * @param	int		height of placeholder image (optional)
 * @param	string	text to display inside placeholder
 * @param	string	color of placeholder
 * @param	boolean	determines whether to wrap it in an image tag or just return the path (optional)
 * @return	string
 */	
function placeholder($width = 100, $height = '', $text = '', $colors = '', $img_tag = FALSE)
{
	$dimentions = $width.( !empty($height) ? 'x'.$height : '');
	$text = !empty($text) ? '&text='.urlencode($text) : '';
	$colors = !empty($colors) ? explode(' ', $colors) : '';
	$colors = !empty($colors) ? '/'.$colors[0].'/'.$colors[1] : '';
	$path = 'http://placehold.it/'.$dimentions.$colors.$text;
	if ($img_tag)
	{
		return '<img src="'.$path.'" alt="'.$text.'" width="'.$width.'" height="'.$height.'" />';
	}
	return $path;
}

// --------------------------------------------------------------------

/**
 * Returns the CI super object
 *
 * @access	public
 * @return	CI super object
 */	
function _get_assets()
{
	$CI =& get_instance();
	if (!isset($CI->asset))
	{
		$CI->load->library('asset');
	}
	return $CI;
}

/* End of file ajax_helper.php */
/* Location: ./modules/fuel/helpers/asset_helper.php */
