<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
 
class Categories_to_articles_model extends MY_Model {
 
    public $record_class = 'Category_to_article';
	public $foreign_keys = array('category_id' => 'categories_model', 'article_id' => 'articles_model', 'author_id' => 'authors_model');

    function __construct()
    {
        parent::__construct('categories_to_articles');
    }
 
    function _common_query()
    {
		$this->db->select('categories_to_articles.*, articles.title, categories.name AS category_name, articles.author_id, categories.published');
        $this->db->join('articles', 'categories_to_articles.article_id = articles.id', 'left');
        $this->db->join('categories', 'categories_to_articles.category_id = categories.id', 'left');
        $this->db->join('authors', 'authors.id = articles.author_id', 'left');
    }
 
}
 
class Category_to_article_model extends Data_record {
    public $category_name = '';
    public $title = '';
	public $author_id;
}