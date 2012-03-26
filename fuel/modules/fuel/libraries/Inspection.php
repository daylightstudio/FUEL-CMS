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
 * @copyright	Copyright (c) 2011, Run for Daylight LLC.
 * @license		http://www.getfuelcms.com/user_guide/general/license
 * @link		http://www.getfuelcms.com
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * User guide library
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/modules/page_analysis
 */

// --------------------------------------------------------------------

class Inspection {
	
	public $file = ''; // path to the file to inspect

	protected $_classes = array(); // cache of classes found in the file
	protected $_functions = array(); // cache of functions found in the file
	protected $_comments = array(); // cache of page comments
	
	/**
	 * Constructor - Sets user guide preferences
	 *
	 * The constructor can be passed an array of config values
	 */
	function __construct($file = NULL)
	{
		$CI =& get_instance();
		$CI->load->helper('inflector');
		$CI->load->helper('markdown');
		$CI->load->helper('security');
		
		if (!empty($file))
		{
			$this->initialize($file);
		}
	}
	
	function initialize($params = NULL)
	{
		if (!empty($params))
		{
			if (is_array($params) AND isset($params['file']))
			{
				foreach ($params as $key => $val)
				{
					if (isset($this->$key) AND substr($key, 0, 1) != '_')
					{
						$this->$key = $val;
					}
				}
			}
			else
			{
				$this->file = $params;
			}
		}
		
		// no file... no go
		if (!file_exists($this->file))
		{
			return FALSE;
		}
		
		// get the contents of the file
		$source = file_get_contents($this->file);
		
		// get the classes of the file
		$classes = $this->parse_classes($source);
		
		$loaded = FALSE;
		if (!empty($classes))
		{
			foreach($classes as $class)
			{
				// load the file if the class does not exist
				if (!class_exists($class) AND !$loaded)
				{
					require_once($this->file);
					$loaded = TRUE;
				}
				$this->_classes[$class] = new Inspection_class($class, $source);
			}
		}
		
		// get the functions of the file
		$functions = $this->parse_functions($source);
		
		if (!empty($functions))
		{
			foreach($functions as $func)
			{
				// load the helper if the function does not exist
				if (!function_exists($func) AND !$loaded)
				{
					require_once($this->file);
					$loaded = TRUE;
				}
				
				if (function_exists($func))
				{
					$this->_functions[$func] = new Inspection_function($func);
				}
			}
		}
		
		// get all comments in the file
		$comments = $this->parse_comments($source);
		
		foreach($comments as $comment)
		{
			$this->_comments[] = new Inspection_comment($comment);
		}
	}
	
	function classes($class = NULL)
	{
		if (!empty($class))
		{
			if (isset($this->_classes[$class]))
			{
				return $this->_classes[$class];
			}
			return FALSE;
		}
		return $this->_classes;
	}
	
	function functions($function = NULL)
	{
		if (!empty($function))
		{
			if (isset($this->_functions[$function]))
			{
				return $this->_functions[$function];
			}
			return FALSE;
		}
		return $this->_functions;
	}
	
	function comments($comment = NULL)
	{
		if (isset($comment))
		{
			if (isset($this->_comments[$comment]))
			{
				return $this->_comments[$comment];
			}
			return FALSE;
		}
		return $this->_comments;
	}
	
	//http://stackoverflow.com/questions/928928/determining-what-classes-are-defined-in-a-php-class-file
	function parse_classes($code)
	{
		$classes = array();
		$tokens = token_get_all($code);
		$count = count($tokens);
		
		for ($i = 2; $i < $count; $i++)
		{
			if ($tokens[$i - 2][0] == T_CLASS
				AND $tokens[$i - 1][0] == T_WHITESPACE
				AND $tokens[$i][0] == T_STRING)
			{

				$class_name = $tokens[$i][1];
				$classes[] = $class_name;
			}
		}
		return $classes;
	}

