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
 * FUEL categories object
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/libraries/fuel_categories
 */

// --------------------------------------------------------------------

require_once('Fuel_modules.php');

class Fuel_categories extends Fuel_module {
	
	protected $module = 'categories';
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns a single category record object based on a slug value
	 *
	 * @access	public
	 * @param	string	the slug value to query on
	 * @return	array
	 */	
	public function find_by_slug($slug)
	{
		$model = $this->model();
		$where['slug'] = $slug;
		$data = $model->find_one($where);
		return $data;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns an array of categories record objects based on a context value
	 *
	 * @access	public
	 * @param	string	the context to query on
	 * @param	string	$order
	 * @param	int	limit
	 * @return	array
	 */	
	public function find_by_context($context, $order = NULL, $limit = NULL)
	{
		$model = $this->model();
		$where = 'FIND_IN_SET("'.$context.'", '.$model->table_name().'.context)';
		$data = $model->find_all($where, $order, $limit);
		return $data;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns an associative array with the keys being the categories slug value and the values (label), being the name of the category
	 *
	 * @access	public
	 * @param	string	key for option
	 * @param	string	value for option
	 * @return	array
	 */	
	public function options_list($key = 'slug', $val = 'name')
	{
		$model = $this->model();
		return $model->options_list($key, $val);
	}	
}

/* End of file Fuel_categories.php */
/* Location: ./modules/fuel/libraries/Fuel_categories.php */