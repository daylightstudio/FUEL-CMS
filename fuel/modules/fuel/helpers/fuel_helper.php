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
 * FUEL Helper
 * 
 * Contains FUEL specific functions. This helper is automatically loaded with the autoload config.
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/helpers/fuel_helper
 */

// --------------------------------------------------------------------

/**
 * Returns the instance of the FUEL object
 *
 * @access	public
 * @param	mixed
 * @return	string
 */
function &fuel_instance()
{
	return Fuel::get_instance();
}

/**
 * Returns the instance of the FUEL object just like fuel_instance but with a similar syntax to the CI() function which returns the CI object.
 * 
 * @access	public
 * @param	mixed
 * @return	string
 */
function &FUEL()
{
	$f = & fuel_instance();
	return $f;
}

// --------------------------------------------------------------------

/**
 * Allows you to load a view and pass data to it.
 *
 * The <dfn>params</dfn> parameter can either be string value (in which case it will assume it is the name of the block view file) or an associative array that can have the following values:

<ul>
	<li><strong>view</strong> - the name of the view block file. Also can be the first parameter of the method</li>
	<li><strong>vars</strong>: an array of variables to pass to the block. Also can be the second parameter of the method.</li>
	<li><strong>view_string</strong> - a string variable that represents the block</li>
	<li><strong>model</strong> - the name of a model to automatically load for the block</li>
	<li><strong>find</strong> - the name of the find method to run on the loaded model</li>
	<li><strong>select</strong> - select parameters to run for the find method</li>
	<li><strong>where</strong> - where parameters to run for the find method</li>
	<li><strong>order</strong> - the order the find method should return</li>
	<li><strong>limit</strong> - the limit number of results to be returned by the find method</li>
	<li><strong>offset</strong> - the find results returned offset value</li>
	<li><strong>return_method</strong>: the return method the find query should use</li>
	<li><strong>assoc_key</strong>: the column name to be used as the associative key in the find method</li>
	<li><strong>data</strong>: the data values to be passed to the block. This variable get's automatically set if you specify the model and find method</li>
	<li><strong>editable</strong>: css class styles to apply to menu items... can be a nested array</li>
	<li><strong>parse</strong>: determines whether to parse the contents of the block. The default is set to 'auto'</li>
	<li><strong>cache</strong>: determines whether to cache the block. Default is false</li>
	<li><strong>mode</strong>: explicitly will look in either the CMS or the views/_blocks folder</li>
	<li><strong>module</strong>: the name of the module to look in for the block</li>
	<li><strong>language</strong>: the language version to use for the block. Must be a value specified found in the 'languages' options in the FUEL configuration</li>
	<li><strong>use_default</strong>: determines whether to find a non-language specified version of a block with the same name if the specified language version is not available in the CMS</li>
</ul>
 *
 * @access	public
 * @param	mixed
 * @return	string
 */
function fuel_block($params, $vars = array(), $check_db = TRUE, $scope = NULL)
{
	$CI =& get_instance();
	return $CI->fuel->blocks->render($params, $vars, $check_db, $scope);
}

// --------------------------------------------------------------------

