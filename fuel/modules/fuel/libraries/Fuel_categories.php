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
 * FUEL categories object
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/libraries/fuel_categories
 */

// --------------------------------------------------------------------
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
	 * @return	array
	 */	
	public function find_by_context($context)
	{
		$model = $this->model();
		$where['context'] = $context;
		$data = $model->find_all($where);
		return $data;
	}
	
}

/* End of file Fuel_categories.php */
/* Location: ./modules/fuel/libraries/Fuel_categories.php */