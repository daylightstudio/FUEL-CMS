<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once(FUEL_PATH.'models/base_module_model.php');

class Articles_model extends Base_module_model {

	public $required = array();
	public $foreign_keys = array('author_id' => 'authors_model');	
	public $parsed_fields = array('content', 'content_formatted');
	
	function __construct()
	{
		parent::__construct('articles'); // table name
	}
	
	function list_items($limit = NULL, $offset = NULL, $col = 'name', $order = 'asc')
	{
		$this->db->join('authors', 'authors.id = articles.author_id', 'left');
		$this->db->select('articles.id, authors.name AS author, title, SUBSTRING(content, 1, 50) AS content, date_added, articles.published', FALSE);
		$data = parent::list_items($limit, $offset, $col, $order);
		return $data;
	}
	
	function tree()
	{
		$CI =& get_instance();
		$CI->load->model('categories_model');
		$CI->load->model('categories_to_articles_model');

		$return = array();
		$categories = $CI->categories_model->find_all(array(), 'id asc');
		$categories_to_articles = $CI->categories_to_articles_model->find_all('', 'categories.name asc');

		$cat_id = -1;
		foreach($categories as $category)
		{
			$cat_id = $category->id;
			$return[] = array('id' => $category->id, 'label' => $category->name, 'parent_id' => 0, 'location' => fuel_url('categories/edit/'.$category->id));
		}
		$i = $cat_id +1;

		foreach($categories_to_articles as $val)
		{
			$attributes = ($val->published == 'no') ? array('class' => 'unpublished', 'title' => 'unpublished') : NULL;
			$return[$i] = array('id' => $i, 'label' => $val->title, 'parent_id' => $val->category_id, 'location' => fuel_url('articles/edit/'.$val->article_id), 'attributes' =>  $attributes);
			$i++;
		}
		return $return;
	}
	
	function form_fields($values = array())
	{
		$fields = parent::form_fields();
		$CI =& get_instance();
		$CI->load->model('authors_model');
		$CI->load->model('categories_model');
		$CI->load->model('categories_to_articles_model');
		
		$category_options = $CI->categories_model->options_list('id', 'name', array('published' => 'yes'), 'name');
		$category_values = (!empty($values['id'])) ? array_keys($CI->categories_to_articles_model->find_all_array_assoc('category_id', array('article_id' => $values['id'], 'categories.published' => 'yes'))) : array();
		$fields['categories'] = array('label' => 'Categories', 'type' => 'array', 'class' => 'add_edit categories', 'options' => $category_options, 'value' => $category_values, 'mode' => 'multi');
		
		if ($CI->fuel_auth->has_permission('authors'))
		{
			$fields['author_id']['class'] = 'add_edit authors';
		}
		return $fields;
	}
	
	// add categories
	function on_after_save($values)
	{
		$data = (!empty($this->normalized_save_data['categories'])) ? $this->normalized_save_data['categories'] : array();
		$this->save_related('categories_to_articles_model', array('article_id' => $values['id']), array('category_id' => $data));
	}
	
	// cleanup articles from categories to articles table
    function on_after_delete($where)
    {
		$this->delete_related('categories_to_articles_model', 'article_id', $where);
    }
    

}

class Article_model extends Data_record {
	
}
?>