/**
 * Creates a menu structure
 * 
 * The <dfn>params</dfn> parameter is an array of options to be used with the <a href="[user_guide_url]libraries/menu">Menu class</a>.
 * If FUEL's configuration mode is set to either <dfn>auto</dfn> or <dfn>cms</dfn>, then it will first look for data from the FUEL navigation module. 
 * Otherwise it will by default look for the file <dfn>views/_variables/nav.php</dfn> (you can change the name of the file it looks for in the <dfn>file</dfn> parameter passed). That file should contain an array of menu information (see <a href="<?=user_guide_url('libraries/menu')?>">Menu class</a> for more information on the required data structure). 
 * The parameter values are very similar to the <a href="[user_guide_url]libraries/menu">Menu class</a>, with a few additions shown below:

<ul>
		<li><strong>items</strong> - the navigation items to use. By default, this is empty and will look for the nav.php file or the records in the Navigation module</li>
		<li><strong>file</strong> - the name of the file containing the navigation information</li>
		<li><strong>var</strong> - the variable name in the file to use</li>
		<li><strong>parent</strong> - the parent id you would like to start rendering from. This is either the database ID or the nav array key of the menu item</li>
		<li><strong>root</strong> - the equivalent to the root_value attribute in the Menu class. It states what the root value of the menu structure should be. Normally you don't need to worry about this</li>
		<li><strong>group_id</strong> - the group ID in the database to use. The default is <dfn>1</dfn>. Only applies to navigation items saved in the admin</li>
		<li><strong>exclude</strong> - nav items to exclude from the menu. Can be an array or a regular expression string</li>
		<li><strong>return_normalized</strong> - returns the raw normalized array that gets used to generate the menu</li>
		<li><strong>render_type</strong>: options are basic, breadcrumb, page_title, collapsible, delimited, array. Default is 'basic'</li>
		<li><strong>active_class</strong>: the active css class. Default is 'active'</li>
		<li><strong>active</strong>: the active menu item</li>
		<li><strong>styles</strong>: css class styles to apply to menu items... can be a nested array</li>
		<li><strong>first_class</strong>: the css class for the first menu item. Default is first</li>
		<li><strong>last_class</strong>: the css class for the last menu item. Default is last</li>
		<li><strong>depth</strong>: the depth of the menu to render at</li>
		<li><strong>use_titles</strong>: use the title attribute in the links. Default is FALSE</li>
		<li><strong>container_tag</strong>: the html tag for the container of a set of menu items. Default is ul</li>
		<li><strong>container_tag_attrs</strong>: html attributes for the container tag</li>
		<li><strong>container_tag_id</strong>: html container id</li>
		<li><strong>container_tag_class</strong>: html container class</li>
		<li><strong>cascade_selected</strong>: cascade the selected items. Default is TRUE</li>
		<li><strong>include_hidden</strong>: include menu items with the hidden attribute. Default is FALSE</li>
		<li><strong>item_tag</strong>: the html list item element. Default is 'li'</li>
		<li><strong>item_id_prefix</strong>: the prefix to the item id</li>
		<li><strong>item_id_key</strong>: either id or location. Default is 'id'</li>
		<li><strong>use_nav_key</strong>: determines whether to use the nav_key or the location for the active state. Default is "AUTO"</li>
		<li><strong>pre_render_func</strong>: function to apply to menu labels before rendering</li>
		<li><strong>delimiter</strong>: the html element between the links </li>
		<li><strong>arrow_class</strong>: the class for the arrows used in breadcrumb type menus</li>
		<li><strong>display_current</strong>: determines whether to display the current active breadcrumb item</li>
		<li><strong>home_link</strong>: the root home link</li>
		<li><strong>append</strong>: appends additional menu items to those items already set (e.g. from the $nav array or from the navigation module). Good to use on dynamic pages where you need to dynamically set a navigation item for a page</li>
		<li><strong>order</strong>: the order to display... for page_title ONLY</li>
		<li><strong>language</strong>: select the appropriate language</li>
		<li><strong>include_default_language</strong>: will merge in the default language with the results. Default is FALSE</li>
		<li><strong>language_default_group</strong>: the default group to be used when including a default language. Default is FALSE</li>
	</ul>

<p class="important">For more information see the <a href="<?=user_guide_url('libraries/menu')?>">Menu class</a>.</p>
 *
 * @access	public
 * @param	mixed
 * @return	string
 */
function fuel_nav($params = array())
{
	$CI =& get_instance();
	return $CI->fuel->navigation->render($params);
}

// --------------------------------------------------------------------

/**
 * Generates a page using the Fuel_page class
 *
 * @access	public
 * @param	array
 * @param	array
 * @return	string
 */
function fuel_page($location, $vars = array(), $params = array())
{
	$CI =& get_instance();
	return $CI->fuel->pages->render($location, $vars, $params, TRUE);
}

// --------------------------------------------------------------------

/**
 * Creates a form using form builder
 *
 * @access	public
 * @param	mixed
 * @param	array
 * @param	array
 * @return	string
 */
function fuel_form($fields, $values = array(), $params = array())
{
	$CI =& get_instance();
	
	// if a string is provided instead of array, we will assume it is a model
	if (is_string($fields))
	{
		$model = $fields;
		if (substr($model, strlen($model) - 6) != '_model')
		{
			$model = $model.'_model';
		}
		$CI->load->model($model);
		
		// check if the model has a form_fields method on it first
		if (!method_exists('form_fields', $CI->$model))
		{
			return '';
		}
		$fields = $CI->$model->form_fields();
	}
	$CI->load->library('form_builder', $params);
	$CI->form_builder->set_fields($fields);
	$CI->form_builder->set_field_values($values);
	return $CI->form_builder->render();
}

// --------------------------------------------------------------------

