<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
http://codeigniter.com/forums/viewthread/56515/
* CodeIgniter
*
* An open source application development framework for PHP 4.3.2 or newer
*
* @package        CodeIgniter
* @author        Rick Ellis
* @copyright    Copyright (c) 2006, EllisLab, Inc.
* @license        http://www.codeignitor.com/user_guide/license.html
* @link        http://www.codeigniter.com
* @since        Version 1.0
* @filesource
*/

// ------------------------------------------------------------------------

/**
* CodeIgniter GOOGLE Helpers
*
* @package        CodeIgniter
* @subpackage    Helpers
* @category    Helpers
* @author        Todd Perkins with recommendation from Code Arachn!d
* @link        http://www.undecisive.com
*/

// ------------------------------------------------------------------------

/**
* Google Analytics
*
* Inserts google analytics tracking code into view
* If a tracking code is passed in, then it will use that uacct info
* Otherwise, it will use the value defined in the google.php config file
* If both values do not exist, nothing will be inserted.
*
* @access    public
* @param    string	The google account number (optional)
* @param    mixed	An array or string of extra parameters to pass to GA. An array will use the key/value to add _gaq.push (optional)
* @param    boolean	Whether to check dev mode before adding it in (optional)
* @return   string
*/
function google_analytics($uacct = '', $other_params = array(), $check_devmode = TRUE) {

	if ($check_devmode AND (function_exists('is_dev_mode') AND is_dev_mode()))
	{
		return FALSE;
	}

	$CI =& get_instance();
	$CI->load->config('google');
	
	if (empty($uacct)) $uacct = $CI->config->item('google_uacct');
	if (!empty($uacct))
	{
		$google_analytics_code = '
			<script type="text/javascript">
			  var _gaq = _gaq || [];
			  _gaq.push([\'_setAccount\', \''.$uacct.'\']);
			 ';

			 if (!empty($other_params))
			 {
				if (is_array($other_params))
				 {
				 	foreach($other_params as $key => $val)
				 	{
				 		$google_analytics_code .= ' _gaq.push([\''.$key.'\', \''.$val.'\'])';
				 	}
				 	
				 }
				 else if (is_string($other_params))
				 {
				 	$google_analytics_code .= "	".$other_params."\n";
				 }		 	
			 }

		$google_analytics_code .= '			 
			  _gaq.push([\'_trackPageview\']);

			  (function() {
			    var ga = document.createElement(\'script\'); ga.type = \'text/javascript\'; ga.async = true;
			    ga.src = (\'https:\' == document.location.protocol ? \'https://ssl\' : \'http://www\') + \'.google-analytics.com/ga.js\';
			    var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(ga, s);
			  })();
			</script>
		';
		return $google_analytics_code;
	}
	else
	{
		return FALSE;
	}
}

// ------------------------------------------------------------------------

/**
* Google map
*
* Returns an iframed Google map
*
* @access    public
* @param    mixed	Address can be either an array with "address", "city", "state" or simply a string
* @param    array	An array of additional map parameter that that includes, "height", "width", hl" (language), "z" (zoom), "t" (map type), "om", (overview map), "iwloc" (display info bubble), "ll" (lat,lng). Friendly names of "display_info" (iwloc), "map_type" (t), and "overview" (om) can be used. (optional)
* @return   string
*/
function google_map($address, $params = array())
{
	// get either the custom map url or the standard google url
	$url = google_map_url($address, $params);

	// set defaults for width and height
	$defaults = array('width' => 500, 'height' => 300);
	foreach($defaults as $key => $val)
	{
		if (!isset($params[$key]))
		{
			$params[$key] = $val;
		}
	}
	$map = '<iframe width="'.$params['width'].'" height="'.$params['height'].'" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="'.$url.'"></iframe>';
	return $map;
}

// ------------------------------------------------------------------------

/**
* Google map URL
*
* Returns a google map URL (used by the google_map function too)
*
* @access    public
* @param    mixed	Address can be either an array with "address", "city", "state" or simply a string. You can also pass lat and lng values as an array
* @param    array	An array of additional map parameter that that includes, "hl" (language), "z" (zoom), "t" (map type), "om", (overview map), "iwloc" (display info bubble), "ll" (lat,lng). Friendly names of "display_info" (iwloc), "map_type" (t), and "overview" (om) can be used. (optional)
* @return   string
*/
function google_map_url($address, $params = array())
{
	// if a query string is passed, then we parse it into an array form
	if (is_string($params))
	{
		$params = parse_str($params);
	}

	// initialize the parameter array
	$p = array();
	
	// if lat and long supplied in array, then we implode
	if (is_array($address))
	{
		if (isset($address['address']))
		{
			$p['q'] = $address['address'];
			if (!empty($address['city']))
			{
				$p['q'] .= ','.$address['city'];	
			}
			if (!empty($address['state']))
			{
				$addr .= ','.$address['state'];	
			}
			$p['q'] = $addr;
		}
		else
		{
			array_walk($address, 'trim');
			$p['q'] = implode(',', $address);	
		}
	}
	else
	{
		$p['q'] = urlencode($p['q']);
	}

	// default parameters
	$defaults = array('hl' => 'en', 'z' => 15, 't' => 'v', 'om' => TRUE, 'll' => NULL, 'iwloc' => '');
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
	
	// FRIENDLY NAME "display_info" display the information... convenience because I can never remember iwloc
	if (isset($params['display_info']) AND $params['display_info'] === TRUE)
	{
		if ($params['display_info'] == TRUE)
		{
			$p['iwloc'] = 'A';
		}
		else
		{
			$p['iwloc'] = '';
		}
		unset($p['display_info']);
	}

	// FRIENDLY NAME "overview"
	if (isset($params['overview']) AND $params['overview'] === TRUE)
	{
		$p['om'] = '1';
		unset($p['om']);
	}

	// FRIENDLY NAME "map_type" set map type value
	if (isset($params['map_type']))
	{
		// "k" satellite, "h" hybrid, "p" terrain, "v" roadmap
		switch($params['map_type'])
		{
			case 'satellite':
				$p['t'] = 'k';
				break;
			case 'hybrid':
				$p['t'] = 'h';
				break;
			case 'terrain':
				$p['t'] = 'p';
				break;
			default:
				$p['t'] = 'v';
		}
		unset($p['map_type']);
	}

	// set output
	$p['output'] = 'embed';
	$url = 'http://maps.google.com/maps?'.http_build_query($p, '', '&amp;');
	$query_str = http_build_query($p, '', '&amp;');
	return $url;
}

// ------------------------------------------------------------------------

/**
* Google geolocate
*
* Finds the latitude and longitude of a given address. 
* Use sleep() or usleep() functions to meter multiple requests (10/s is limit I believe)
*
* @access    public
* @param    mixed	Address can be either an array with "address", "city", "state" or simply a string
* @return   array  (e.g. array('latitude' => xxxx, 'longitude' => xxxx))
*/
function google_geolocate($data)
{
	$address = '';
	
	$values = array('latitude' => NULL, 'longitude' => NULL);
	if (is_array($data) AND isset($data['address']))
	{
		$address = $data['address'];
		if (!empty($data['city']))
		{
			$address .= ','.$data['city'];	
		}
		if (!empty($data['state']))
		{
			$address .= ','.$data['state'];	
		}
	}
	else if (is_string($data))
	{
		$address = $data;
	}

	$address = urlencode($address);
	unset($data);

	$url = "https://maps.googleapis.com/maps/api/geocode/json?address=".$address."&sensor=false";

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER,0);
	curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$response = curl_exec($ch);
	if(curl_getinfo($ch, CURLINFO_HTTP_CODE) == '200') 
	{
		$json = json_decode($response, TRUE);
		if (isset($json['results']))
		{
			$values['latitude'] = $json['results'][0]['geometry']['location']['lat'];
			$values['longitude'] = $json['results'][0]['geometry']['location']['lng'];
		}
	}
	curl_close($ch);
	return $values;
}

/* End of file google_helper.php */
/* Location: ./modules/fuel/helpers/google_helper.php */
