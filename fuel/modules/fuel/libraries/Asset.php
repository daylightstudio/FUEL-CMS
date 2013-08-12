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
 * @copyright	Copyright (c) 2013, Run for Daylight LLC.
 * @license		http://docs.getfuelcms.com/general/license
 * @link		http://www.getfuelcms.com
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * FUEL Asset Class
 *
 * This class allows you to output css, js links and/or files as well as
 * allows you to compress and cache them. It also has convenience methods for 
 * paths to different assets like images, pdfs, javascript css etc.
 * 
 * Additionally, you can use the <a href="[user_guide_url]helpers/asset">asset helper</a>
 * which provides a shortcut for many of the methods of the Asset class. 
 * 
 * This class is auto-loaded.
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/libraries/asset
 */

class Asset {
	
	// relative to web_root
	public $assets_path = 'assets/';

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
	
	/**
	 * Optimize and/or cache assets. Options are:
	 *
	<ul>
		<li><strong>FALSE</strong> - no optimization</li>
		<li><strong>TRUE</strong> - will combine files, strip whitespace, and gzip</li>
		<li><strong>inline</strong> - will render the files inline</li>
		<li><strong>gzip</strong> - will combine files (if multiple) and gzip without stripping whitespace</li>
		<li><strong>whitespace</strong> - will combine files (if multiple) and strip out whitespace without gzipping</li>
		<li><strong>combine</strong> - will combine files (if multiple) but will not strip out whitespace or gzip</li>
	</ul>
	 */
	public $assets_output = FALSE;

	// force assets to recompile on each load
	public $force_assets_recompile = FALSE;

	// cache folder relative to the application folder... must be writable directory (default is the application/assets/cache folder)
	public $assets_cache_folder = 'cache/';

	// time limit on gzip cache file in seconds
	public $assets_gzip_cache_expiration = 3600;
	
	// module assets path 
	public $assets_module_path = 'fuel/modules/{module}/assets/';
	
	// module context for assets
	public $assets_module = '';
	
	// will ignore loading css and js files if loaded already
	public $ignore_if_loaded = FALSE;
	
	// an array of all the css/js files used so we can check as to whether we need to call them again.
	protected $_used = array();
	
	// cache of module configs loaded
	protected $_module_config_loaded = array();
	
	// has assets configuration been loaded?
	protected $_asset_config_loaded = FALSE;
	
	// the collection of files to cache
	protected $_cacheable_files = array();
	
	// --------------------------------------------------------------------
	