	//http://stackoverflow.com/questions/2666554/how-to-get-list-of-declared-functions-with-their-data-from-php-file
	function parse_functions($code, $include_underscore_funcs = FALSE)
	{
		$functions = array();
		$tokens = token_get_all($code);
		$count = count($tokens);
		
		$next_string_is_func = false;
	    $in_class = false;
	    $braces_count = 0;
		foreach($tokens as $token)
		{
			switch($token[0])
			{
				case T_CLASS:
					$in_class = TRUE;
					break;
				case T_FUNCTION:
					if (! $in_class)
					{
						$next_string_is_func = TRUE;
					}
					break;
				case T_STRING:
					if ($next_string_is_func)
					{
						$next_string_is_func = FALSE;
						if (!$include_underscore_funcs AND substr($token[1], 0, 1) == '_')
						{
							continue;
						}
						$functions[] = $token[1];
					}
					break;
				
				// Anonymous functions
				case '(':
				case ';':
					$next_string_is_func = false;
				break;

				// Exclude Classes
				case '{':
					if ($in_class)
					{
						$braces_count++;
					}
					break;

				case '}':
					if($in_class)
					{
						$braces_count--;
						if ($braces_count === 0)
						{
							$in_class = FALSE;
						}
					}
					break;
		    }
		}
		return $functions;
	}
	
	function parse_comments($code)
	{
		$comments = array();
		$tokens = token_get_all($code);
		
		foreach($tokens as $token)
		{
			switch($token[0])
			{
				case T_DOC_COMMENT:
					$comments[] = $token[1];
					break;
		    }
		}
		return $comments;
		
	}
}

/**
 * Inspection class ... class
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @autodoc		FALSE
 */
class Inspection_class extends Inspection_base {
	
	protected $_methods;
	protected $_props;
	protected $_file; // for further processing for properties
	
	function __construct($class, $file = '')
	{
		parent::__construct('ReflectionClass', $class);
		$this->_file = $file;
	}
	
	function properties($types = array(), $include_parent = FALSE)
	{
		if (!isset($this->_props))
		{
			$ref_props = $this->reflection->getProperties();
			
			foreach($ref_props as $p)
			{
				$prop_obj = new Inspection_property($this->name, $p->name, $this);
				$this->_props[$p->name] = $prop_obj;
				
			}
		}
		
		// get properties area to parse out for comments later on
		preg_match('#[c|C]lass\s+'.$this->name.'.+\{.+function __construct#Us', $this->_file, $props_match);
		if (isset($props_match[0]))
		{
			$props_block = $props_match[0];
			unset($props_match);
		}
		else
		{
			$props_block = '';
		}

		if (empty($types))
		{
			$types = array('public');
		}
		
		if (is_string($types))
		{
			// if all is set, then we set $types to NULL so that it won't do any additional filtering'
			if (strtolower($types) == 'all')
			{
				$types = NULL;
			}
			else
			{
				$types = (array) $types;
			}
		}
		
		$props = array();
		foreach($this->_props as $name => $p)
		{
			if (!$include_parent AND ($p->getDeclaringClass()->name != $this->name))
			{
				continue;
			}
			if (!empty($types))
			{
				foreach($types as $type)
				{
					$type_method = 'is'.ucfirst($type);
					if (method_exists($p->reflection, $type_method) AND $p->reflection->$type_method())
					{
						// look for comments on the right of the property
						preg_match('#\s*'.$type.'\s+(\$'.$name.').*; *//\s*(.+)#', $props_block, $matches);
						if (isset($matches[2]) AND !$p->comment->text())
						{
							$p->comment->set_text($matches[2]);
						}
						else
						{
							// if not found on the right, then we look to the direct top
							preg_match('#//\s*(.+)\s*\n\s+'.$type.'\s+(\$'.$name.').*#Um', $props_block, $matches);
							if (isset($matches[1]) AND !$p->comment->text())
							{
								$p->comment->set_text($matches[1]);
							}
							
						}
						
						
						$props[$name] = $p;
						
					}
				}
			}
			else
			{
				$props[$key] = $p;
			}
		}
		return $props;
	}
	
	function parent()
	{
		$parent = $this->reflection->getParentClass();
		if ($parent)
		{
			return $parent->name;
		}
	}
	
	function method($method)
	{
		$methods = $this->methods();
		if (isset($methods[$method]))
		{
			return $methods[$method];
		}
		return FALSE;
	}

