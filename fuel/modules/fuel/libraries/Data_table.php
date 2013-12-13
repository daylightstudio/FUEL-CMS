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
 */

// ------------------------------------------------------------------------

/**
 * A data table builder
 *
 * This class allows you to easily create tables of data by passing in a 
 * multi-dimensional array of data. This class provides methods to set the
 * sorting, headers and data of the table
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/libraries/data_table
 */
// ------------------------------------------------------------------------

class Data_table {
	
	public $id = 'data_table'; // id to be used for the form
	public $css_class = 'data'; // css class to be used with the form
	public $headers = array(); // the table headers
	public $table_attrs = array('cellpadding' => '0',  'cellspacing' => '0'); // the table attributes
	public $header_on_class = 'on'; // table header on class
	public $header_asc_class = 'asc'; // table header asc class
	public $header_desc_class = 'desc'; // table header desc class
	public $row_alt_class = 'alt'; // row alternating class
	public $sort_js_func = "alert('you must assign a javascript function to the \$sort_js_func attribute of the Data_table instance'); return false;"; // javascript sorting function
	public $body_attrs = array(); // the tbody attributes
	public $action_delimiter = '|'; // the text delimiter between each action
	public $actions_field = 'first'; // which column the actions are in, first or last
	public $auto_sort = TRUE; // should sorting be done automatically?
	public $only_data_fields = array(); // data columns that won't be displayed'
	public $default_field_action = NULL; // the default column action
	public $row_id_key = 'id'; // the <tr> id prefix
	public $row_action = FALSE; // does the row have actions?
	public $data = array(); // the data applied to the table
	public $rows = array(); // an array of table rows
	public $inner_td_class = ''; // the css class to be used for the span inside the td
	public $no_data_str = 'No data to display.'; //The default string to display when no data exists
	public $lang_prefix = 'table_header_'; // the language prefix to associate with table headers
	public $field_styles = array(); // styles to apply to the data columns. Index is the column and the value is the style