/**
 * Loads a module model and creates a variable in the view that you can use to merge data. 
 *
 * The <dfn>params</dfn> parameter is an associative array that can have the following values:
 *
<ul>
	<li><strong>find</strong> - the find method to use on the module model. Options are "one", "key", "all" or any method name on the model that begins with "find_" (excluding "find_" from the value)</li>
	<li><strong>select</strong> - the select condition to filter the results of the find query</li>
	<li><strong>where</strong> - the where condition to be used in the find query</li>
	<li><strong>order</strong> - order the data results and sort them </li>
	<li><strong>limit</strong> - limit the number of data results returned</li>
	<li><strong>offset</strong> - offset the data results</li>
	<li><strong>return_method</strong> - the return method to use which can be an object or an array</li>
	<li><strong>assoc_key</strong> - the field to be used as an associative key for the data results</li>
	<li><strong>var</strong> - the variable name to assign the data returned from the module model query</li>
	<li><strong>module</strong> - specifies the module folder name to find the model</li>
</ul>
 * @access	public
 * @param	string
 * @param	mixed
 * @param	mixed
 * @return	string
 */
function fuel_model($module, $params = array(), $where = array())
{
	$CI =& get_instance();
	$module = $CI->fuel->modules->get($module, FALSE);
	if ($module)
	{
		return $module->find($params, $where);
	}
	return FALSE;
}

// --------------------------------------------------------------------

/**
 * Sets a variable for all views to use (including layouts) no matter what view it is declared in. 
 * 
 * Using fuel_set_var in a layout field in the admin, will have no affect (e.g. {fuel_set_var('layout', 'my_layout')}).
 *
 * @access	public
 * @param	string
 * @param	array
 * @return	string
 */
function fuel_set_var($key, $val = NULL)
{
	$CI =& get_instance();
	if (strtoupper($CI->fuel->config('double_parse')) == 'AUTO')
	{
		$CI->fuel->set_config('double_parse', TRUE);
	}

	if (is_array($key))
	{
		$CI->load->vars($key);
	}
	else if (is_string($key))
	{
		$vars[$key] = $val;
		$CI->load->vars($vars);
	}
}

// --------------------------------------------------------------------

/**
 * Appends a value to an array variable for all views to use no matter what view it is declared in.
 *
<code>
	// EXAMPLE HEADER FILE
	...
	&lt;?php echo css(&#x27;main&#x27;); ?&gt;
	&lt;?php echo css($css); ?&gt;

	&lt;?php echo js(&#x27;jquery, main&#x27;); ?&gt;
	&lt;?php echo js($js); ?&gt;
	...

	// Then in your view file
	...
	&lt;php
	fuel_var_append('css', 'my_css_file.css');
	fuel_var_append('js', 'my_js_file.js');
	?&gt;
	<h1>About our company</h1>
	...
 </code>
 * @access	public
 * @param	string
 * @param	mixed
 * @return	void
 */
function fuel_var_append($key, $value)
{
	$CI =& get_instance();
	$vars = $CI->load->get_vars();
	if (isset($vars[$key]) AND is_array($vars[$key]))
	{
		if (is_array($value))
		{
			$vars[$key] = array_merge($vars[$key], $value);
		}
		else
		{
			array_push($vars[$key], $value);
		}
		fuel_set_var($key, $vars[$key]);
	}
}

// --------------------------------------------------------------------

/**
 * Returns a variable and allows for a default value.
 * Also creates inline editing marker.
 * The <dfn>default</dfn> parameter will be used if the variable does not exist.
 * The <dfn>edit_module</dfn> parameter specifies the module to include for inline editing.
 * The <dfn>evaluate</dfn> parameter specifies whether to evaluate any php in the variables.
 *
<p class="important">You should not use this function inside of another function because you may get unexepected results. This is
because it returns inline editing markers that later get parsed out by FUEL. For example:</p>

<code>
// NO
&lt;a href="&lt;?=site_url(fuel_var('my_url'))?&gt;"&gt;my link&lt;/a&gt;

// YES
&lt;?=fuel_edit('my_url', 'Edit Link')?&gt; &lt;a href="&lt;?=site_url($my_url)?&gt;"&gt;my link&lt;/a&gt;
</code>

 * @access	public
 * @param	string
 * @param	string
 * @param	boolean
 * @return	string
 */