	function methods($types = array(), $include_parent = FALSE, $include_constructor = FALSE)
	{
		if (!isset($this->_methods))
		{
			$ref_methods = $this->reflection->getMethods();
			foreach($ref_methods as $m)
			{
				$m_obj = new Inspection_method($m->name, $this->name);
				$this->_methods[$m->name] = $m_obj;
			}
		}
		if (empty($types))
		{
			$types = array('public');
		}
		
		if (is_string($types))
		{
			// if all is set, then we set $types to NULL so that it won't do any additional filtering'
			if (strtolower($types) == 'all')
			{
				$types = NULL;
			}
			else
			{
				$types = (array) $types;
			}
		}

		// static, public, protected, private, abstract, final
		$methods = array();
		foreach($this->_methods as $name => $m)
		{
			// filter out contstructors
			if (!$include_constructor AND ($name == '__construct' OR $name == $this->name))
			{
				continue;
			}

			// filter out parent methods
			if (!$include_parent AND ($m->getDeclaringClass()->name != $this->name))
			{
				continue;
			}

			if (!empty($types))
			{
				foreach($types as $type)
				{
					$type_method = 'is'.ucfirst($type);
					if (method_exists($m->reflection, $type_method) AND $m->reflection->$type_method())
					{
						$methods[$name] = $m;
					}
				}
			}
			else
			{
				$methods[$key] = $m;
			}
		}
		return $methods;
	}
}


/**
 * Inspection function class
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @autodoc		FALSE
 */
class Inspection_function extends Inspection_base {
	
	function __construct($function)
	{
		parent::__construct('ReflectionFunction', $function);
	}
}


/**
 * Inspection method class
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @autodoc		FALSE
 */
class Inspection_method extends Inspection_base {
	
	protected $_params;
	
	function __construct($method, $obj)
	{
		parent::__construct('ReflectionMethod', $method, $obj);
	}

}


/**
 * Inspection class object
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @autodoc		FALSE
 */
class Inspection_property extends Inspection_base {
	public $class;
	public $value;
	
	function __construct($obj, $method = NULL)
	{
		parent::__construct('ReflectionProperty', $method, $obj);
		if ($this->is_public())
		{
			$class = $this->reflection->class;
			$this->value = $this->reflection->getValue(new $class());
		}
	}
	
}


/**
 * Inspection param class
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @autodoc		FALSE
 */
class Inspection_param extends Inspection_base {
	
	public $function;
	
	function __construct($method, $obj = NULL)
	{
		if (isset($obj))
		{
			parent::__construct('ReflectionParameter', $method, $obj);
		}
		else
		{
			parent::__construct('ReflectionParameter', $method);
		}
	}
	
	function default_value($to_string = FALSE)
	{
		if ($this->is_default_value_available())
		{
			$val = $this->reflection->getDefaultValue();
			
			if ($to_string)
			{
				if (is_null($val))
				{
					return 'NULL';
				}
				else if (is_bool($val))
				{
					return ($val === TRUE) ? 'TRUE' : 'FALSE';
				}
				else if (is_string($val))
				{
					return '\''.$val.'\'';
				}
				else
				{
					// remove extra spaces and lowercase
					return preg_replace('#\s*#', '', strtolower(print_r($val, TRUE)));
				}
				
			}
			return $this->reflection->getDefaultValue();
		}
		
		return FALSE;
	}
	
	function is_default_array()
	{
		if ($this->is_array())
		{
			return TRUE;
		}
		else if ($this->is_default_value_available())
		{
			return is_array($this->default_value());
		}
		else if ($this->function->comment->param($this->position(), 'type') == 'array')
		{
			return TRUE;
		}
		return FALSE;
	}
}


/**
 * Inspection comment class
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @autodoc		FALSE
 */
class Inspection_comment {
	
	protected $_text = '';
	protected $_description;
	protected $_example;
	protected $_tags;
	protected $_filters = array(); // pre processing functions
	
	function __construct($comment)
	{
		$this->initialize($comment);
	}
	
	function initialize($comment)
	{
		$this->_text = (string)$comment;

		$tags = array(
			"access"	=>	'',
			"author"	=>	'',
			"copyright"	=>	'',
			"deprecated"=>	'',
			"example"	=>	'',
			"ignore"	=>	'',
			"internal"	=>	'',
			"link"		=>	'',
			"param"		=>	'',
			"return"	=> 	'',
			"see"		=>	'',
			"since"		=>	'',
			"tutorial"	=>	'',
			"version"	=>	''
		);
		$this->tags = $tags;
		
		preg_match_all('#^\s+\*\s+@(\w+)\s+(.+)\n#m', $this->_text, $matches);
		if (!empty($matches[2]))
		{
			$params = array();
			
			foreach($matches[1] as $key => $tag)
			{
				$value = $matches[2][$key];
				if ($tag == 'param')
				{
					$params[] = trim(str_replace("\t", ' ', $value));
				}
				else if (empty($this->_tags[$tag]))
				{
					$this->_tags[$tag] = trim(str_replace("\t", ' ', $value));
				}
			}
			$this->_tags['param'] = $params;
			
		}
	}
	
