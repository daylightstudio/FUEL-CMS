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
 * FUEL tags object
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/libraries/fuel_tags
 */

// --------------------------------------------------------------------
class Fuel_tags extends Fuel_module {
	
	protected $module = 'tags';
	
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns a tag model record object
	 *
	 * @access	public
	 * @param	string	the name of the tag
	 * @param	string	the name of the category the tag belongs to (optional)
	 * @return	object
	 */	
	public function find_by_tag($tag, $category = NULL)
	{
		$model = $this->model();
		$where['slug'] = $tag;
		if (!empty($category))
		{
			$categories_table = $model->tables('categories');
			$where[$categories_table.'.slug'] = $category;
		}
		$tag = $model->find_one($where);
		return $tag;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns a related category model with the active record query already applied
	 *
	 * @access	public
	 * @param	string	the name of the category
	 * @return	array
	 */	
	public function find_by_category($category)
	{
		$this->CI->load->module_model(FUEL_FOLDER, 'relationships_model');
		$model =& $this->model();
		$categories_table = $model->tables('categories');
		$tags_table = $model->tables('tags');
		if (is_int($category))
		{
			$where[$tags_table.'.category_id'] = $category;
		}
		else
		{
			$where[$categories_table.'.slug'] = $category;
		}
		$data = $model->find_all($where);
		return $data;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns an associative array with the keys being the tags slug value and the values (label), being the name of the tag
	 *
	 * @access	public
	 * @param	string	related slug value
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