	protected $_ordering = TRUE; // sorting order
	protected $_field = NULL; // sorted column
	protected $_field_formatters = array(); // an array of function to format columns
	protected $_actions = array(); // table row actions
	protected $_render_row_index = 0; // internal pointer to the row index
	
	
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
		$this->initialize($params);
    }

	// --------------------------------------------------------------------

	/**
	 * Initialize preferences
	 *
	 * @access	public
	 * @param	array
	 * @return	void
	 */
	public function initialize($params = array())
	{
		$this->reset();
		
		// load localization helper if not already
		if (!function_exists('lang'))
		{
			$this->_CI->load->helper('language');
		}
		
		$this->set_params($params);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Set object parameters
	 *
	 * @access	public
	 * @param	array
	 * @return	void
	 */
	public function set_params($params)
	{
		if (is_array($params) AND count($params) > 0)
		{
			foreach ($params as $key => $val)
			{
				if (isset($this->$key))
				{
					$this->$key = $val;
				}
			}		
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Assign data to the table to be rendered
	 * 
	 * If headers is empty, the table will automatically create them from the data array.
	<code>
	$data[] = array(
		'name' => 'Darth Vader',
		'weapon' => 'light saber',
		'darkside' => TRUE,
		'active' => 'yes'
	);
	$data[] = array(
		'name' => 'Luke Skywalker',
		'weapon' => 'light saber',
		'darkside' => FALSE,
		'active' => 'yes'
	);

	$data[] = array(
		'name' => 'Han Solo',
		'weapon' => 'blaster',
		'darkside' => FALSE,
		'active' => 'yes',
		'__field__' => array('name' => array('class' => 'highlight'))
	);

	$this->data_table->assign_data($data);
	</code>
	
	<p class="important">Note the <dfn>'__field__'</dfn> field in the example below (that's 2 underscores on each side). 
	This is a special field that allows you to add attributes to the parent &lt;td&gt; tag. In the above example, the CSS class of "highlight" 
	is applied to the Han Solo named column.</p>
	 *
	 * @access	public
	 * @param	array table data
	 * @param	array header key/names
	 * @param	mixed row attributes
	 * @return	void
	 */
	public function assign_data($data, $headers = array(), $attrs = array())
	{
		
		// manually add the special __field__ column
		$this->only_data_fields[] = '__field__';
		
		$this->data = (array)$data;
		
		// to reset index values just in case
		$this->data = array_values($this->data);
		
		if (empty($headers))
		{
			if (current($data))
			{
				$headers = array();
				foreach(current($data) as $key => $val)
				{
					$headers[] = $key;
					$sorting_params[] = ($this->auto_sort) ? $key : NULL;
				}

				$this->add_headers($headers, $sorting_params);
				$this->add_rows($data, $attrs, $this->default_field_action);
			}
		}
		else
		{
			// if key is number then convert it to the proper header
			$new_headers = array();
			$i = 0;
			foreach($headers as $key => $val)
			{
				if (is_object($val)) $val = get_object_vars($val);
				if ($this->auto_sort)
				{
					$sorting_params[$i] = (is_int($key)) ? $val : $key;
				}
				else
				{
					$sorting_params[$i] = NULL;
				}
				
				$i++;
			}

			$this->add_headers($headers, $sorting_params);
			$rows = array();
			
			// now filter rows to just the columns we want based on the headers passed
			foreach($data as $key => $val)
			{
				foreach($this->headers as $val2)
				{
					 $rows[$key][$val2->col_key] = $data[$key][$val2->col_key];
				}
				
				// add back in the special __field__ column
				if (isset($data[$key]['__field__']))
				{
					$rows[$key]['__field__'] = $data[$key]['__field__'];
				}
			}
			$this->add_rows($rows, $attrs, $this->default_field_action);
		}
	}
	
	
	// --------------------------------------------------------------------

	/**
	 * Render the entire table
	 * 
	 * @access	public
	 * @param	boolean echo the output?
	 * @return	string
	 */
	public function render($echo = FALSE)
	{
		$str = '';
		if (count($this->rows) > 0)
		{
			if (isset($this->table_attrs['id'])) unset($this->table_attrs['id']);
			if (isset($this->table_attrs['class'])) unset($this->table_attrs['class']);
			$str .= '<table';
			$str .= $this->_render_attrs($this->table_attrs);
			if (!empty($this->id)) $str .= ' id="'.$this->id.'"';
			if (!empty($this->css_class)) $str .= ' class="'.$this->css_class.'"';
			$str .= ">\n";

			$str .= $this->render_headers($this->headers);

			// set body
			$str .= '<tbody';
			$str .= $this->_render_attrs($this->body_attrs);
			$str .= ">\n";
			$str .= $this->render_rows($this->rows);
			$str .= "</tbody>\n";
			$str .= "</table>\n";
		}
		else
		{
			$str .= "<div class=\"nodata\">".$this->no_data_str."</div>\n";
		}
		if (!empty($echo)) echo $str;
		return $str;
	}
	// --------------------------------------------------------------------

	/**
	 * Render the headers of the table
	 * 
	 * @access	public
	 * @param	array of Data_table_header(s)
	 * @return	string
	 */
	public function render_headers($headers)
	{
		$str = '';
		if (!empty($headers))
		{
			$str .= "<thead>\n";
			$str .= "<tr>\n";
			$i = 0;
			
			foreach($headers as $th)
			{
				if (in_array($th->col_key, $this->only_data_fields))
				{
					continue;
				}
				
				$str .= "\t<th";
				if (!empty($th->attrs['class']))
				{
					$th->attrs['class'] .= ' col'.($i + 1);
				}
				else
				{
					$th->attrs['class'] = 'col'.($i + 1);
				}
				
				if (!empty($th->sorting_param) AND $th->sorting_param == $this->_field)
				{
					$sort_class = ($this->_ordering == 'asc') ? $this->header_asc_class : $this->header_desc_class;
					if (!strstr($th->attrs['class'], $sort_class))
					{
						$nosort = empty($th->sorting_param) ? ' nosort' : '';
						$th->attrs['class'] = $th->attrs['class'].' '.$sort_class.' '.$this->header_on_class.$nosort;
					}
				}
				$str .= $this->_render_attrs($th->attrs);
				$str .= ">\n";
				if (!empty($th->sorting_param) AND !empty($this->sort_js_func))
				{
					$str .= '<a href="javascript:;" onclick="'.$this->sort_js_func.'(\''.$th->sorting_param.'\', this);return false;">'.$th->name.'</a>';
				}
				else
				{
					$str .= '<span>'.$th->name.'</span>';
				}
				$str .= "</th>\n";
				$i++;
			}
			$str .= "</tr>\n";
			$str .= "</thead>\n";
		}
		return $str;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Render the rows of the table
	 * 
	 * @access	public
	 * @param	array of Data_table_row(s)
	 * @return	string
	 */
	public function render_rows($rows)
	{
		$str = '';
		$this->_render_row_index = 0;
		if (is_array($rows))
		{
			$i = 0;
			foreach($rows as $row)
			{
				$str .= '<tr';
				if (!empty($row->attrs))
				{
					$str .= $this->_render_attrs($row->attrs);
				}
				if (!array_key_exists('class', $row->attrs))
				{
					$str .= ' class="';
					if (!empty($this->row_alt_class) AND $i % 2) $str .= $this->row_alt_class;
					if ($this->row_action) $str .= ' rowaction';
					$str .= '"';
				}
				$str .= ">\n";
				if (!empty($row)) $str .= $this->render_fields($row->fields);
				$str .= "</tr>\n";
				$this->_render_row_index++;
				$i++;
			}
			return $str;
		}
		else
		{
			return $rows;
		}
	}
	
	// --------------------------------------------------------------------

	/**
	 * Render the columns of the table
	 * 
	 * @access	public
	 * @param	array of Data_table_col(s)
	 * @return	string
	 */
	public function render_fields($fields)
	{
		$str = '';
		if (!empty($fields))
		{
			$i = 0;
			$render_cols = array();
			foreach($fields as $field)
			{
				if (!in_array($field->heading, $this->only_data_fields))
				{
					$render_fields[] = $field;
				}
			}
			foreach($render_fields as $col)
			{
				
				$str .= "\t<td";
				if (!empty($col->action)) $str .= ' '.$col->action;
				if (!empty($col->attrs['class']))
				{
					$col->attrs['class'] = 'col'.($i + 1).' '.$col->attrs['class'];
				}
				else
				{
					
					if ($i == 0)
					{
						$col->attrs['class'] = 'col'.($i + 1).' first';
					}
					else if ($i == (count($render_cols) - 1))
					{
						$col->attrs['class'] = 'col'.($i + 1).' last';
					}
					else if ($i == (count($render_cols) - 2))
					{
						$col->attrs['class'] = 'col'.($i + 1).' next_last';
					}
					else
					{
						$col->attrs['class'] = 'col'.($i + 1);
					}
					
				}
				
				// now set styles
				if (!empty($this->field_styles))
				{
					if (isset($this->field_styles[$i]))
					{
						$col->attrs['class'] .= ' '.$this->field_styles[$i];
					}
					else if (isset($this->field_styles[$col->heading]))
					{
						$col->attrs['class'] .= ' '.$this->field_styles[$col->heading];
					}
				}
				
				$str .= $this->_render_attrs($col->attrs);
				$str .= ">";
				if (!empty($this->inner_td_class))
				{
					if (is_array($this->inner_td_class) AND !empty($this->inner_td_class[$i]))
					{
						$str .= "<span class=\"".$this->inner_td_class[$i]."\">";
					}
					else if (is_string($this->inner_td_class))
					{
						$str .= "<span class=\"".$this->inner_td_class."\">";
					}
				}
				if (isset($col->value)) 
				{
				
					if (!empty($this->_field_formatters[$col->heading]) AND isset($this->data[$this->_render_row_index]))
					{
						$func = $this->_field_formatters[$col->heading];
						$str .= call_user_func($func, $this->data[$this->_render_row_index], $col->heading);
					}
					else
					{
						$str .= stripslashes($col->value);
					}
				}
				else
				{
					$str .= '&nbsp;';
				}
				if (!empty($this->inner_td_class)) $str .= "</span>";
				$str .= "</td>\n";
				$i++;
			}
		}
		return $str;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Same as reset
	 * 
	 * @access	public
	 * @return	void
	 */
	public function clear()
	{
		$this->reset();
	}
	
	// --------------------------------------------------------------------

	/**
	 * Reset the header and rows data
	 * 
	 * @access	public
	 * @return	void
	 */
	public function reset()
	{
		$this->headers = array();
		$this->rows = array();
	}

	// --------------------------------------------------------------------

	/**
	 * Add actions to each row
	 * 
	The <dfn>type</dfn> attribute can be either <dfn>url</dfn> or <dfn>func</dfn>.
	Type <dfn>url</dfn> is a string value that can have placeholder with <dfn>{}</dfn> surrounding column values that need to be substituted in.
	Type <dfn>func</dfn> is a string value of a function that will have the rows field values passed to it as the only parameter. 

	The default is <dfn>url</dfn>.

	<code>
	// with just a url
	$action = array('EDIT' => 'example/edit/{id}');
	$this->data_table->add_action($field, $action);
	</pre>

	<pre class="brush: php">
	// with a function
	$delete_func = '
	$CI =& get_instance();
	$link = "";
	if ($CI->auth->has_permission("delete"))
	{
		$url = site_url("example/delete/".$cols["id"]);
		$link = "<a href=\"".$url."\">DELETE</a>";
	}
	return $link;';

	$delete_func = create_function('$cols', $delete_func);
	$this->data_table->add_action($field, $action);
	</code>
	 * @access	public
	 * @param	string text label to display for the action
	 * @param	string a link path or a function
	 * @param	string the type (either url or func)
	 * @param	mixed the anchor attributes (e.g. target="_blank")
	 * @return	void
	 */
	public function add_action($label, $action, $type = 'url', $attrs = array())
	{
		$action_arr = array($type => $action, 'attrs' => $attrs);
		$this->_actions[$label] = $action_arr;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Add a formatter to a column

	<p>The formatter should be a function that accepts an array of field values.</p>

	<code>
	public function is_darkside($fields)
	{
		return ($fields['darkside']) ? 'yes' : 'no';
	}

	$this->data_table->add_field_formatter('darkside', 'is_darkside'); // will echo out either yes or no for the field value
	</code>
	 * 
	 * @access	public
	 * @param	string text label to display for the action
	 * @param	string a string that represents a function name
	 * @return	void
	 */
	public function add_field_formatter($field, $func)
	{
		$this->_field_formatters[$field] = $func;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Sets the sorting information of which column and which direction
	 * 
	 * @access	public
	 * @param	string which column to display sorted
	 * @param	boolean is the order ascending?
	 * @return	void
	 */
	public function set_sorting($field, $ordering = TRUE)
	{
 		$this->_field = $field;
		if ($ordering != 'desc') $ordering = 'asc';
		$this->_ordering = $ordering;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Sets the sorting information of which column and which direction
	 * 
	 * @access	public
	 * @param	string which column to display sorted
	 * @param	boolean is the order ascending?
	 * @return	void
	 */
	public function get_sorted_order($col, $ordering = TRUE)
	{
		return $this->_ordering;
	}

	// --------------------------------------------------------------------

	/**
	 * Sets the sorting information of which column and which direction
	 * 
	 * @access	public
	 * @param	string which column to display sorted
	 * @param	boolean is the order ascending?
	 * @return	void
	 */
	public function get_sorted_field($col, $ordering = TRUE)
	{
 		return $this->_field;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Set the table html attributes
	 * 
	 * @access	public
	 * @param	array table attributes
	 * @return	void
	 */
	public function set_table_attributes($attrs)
	{
		$this->table_attrs = array_merge($this->table_attrs, $attrs);
	}

	// --------------------------------------------------------------------

	/**
	 * Set the table tbody attributes
	 * 
	 * @access	public
	 * @param	array tbody attributes
	 * @return	void
	 */
	public function set_body_attributes($attrs)
	{
		$this->body_attrs = array_merge($this->body_attrs, $attrs);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Add a single header to the table

	The <dfn>$sorting_param</dfn> is the column to sort on if it is different then the $key.

	<code>
	$this->data_table->add_header('name', 'Name', 'name', '', 1);
	</code>
	 * 
	 * @access	public
	 * @param	string the column key value to associate to the row data
	 * @param	string header name
	 * @param	string sorting parameter to use with sorting
	 * @param	mixed header attributes
	 * @param	mixed string or in index array value for the header
	 * @return	Data_table_header object
	 */
	public function add_header($key, $name, $sorting_param = NULL, $attrs = array(), $index = NULL)
	{
		if (!is_numeric($index)) $index = count($this->headers);
		$this->headers[$index] = new Data_table_header($key, $name, $sorting_param, $attrs);
		return $this->headers[$index];
	}


	// --------------------------------------------------------------------

	/**
	 * Add multiple headers to the table using an array instead of a Data_table_header object
	 * 
	<code>
	$headers = array(
		'name' => 'Name',
		'weapon' => 'Weapon',
		'active' => 'Active'
	);
	$sorting_cols = array('name', 'weapon', 'active');

	$this->data_table->add_headers($headers, $sorting_cols, '');
	</code>

	 * @access	public
	 * @param	array key/values of column and display names for headers
	 * @param	array sorting parameters
	 * @param	mixed header attributes
	 * @return	void
	 */
	public function add_headers($headers, $sorting_params = array(), $attrs = array())
	{
		$i = 0;
		$s = 0;
		$num = count($headers);
		foreach($headers as $key => $val)
		{
			// auto set name if it is non numerically indexed array
			if (is_int($key))
			{
				$key = $val;
				if (isset($this->lang_prefix) AND $lang_name = lang($this->lang_prefix.$key))
				{
					$val = $lang_name;
				}
				else
				{
					$val = ucwords(str_replace('_', ' ', $key));
				}
			}
			if ($i == 0 AND !empty($this->_actions) AND $this->actions_field == 'first')
			{
				$this->add_header($key, '&nbsp;', NULL, $attrs, 'actions');
				$i++;
			}
			$sorting_param = !empty($sorting_params[$s]) ? $sorting_params[$s] : NULL;
			
			$this->add_header($key, $val, $sorting_param, $attrs, $i);
			$i++;
			if ($i == $num AND !empty($this->_actions) AND $this->actions_field == 'last')
			{
				$this->add_header($key, '&nbsp;', NULL, $attrs, 'actions');
				$i++;
			}
			$s++;
		}
	}
	

	// --------------------------------------------------------------------

	/**
	 * Add a single row to the table
	
	<code>
	$row = array(
		'id' => 1,
		'name' => 'Luke Skywalker',
		'weapon' => 'light saber',
		'active' => 'yes'
	);
	$action = array('EDIT' => 'example/edit/{id}');

	$this->data_table->add_row($row, '', 1, $action);
	</code>
	 * 
	 * @access	public
	 * @param	array columns data
	 * @param	mixed row attributes
	 * @param	mixed string or in index array value for the header
	 * @param	mixed actions for the row
	 * @return	void
	 */
	public function add_row($columns = array(), $attrs = array(), $index = NULL, $action = NULL)
	{
		if ($index == NULL) $index = count($this->rows);
		$fields = array();
		$num = count($columns);
		if (is_array($columns))
		{
			$i = 0;
			$this->row_data[] = array();
			
			
			
			foreach($columns as $key => $val)
			{
				// handle the __field__
				$col_attrs = (isset($columns['__field__'][$key])) ? $columns['__field__'][$key] : array();
				
				if ($i == 0 AND !empty($this->_actions) AND $this->actions_field == 'first') 
				{
					$fields[] = new Data_table_field('actions', $this->_render_actions($this->_actions, $columns), array('class' => 'actions'));
					$i++;
				}
				// add the actions
				if (empty($action)) $action = $this->default_field_action;
				if (!empty($action))
				{

					//e modifier is deprecated so we have to do this
					$callback = create_function('$match', '
							$return = $match[0];
							if (!empty($match[2]))
							{
								$return = $match[1].$GLOBALS["__tmp_transient_columns__"][$match[2]].$match[3];
							}
							return $return;');

					// hacky but avoids 5.3 funcation syntax (which is nicer but doesn't work with 5.2)
					$GLOBALS['__tmp_transient_columns__'] = $columns;
					$action = preg_replace_callback('#^(.*)\{(.+)\}(.*)$#', $callback, $action);
					$fields[] = new Data_table_field($key, $val, array(), $action);
				}
				else
				{
					$fields[] = new Data_table_field($key, $val, $col_attrs);
				}

				$i++;
				if ($i == $num AND !empty($this->_actions) AND $this->actions_field == 'last')
				{
					$fields[] = new Data_table_field('actions', $this->_render_actions($this->_actions, $columns), array('class' => 'actions'));
					$i++;
				}
			}

			// hacky cleanup to avoid using 5.3 syntax
			if (isset($GLOBALS["__tmp_transient_columns__"]))
			{
				unset($GLOBALS["__tmp_transient_columns__"]);
			}

		}
		$attrs['id'] = (!empty($columns[$this->row_id_key])) ? $this->id.'_row'.$columns[$this->row_id_key] : $this->id.'_row'.$index;
		$this->rows[$index] = new Data_table_row($fields, $attrs, $col_attrs);
		return $this->rows[$index];
	}
	
	// --------------------------------------------------------------------

	/**
	 * Add multiple headers to the table using an array instead of a Data_table_header object
	 * 
	<code>
	$rows[] = array(
		'id' => 1,
		'name' => 'Luke Skywalker',
		'weapon' => 'light saber',
		'active' => 'yes'
	);

	$rows[] = array(
		'id' => 2,
		'name' => 'Han Solo',
		'weapon' => 'blaster',
		'active' => 'yes'
	);
	$action = array('EDIT' => 'example/edit/{id}');

	$this->data_table->add_rows($rows, '', $action);
	</code>
	 * @access	public
	 * @param	array row data
	 * @param	array row attributes
	 * @param	mixed row actions
	 * @return	void
	 */
	public function add_rows($data, $attrs = array(), $action = NULL)
	{
		foreach($data as $val)
		{
			$this->add_row($val, $attrs, NULL, $action);
		}
	}
	
	// --------------------------------------------------------------------

	/**
	 * Generic protected method to render attributes for various
	 * 
	 * @access	protected
	 * @param	mixed html attributes
	 * @return	string
	 */
	protected function _render_attrs($attrs)
	{
		if (is_array($attrs))
		{
			$str = '';
			foreach($attrs as $key => $val)
			{
				$str .= ' '.$key.'="'.$val.'"';
			}
			return $str;
		}
		else
		{
			return $attrs;
		}
		
	}
	
	// --------------------------------------------------------------------

	/**
	 * Renders the actions for a row
	 * 
	 * @access	protected
	 * @param	array actions
	 * @param	array columns
	 * @return	void
	 */
	protected function _render_actions($actions, $fields)
	{
		$str = '';
		$actions = array();
		foreach($this->_actions as $key => $val)
		{
			// normalize the data
			if (!is_array($val))
			{
				$val = array('url' => $val);
			}
			if (!empty($val['func']))
			{
				$action = call_user_func($val['func'], $fields);
				if (!empty($action)) $actions[] = $action;
			}
			else
			{
				//e modifier is deprecated so we have to do this
				$callback = create_function('$match', '
						$return = "";
						if (!empty($match[2]))
						{
							$return = $match[1].$GLOBALS["__tmp_transient_fields__"][$match[2]].$match[3];
						}
						return $return;');

				// hacky but avoids 5.3 funcation syntax (which is nicer but doesn't work with 5.2)
				$GLOBALS['__tmp_transient_fields__'] = $fields;
				$url = preg_replace_callback('#^(.*)\{(.+)\}(.*)$#', $callback, $val['url']);
				

				$attrs = (!empty($val['attrs'])) ? ' '.$this->_render_attrs($val['attrs']) : '';
				$actions[] ='<a href="'.$url.'"'.$attrs.'>'.$key.'</a>';
			}
		}

		// hacky cleanup to avoid using 5.3 syntax
		if (isset($GLOBALS["__tmp_transient_fields__"]))
		{
			unset($GLOBALS["__tmp_transient_fields__"]);
		}

		if (!empty($actions)) $str = implode('&nbsp; '.$this->action_delimiter.'  &nbsp;', $actions);
		return $str;
	}
	
}

// ------------------------------------------------------------------------

/**
 * A Data table body element
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @autodoc		FALSE
 */
class Data_table_body {
	public $rows = array();
	public $attrs = array();
	
	public function Data_table_body($rows, $attrs = array())
	{
		$this->rows = $rows;
		$this->attrs = $attrs;
	}
}


// ------------------------------------------------------------------------

/**
 * A table row object
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @autodoc		FALSE
 */
class Data_table_row {
	public $fields = array();
	public $attrs = array();
	public $col_attrs = array();

	public function __construct($fields = array(), $attrs = array(), $col_attrs = array())
	{
		$this->fields = $fields;
		$this->attrs = $attrs;
		$this->col_attrs = $col_attrs;
	}
	
	public function add_column($heading, $value, $attrs = array(), $action = NULL)
	{
		$attrs = settype($attrs, 'array');
		if (!empty($this->col_attrs))
		{
			$attrs = array_merge($this->col_attrs, $attrs);
		}
		$this->fields[] = new Data_table_field($heading, $value, $attrs, $action);
	}
}


// ------------------------------------------------------------------------

/**
 * A table header object
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @autodoc		FALSE
 */
class Data_table_header {
	public $col_key = '';
	public $name = '';
	public $sorting_param = NULL;
	public $attrs = array();
	
	public function __construct($col_key, $name, $sorting_param, $attrs = array())
	{
		$this->col_key = $col_key;
		$this->name = $name;
		$this->sorting_param = $sorting_param;
		$this->attrs = $attrs;
	}
}


// ------------------------------------------------------------------------

/**
 * A table column object
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @autodoc		FALSE
 */
class Data_table_field {
	public $heading = '';
	public $value = '';
	public $attrs = array();
	public $action = '';
	
	public function __construct($heading, $value, $attrs = array(), $action = NULL)
	{
		$this->heading = $heading;
		$this->value = $value;
		$this->attrs = $attrs;
		$this->action = $action;
	}
}

/* End of file Data_table.php */
/* Location: ./modules/fuel/libraries/Data_table.php */