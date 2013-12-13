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
		$p['q'] = urlencode($address);
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
* More on the return types here: https://developers.google.com/maps/documentation/geocoding/
*
* @access    public
* @param    mixed	Address can be either an array with "address", "city", "state" or simply a string
* @param    mixed	Return type can be 'all' (default), 'address_components', 'formatted_address', 'geometry', 'location', 'street_number', 'route', 'neighborhood', 'city',  'county', 'state', 'country', 'zip' (optional)
* @return   mixed  usually an array (e.g. array('latitude' => xxxx, 'longitude' => xxxx))
*/
function google_geolocate($data, $return = 'all')
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

		$lookup_func = create_function('$data, $key, $single = FALSE', '
			$components = $data["address_components"];
			$return = array("long_name" => "", "short_name" => "");

			foreach($components as $c)
			{
				if (isset($c["types"]) AND in_array($key, $c["types"]))
				{
					if (isset($c["long_name"]))
					{
						$return["long_name"] = $c["long_name"];
						if ($single)
						{
							return $return["long_name"];
						}
					}
					if (isset($c["short_name"]))
					{
						$return["short_name"] = $c["short_name"];
						if ($single)
						{
							return $return["long_name"];
						}
					}
					return $return;
				}
			}
		');
		if (isset($json['results']) AND $json['status'] == 'OK')
		{
			$results = $json['results'][0];
			$return = strtolower($return);
			switch($return)
			{
				case 'address_components':
					$values = $results['address_components'];
					break;
				case 'formatted_address':
					$values = $results['formatted_address'];
					break;
				case 'geometry':
					$values = $results['geometry'];
					break;
				case 'location':
					$values['latitude'] = $results['geometry']['location']['lat'];
					$values['longitude'] = $results['geometry']['location']['lng'];
					break;
				case 'street_number':
					$values = $lookup_func($results, 'street_number', TRUE);
					break;
				case 'route':
					$values = $lookup_func($results, 'route');
					break;
				case 'neighborhood':
					$values = $lookup_func($results, 'neighborhood', TRUE);
					break;
				case 'city': case 'locality':
					$values = $lookup_func($results, 'locality');
					break;
				case 'county': case 'administrative_area_level_2':
					$values = $lookup_func($results, 'administrative_area_level_2');
					break;
				case 'state': case 'administrative_area_level_1':
					$values = $lookup_func($results, 'administrative_area_level_1');
					break;
				case 'country':
					$values = $lookup_func($results, 'country');
					break;
				case 'zip': case 'postal_code':
					$values = $lookup_func($results, 'postal_code', TRUE);
					break;
				default:
					$values = $results;
			}
		}
	}
	curl_close($ch);
	return $values;
}

/* End of file google_helper.php */
/* Location: ./modules/fuel/helpers/google_helper.php */