function fuel_var($key, $default = '', $edit_module = 'pagevariables', $evaluate = FALSE)
{
	$CI =& get_instance();
	$CI->load->helper('inflector');

	$key_arr = explode('|', $key);
	$key = $key_arr[0];

	if (isset($GLOBALS[$key]))
	{
		$val = $GLOBALS[$key];
	}
	else if ($CI->load->get_var($key))
	{
		$val = $CI->load->get_var($key);
	}
	else
	{
		$val = $default;
	}
	
	if (is_string($val) AND $evaluate)
	{
		$val = eval_string($val);
	}
	else if (is_array($val) AND $evaluate)
	{
		if (isset($key_arr[1]))
		{
			if (isset($val[$key_arr[1]]))
			{
				$val = $val[$key_arr[1]];
			}
			else
			{
				$val = $default;
			}
		}
		else
		{
			foreach($val as $k => $v)
			{
				$val[$k] = eval_string($v);
			}
		}
	}
	
	if ($edit_module === TRUE) $edit_module = 'pagevariables';
	if (!empty($edit_module) AND $CI->fuel->pages->mode() != 'views' AND !defined('FUELIFY') OR (defined('FUELIFY') AND FUELIFY))
	{
		$marker = fuel_edit($key, humanize($key), $edit_module);
	}
	else
	{
		$marker = '';
	}
	
	if (is_string($val))
	{
		return $marker.$val;
	}
	else
	{
		if (!empty($marker))
		{
			// used to help with javascript positioning
			$marker = '<span>'.$marker.'</span>';
		}
		return $marker;
	}
}

// --------------------------------------------------------------------

/**
 * Sets a variable marker (pencil icon) in a page which can be used for inline editing.
 * 
 * The <dfn>id</dfn> parameter is the unique id that will be used to query the module. You can also pass an id value
 * and a field like so <dfn>id|field</dfn>. This will display only a certain field instead of the entire module form.
 * Alternatively, you can now also just pass the entire object and it will generate the id, label, module and published values automatically.
 * The <dfn>is_published</dfn> parameter specifies whether to indicate with the pencil icon that the item is active/published
 * The <dfn>label</dfn> parameter specifies the label to display next to the pencil icon.
 * The <dfn>xOffset</dfn> and <dfn>yOffset</dfn> are pixel values to offset the pencil icon.
 *
 * @access	public
 * @param	mixed
 * @param	string
 * @param	string
 * @param	boolean
 * @param	int
 * @param	int
 * @return	string
 */
function fuel_edit($id, $label = NULL, $module = 'pagevariables', $is_published = TRUE, $xoffset = NULL, $yoffset = NULL)
{
	$CI =& get_instance();
	$page = $CI->fuel->pages->active();
	if (empty($page))
	{
		$page = $CI->fuel->pages->create();
	}
	if (!empty($id) AND (!defined('FUELIFY') OR defined('FUELIFY') AND FUELIFY !== FALSE))
	{
		
		if (is_object($id) AND is_a($id, 'Data_record') AND isset($id->id))
		{
			$ref_id = $id->id;
			
			if (empty($module) OR $module == 'pagevariables')
			{
				$module = $id->parent_model()->table_name();
				$tables = array_flip($id->parent_model()->tables());
				if (isset($tables[$module]))
				{
					$module = $tables[$module];
				}
				unset($tables);
				$mod = $CI->fuel->modules->get($module, FALSE);
				if (!empty($mod))
				{
					$module = $mod->info('module_uri');
				}
			}
			
			if (empty($label))
			{
				$label = lang('action_edit').': ';

				if (isset($id->title))
				{
					$label .= $id->title;
				}
				else if ($id->name)
				{
					$label .= $id->name;
				}
			}
			
			if (isset($id->published))
			{
				$is_published = is_true_val($id->published);
			}
			else if (isset($id->active))
			{
				$is_published = is_true_val($id->active);
			}
		}
		else
		{
			$ref_id = $id;
		}
		
		$marker['id'] = $ref_id;
		$marker['label'] = $label;
		$marker['module'] = $module;
		$marker['published'] = $is_published;
		$marker['xoffset'] = $xoffset;
		$marker['yoffset'] = $yoffset;

		$key = $page->add_marker($marker);

		return '<!--'.$key.'-->';
	}
	return '';
}

// --------------------------------------------------------------------

/**
 * 	Creates the cache ID for the fuel page based on the URI. 
 * 
 * If no <dfn>location</dfn> value is passed, it will default to the current <a href="<?=user_guide_url('my_url_helper')?>">uri_path</a>.
 *
 * @access	public
 * @param	string
 * @return	string
 */
