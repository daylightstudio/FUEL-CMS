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
	
	public $file;

	public $_classes = array();
	public $_functions = array();
	

	/**
	 * Constructor - Sets user guide preferences
	 *
	 * The constructor can be passed an array of config values
	 */
	function __construct($file = NULL)
	{
		$this->initialize($file);
	}
	
	function initialize($params = NULL)
	{
		if (!empty($params))
		{
			if (is_array($params) AND isset($params['file']))
			{
				$this->file = $params['file'];
			}
			else
			{
				$this->file = $params;
			}
		}
		
		// testing 
//		$this->file = '/Library/WebServer/Documents/daylight/FUEL/v.9/fuel/modules/fuel/libraries/Fuel_cache.php';
		if (!file_exists($this->file))
		{
			return FALSE;
		}

		require_once($this->file);

		// get the contents of the file
		$source = file_get_contents($this->file);
		
		// get the classes of the file
		$classes = $this->parse_classes($source);
		if (!empty($classes))
		{
			foreach($classes as $class)
			{
				$this->_classes[$class] = new Inspection_class($class);
			}
		}
		
		// get the functions of the file
		$functions = $this->parse_functions($source);
		if (!empty($functions))
		{
			foreach($functions as $func)
			{
//				$this->_functions[$func] = new Inspection_function($func);
			}
		}
	}
	
	function classes($class = NULL)
	{
		if (isset($this->_classes[$class]))
		{
			return $this->_classes[$class];
		}
		return $this->_classes;
	}
	
	function functions($function = NULL)
	{
		if (isset($this->_functions[$function]))
		{
			return $this->_functions[$function];
		}
		return $this->_functions;
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
				AND $tokens[$i][0] == T_STRING) {

				$class_name = $tokens[$i][1];
				$classes[] = $class_name;
			}
		}
		return $classes;
	}


	//http://stackoverflow.com/questions/928928/determining-what-classes-are-defined-in-a-php-class-file
	function parse_functions($code, $include_underscore_funcs = FALSE)
	{
		$functions = array();
		$tokens = token_get_all($code);
		$count = count($tokens);
		for ($i = 2; $i < $count; $i++)
		{
			if ($tokens[$i - 2][0] == T_FUNCTION
				AND $tokens[$i - 1][0] == T_WHITESPACE
				AND $tokens[$i][0] == T_STRING) {

				$func_name = $tokens[$i][1];
				if (!$include_underscore_funcs AND substr($func_name, 0, 1) == '_')
				{
					continue;
				}
				$functions[] = $func_name;
			}
		}
		return $functions;
	}


	function publish()
	{
		
	}
	
}

class Inspection_class {
	public $name;

	protected $reflection; //ReflectionMethod
	protected $_comment; //Fuel_documentizer_comment
	protected $_methods;

	function __construct($class)
	{
		$this->name = $class;
		$this->reflection = new ReflectionClass($this->name);
	}

	function comment()
	{
		// lazy initialize
		if (!isset($this->_comment))
		{
			$comment = $this->reflection->getDocComment();
			$this->_comment = new Inspection_comment($comment);
		}
		return $this->_comment;
	}
	
	function properties()
	{
		
	}
	
	function inherited()
	{
		
	}
	
	function methods($method = NULL, $types = array(), $include_constructor = FALSE)
	{
		if (!isset($this->_methods))
		{
			$ref_methods = $this->reflection->getMethods();
			
			foreach($ref_methods as $m)
			{
				$m_obj = new Inspection_method($this->name, $m->name);
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
			if (!$include_constructor AND ($m == '__construct' OR $m == $this->name))
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
	
		if (!empty($method))
		{
			if (!isset($methods[$method]))
			{
				return FALSE;
			}
			return $methods[$method];
		}
		
		return $methods;
	}
}

class Inspection_method extends Inspection_function {
	
	public $obj;
	function __construct($obj, $method)
	{
		$this->obj = $obj;
		$this->name = $method;
		$this->reflection = new ReflectionMethod($this->obj, $this->name);
	}
}

class Inspection_function {
	
	public $reflection; //ReflectionMethod
	protected $_comment; //Fuel_documentizer_comment
	protected $_params;
	protected $type = 'function';
	
	function __construct($function)
	{
		$this->name = $function;
		$this->reflection = new ReflectionFunction($this->name);
	}
	
	function name()
	{
		
		return $this->reflection->name;
	}
	
	function comment()
	{
		// lazy initialize
		if (!isset($this->_comment))
		{
			$comment = $this->reflection->getDocComment();
			$this->_comment = new Inspection_comment($comment);
		}
		return $this->_comment;
	}
	
	function params($index = NULL)
	{
		if (!isset($this->_params))
		{
			$params = $this->reflection->getParameters();
			foreach($params as $param)
			{
				$p = new Inspection_param($param);
				$this->_params[] = $p;
			}
		}
		
		if (isset($index))
		{
			$index = (int) $index;
			return $this->_params[$index];
		}
	
		return $this->_params;
	}
}

class Inspection_param {
	
	public $reflection; //ReflectionParameter
	
	function __construct($ref_obj)
	{
		$this->reflection = $ref_obj;
	}
	
	function name()
	{
		return $this->reflection->name;
	}
	
	function is_optional()
	{
		return $this->reflection->isOptional();
	}
	
	function has_default()
	{
		return $this->reflection->isDefaultValueAvailable();
	}
	function default_value()
	{
		return $this->reflection->getDefaultValue();
	}
	
	function is_array()
	{
		return $this->reflection->isArray();
	}
}

class Inspection_comment {
	
	protected $_text = '';
	protected $_description;
	protected $_tags;
	
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
					$params[] = trim($value);
				}
				else if (empty($this->_tags[$tag]))
				{
					$this->_tags[$tag] = trim($value);
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
	
	function description($long = TRUE)
	{
		if (!isset($this->_description))
		{
			preg_match('#/\*\*\s*(.+)@#Ums', $this->_text, $matches);
			if (isset($matches[1]))
			{
				$this->_description = preg_replace('#\*\s+#ms', '', $matches[1]);
			}
		}
		
		if (!$long)
		{
			$desc = explode(PHP_EOL, $this->_description);
			if (!empty($desc[1]))
			{
				return $desc[1];
			}
		}
		
		return $this->_description;
	}

}
/* End of file Fuel_user_guide.php */
/* Location: ./modules/fuel/libraries/Fuel_user_guide.php */