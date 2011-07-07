<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Fuel_page {
	
	public $id = NULL; // the page ID if it is coming from a database
	public $location = ''; // the uri location 
	public $layout = ''; // the layout file to apply to the view
	public $is_published = TRUE; // whether the page can be seen or not
	public $is_cached = TRUE; // is the file cached in the system cache directory
	public $views_path = ''; // the path to the views folder for rendering. Used with modules
	public $render_mode = 'views'; // is the page being rendered from the views folder or the DB
	public $markers = array();
	public static $marker_key = '__FUEL_MARKER__';
	
	private $_CI = NULL;
	private $_variables = array(); // variables applied to the page
	private $_segments = array(); // uri segments to pass to the page
	private $_page_data = array(); // specific data about the page (not variables being passed necessarily)
	private $_fuelified = FALSE; // is the person viewing the page logged in?... If so we will make inline editing and toolbar visible
	private $_only_published = TRUE; // view only published pages is used if the person viewing page is not logged in
	private $_fuelified_processed = FALSE; // if fuelify has already been called
	private $_marker_salt = NULL;
	
	function __construct($params = array())
	{
		$this->_CI =& get_instance();
		$this->_CI->load->helper('cookie');
		//$this->_CI->load->module_helper(FUEL_FOLDER, 'fuel'); // already loaded in autoload
		$this->_CI->load->module_config(FUEL_FOLDER, 'fuel', TRUE);

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
	function initialize($config = array())
	{
		// setup any intialized variables
		foreach ($config as $key => $val)
		{
			if (isset($this->$key))
			{
				$this->$key = $val;
			}
		}
		
		// cookie check... would be nice to remove to speed things up a little bit
		if (is_fuelified())
		{
			$this->_fuelified = TRUE;
			$this->_only_published = FALSE;
		}
		
		// assign the location of the page
		$this->_assign_location();

		// assign variables to the page
		$this->_assign_variables();
		
		// assign layout
		$this->_assign_layout();

	}
	
	private function _assign_location()
	{
		$default_home = $this->_CI->config->item('default_home_view', 'fuel');
		
		if ($this->location == 'page_router') $this->location = $default_home;
		
		$page_data = array('id' => NULL, 'cache' => NULL, 'published' => NULL, 'layout');
		$this->_page_data = $page_data;
		if ($this->render_mode == 'views')
		{
			return;
		}
		
		// if a location is provided in the init config, then use it instead of the uri segments
		if (!empty($this->location))
		{
			$segs = explode('/', $this->location);
			$segments = array_combine(range(1, count($segs)), array_values($segs));
		}
		else
		{
			$segments = $this->_CI->uri->rsegment_array();
		}
		
		// in case a module has a name the same (like news...)
		if (!empty($segments) AND $segments[count($segments)] == 'index')
		{
			array_pop($segments);
		}

		// MUST LOAD AFTER THE ABOVE SO THAT IT DOESN'T THROW A DB ERROR WHEN A DB ISN'T BEING USED
		// get current page segments so that we can properly iterate through to determine what the actual location 
		// is and what are params being passed to the location
		$this->_CI->load->module_model(FUEL_FOLDER, 'pages_model');

		if (count($this->_CI->uri->segment_array()) == 0 OR $this->location == $default_home) 
		{
			$page_data = $this->_CI->pages_model->find_by_location($default_home, $this->_only_published);
			$location = $default_home;
		} 
		else 
		{
			// if $location = xxx/yyy/zzz/, check first to see if /xxx/yyy/zzz exists in the DB, then reduce segments to xxx/yyy,
			// xxx... until one is found in the DB. If only xxx is found in the database yyy and zzz will be treated as parameters
			while(count($segments) >= 1){
				if (count($this->_segments) > $this->_CI->config->item('max_page_params', 'fuel')) break;
				$location = implode('/', $segments);
				
				// if a prefix for the location is provided in the config, change the location value accordingly so we can find it
				$prefix = $this->_CI->config->item('page_uri_prefix', 'fuel');
				if ($prefix)
				{
					if (strpos($location, $prefix) === 0){
						$location = substr($location, strlen($prefix));
					}
					$page_data = $this->_CI->pages_model->find_by_location($location, $this->_only_published);
				}
				else
				{
					$page_data = $this->_CI->pages_model->find_by_location($location, $this->_only_published);
				}
				
				if (!empty($page_data)){
					break;
				}
				$this->_segments[] = array_pop($segments);
			}
		}
		
		if (!empty($page_data))
		{
			$this->id = $page_data['id'];
			$this->location = $page_data['location'];
			$this->layout = $page_data['layout'];
			$this->is_published = is_true_val($page_data['published']);
			$this->is_cached = is_true_val($page_data['cache']);
			$this->_segments = $segments;
			$this->_page_data = $page_data;
		}
		
		// assign page segment values
		$this->_segments = array_reverse($this->_segments);
	}
	
	private function _assign_variables()
	{
		$this->_CI->load->module_library(FUEL_FOLDER, 'fuel_pagevars');
		$page_mode = $this->_CI->config->item('fuel_mode', 'fuel');
		if (empty($this->views_path)) $this->views_path = APPPATH.'views/';
		$this->_CI->fuel_pagevars->vars_path = $this->views_path.'_variables/';
		$vars = $this->_CI->fuel_pagevars->retrieve($this->location, $page_mode);
		$this->add_variables($vars);
	}
	
	private function _assign_layout()
	{
		$this->_CI->load->module_library(FUEL_FOLDER, 'fuel_layouts');
	}
	
	function render($return = FALSE, $fuelify = TRUE)
	{
		// set this config item so we can use it in the fuel_helper later for marking editable areas
		if (!isset($this->_page_data['id']))
		{
			// can't use set item when setting for a section'
			//$this->_CI->config->config['fuel']['fuel_mode'] = 'views';
			$this->render_mode = 'views';
			return $this->variables_render($return, $fuelify);
		}
		else
		{
			// can't use set item when setting for a section'
			//$this->_CI->config->config['fuel']['fuel_mode'] = 'cms';
			$this->render_mode = 'cms';
			return $this->cms_render($return, $fuelify);
		}
	}
	
	function cms_render($return = FALSE, $fuelify = FALSE){

		$this->_CI->load->library('template');
		$this->_CI->load->library('parser');
		
		// render template with page variables if data exists
		if (!empty($this->layout))
		{
			// get master layout files
			$parts = $this->_CI->fuel_layouts->parts($this->layout);
			
			// assign layouts to the layout object
			if (is_array($parts))
			{
				$this->_CI->template->assign_tpl($parts);
			}
			else
			{
				$this->_CI->template->file = $parts;
			}

			// assign vars to specific parts
			if (is_array($parts))
			{
				foreach($parts as $key => $val)
				{
					$part_fields = $this->_CI->fuel_layouts->part_field_values($key);
					
					// first assign default values for the layout
					$this->_CI->template->assign_to($key, $part_fields);
					
					// then assign any variables assigned to the keys
					$this->_CI->template->assign_to($key, $this->variables());
				}
			}
			else
			{
				$part_fields = $this->_CI->fuel_layouts->part_field_values($this->layout);
				// first assign default values for the layout
				$this->_CI->template->assign($part_fields);
				
				// then assign any variables assigned to the keys
				$this->_CI->template->assign($this->variables());
			}
			
			
			
//			$this->_CI->template->assign_global('CI', $CI);
			
			// call layout hooks
			$this->_CI->fuel_layouts->call_hook($this->layout, 'pre_render');
			
			// render the content
			$output = $this->_CI->template->render(TRUE);
			$vars = array_merge($this->variables(), $this->_CI->load->_ci_cached_vars);
			$this->_CI->load->vars($vars);

			//unset($vars['CI']);
			
			// now parse any template like syntax... not good if javascript is used in templates
			$output = $this->_CI->parser->parse_string($output, $vars, TRUE);
			
			// turn on inline editing if they are logged in and cookied
			if ($fuelify) $output = $this->fuelify($output);
			if ($return) 
			{
				return $output;
			}
			$this->_CI->output->set_output($output);
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	function variables_render($return = FALSE, $fuelify = FALSE)
	{

		// get the location and load page vars
		$page = $this->location;

		$vars = $this->variables();
		
		// load helpers
		if (!empty($vars['helpers']))
		{
			if (is_array($vars['helpers']))
			{
				foreach($vars['helpers'] as $key => $val)
				{
					if (!is_numeric($key))
					{
						$this->_CI->load->module_helper($key, $val);
					}
					else
					{
						$this->_CI->load->helper($val);
					}
				}
			}
			else
			{
				$this->_CI->load->helpers($vars['helpers']);
			}
		}
				
		// load libraries
		if (!empty($vars['libraries']))
		{
			if (is_array($vars['libraries']))
			{
				foreach($vars['libraries'] as $key => $val)
				{
					if (!is_numeric($key))
					{
						$this->_CI->load->module_library($key, $val);
					}
					else
					{
						$this->_CI->load->library($val);
					}
				}
			}
			else
			{
				$this->_CI->load->library($vars['libraries']);
			}
		}
		
		// load models
		if (!empty($vars['models']))
		{
			if (is_array($vars['models']))
			{
				foreach($vars['models'] as $key => $val)
				{
					if (!is_numeric($key))
					{
						$this->_CI->load->module_model($key, $val);
					}
					else
					{
						$this->_CI->load->model($val);
					}
				}
			}
			else
			{
				$this->_CI->load->model($vars['models']);
			}
		}
		
		// for convenience we'll add the $CI object'
		$vars['CI'] = &$this->_CI;
		$this->_CI->load->vars($vars);

		if (!empty($vars['view']))
		{
			$view = $vars['view'];
		}
		else
		{
			$view = $page;

			// if view is the index.html file, then show a 404
			if ($view == 'index.html')
			{
				show_404();
			}

			// do not display any views that have an underscore at the beginning of the view name, or is part of the path (e.g. about/_hidden/contact_email1.php)
			$view_parts = explode('/', $view);

			foreach($view_parts as $view_part)
			{
				if (!strncmp($view_part, '_', 1))
				{
					show_404();
				}
			}

		}

		$output = NULL;
		
		// set the extension... allows for sitemap.xml for example
		$ext = '.'.pathinfo($view, PATHINFO_EXTENSION);
		$check_file = $this->views_path.$view;
		if (empty($ext) OR $ext == '.') 
		{
			$ext = EXT;
			$check_file = $this->views_path.$view.$ext;
		}

		// find a view file
		if (!file_exists($check_file))
		{
			if (!isset($vars['find_view']) OR (isset($vars['find_view']) AND $vars['find_view'] !== FALSE))
			{
				$view = $this->find_view_file($view);
				$check_file = $this->views_path.$view.$ext;
			}
		}
		
		// if view file exists, set the appropriate layout 
		if (file_exists($check_file))
		{

			// load the file so we can parse it 
			if (!empty($vars['parse_view']))
			{
				// load her to save on execution time... 
				$this->_CI->load->library('parser');
				
				$body = file_get_contents($check_file);

				// now parse any template like syntax
				$vars = $this->_CI->load->_ci_cached_vars;
				$body = $this->_CI->parser->parse_string($body, $vars, TRUE);
			}
			else
			{
				$body = $this->_CI->load->module_view('app', $view, $vars, TRUE);
			}
			
			// now set $vars to the cached so that we have a fresh set to send to the layout in case any were declared in the view
			$vars = $this->_CI->load->_ci_cached_vars;

			$layout = (!empty($vars['layout'])) ? $vars['layout'] : $this->layout;
			
			// set layout variables
			$this->layout = $layout;
			$this->_page_data['layout'] = $layout;

			if (!empty($layout))
			{
				if (is_array($layout))
				{
					$this->_CI->load->library('template');
					foreach($layout as $key => $val)
					{
						if (strncmp($val, '_layouts', 8) !== 0) $val = '_layouts/'.$val;
						$this->_CI->template->assign_tpl($key, $val);
					}
					$this->_CI->template->assign_to('body', 'body', $body);
					$output = $this->_CI->template->render(TRUE);
				}
				else
				{
					$vars['body'] = $body;
					if (strncmp($layout, '_layouts', 8) !== 0) $layout = '_layouts/'.$layout;
					$output = $this->_CI->load->view($layout, $vars, TRUE);
				}

			}
			else
			{
				$output = $body;
			}
		}
		
		if (empty($output) && empty($vars['allow_empty_content'])) return FALSE;
		
		if ($fuelify) $output = $this->fuelify($output);

		if ($return)
		{
			return $output;
		}
		else
		{
			$this->_CI->output->set_output($output);
			return TRUE;
		}
	}
	
	function fuelify($output)
	{
		// if not logged in then we remove the markers
		if (!$this->_CI->config->item('admin_enabled', 'fuel') OR $this->variables('fuelified') === FALSE OR 
			!$this->_fuelified OR empty($output) OR (defined('FUELIFY') AND FUELIFY === FALSE)) 
		{
			return $this->remove_markers($output);
		} 
		
		$this->_CI->load->library('session');
		$this->_CI->load->helper('convert');
		
		
		// add top edit bar for fuel
		$this->_CI->config->module_load('fuel', 'fuel', TRUE);
		
		// render the markers to the proper html
		$output = $this->render_all_markers($output);
		
		// set main image and assets path before switching to fuel assets path
		$vars['init_params'] = array(
			'assetsImgPath' => img_path(''),
			'assetsPath' => assets_path(''),
			);
		
		$this->_CI->asset->assets_path = $this->_CI->config->item('fuel_assets_path', 'fuel');
		$this->_CI->load->helper('ajax');
		$this->_CI->load->library('form');
		$vars['page'] = $this->properties();
		$vars['layouts'] = $this->_CI->fuel_layouts->layouts_list(TRUE);
		$last_page = uri_path();
		if (empty($last_page)) $last_page = $this->_CI->config->item('default_home_view', 'fuel');
		$vars['last_page'] = uri_safe_encode($last_page);

		$editable_asset_types = $this->_CI->config->item('editable_asset_filetypes', 'fuel');


		// add javascript
		$vars['init_params']['pageId'] = (!empty($vars['page']['id']) ? $vars['page']['id'] : 0);
		$vars['init_params']['basePath'] = WEB_PATH;
		$vars['init_params']['imgPath'] = img_path('', 'fuel'); 
		$vars['init_params']['cssPath'] = css_path('', 'fuel'); 
		$vars['init_params']['jsPath'] = js_path('', 'fuel');
		$vars['init_params']['editor'] = $this->_CI->config->item('text_editor', 'fuel');
		$vars['init_params']['editorConfig'] = $this->_CI->config->item('ck_editor_settings', 'fuel');
		
		// load language files
		$this->_CI->load->module_language(FUEL_FOLDER, 'fuel_inline_edit');
		$this->_CI->load->module_language(FUEL_FOLDER, 'fuel_js');
		
		
		// json localization
		$vars['js_localized'] = json_lang('fuel/fuel_js');
		$vars['assetsAccept']['assetsAccept'] = (!empty($editable_asset_types['media']) ? $editable_asset_types['media'] : 'jpg|gif|png');
		
		// database specific... so we must check the fuel mode to see if we actually need to make a call to the database. 
		// otherwise we get an error when the mode is set to views
		if ($this->_CI->config->item('fuel_mode', 'fuel') == 'views')
		{
			$vars['others'] = array();
		}
		else
		{
			$this->_CI->load->module_model(FUEL_FOLDER, 'pages_model');
			$vars['others'] = $this->_CI->pages_model->get_others('location', $this->location, 'location');
		}
		
		if (!$this->_fuelified_processed)
		{
			$inline_edit_bar = $this->_CI->load->module_view(FUEL_FOLDER, '_blocks/inline_edit_bar', $vars, TRUE);
			$output = preg_replace('#(</head>)#i', css('fuel_inline', 'fuel')."\n$1", $output);
			$output = preg_replace('#(</body>)#i', $inline_edit_bar."\n$1", $output);
			$this->_CI->config->set_item('assets_path', $this->_CI->config->item('assets_path'));
		}
		$this->_fuelified_processed = TRUE;
		return $output;
	}
	
	function add_marker($marker)
	{
		$key = $this->get_marker_prefix().count($this->markers);
		$this->markers[$key] = $marker;
		return $key;
	}
	
	function get_marker_prefix()
	{
		return Fuel_page::$marker_key;
	}
	
	function get_marker_regex()
	{
		$marker_prefix = $this->get_marker_prefix();
		$marker_reg_ex = '<!--('.$marker_prefix.'\d+)-->';
		return $marker_reg_ex;
	}
	
	function render_marker($key)
	{
		if (!isset($this->markers[$key])) return '';
			
		$marker = $this->markers[$key];
		
		if (empty($marker)) return '';
		extract($marker);
		
		if ($this->_CI->config->item('admin_enabled', 'fuel') AND 
			is_fuelified() AND 
			(!isset($this->_CI->load->_ci_cached_vars['fuelified']) OR $this->_CI->load->_ci_cached_vars['fuelified'] === TRUE)
			)
		{
			if (empty($label))
			{
				$label_arr = explode('|', $id);
				$label = (!empty($label_arr[1])) ? $label_arr[1] : $label_arr[0];
				$label = ucfirst(str_replace('_', ' ', $label));
			}

			// must use span tags because nesting anchors can cause issues
			$output = '<span class="__fuel_marker__" data-href="'.fuel_url($module).'/inline_edit/" data-rel="'.$id.'" title="'.$label.'" data-module="'.$module.'"';

			if (isset($xoffset) OR isset($yoffset))
			{
				$output .= ' style="';
				if (isset($xoffset)) $output .= 'left:'.$xoffset.'px;';
				if (isset($yoffset)) $output .= 'top:'.$yoffset.'px;';
				$output .= '"';
			}
			$output .= "></span>";
			return $output;
		}
		return '';
	}
	
	function render_all_markers($output)
	{
		// get the marker regex
		$marker_reg_ex = $this->get_marker_regex();
		if (stripos($output, '<html') !== FALSE AND stripos($output, '</html>') !== FALSE)
		{	

			// move all edit markers in attributes to before the node
			$callback = create_function(
			            // single quotes are essential here,
			            // or alternative escape all $ as \$
			            '$matches',
			            '
						$CI =& get_instance();
						$marker_reg_ex = $CI->fuel_page->get_marker_regex();
						$output = $matches[0];
						preg_match_all("#".$marker_reg_ex."#", $matches[0], $tagmatches);
						if (!empty($tagmatches[0]))
						{
							// clean out the tag and append them before the node
							$output = $CI->fuel_page->remove_markers($matches[0]);
							$output = implode($tagmatches[0], " ").$output;
						}
						return $output;'
			        );
			$output = preg_replace_callback('#<[^>]+=["\'][^<]*'.$marker_reg_ex.'.*(?<!--)>#Ums', $callback, $output);
			//$output = preg_replace('#(=["\'][^<]*)('.$marker_reg_ex.')#Ums', '${2}${1}', $output);  // doesn't work with fuel_var in multiple tag attributes

			// extract everything above the body
			preg_match_all('/(.*)<body/Umis', $output, $head);

			// get all the markers in the head and move them to within the body
			if (!empty($head[1][0]))
			{
				// match all markers in head
				preg_match_all('/('.$marker_reg_ex.')/Umis', $head[1][0], $matches);
				
				// append them to the body
				if (!empty($matches[1]))
				{
					$head_markers = implode("\n", array_unique($matches[1]));
					$output = preg_replace('/(<body[^>]*>)/e', '"\\1\n".\$head_markers', $output);
				}
				
				// remove the markers from the head now that we've captured them'
				$cleaned_head = preg_replace('/('.$marker_reg_ex.')/', '', $head[1][0]);
				
				// replace the cleaned head back on.. removed 's' modifier because it was causing memory issues
				//$output = preg_replace('/(.*)(<body.+)/Umis', "$cleaned_head\\2", $output);
				preg_match('/(<body.+){1}/mis', $output, $matches);
				$body = $matches[0];
				$output = $cleaned_head.$body;
			}
		}
		else
		{
			// if not html, then we remove the marker and inline editing is not available
			$output = $this->remove_markers($output);
		}

		$output = preg_replace_callback('#'.$marker_reg_ex.'#U', array($this, '_render_markers_callback'), $output);
		return $output;
	}
	
	private function _render_markers_callback($matches)
	{
		return $this->render_marker($matches[1]);
	}
	
	function remove_markers($output)
	{
		// if no markers, then we simply return the output to speed up processing
//		if (empty($this->markers)) return $output; // needs to run all the time in case it's cached'
		
		// get the marker regex and replace it with nothing
		$marker_reg_ex = $this->get_marker_regex();
		$output = preg_replace('#'.$marker_reg_ex.'#', '', $output);
		return $output;
	}
	
	function properties($prop = NULL)
	{
		if (is_string($prop) AND isset($this->_page_data[$prop])) return $this->_page_data[$prop];
		return $this->_page_data;
	}
	
	function variables($key = NULL)
	{
		if (is_string($key) AND isset($this->_variables[$key])) return $this->_variables[$key];
		return $this->_variables;
	}
	
	function add_variables($vars = array())
	{
		$vars = (array) $vars;
		$this->_variables = array_merge($this->_variables, $vars);
	}
	
	function segment($n)
	{
		if (is_int($n) AND isset($this->_segments[$n])) return $this->_segments[$n];
		return $this->_segments;
	}
	
	function has_cms_data()
	{
		return !empty($this->_page_data['location']);
	}
	
	function find_view_file($view)
	{
		if (!$this->_CI->config->item('auto_search_views', 'fuel')) return NULL;
		static $cnt;
		if (is_null($cnt)) $cnt = 0;
		$cnt++;
		$view_parts = explode('/', $view);
		array_pop($view_parts);
		$view = implode('/', $view_parts);
		if (!file_exists($this->views_path.$view.EXT) AND count($view_parts) > 1 AND $cnt < 6) // hard coded 6 levels
		{
			$view = $this->find_view_file($view);
		}
		return $view;
	}
}
/* End of file Fuel_page.php */
/* Location: ./modules/fuel/libraries/fuel/Fuel_page.php */