	function tags($type = 'param')
	{
		if (!empty($type))
		{
			if (isset($this->_tags[$type]))
			{
				return $this->_tags[$type];
			}
			return FALSE;
		}
		return $this->_tags;
	}
	
	function param($index, $type = NULL)
	{
		if (!isset($this->_tags['param']))
		{
			return FALSE;
		}
		
		$params = $this->_tags['param'];
		if (!is_int($index) OR !isset($params[$index])) return FALSE;
		
		$value = $params[$index];

		if (isset($type))
		{
			$split = preg_split('#\s#', $value);
			if ($type == 'type')
			{
				$value = $split[0];
			}
			else if ($type == 'comment' AND isset($split[1]))
			{
				array_shift($split);
				$value = implode(' ', $split);
			}
			else
			{
				return FALSE;
			}
		}
		
		return $value;
	}

	function return_value($type = NULL)
	{
		if (!isset($this->_tags['return']))
		{
			return FALSE;
		}
		$value = $this->_tags['return'];
		
		if (isset($type))
		{
			$split = preg_split('#\s#', $value);
			if ($type == 'type')
			{
				$value = $split[0];
			}
			else if ($type == 'comment' AND isset($split[1]))
			{
				array_shift($split);
				$value = implode(' ', $split);
			}
			else
			{
				return FALSE;
			}
		}
		
		return $value;
	}
	
	function description($format = FALSE)
	{

		if (!isset($this->_description))
		{
			preg_match('#/\*\*\s*(.+ )(@|\*\/)#Ums', $this->_text, $matches);
			if (isset($matches[1]))
			{
				$this->_description = $matches[1];
				
				// removing preceding * and tabs
				$this->_description = preg_replace('#\* *#m', "", $matches[1]);
				$this->_description = preg_replace("#^ +#m", "", $this->_description);
				
				// remove code examples since they are handled by the example method
				$this->_description = preg_replace('#<code>.+</code>#ms', '', $this->_description);
				$this->_description = trim($this->_description);
			}
			else
			{
				$this->_description = $this->_text;
			}
		}

		$desc = $this->_description;
		
		$desc = $this->filter($desc);
		
		// apply different formats
		if ($format)
		{
			if (is_string($format))
			{
				$format = (array) $format;
			}
			foreach($format as $f)
			{
				switch(strtolower($f))
				{
					case 'markdown':
						// must escape underscores to prevent <em> tags
						$desc = str_replace('_', '\_', $desc);

						$desc = markdown($desc);

						// the we replace back any that didn't get processed'(e.g. ones inside links)
						$desc = str_replace('\_', '_', $desc);
						
						break;
					case 'short':
						$desc_lines = explode(PHP_EOL, $desc);
						$first_line = TRUE;
						foreach($desc_lines as $d)
						{
							if (!empty($d))
							{
								if ($first_line)
								{
									$first_line = FALSE;
									$desc = $d;
									break;
								}
							}
						}
						break;
					case 'long':
						$desc_lines = explode(PHP_EOL, $desc);

						$new_desc = '';
						$first_line = TRUE;
						foreach($desc_lines as $d)
						{
							if (!empty($d))
							{
								if ($first_line)
								{
									$first_line = FALSE;
									continue;
								}
								else
								{
									$new_desc .= $d;
								}
							}
							else if (!$first_line)
							{
								$new_desc .= "\n\n";
							}
						}
						$desc = $new_desc;
						break;
					case 'one_line':
						$desc = str_replace(PHP_EOL, ' ', $desc);
						break;
					case 'entities':
						$desc = htmlentities($desc);
						break;
					case 'eval':
						$desc = eval_string($desc);
						break;
					case 'periods':
						$desc_lines = explode(PHP_EOL, $desc);
						$lines = '';
						$past_first = FALSE;
						foreach($desc_lines as $d)
						{
							$d = trim($d);
							if (!empty($d))
							{
								if (!$past_first)
								{
									$d = preg_replace('#(.+[^\.|>]\s*)$#', '$1. ', $d);
								}
								$lines .= $d.' ';
								$past_first = TRUE;
							}
							else if ($past_first)
							{
								$lines .= "\n\n";
							}
						}
						
						$lines = preg_replace('#(.+[^\.|>]\s*)$#', '$1. ', trim($lines));
						
						$desc = $lines;
				}
			}
		}
		
		// auto link
		$desc = auto_link($desc);
		
		// trim white space
		$desc = trim($desc);
		
		// clean
		$desc = xss_clean($desc);
		
		$desc = strip_image_tags($desc);
		
		return $desc;
	}
	
