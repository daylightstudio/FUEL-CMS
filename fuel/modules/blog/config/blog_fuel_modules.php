<?php
$config['modules']['blog_posts'] = array(
	'module_name' => 'Posts',
	'module_uri' => 'blog/posts',
	'model_name' => 'blog_posts_model',
	'model_location' => 'blog',
	'table_headers' => array(
		'id', 
		'title', 
		'author', 
		'date_added', 
		'published', 
	),
	'display_field' => 'title',
	'preview_path' => 'blog/id/{id}',
	'permission' => 'blog/posts',
	'instructions' => lang('module_instructions_default', 'blog posts'),
	'archivable' => TRUE,
	'configuration' => array('blog' => 'blog'),
	'nav_selected' => 'blog/posts',
//	'language' => array('blog' => 'blog'),
	'default_col' => 'date_added',
	'default_order' => 'desc',
	'sanitize_input' => array('template','php')
);

$config['modules']['blog_categories'] = array(
	'module_name' => 'Categories',
	'module_uri' => 'blog/categories',
	'model_name' => 'blog_categories_model',
	'model_location' => 'blog',
	'table_headers' => array(
		'id', 
		'name', 
		'published', 
	),
	'display_field' => 'name',
	'preview_path' => 'blog/categories/{permalink}',
	'permission' => 'blog/categories',
	'instructions' => lang('module_instructions_default', 'blog categories'),
	'archivable' => TRUE,
	'configuration' => array('blog' => 'blog'),
	'nav_selected' => 'blog/categories',
//	'language' => array('blog' => 'blog')
	
);

$config['modules']['blog_comments'] = array(
	'module_name' => 'Comments',
	'module_uri' => 'blog/comments',
	'model_name' => 'blog_comments_model',
	'model_location' => 'blog',
	'table_headers' => array(
		'id', 
		'post_title', 
		'comment', 
		'comment_author_name', 
		'is_spam', 
		'date_submitted',
		'published', 
	),
	'display_field' => 'author_name',
	'default_col' => 'date_submitted',
	'default_order' => 'desc',
	'preview_path' => 'blog/id/{post_id}',
	'permission' => 'blog/comments',
	'instructions' => lang('module_instructions_default', 'blog comments'),
	'archivable' => TRUE,
	'configuration' => array('blog' => 'blog'),
	'nav_selected' => 'blog/comments',
//	'language' => array('blog' => 'blog'),
);

$config['modules']['blog_links'] = array(
	'module_name' => 'Links',
	'module_uri' => 'blog/links',
	'model_name' => 'blog_links_model',
	'model_location' => 'blog',
	'display_field' => 'url',
	'default_col' => 'name',
	'preview_path' => '',
	'permission' => 'blog/links',
	'instructions' => lang('module_instructions_default', 'blog links'),
	'archivable' => TRUE,
	'configuration' => array('blog' => 'blog'),
	'nav_selected' => 'blog/links',
//	'language' => array('blog' => 'blog')
);

$config['modules']['blog_users'] = array(
	'module_name' => 'Authors',
	'module_uri' => 'blog/users',
	'model_name' => 'blog_users_model',
	'model_location' => 'blog',
	'table_headers' => array(
		'fuel_user_id', 
		'name', 
		'display_name', 
		'active' 
	),
	
	'display_field' => 'display_name',
	'preview_path' => 'blog/authors/{id}',
	'permission' => 'blog/users',
	'instructions' => lang('module_instructions_default', 'blog authors'),
	'archivable' => TRUE,
	'configuration' => array('blog' => 'blog'),
	'nav_selected' => 'blog/users',
//	'language' => array('blog' => 'blog')
);