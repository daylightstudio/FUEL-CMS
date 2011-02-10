<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
 
require_once(FUEL_PATH.'models/base_module_model.php');
 
class Categories_model extends Base_module_model {

    public $record_class = 'Category';

    function __construct()
    {
        parent::__construct('categories');
    }
 

	 // cleanup category to articles
	 function on_after_delete($where)
	 {
		$this->delete_related('categories_to_articles_model', 'category_id', $where);
	 }

	// add unique name validation
	function on_before_validate($values)
	{
		if (!empty($values['id']))
		{
			$this->add_validation('name', array(&$this, 'is_editable'), lang('error_val_empty_or_already_exists', lang('form_label_name')), array('name', $values['id']));
		}
		else
		{
			$this->add_validation('name', array(&$this, 'is_new'), lang('error_val_empty_or_already_exists', lang('form_label_name')), 'name');
		}
		return $values;
	}
	
 
}
 
class Category_model extends Base_module_record {
	
	function get_articles()
	{
		$this->_CI->load->model('categories_to_articles_model');
		$where = array('category_id' => $this->id, 'articles.published' => 'yes');
		$articles = $this->_CI->categories_to_articles_model->find_all($where);
		return $articles;
	}
}