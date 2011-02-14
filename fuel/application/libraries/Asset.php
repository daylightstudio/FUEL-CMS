<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * FUEL CMS
 * http://www.getfuelcms.com
 *
 * An open source Content Management System based on the 
 * Codeigniter framework (http://codeigniter.com)
 *
 * @package		FUEL CMS
 * @author		David McReynolds @ Daylight Studio
 * @copyright	Copyright (c) 2011, Run for Daylight LLC.
 * @license		http://www.getfuelcms.com/user_guide/general/license
 * @link		http://www.getfuelcms.com
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * FUEL Asset Class
 *
 * This class allows you to output css, js links and/or files as well as
 * allows you to compress and cache them. Also has convenience methods for 
 * paths to assets
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/libraries/asset
 */

class Asset {
	
	// relative to web_root
	public $assets_path = 'assets/';

	// file path to assets folder
	public $assets_server_path = '';

	// relative to web_root/assets_path
	public $assets_folders = array(
		'images' => 'images/',
		'css' => 'css/',
		'js' => 'js/',
		'pdf' => 'pdf/',
		'swf' => 'swf/',
		'media' => 'media/',
		'captchas' => 'captchas/'
		);

	// makes paths to assets absolute
	public $assets_absolute_path = TRUE;

	// used for caching
	public $assets_last_updated = '00/00/0000 00:00:00';

	// appends timestamp of last updated after file name
	public $asset_append_cache_timestamp = array('js', 'css');
	
	// optimize/cache assets
	public $assets_output = FALSE;

	// cache folder relative to the application folder... must be writable directory (default is the application/assets/cache folder)
	public $assets_cache_folder = 'cache/';

	// time limit on gzip cache file in seconds
	public $assets_gzip_cache_expiration = 3600;
	
	// module assets path 
	public $assets_module_path = 'fuel/modules/{module}/assets/';
	
	// module context for assets
	public $assets_module = '';
	
	// an array of all the css/js files used so we can check as to whether we need to call them again.
	protected $_used = array();
	
	// cache of module configs loaded
	protected $_module_config_loaded = array();
	
	// has assets configuration been loaded?
	protected $_asset_config_loaded = FALSE;
	
	/**
	 * Constructor - Sets Asset preferences
	 *
	 * The constructor can be passed an array of config values
	 */
	public function __construct($params = array())
	{
		if (!defined('WEB_ROOT')) define('WEB_ROOT', str_replace(SELF, '', FCPATH));
		if (!defined('WEB_PATH')) define('WEB_PATH', str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']));
		if (count($params) > 0)
		{
			$this->initialize($params);		
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Initialize the user preferences
	 *
	 * Accepts an associative array as input, containing display preferences
	 *
	 * @access	public
	 * @param	array	config preferences
	 * @return	void
	 */	
	public function initialize($config = array())
	{
		foreach ($config as $key => $val)
		{
			if (isset($this->$key))
			{
				$this->$key = $val;
			}
		}
	}

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
	public function img_path($file = NULL, $module = NULL, $absolute = NULL)
	{
		return $this->assets_path($file, 'images', $module, $absolute);
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
	public function css_path($file = NULL, $module = NULL, $absolute = NULL)
	{
		if (!empty($file)) 
		{
			if (!preg_match('#(\.css|\.php)(\?.+)?$#', $file))
			{
				$file = $file.'.css';
			}
		}
		return $this->assets_path($file, 'css', $module, $absolute);
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
	public function js_path($file = NULL, $module = NULL, $absolute = NULL)
	{
		if (!empty($file)) 
		{
			if (!preg_match('#(\.js|\.php)(\?.+)?$#', $file))
			{
				$file = $file.'.js';
			}
		}
		return $this->assets_path($file, 'js', $module, $absolute);
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
	public function swf_path($file = NULL, $module = NULL, $absolute = NULL)
	{
		if (!empty($file))
		{
			if (!preg_match('#(\.swf|\.php)(\?.+)?$#', $file))
			{
				$file = $file.'.swf';
			}
		}
		return $this->assets_path($file, 'swf', $module, $absolute);
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
	public function pdf_path($file = NULL, $module = NULL, $absolute = NULL)
	{
		if (!empty($file))
		{
			if (!preg_match('#(\.pdf|\.php)(\?.+)?$#', $file))
			{
				$file = $file.'.pdf';
			}
		}
		return $this->assets_path($file, 'pdf', $module, $absolute);
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
	public function media_path($file = NULL, $module = NULL, $absolute = NULL)
	{
		return $this->assets_path($file, 'media', $module, $absolute);
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
	public function cache_path($file = NULL, $module = NULL, $absolute = NULL)
	{
		return $this->assets_path($file, 'assets_cache_folder', $module, $absolute);
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
	public function captcha_path($file = NULL, $module = NULL, $absolute = NULL)
	{
		return $this->assets_path($file, 'captchas', $module, $absolute);
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
	public function assets_path($file = NULL, $path = NULL, $module = NULL, $absolute = NULL)
	{
		$cache = '';
		if (!isset($absolute)) $absolute = $this->assets_absolute_path;
		
		$CI = $this->_get_assets_config();
		if ($this->asset_append_cache_timestamp AND in_array($path, $this->asset_append_cache_timestamp) AND !empty($file))
		{
			$q_str = (strpos($file, '?') === FALSE) ? '?' : '&';
			$cache = $q_str.'c='.strtotime($this->assets_last_updated);
		}
	
		// if it is an absolute path already provided, then we just return it without any caching
		if (!$this->_is_local_path($file))
		{
			return $file.$cache;
		}
		
		$assets_path = $this->_get_assets_path($module);
		
		$assets_folders = $this->assets_folders;

		$asset_type = (!empty($assets_folders[$path])) ? $assets_folders[$path] : $CI->config->item($path);
		$path = WEB_PATH.$assets_path.$asset_type.$file.$cache;

		if ($absolute)
		{
			$path = 'http://'.$_SERVER['HTTP_HOST'].$path;
		}
		return $path;
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
	public function assets_server_path($file = NULL, $path = NULL, $module = NULL)
	{
		$CI = $this->_get_assets_config();

		$assets_path = $this->_get_assets_path($module);
		$assets_folders = $this->assets_folders;
		
		$asset_type = (!empty($assets_folders[$path])) ? $assets_folders[$path] : $CI->config->item($path);
		$path = WEB_ROOT.$assets_path.$asset_type.$file;
		//$path = str_replace('/', DIRECTORY_SEPARATOR, $path); // for windows
		return $path;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Convert a server path to a web path
	 *
	 * @access	public
	 * @param	string	server path to asset file
	 * @return	string
	 */	
	public function assets_server_to_web_path($file, $truncate_to_asset_folder = FALSE)
	{
		$file = str_replace('\\', '/', $file); // for windows
		$web_path = str_replace(WEB_ROOT, '', '/'.$file);
	//	$assets_path = str_replace('/', DIRECTORY_SEPARATOR, $this->assets_path); // for windows
		$assets_path = str_replace($this->assets_path, '', $web_path);
		
		if ($truncate_to_asset_folder)
		{
			if (strncmp($assets_path, '/', 1) === 0) $asset_path = substr($assets_path, 1);  // to remove beginning slash
			return $assets_path;
		}
		return str_replace('//', '/', $this->assets_path($assets_path));
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
	public function js($path, $module = '', $options = array())
	{
		// if the path is an associative array, than we assume the key is the module
		if (is_array($path))
		{
			$path_arr = each($path);
			if (!is_numeric($path_arr['key']))
			{
				$module = $path_arr['key'];
				$path = $path_arr['value'];
			}
		}
		
		if (!empty($options['attrs']))
		{
			$options['attrs'] = $this->_array_to_attr($options['attrs']);
			if (strpos($options['attrs'], 'type="text/javascript"') === FALSE)
			{
				$options['attrs'] .= 'type="text/javascript"';
			}

			if (strpos($options['attrs'], 'charset="utf-8"') === FALSE)
			{
				$options['attrs'] .= ' charset="utf-8"';
			}
		}
		else
		{
			$options['attrs'] = 'type="text/javascript" charset="utf-8"';
		}
		
		
		if (!isset($options['output']))
		{
			$options['output'] = $this->assets_output;
		}
		
		if ($options['output'] === 'inline')
		{
			$open = "<script type=\"text/javascript\" charset=\"utf-8\">\n";
			$open .= "\t//<![CDATA[\n";
			$close = "\n\t//]]>\n";
			$close .= "\t</script>";
		}
		else
		{
			$open = '<script src="';
			$close = '></script>';
		}

		$str = $this->_output('js', $module, $open, $close, $path, $options);
		if (!empty($options['echo'])) echo $str;
		$str = $str;
		return $str;
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
	public function css($path, $module = '', $options = array())
	{
		// if the path is an associative array, than we assume the key is the module
		if (is_array($path))
		{
			$path_arr = each($path);
			if (!is_numeric($path_arr['key']))
			{
				$module = $path_arr['key'];
				$path = $path_arr['value'];
			}
		}
		
		if (!empty($options['attrs']))
		{
			$options['attrs'] = $this->_array_to_attr($options['attrs']);
			if (strpos($options['attrs'], 'rel="stylesheet"') === FALSE)
			{
				$options['attrs'] .= 'rel="stylesheet"';
			}
		}
		else
		{
			$options['attrs'] = 'media="all" rel="stylesheet"';
		}
		
		if (!isset($options['output']))
		{
			$options['output'] = $this->assets_output;
		}
		
		if ($options['output'] === 'inline')
		{
			$open = "<style type=\"text/css\" media=\"screen\">\n";
			$close = "\n\t</style>";
		}
		else
		{
			$open = '<link href="';
			$close = '/>';
		}

		$str = $this->_output('css', $module, $open, $close, $path, $options);

		// fix background images urls
		if ($options['output'] === 'inline')
		{
			$str = str_replace('url(../images/', 'url('.$this->img_path('', $module).'../images/', $str);
			$str = str_replace('@import url(', '@import url('.$this->css_path('', $module), $str);
		}
		if (!empty($options['echo'])) echo $str;
		return $str;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns an swf asset path
	 *
	 * @access	private
	 * @param	string	file name of the swf file including extension
	 * @param	string	module module folder if any
	 * @param	array	additional parameter to include (attrs, ie_conditional, and output)
	 * @return	string
	 */	
	private function _output($type, $module, $open, $close, $path, $options)
	{
		$attrs = ''; 
		$ie_conditional = ''; 
		$output = FALSE; 
		$echo = FALSE; 
		
		extract($options);
		
		if (empty($path)) return;
		$CI = $this->_get_assets_config();

		//normalize
		if (is_string($output))
		{
			$output = strtolower($output);
		}
		
		$use_cache = ($output !== FALSE AND $output !== 'inline');
		$str = '';
		$nested = '';

		// open
		if (!empty($ie_conditional)) $open = "\n\t<!--[if ".$ie_conditional."]>\n\t".$open;
	
		// close
		if ($output !== 'inline')
		{
			if (!empty($attrs))
			{
				$attrs = $this->_array_to_attr($attrs);
				$close = '" '.$attrs.$close;
			}
			else
			{
				$close = '"'.$close;
			}
		}
		if (!empty($ie_conditional)) $close .= "\n\t<![endif]-->\n";
	
		// normalize $path
		if (is_string($path) AND strpos($path, ',') !== FALSE)
		{
			$path = preg_replace("/\s/", "", $path);
			$path = explode(',', $path);
		}
		
		if ($use_cache AND $output !== 'inline')
		{
			$cache_file = $this->_check_cache($path, $type, $output, $module);
			$str .= $open;
			$str .= $cache_file;
			$str .= $close;
			$str .= "\n\t";
		}
		else
		{
			// convert to an array if not already 
			$path = (array) $path;
			foreach($path as $val)
			{
				// if (is_array($val))
				// {
				// 	$nested .= $this->$type($val, '', $options);
				// }
				// else
				// {
					$str .= $open;
					$type_path = $type.'_path';
					$assets_folders = $this->assets_folders;
					if (!$this->_is_local_path($val) AND $output !== 'inline')
					{
						$str .= $val;
					}
					else
					{
						if ($output === 'inline')
						{
							$contents_path = $this->assets_server_path($val, $type, $module).'.'.$type;
							if (file_exists($contents_path))
							{
								$str .= file_get_contents($contents_path);
							}
						}
						else
						{
							$str .= $this->$type_path($val, $module);
						}
					}

					$str .= $close;
					$str .= "\n\t";
					$this->_add_used($type, $val);
				// }

			}
		}
	//	$str .= $nested;
		return $str;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Embeds a flash file using swfobject
	 *
	 * @access	public
	 * @param	string	file name of the swf file including extension
	 * @param	string	html id that the flash will replace with swfobject
	 * @param	int		width of the flash file
	 * @param	int		height of the flash file
	 * @param	array	additional parameter to include (vars, version, and color, params)
	 * @return	string
	 */	
	public function swf($flash, $id, $width, $height, $options = array())
	{
		$vars = NULL; 
		$version = 9; 
		$color = '#ffffff'; 
		$params = array(); 

		if (is_array($options))
		{
			if (isset($options['vars'])) $vars = $options['vars'];
			if (isset($options['version'])) $version = $options['version'];
			if (isset($options['color'])) $color = $options['color'];
			if (isset($options['params'])) $params = $options['params'];
		}
		
		if (empty($flash)) return;
		$CI = $this->_get_assets_config();
		if (!empty($CI))
		{
			$swf_path = $CI->config->item('swf_path');
		}
	
		$str = '';
		if (empty($id))
		{
			$id_arr = explode('.', $flash);
			$id = $id_arr[0];
			$str .= '
			<div id="'.$id.'">
			</div>';
		}
		if (!$this->_is_used('js', 'swfobject') AND !$this->_is_used('js', 'swfobject.js'))
		{
			$str .= $this->js('swfobject');
		}
		$str .= '
		<script type="text/javascript">
		//<![CDATA[
		   var so = new SWFObject("'.$this->swf_path($flash).'", "'.$id.'_swf", "'.$width.'", "'.$height.'", "'.$version.'", "'.$color.'");
		 ';
		if(!is_array($vars))
		{
			parse_str($vars, $vars);
		}
	 	foreach($vars as $key => $val)
	{
			$str .= '		so.addVariable("'.$key.'", "'.$val.'"); 
	';
		}
	
		 foreach($params as $key => $val)
		{
			$str .= '		so.addParam("'.$key.'", "'.$val.'"); 
	';
		}

		$str .= '		so.write("'.$id.'");
		// ]]>
		</script>
	';
		return $str;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Set and get cache version
	 *
	 * @access	private
	 * @param	string	file name of the swf file including extension
	 * @param	string	type of file (e.g. images, js, css... etc)
	 * @param	string	additional parameter to include (attrs, ie_conditional, and output)
	 * @param	string	type module folder if any
	 * @return	string
	 */	
	private function _check_cache($files, $type, $optimize, $module)
	{
		$CI =& get_instance();
		$files = (array) $files;
		$cache_file_name = '';
		$cache_dir = $this->assets_server_path($this->assets_cache_folder, 'cache', $module);
		
		$cacheable_files = array();
		$return = array();
	
		// first create file name
		foreach($files as $file)
		{
			if ($this->_is_local_path($file))
			{
				if (substr($file, -(strlen($type)), (strlen($type) + 1)) == '.'.$type)
				{
					//$file = $file.'.'.$type;
					$file = substr($file, -(strlen($type)), (strlen($type) + 1));
				}

				$cacheable_files[] = trim($file);

				// replace backslashes with hyphens
				$file = str_replace('/', '_', $file);
				$cache_file_name .= $file.'|';
			}
		}
		$cache_file_name = $cache_file_name.'.'.$type;
	
		$cache_file_name_md5 = md5($cache_file_name);
		$ext = ($optimize === TRUE OR $optimize == 'gzip') ? 'php' : $type;
		$cache_file_name = $cache_file_name_md5.'_'.strtotime($this->assets_last_updated).'.'.$ext;
		$cache_file = $cache_dir.$cache_file_name;
	
		// create cache file if it doesn't exist'
		if (!file_exists($cache_file))
		{
			$CI->load->helper('file');
			$assets_folders = $this->assets_folders;
			//$asset_folder = WEB_ROOT.'/'.$this->assets_path.$assets_folders[$type];
			$asset_folder = $this->assets_server_path('', $type, $module);
			
			$output = '';
			foreach($cacheable_files as $file)
			{
				// replace backslashes with hyphens
				$file_path = $asset_folder.$file.'.'.$type;

				if (file_exists($file_path))
				{
					$output .= file_get_contents($file_path).PHP_EOL;
				}
			}
		
			// optimize file by removing returns and tabs
			if ($type == 'js')
			{
				if ($optimize === TRUE OR $optimize == 'whitespace')
				{
					$output = str_replace(array("\t"), '', $output);
					$output = preg_replace("/^\s/m", '', $output);
				
					// no replacing multi-line comments because it normally has copyright stuff
				} 

				$mime = 'text/javascript';
			}
			else if ($type == 'css')
			{
		
				// now include all import files as well ... only 1 deep though
				preg_match_all('/@import url\((.+)\);/U', $output, $imports);
				if (!empty($imports[1][0]))
				{
					foreach($imports[1] as $import)
					{
					
						$import_file_path = $this->assets_server_path($import, $type, $module);
						if (file_exists($import_file_path))
						{
							$import_files[$import] = file_get_contents($import_file_path);
						}
					}
					// remove calls to the import since they are combined into the same css
					if (!empty($import_files))
					{
						$output = preg_replace('/@import url\(([^)]+)\);/Ue', "\$import_files[('\\1')]", $output);
					}
				}
			
				// strip unnecessary whitespace
				if ($optimize === TRUE OR $optimize == 'whitespace')
				{
					$output = str_replace(array("\n", "\r", "\t"), '', $output);
					$output = preg_replace('<\s*([@{}:;,]|\)\s|\s\()\s*>S', '\1', $output);// Remove whitespace around separators,
					// remove multi-line comments...
					//$output = preg_replace("/((?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/?)|(?:\/\/.*))/", "", $output);// buggy with absolute image paths
					$output = preg_replace("#/\*[^*]*\*+(?:[^/*][^*]*\*+)*/#", "", $output);
				}
			
				$mime = 'text/css';
			
			}

			// gzip if enabled in config and the server
			if (($optimize === TRUE OR $optimize == 'gzip') AND extension_loaded('zlib'))
			{
				if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) AND strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== FALSE)
				{
					$gzip = "<?php".PHP_EOL;
					$gzip .= "ob_start();".PHP_EOL;
				
					// start an inner buffer so we can get the content length
					$gzip .= "ob_start (\"ob_gzhandler\");".PHP_EOL;
					$gzip .= "\n?>";
					$gzip .= $output;
					$gzip .= "<?php".PHP_EOL;
					$gzip .= "ob_end_flush();".PHP_EOL;
				
					// now begin inner buffer headers
					$gzip .= "header(\"Content-type: ".$mime."; charset: UTF-8\");".PHP_EOL;
					$gzip .= "header(\"Cache-Control: must-revalidate\");".PHP_EOL;
					$gzip .= "\$offset = ".$this->assets_gzip_cache_expiration.";".PHP_EOL;
					$gzip .= "\$exp = \"Expires: \".gmdate(\"D, d M Y H:i:s\",time() + \$offset).\" GMT\";".PHP_EOL;
					$gzip .= "header(\$exp);".PHP_EOL;
					$gzip .= "\$size = \"Content-Length: \".ob_get_length();".PHP_EOL;
					$gzip .= "header(\$size);".PHP_EOL;
					$gzip .= 'ob_end_flush();';
					$gzip .= "\n?>".PHP_EOL;
					$output = $gzip;
				}
			}

			// try to create directories if not there
			if (!is_dir($cache_dir) AND is_writable($cache_dir))
			{
				@mkdir($cache_dir, 0777, TRUE);
			}
		
		
			// cleanup files with the same prefix without the last updated time
			$dir_files = (array) get_filenames($cache_dir);
			foreach($dir_files as $dir_file)
			{
				if (strncmp($dir_file, $cache_file_name_md5, 10) === 0)
				{
					 unlink($cache_dir.$dir_file);
				}
			}
			write_file($cache_file, $output); // write cache file
		}
		return $this->cache_path($cache_file_name, $module);
	}


	// --------------------------------------------------------------------
	
	/**
	 * Creates attributes for a tag
	 *
	 * @access	private
	 * @param	mixed	array or string of attribute values
	 * @return	string
	 */	
	private function _array_to_attr($arr)
	{
		if (is_array($arr))
		{
			$str = '';
			foreach($arr as $key => $val)
			{
				$str .= $key.'="'.$val.'"';
			}
			return $str;
		}
		else
		{
			return $arr;
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Helper function to determine if it is a local path
	 *
	 * @access	private
	 * @param	file	path to the file
	 * @return	boolean
	 */	
	private function _is_local_path($path)
	{
		if (strncmp($path, 'http', 4) === 0)
		{
			return FALSE;
		}
		return TRUE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Add to the used array
	 *
	 * @access	private
	 * @param	string	type of file (e.g. images, js, css... etc)
	 * @param	string	file name
	 * @return	void
	 */	
	private function _add_used($type, $file)
	{
		if (!isset($this->_used[$type])) $this->_used[$type] = array();
		$this->_used[$type][] = $file;
		
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Check to see whether a css/js file has been used yet
	 *
	 * @access	private
	 * @param	string	type of file (e.g. images, js, css... etc)
	 * @param	string	file name
	 * @return	boolean
	 */	
	private function _is_used($type, $file)
	{
		return (isset($this->_used[$type]) AND in_array($file, $this->_used[$type]));
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the path to the asset. 
	 *
	 * if a module is provided, we look in the modules folder or whatever it states in the {module}_assets_path config value
	 *
	 * @access	private
	 * @param	string	module module folder if any
	 * @return	string
	 */	
	private function _get_assets_path($module = NULL)
	{
		if (!isset($module)) $module = $this->assets_module;
		$assets_path = '';

		// if a module is provided, we look in the modules folder or whatever it states in the {module}_assets_path config value
		if (!empty($module))
		{
			if (empty($this->_module_config_loaded[$module]))
			{
				$assets_path = $this->assets_module_path;
				$module_config = MODULES_PATH.$module.'/config/'.$module.EXT;
				if (file_exists($module_config))
				{
					include_once($module_config);
					if (!empty($config[$module.'_assets_path']))
					{
						$assets_path = $config[$module.'_assets_path'];

					}
				}
			}
			else
			{
				$assets_path = $this->_module_config_loaded[$module];
			}
		}
		else
		{
			$assets_path = $this->assets_path;
		}
		$assets_path = str_replace('{module}', $module, $assets_path);

		// cache it so we only include it once
		if (!empty($module))
		{
			$this->_module_config_loaded[$module] = $assets_path;
		}
		return $assets_path;
	}
	
	
	// --------------------------------------------------------------------
	
	/**
	 * Loads the asset config and returns the CI super global object
	 *
	 * @access	private
	 * @return	object
	 */	
	private function _get_assets_config()
	{
		if (function_exists('get_instance'))
		{
			$CI =& get_instance();
			if (!$this->_asset_config_loaded)
			{
				$CI->load->config('asset');
				$this->_asset_config_loaded = TRUE;
			}
			return $CI;
		}
		return NULL;
	}
}

/* End of file Asset.php */
/* Location: ./application/libraries/Asset.php */