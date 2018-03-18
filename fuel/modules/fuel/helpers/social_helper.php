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
 * @copyright	Copyright (c) 2018, Daylight Studio LLC.
 * @license		http://docs.getfuelcms.com/general/license
 * @link		http://www.getfuelcms.com
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Social Helper
 *
 * @package		FUEL CMS
 * @subpackage	Helpers
 * @category	Helpers
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/helpers/social_helper
 */

// --------------------------------------------------------------------

if (!function_exists('share'))
{
	/**
	 * Uses <a href="../libraries/social">Social</a> class's share method to return social links
	 *
	 * @access	public
	 * @param	string	The type of social URL. A list of supported URLs can be found in the fuel/application/config/social.php file under the $config['share_url']
	 * @param	mixed	Can be an object or an array of values
	 * @return	string
	 */
	function share($type, $values = NULL)
	{
		$CI =& get_instance();
		if (!isset($CI->social))
		{
			$CI->load->library('social');	
		}
		return $CI->social->share($type, $values);
	}
}

// --------------------------------------------------------------------

if (!function_exists('og'))
{
	/**
	 * Uses <a href="../libraries/social">Social</a> class's og method to create open graph links
	 *
	 * @access	public
	 * @param	array	An array of values for the open graph which can include array keys of 'title', 'url', 'description', 'image', 'site_name', 'type'
	 * @return	string
	 */
	function og($values)
	{
		$CI =& get_instance();
		if (!isset($CI->social))
		{
			$CI->load->library('social');	
		}
		return $CI->social->og($values);
	}
}

// --------------------------------------------------------------------

if (!function_exists('social_popup_js'))
{
	/**
	 * Creates the popup window javascript for the share links and writes the javascript only once to the page.
	 *
	 * @access	public
	 * @param	int		The width of the popup javascript window (optional)
	 * @param	int		The height of the popup javascript window (optional)
	 * @param	string	The selector used for the popup window. Default is .popup (optional)
	 * @return	string
	 */
	function social_popup_js($width = 640, $height = 500, $selector = 'popup')
	{
		// only write the javascript one time so we create a static variable
		static $output;
		if (empty($output)) {
			$output = '
			<script>
			if (typeof(jQuery) != \'undefined\'){
				jQuery(function(){
					jQuery(\'.'.$selector.'\').on(\'click\', function(e){
						var url = $(this).attr(\'href\');
						window.open(url,\'popUpWindow\',\'width='.$width.',height='.$height.',left=10,top=10,resizable=yes,scrollbars=yes,toolbar=yes,menubar=no,location=no,directories=no,status=yes\');
						return false;
					})
				})
			}
			</script>';
			return $output;
		}
		return '';
		
	} 
}

/* End of file social_helper.php */
/* Location: ./modules/fuel/helpers/social_helper.php */