	/**
	 * Constructor
	 *
	 * Accepts an associative array as input, containing preferences (optional)
	 *
	 * @access	public
	 * @param	array	config preferences
	 * @return	void
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
	<code>
	echo $this->asset->img_path('banner.jpg');
	// /assets/images/banner.jpg

	echo $this->asset->img_path('banner.jpg', 'my_module');
	// /fuel/modules/my_module/assets/images/banner.jpg (assuming /fuel/modules is where the module folder is located)

	echo $this->asset->img_path('banner.jpg', NULL, TRUE);
	// http://www.mysite.com/assets/images/banner.jpg (if the "assets_module" Asset class property is empty)
	// http://www.mysite.com/fuel/modules/my_module/assets/images/banner.jpg (if the "assets_module" Asset class property is my_module)

	echo $this->asset->img_path('banner.jpg', '', TRUE);
	// http://www.mysite.com/assets/images/banner.jpg (and empty string for the module parameter will properly ignore anything in the assets_module Asset class property)

	</code>
	<p class="important">File extension <strong>must</strong> be included.</p>
	
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
	<code>
	echo $this->asset->css_path('main');
	// /assets/css/main.css

	echo $this->asset->css_path('main', 'my_module');
	// /fuel/modules/my_module/assets/css/main.css (assuming /fuel/modules is where the module folder is located)

	echo $this->asset->css_path('main', NULL, TRUE);
	// http://www.mysite.com/assets/css/main.css
	</pre>

	</code>
	<p class="important">The <kbd>.css</kbd> file extension will automatically be added if it is not found in the file name (first parameter).</p>

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
	<code>
	echo $this->asset->js_path('main');
	// /assets/js/main.js

	echo $this->asset->js_path('main', 'my_module');
	// /fuel/modules/my_module/assets/js/main.js (assuming /fuel/modules is where the module folder is located)

	echo $this->asset->js_path('main', NULL, TRUE);
	// http://www.mysite.com/assets/js/main.js
	</code>

	<p class="important">The <kbd>.js</kbd> file extension will automatically be added if it is not found in the file name (first parameter).</p>
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
	<code>
	echo $this->asset->swf_path('main');
	// /assets/swf/home.swf

	echo $this->asset->swf_path('main', 'my_module');
	// /fuel/modules/my_module/assets/swf/home.swf (assuming /fuel/modules is where the module folder is located)

	echo $this->asset->swf_path('main', NULL, TRUE);
	// http://www.mysite.com/assets/swf/home.swf
	</code>

	<p class="important">The <kbd>.swf</kbd> file extension will automatically be added if it is not found in the file name (first parameter).</p>
	
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
	<code>
	echo $this->asset->pdf_path('newsletter');
	// /assets/swf/newsletter.pdf

	echo $this->asset->pdf_path('main', 'my_module');
	// /fuel/modules/my_module/assets/pdf/newsletter.pdf (assuming /fuel/modules is where the module folder is located)

	echo $this->asset->pdf_path('main', NULL, TRUE);	
	// http://www.mysite.com/assets/pdf/newsletter.pdf
	</code>

	<p class="important">The <kbd>.pdf</kbd> file extension will automatically be added if it is not found in the file name (first parameter).</p>
	
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
	<code>
	echo $this->asset->media_path('mymovie.mov');
	// /assets/media/mymovie.mov

	echo $this->asset->media_path('mymovie.mov', 'my_module');
	// /fuel/modules/my_module/assets/media/nmymovie.mov (assuming /fuel/modules is where the module folder is located)

	echo $this->asset->media_path('mymovie.mov', NULL, TRUE);
	// http://www.mysite.com/assets/media/mymovie.mov
	</code>

	<p class="important">File extensions <strong>must</strong> be included.</p>
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
	 * Returns a document asset path (e.g. doc, docx)
	 *
	<code>
	echo $this->asset->docs_path('mydoc.doc');
	// /assets/docs/mydoc.doc

	echo $this->asset->docs_path('mydoc.doc', 'my_module');
	// /fuel/modules/my_module/assets/docs/mydoc.doc (assuming /fuel/modules is where the module folder is located)

	echo $this->asset->media_path('mydoc.doc', NULL, TRUE);
	// http://www.mysite.com/assets/docs/mydoc.doc
	</code>

	<p class="important">File extensions <strong>must</strong> be included.</p>

	 * @access	public
	 * @param	string	doc file name including extension
	 * @param	string	module folder if any
	 * @param	boolean	whether to include http://... at beginning
	 * @return	string
	 */	
	public function docs_path($file = NULL, $module = NULL, $absolute = NULL)
	{
		return $this->assets_path($file, 'docs', $module, $absolute);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns a cache asset path
	 *
	<code>
	echo $this->asset->cache_path('3c38643da81c3cee289feac34465c353_943948800.php');
	// /assets/cache/3c38643da81c3cee289feac34465c353_943948800.php

	echo $this->asset->cache_path('3c38643da81c3cee289feac34465c353_943948800.php', 'my_module');
	// /fuel/modules/my_module/assets/cache/3c38643da81c3cee289feac34465c353_943948800.php (assuming /fuel/modules is where the module folder is located)

	echo $this->asset->cache_path('3c38643da81c3cee289feac34465c353_943948800.php', NULL, TRUE);
	// http://www.mysite.com/assets/cache/3c38643da81c3cee289feac34465c353_943948800.php
	</code>

	<p class="important">File extensions <strong>must</strong> be included. 
	Modules should include a <strong>writable</strong> asset cache folder (e.g. assets/cache) if asset optimizing is used
	</p>
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
	<code>
	echo $this->asset->captcha_path('123456_captcha.jpg');
	// /assets/captcha/123456_captcha.jpg

	echo $this->asset->captcha_path('123456_captcha.jpg', 'my_module');
	// /fuel/modules/my_module/assets/captcha/123456_captcha.jpg (assuming /fuel/modules is where the module folder is located)

	echo $this->asset->captcha_path('123456_captcha.jpg', NULL, TRUE);
	// http://www.mysite.com/assets/captcha/123456_captcha.jpg
	</code>
	
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
	<code>
	echo $this->asset->assets_path();
	// /assets/

	echo $this->asset->assets_path('banner.jpg', 'images');
	// /assets/images/banner.jpg

	echo $this->asset->assets_path('banner.jpg', 'images', 'my_module');
	// /fuel/modules/my_module/assets/images/banner.jpg (assuming /fuel/modules is where the module folder is located)

	echo $this->asset->assets_path('banner.jpg', 'images', NULL, TRUE);
	// http://www.mysite.com/assets/images/banner.jpg
	</code>

	<p class="important">File extensions <strong>must</strong> be included. This folder must be <strong>writable</strong>.</p>

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
		
		$assets_folders = $this->assets_folders;

		$asset_type = (!empty($assets_folders[$path])) ? $assets_folders[$path] : $CI->config->item($path);

		// if absolute path, then we just return that
		if (!$this->_is_local_path($this->assets_path))
		{
			return $this->assets_path.$asset_type.$file.$cache;
		}

		$assets_path = $this->_get_assets_path($module);
		
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
	<code>
	echo $this->asset->assets_server_path();
	// /Library/WebServer/Documents/assets/

	echo $this->asset->assets_path('banner.jpg', 'images');
	// /Library/WebServer/Documents/assets/images/banner.jpg

	echo $this->asset->assets_path('banner.jpg', 'images', 'my_module');
	// /Library/WebServer/Documents/fuel/modules/my_module/assets/images/banner.jpg (assuming /fuel/modules is where the module folder is located)
	</code>

	<p class="important">File extensions <strong>must</strong> be included. This folder must be <strong>writable</strong>.</p>

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
	<code>
	$file_server_path = '/Library/WebServer/Documents/assets/images/my_img.jpg';
	echo $this->asset->assets_server_to_web_path($file_server_path);
	// /assets/images/my_img.jpg
	</code>
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
	 * Returns a boolean value of whether a file exists
	 *
	<code>
	if ($this->asset->asset_exists('banner.jpg'))
	{
		echo 'file exists!';
	}
	</code>

	 * @access	public
	 * @param	string	asset file name including extension
 	 * @param	string	subfolder to asset file (e.g. images, js, css... etc)
 	 * @param	string	module folder if any
	 * @return	boolean
	 */	
	public function asset_exists($file = NULL, $path = NULL, $module = NULL)
	{
		$asset_file = assets_server_path($file, $path, $module);
		return (file_exists($asset_file));
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the file size of an asset
	 *
	<code>
	echo $this->asset->asset_filesize('banner.jpg');
	// 20500

	echo $this->asset->assets_path('banner.jpg', 'images', '', TRUE);
	// 20.5 KB 
	</code>

	 * @access	public
	 * @param	string	asset file name including extension
 	 * @param	string	subfolder to asset file (e.g. images, js, css... etc)
 	 * @param	string	module folder if any
 	 * @param	boolean	format
	 * @return	string
	 */	
	public function asset_filesize($file = NULL, $path = NULL, $module = NULL, $format = TRUE)
	{
		$asset_file = assets_server_path($file, $path, $module);
		$filesize = 0;
		if (file_exists($asset_file))
		{
			$filesize = filesize($asset_file);
		}
		if ($format)
		{
			if (!function_exists('byte_format'))
			{
				$CI = $this->_get_assets_config();
				$CI->load->helper('number');
			}
			$filesize = byte_format($filesize);
		}
		return $filesize;
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
	public function jquery($version = '1.7.1', $default = 'jquery')
	{
		$js = '<script src="//ajax.googleapis.com/ajax/libs/jquery/'.$version.'/jquery.min.js"></script>';
		$js .= '<script>window.jQuery || document.write(\'<script src="'.js_path($default).'"><\/script>\');</script>';
		return $js;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Inserts <script ...></script> tags based on configuration settings for js file path
	 *
	<p>The third parameter is an <kbd>array</kbd> of additional attributes to pass. Those attributes can be the following</p>
	<ul>
		<li><strong>attrs</strong> - additional attributes to pass to the <kbd>&lt;script&gt;</kbd> tag. Can be a string or an array</li>
		<li><strong>output</strong> - the output method to be applied to the contents of the file. Can be any of the <kbd>assets_output</kbd></li>
		<li><strong>ie_conditional</strong> - applies an IE specific conditional comment around the <kbd>&lt;script&gt;</kbd> tag</li>
	</ul>

	<p>Additionally, if the asset configuration of <strong>asset_append_cache_timestamp</strong> includes <strong>js</strong>,
	then the caching timestamp will be appended as a query string parameter at the end just like if you were to use
	<kbd>$this->asset->js_path().</kbd>
	Examples:
	</p>

	<code>
	echo $this->asset->js('main');
	// &lt;script src="/assets/js/main.js" type="text/javascript" charset="utf-8"&gt;&lt;/script&gt;

	echo $this->asset->js('jquery, main');
	// &lt;script src="/assets/js/jquery.js" type="text/javascript" charset="utf-8"&gt;&lt;/script&gt;
	// &lt;script src="/assets/js/main.js" type="text/javascript" charset="utf-8"&gt;&lt;/script&gt;

	echo $this->asset->js(array('jquery', 'main'));
	// &lt;script src="/assets/js/jquery.js" type="text/javascript" charset="utf-8"&gt;&lt;/script&gt;
	// &lt;script src="/assets/js/main.js" type="text/javascript" charset="utf-8"&gt;&lt;/script&gt;

	echo $this->asset->js('main', 'my_module');
	// &lt;script src="/fuel/modules/my_module/assets/js/jquery.js" type="text/javascript" charset="utf-8"&gt;&lt;/script&gt;

	echo $this->asset->js('main', NULL, array('output' => TRUE, 'attrs' => 'onload=myOnloadFunc()', 'ie_conditional' => 'lte IE 6'));
	// &lt;!--[if lte IE 6]&gt;
	// &lt;script src="/assets/cache/3c38643da81c3cee289feac34465c353_943948800.php" type="text/javascript" charset="utf-8" onload="myOnloadFunc"&gt;&lt;/script&gt;
	// &lt;![endif]--&gt;
	</code>

	<p class="important"><strong>Important</strong> - All path references in the javascript file (e.g. paths to image files), should be changed to absolute if the script is printed <strong>inline</strong>.</p>
	
	 * @access	public
	 * @param	string	file name of the swf file including extension
	 * @param	string	module module folder if any
	 * @param	array	additional parameter to include (attrs, ie_conditional, and output... Can be any of the <strong>assets_output</strong>)
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
	<p>The third parameter is an <kbd>array</kbd> of additional attributes to pass. Those attributes can be the following</p>
	<ul>
		<li><strong>attrs</strong> - additional attributes to pass to the <kbd>&lt;script&gt;</kbd> tag. Can be a string or an array</li>
		<li><strong>output</strong> - the output method to be applied to the contents of the file. Can be any of the <kbd>assets_output</kbd></li>
		<li><strong>ie_conditional</strong> - applies an IE specific conditional comment around the <kbd>&lt;script&gt;</kbd> tag</li>
	</ul>
	
	<p>Additionally, if the asset configuration of <strong>asset_append_cache_timestamp</strong> includes <strong>js</strong>,
	then the caching timestamp will be appended as a query string parameter at the end just like if you were to use
	<kbd>$this->asset->js_path().</kbd>
	Examples:
	</p>

	<code>
	echo $this->asset->css('main');
	// &lt;link href="/assets/css/main.css" media="all" rel="stylesheet"/&gt;

	echo $this->asset->css('main, home');
	// &lt;link href="/assets/css/main.css" media="all" rel="stylesheet"/&gt;
	// &lt;link href="/assets/css/home.css" media="all" rel="stylesheet"/&gt;

	echo $this->asset->css(array('main', 'home'));
	// &lt;link href="/assets/css/main.css" media="all" rel="stylesheet"/&gt;
	// &lt;link href="/assets/css/home.css" media="all" rel="stylesheet"/&gt;

	echo $this->asset->css('main', 'my_module');
	// &lt;link href="fuel/modules/my_module/assets/css/main.css" media="all" rel="stylesheet"/&gt;

	echo $this->asset->css('main', NULL, array('output' => TRUE, 'attrs' => 'media="print"', 'ie_conditional' => 'lte IE 6'));
	// &lt;!--[if lte IE 6]&gt;
	// &lt;link href="/assets/css/main.css" media="print" rel="stylesheet"/&gt;
	// &lt;![endif]--&gt;
	</code>

	<p class="important"><strong>Important</strong> - All path references in the css file (e.g. paths to background image files), should be changed to absolute if the script is printed <strong>inline</strong></p>
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
	 * Convenience method that returns the HTML for the css() and js() methods
	 *
	 * @access	protected
	 * @param	string	the type of file to output. Options are js or css
	 * @param	string	module folder if any
	 * @param	string	the opening html
	 * @param	string	the closing html
	 * @param	string	the path to the asset
	 * @param	array	additional parameter to include (attrs, ie_conditional, and output)
	 * @return	string
	 */	
	protected function _output($type, $module, $open, $close, $path, $options)
	{
		$attrs = ''; 
		$ie_conditional = ''; 
		$output = FALSE; 
		$echo = FALSE; 
		
		extract($options);
		
		if (empty($path)) return;
		$CI = $this->_get_assets_config();
		
		if (!isset($ignore_if_loaded))
		{
			$ignore_if_loaded = $this->ignore_if_loaded;
		}

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
			// reset cacheable files array
			$this->_cacheable_files = array();
			
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
			$files_arr = array();
			$default_module = $module;
			
			foreach($path as $key => $val)
			{
				if ($ignore_if_loaded AND $this->is_used($type, $val))
				{
					continue;
				}
				
				$module = (is_string($key)) ? $key : $default_module;

				if (is_array($val))
				{
					$nested .= $this->$type($val, $module, $options);
				}
				else
				{
					$file_str = $open;
					$type_path = $type.'_path';
					$assets_folders = $this->assets_folders;
					if (!$this->_is_local_path($val) AND $output !== 'inline')
					{
						$file_str .= $val;
					}
					else
					{
						if ($output === 'inline')
						{
							$contents_path = $this->assets_server_path($val, $type, $module).'.'.$type;
							if (file_exists($contents_path))
							{
								$file_str .= file_get_contents($contents_path);
							}
						}
						else
						{
							$file_str .= $this->$type_path($val, $module);
						}
					}

					$file_str .= $close;
					$files_arr[] = $file_str;
					//$file_str .= "\n\t";
					$this->_add_used($type, $val);
				}

			}
			// use implode so it doesn't add the trailing \n\t'
			$str = $str.implode("\n\t", $files_arr);

			
		}
		
		$str .= "\n\t".$nested;
		return $str;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Embeds a flash file using swfobject
	 *
	<p>The fourth parameter is a catch all for additional parameter that can be passed which include:</p>
	<ul>
		<li><strong>vars</strong> - FlashVar variables to pass to the swf file</li>
		<li><strong>version</strong> - the Flash Player version to detect for. Default is Flash Player 9</li>
		<li><strong>color</strong> - the background color to be used. May be seen briefly before the swf file runs.</li>
		<li><strong>params</strong> - additional parameters to be passed to the swf file. For information on the additional parameters, visit the <a href="http://http://code.google.com/p/swfobject/wiki/documentation">swfobject documentation</a></li>
	</ul>
	<code>
	echo $this->asset->swf('home', 'home_flash', 800, 300, array('color' => '#000000', 'version' => 9));

	// &lt;script src="/assets/js/swfobject.js" type="text/javascript" charset="utf-8"&gt;&lt;/script&gt;
	// &lt;script type="text/javascript"&gt;
	// //&lt;![CDATA[
	//     var so = new SWFObject("/assets/swf/home.swf", "flash_swf", "800", "300", "9", "#000000");
	//     so.write("flash");
	// // ]]&gt;
	// &lt;/script&gt;

	</code>

	<p class="important"><strong>Important</strong> - Requires the <kbd>swfobject.js</kbd> to be located in the javascript assets folder. The swfoject being used is an older version (1.5)</p>
	
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
		if (!$this->is_used('js', 'swfobject') AND !$this->is_used('js', 'swfobject.js'))
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
	 * Check to see whether a css/js file has been used yet
	 *
	 * @access	public
	 * @param	string	type of file (e.g. images, js, css... etc)
	 * @param	string	file name
	 * @return	boolean
	 */	
	public function is_used($type, $file)
	{
		return (isset($this->_used[$type]) AND in_array($file, $this->_used[$type]));
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Set and get cache version
	 *
	 * @access	protected
	 * @param	string	file name of the swf file including extension
	 * @param	string	type of file (e.g.  js or css)
	 * @param	string	optimization methods which include FALSE, TRUE, 'inline', 'gzip', 'whitespace' and 'combine'
	 * @param	string	type module folder if any
	 * @return	string
	 */	
	protected function _check_cache($files, $type, $optimize, $module)
	{
		$CI =& get_instance();
		$files = (array) $files;
		$cache_file_name = '';
		$cache_dir = $this->assets_server_path($this->assets_cache_folder, 'cache', $module);
		
		$return = array();
	
		$default_module = $module;
	
		// first create file name
		foreach($files as $file)
		{
			if (is_array($file))
			{
				foreach($file as $key => $f)
				{
					$mod = (is_string($key)) ? $key : $default_module;
					$this->_cacheable_files[] = array($mod => $f);
					$cache_file_name .= $mod.'/'.$f.'|';
				}
			}
			else
			{
				if ($this->_is_local_path($file))
				{
					if (substr($file, -(strlen($type)), (strlen($type) + 1)) == '.'.$type)
					{
						//$file = $file.'.'.$type;
						$file = substr($file, -(strlen($type)), (strlen($type) + 1));
					}

					$this->_cacheable_files[] = array($module => trim($file));

					// replace backslashes with hyphens
					$file = str_replace('/', '_', $file);
					$cache_file_name .= $module.'/'.$file.'|';
				}
			}
			
		}

		$cache_file_name = $cache_file_name.'.'.$type;

		$cache_file_name_md5 = md5($cache_file_name);
		$ext = ($optimize === TRUE OR $optimize == 'gzip') ? 'php' : $type;
		$cache_file_name = $cache_file_name_md5.'_'.strtotime($this->assets_last_updated).'.'.$ext;
		$cache_file = $cache_dir.$cache_file_name;



		// create cache file if it doesn't exist'
		if (!file_exists($cache_file) OR $this->force_assets_recompile)
		{
		
			$CI->load->helper('file');
			$assets_folders = $this->assets_folders;
			//$asset_folder = WEB_ROOT.'/'.$this->assets_path.$assets_folders[$type];
			
			$output = '';

			// set optimization parameters
			$optimize_params['type'] = $type;
			$optimize_params['js_minify'] = TRUE;

			if ($optimize === TRUE OR $optimize == 'whitespace')
			{
				$optimize_params['whitespace'] = TRUE;
			}

			if ($optimize === TRUE OR $optimize == 'gzip')
			{
				$optimize_params['gzip'] = TRUE;
			}

			$output = $this->optimize($this->_cacheable_files, $optimize_params);


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
	 * Optimizes CSS and JS files by combining files together with options to remove whitespace and optimize code.
	 *
	<ul>
		<li><strong>type</strong> - valid options are "js", "css" and auto. The default is "auto" and will look for the first file extension in the list of files passed</li>
		<li><strong>destination</strong> - the path and file name of the file to save the output to. Default is FALSE which means no file will be written</li>
		<li><strong>whitespace</strong> - whether to perform basic removal of whitespace.</li>
		<li><strong>js_minify</strong> - will use <a href="https://developers.google.com/closure/compiler/" target="_blank">Google's Closure Compiler</a> for javascript minification.</li>
		<li><strong>compilation_level</strong> - WHITESPACE_ONLY, SIMPLE_OPTIMIZATIONS and ADVANCED_OPTIMIZATION. More can be found here <a href="https://developers.google.com/closure/compiler/docs/api-ref" target="_blank">here</a>. The default is WHITESPACE_ONLY.</li>
		<li><strong>gzip</strong> - determines whether to add PHP code to gzip the file. Must be saved as a php file.</li>
	</ul>
	<code>
	$output = $this->asset->optimize(array('file1, 'file2.js', array('type' => 'js', 'destination' => 'my.min.js', 'whitespace' => TRUE, 'js_minify' => TRUE, 'gzip' => TRUE));
	</code>

	 *
	 * @access	protected
	 * @param	mixed	file(s) to optimize. Can be an array or string
	 * @param	array  an array of parameters including "destination", "whitespace", "js_minify", "compilation_level" and "type"
	 * @return	string
	 */	
	public function optimize($files, $params = array())
	{
		$CI =& get_instance();
		$CI->load->helper('file');

		// removes basic whitespace
		if (!isset($params['destination']))
		{
			$params['destination'] = FALSE;
		}

		// removes basic whitespace
		if (!isset($params['whitespace']))
		{
			$params['whitespace'] = TRUE;
		}

		// will CURL http://closure-compiler.appspot.com/compile
		if (empty($params['js_minify']))
		{
			$params['js_minify'] = TRUE;
		}

		// sets the curl level of http://closure-compiler.appspot.com/compile
		if (empty($params['compilation_level']))
		{
			$params['compilation_level'] = 'WHITESPACE_ONLY';
		}

		// type of optimization ("css" or "js")
		if (empty($params['type']))
		{
			$params['type'] = 'auto';
		}

		// add gzip compression and make it a .php file
		if (!isset($params['gzip']))
		{
			$params['gzip'] = FALSE;
		}

		$assets_folders = $this->assets_folders;
		
		$output = '';

		// normalize $files array
		if (!is_array($files))
		{
			$files = array($files);
		}

		// automatically come up with the type of file based on first file that you can detect extension
		$valid_exts = array('css', 'js');
		if (strtolower($params['type']) == 'auto')
		{
			// set type to javascript by default
			$params['type'] = 'js';
			foreach($files as $file)
			{
				$ext =  end(explode('.', $files[0]));
				if (in_array($ext, $valid_exts))
				{
					$params['type'] = $ext;
					break;
				}
			}
		}
		else if (!in_array($params['type'], $valid_exts))
		{
			return FALSE;
		}

		$mime = NULL;

		// loop through files to combine them
		foreach($files as $key => $file)
		{	
			if (!empty($params['module']))
			{
				$module = $params['module'];
			}
			else
			{
				$module = $this->assets_module;	
			}
			
			if (is_array($file))
			{
				$path_arr = each($file);
				if (!is_numeric($path_arr['key']))
				{
					$module = $path_arr['key'];
					$file = $path_arr['value'];
				}
			}

			// check for extension... if not there, add it
			if (!preg_match('#(\.'.$params['type'].'|\.php)(\?.+)?$#', $file))
			{
				$file = $file.'.'.$params['type'];
			}
			// replace backslashes with hyphens
			$asset_folder = $this->assets_server_path('', $params['type'], $module);
			$file_path = $asset_folder.$file;

			if (file_exists($file_path))
			{
				$output .= file_get_contents($file_path).PHP_EOL;
			}
		}

		// optimize file by removing returns and tabs
		if ($params['type'] == 'js')
		{
			if ($params['whitespace'] == TRUE)
			{
				$output = str_replace(array("\t"), '', $output);

				// remove whitespace from the beginning of the line
				$output = preg_replace("/^\s+/m", '', $output);

				// no replacing multi-line comments because it normally has copyright stuff
			} 

			if ($params['js_minify'] == TRUE AND extension_loaded('curl'))
			{

				// REST API arguments
				$api_args = array(
					'compilation_level' => $params['compilation_level'],
					'output_format' => 'text',
					'output_info' => 'compiled_code'
				);
				
				$args = 'js_code=' . urlencode($output);
				foreach ($api_args as $key => $value)
				{
					$args .= '&' . $key . '=' . urlencode($value);
				}
				// API call using cURL
				$ch = curl_init();
				curl_setopt_array($ch, array(
					CURLOPT_URL => 'http://closure-compiler.appspot.com/compile',
					CURLOPT_POST => 1,
					CURLOPT_POSTFIELDS => $args,
					CURLOPT_RETURNTRANSFER => 1,
					CURLOPT_HEADER => 0,
					CURLOPT_FOLLOWLOCATION => 0
				));

				if (curl_error($ch) == '')
				{
					$output = curl_exec($ch);	
				}
				else
				{
					exit(lang('error_curl_page'));
				}
			}

			$mime = 'text/javascript';
		}
		else if ($params['type'] == 'css')
		{
			// now include all import files as well ... only 1 deep though
			preg_match_all('/@import url\(([\'|"])*(.+)\\1\);/U', $output, $imports);
			if (!empty($imports[2][0]))
			{
				foreach($imports[2] as $import)
				{
					$import_file_path = $this->assets_server_path($import, $params['type'], $module);
					if (file_exists($import_file_path))
					{
						$import_files[$import] = file_get_contents($import_file_path);
					}
				}

				$callback = create_function('$matches', '
					if (isset($matches[2]))
					{
						return $GLOBALS["__TMP_CSS_IMPORT__"][$matches[2]];	
					} else {
						return "";
					}');

				// remove calls to the import since they are combined into the same css
				if (!empty($import_files))
				{
					// temporarily put it in the global space so the anonymoous function can grab it
					$GLOBALS["__TMP_CSS_IMPORT__"] = $import_files;
					$output = preg_replace_callback('/@import url\(([\'|"])*(.+)\\1\);/U', $callback, $output);
					unset($GLOBALS["__TMP_CSS_IMPORT__"]);
				}
			}
		
			// strip unnecessary whitespace
			if ($params['whitespace'] == TRUE)
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
		if (($params['gzip'] == TRUE) AND extension_loaded('zlib'))
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
				if (!empty($mime))
				{
					$gzip .= "header(\"Content-type: ".$mime."; charset: UTF-8\");".PHP_EOL;	
				}
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

		// write contents to file
		if (!empty($params['destination']))
		{
			$destination_dir = dirname($params['destination']);
			if (is_writable($destination_dir))
			{
				write_file($params['destination'], $output);	
			}
			
		}
		return $output;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Creates attributes for a tag
	 *
	 * @access	protected
	 * @param	mixed	array or string of attribute values
	 * @return	string
	 */	
	protected function _array_to_attr($arr)
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
	 * @access	protected
	 * @param	file	path to the file
	 * @return	boolean
	 */	
	protected function _is_local_path($path)
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
	 * @access	protected
	 * @param	string	type of file (e.g. images, js, css... etc)
	 * @param	string	file name
	 * @return	void
	 */	
	protected function _add_used($type, $file)
	{
		if (!isset($this->_used[$type])) $this->_used[$type] = array();
		$this->_used[$type][] = $file;
		
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the path to the asset. 
	 *
	 * if a module is provided, we look in the modules folder or whatever it states in the {module}_assets_path config value
	 *
	 * @access	protected
	 * @param	string	module module folder if any
	 * @return	string
	 */	
	protected function _get_assets_path($module = NULL)
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
	 * @access	protected
	 * @return	object
	 */	
	protected function _get_assets_config()
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
/* Location: ./modules/fuel/libraries/Asset.php */