	function example($opening = '', $closing = '')
	{
		if (!isset($this->_example))
		{
			preg_match('#/\*\*\s*.+<code>(.+)</code>#Ums', $this->_text, $matches);
			if (isset($matches[1]))
			{
				$this->_example = preg_replace('#\*\s+#ms', '', $matches[1]);
			}
		}
		$this->_example = htmlentities($this->_example, ENT_NOQUOTES, 'UTF-8', FALSE);
		$example = $opening.$this->_example.$closing;
		return $example;
	}
	
	function text()
	{
		return $this->_text;
	}
	
	function set_text($text)
	{
		$this->_text = $text;
	}
	
	
	function add_filter($func, $key = NULL)
	{
		if (empty($key))
		{
			$this->_filters[] = $func;
		}
		else
		{
			$this->_filters[$key] = $func;
		}
	}
	
	function remove_filter($key)
	{
		unset($this->_filters[$key]);
	}
	
	protected function filter($source)
	{
		foreach($this->_filters as $func)
		{
			$source = call_user_func($func, $source);
		}
		return $source;
	}
	
}



/**
 * Inspection class object
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @autodoc		FALSE
 */
abstract class Inspection_base {
	
	public $reflection; // Reflection object
	public $comment; // Fuel_documentizer_comment
	public $name; // basic name value
	public $obj; // the object (if any)
	public $params; // the parameters of the object (if any)
	
	function __construct($ref_class = NULL, $method = NULL, $obj = NULL)
	{
		if (!empty($ref_class) AND !empty($method))
		{
			$this->initialize($ref_class, $method, $obj);
		}
	}
	
	function initialize($ref_class, $method, $obj = NULL)
	{
		if (is_object($method) AND strncasecmp(get_class($method), 'Reflection', 10) === 0)
		{
			$this->reflection = $method;
		}
		else if (isset($obj))
		{
			$this->reflection = new $ref_class($obj, $method);
			$this->obj = $obj;
		}
		else
		{
			$this->reflection = new $ref_class($method);
		}
		
		$this->name = $this->reflection->getName();

		if (method_exists($this->reflection, 'getDocComment'))
		{
			$comment = $this->reflection->getDocComment();
			$this->comment = new Inspection_comment($comment);
		}
		
		// used for functions and method objects
		if (method_exists($this->reflection, 'getParameters'))
		{
			$params = $this->reflection->getParameters();
			foreach($params as $param)
			{
				$p = new Inspection_param($param);
				$p->function = &$this;
				$this->params[] = $p;
			}
		}
	}
	
	
	function friendly_name()
	{
		return humanize($this->name);
	}

	function comment()
	{
		return $this->comment;
	}
	
	function param($index)
	{
		if (isset($this->params[$index]))
		{
			$index = (int) $index;
			return $this->params[$index];
		}
		else
		{
			return FALSE;
		}
	}
	
	function params()
	{
		return $this->params;
	}
	
	
	function __call($name, $args)
	{
		if (!preg_match('#^get|is|set#', $name))
		{
			$name = 'get_'.$name;
		}
		
		if (strpos($name, '_') !== FALSE)
		{
			$name = camelize($name);
		}
		if (method_exists($this->reflection, $name))
		{
			if (!empty($args))
			{
				return $this->reflection->$name($args);
			}
			else
			{
				return $this->reflection->$name();
			}
		}
		else
		{
			throw new Exception(lang('error_method_does_not_exist', $name));
		}
	}
}
/* End of file Fuel_user_guide.php */
/* Location: ./modules/fuel/libraries/Fuel_user_guide.php */