function fuel_cache_id($location = NULL)
{
	$CI =& get_instance();
	return $CI->fuel->cache->create_id($location);
}

// --------------------------------------------------------------------

/**
 * Creates the admin URL for FUEL (e.g. http://localhost/MY_PROJECT/fuel/admin)
 *
 * @access	public
 * @param	string
 * @param	boolean
 * @return	string
 */
function fuel_url($uri = '', $query_string = FALSE)
{
	$CI =& get_instance();
	$uri = fuel_uri($uri, $query_string);
	return site_url($uri);
}

// --------------------------------------------------------------------

/**
 * Returns the FUEL admin URI path
 *
 * @access	public
 * @param	string
 * @return	string
 */
function fuel_uri($uri = '', $query_string = FALSE)
{
	$CI =& get_instance();
	if (is_bool($query_string) AND $query_string !== FALSE)
	{
		$query_string = $_GET;
	}
	$q = '';
	if (!empty($query_string))
	{
		if (is_array($query_string))
		{
			$q_str = http_build_query($query_string);
			if (!empty($q_str))
			{
				$q = '?'.$q_str;
			}
		}
		else if (is_string($query_string))
		{
			$q = (strncmp($query_string, '?', 1) !== 0) ? '?'.$query_string : $query_string;
		}
	}
	return $CI->fuel->config('fuel_path').$uri.$q;
}

// --------------------------------------------------------------------

/**
 * Returns the uri segment based on the FUEL admin path
 *
 * @access	public
 * @param	int
 * @param	boolean
 * @return	string
 */
function fuel_uri_segment($seg_index = 0, $rerouted = FALSE)
{
	$CI =& get_instance();
	if ($rerouted)
	{
		return $CI->uri->rsegment(fuel_uri_index($seg_index));
	}
	else
	{
		return $CI->uri->segment(fuel_uri_index($seg_index));
	}
}

// --------------------------------------------------------------------

/**
 * Returns the uri index number based on the FUEL admin path
 *
 * @access	public
 * @param	int
 * @return	int
 */
function fuel_uri_index($seg_index = 0)
{
	$CI =& get_instance();
	$fuel_path = $CI->fuel->config('fuel_path');
	$start_index = count(explode('/', $fuel_path)) - 1;
	return $start_index + $seg_index;
}

// --------------------------------------------------------------------

/**
 * Returns the uri string based on the FUEL admin path
 *
 * @access	public
 * @param	int
 * @param	int
 * @param	boolean
 * @return	string
 */
function fuel_uri_string($from = 0, $to = NULL, $rerouted = FALSE)
{
	$CI =& get_instance();
	$fuel_index = fuel_uri_index($from);
	
	if ($rerouted)
	{
		$segs = $CI->uri->rsegment_array($fuel_index);
	}
	else
	{
		$segs = $CI->uri->segment_array($fuel_index);
	}
	$from = fuel_uri_index($from);
	if (isset($to))
	{
		$to = fuel_uri_index($to);
		if ($from < $to)
		{
			$to = $from;
		}
	}
	else
	{
		$to = sizeof($segs);
	}
	$segs = array_slice($segs, $from, $to);
	$uri = implode('/', $segs);
	return $uri;
}

// --------------------------------------------------------------------

/**
 * Check to see if you are logged in and can use inline editing
 *
 * @access	public
 * @return	boolean
 */
function is_fuelified()
{
	$CI =& get_instance();
	return $CI->fuel->auth->is_fuelified();
}

// --------------------------------------------------------------------

/**
 * Check to see if you are in the FUEL admin
 *
 * @access	public
 * @return	boolean
 */
function in_fuel_admin()
{
	return (defined('FUEL_ADMIN') AND FUEL_ADMIN === TRUE);
}

// --------------------------------------------------------------------

/**
 * Returns the user language of the person logged in... used for inline editing
 *
 * @access	public
 * @return	boolean
 */
function fuel_user_lang()
{
	$CI =& get_instance();
	return $CI->fuel->auth->user_lang();
}

// --------------------------------------------------------------------

/**
 * Returns the setting(s) for a particular module
 *
 * @access	public
 * @param	string	Module name
 * @param	string	Settings key (optional)
 * @return	mixed
 */
function fuel_settings($module, $key = '')
{
	$CI =& get_instance();
	return $CI->fuel->settings->get($module, $key);
}

/* End of file fuel_helper.php */
/* Location: ./modules/fuel/helpers/fuel_helper.php */
