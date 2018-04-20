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
 * FUEL tags object
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/libraries/fuel_tags
 */

// --------------------------------------------------------------------

require_once('Fuel_modules.php');

class Fuel_tags extends Fuel_module {
	
	protected $module = 'tags';
	
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns a tag model record object
	 *
	 * @access	public
	 * @param	string	the slug value of the tag
	 * @param	string	the slug of the category the tag belongs to (optional)
	 * @return	object
	 */	
	public function find_by_tag($tag, $category = NULL)
	{
		$model = $this->model();
		$where['slug'] = $tag;
		if (!empty($category))
		{
			$categories_table = $model->tables('fuel_categories');
			$where[$categories_table.'.slug'] = $category;
		}
		$tag = $model->find_one($where);
		return $tag;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns multiple/one tag models based on a context value
	 *
	 * @access	public
	 * @param	string	the context value in which to search for a tag
	 * @param	boolean	determines whether to return just one or not (optional)
	 * @param	string	$order (optional)
	 * @param	int	$limit (optional)
	 * @param	int	$offset (optional)
	 * @return	object
	 */	
	public function find_by_context($context, $one = FALSE, $order = NULL, $limit = NULL, $offset = NULL)
	{
		$model = $this->model();
		$model = $this->model();
		$where = 'FIND_IN_SET("'.$context.'", '.$model->table_name().'.context)';
		if ($one)
		{
			$data = $model->find_one($where);
		}
		else
		{
			$data = $model->find_all($where, $order, $limit, $offset);
		}
		return $data;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns a related category model with the active record query already applied
	 *
	 * @access	public
	 * @param	string	the name of the category
	 * @param	string	$order (optional)
	 * @param	int	$limit (optional)
	 * @param	int	$offset (optional)
	 * @return	array
	 */	
	public function find_by_category($category, $order = NULL, $limit = NULL, $offset = NULL)
	{
		$this->CI->load->module_model(FUEL_FOLDER, 'fuel_relationships_model');
		$model =& $this->model();
		$categories_table = $model->tables('fuel_categories');
		$tags_table = $model->tables('fuel_tags');
		if (is_int($category))
		{
			$where[$tags_table.'.category_id'] = $category;
		}
		else
		{
			$where[$categories_table.'.slug'] = $category;
		}
		$data = $model->find_all($where, $order, $limit, $offset);
		return $data;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns an associative array with the keys being the tags slug value and the values (label), being the name of the tag
	 *
	 * @access	public
	 * @param	string	key for option
	 * @param	string	value for option
	 * @return	array
	 */	
	public function options_list($key = 'slug', $val = 'name')
	{
		$model =& $this->model();
		return $model->options_list($key, $val);
	}

}

/* End of file Fuel_tags.php */
/* Location: ./modules/fuel/libraries/Fuel_tags.php */