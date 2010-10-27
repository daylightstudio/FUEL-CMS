<?php
// included in the main config/MY_fuel_modules.php

$config['modules']['blog_posts'] = array(
	'module_name' => 'Posts',
	'module_uri' => 'blog/posts',
	'model_name' => 'blog_posts_model',
	'model_location' => 'blog',
	'display_field' => 'title',
	'preview_path' => 'blog/id/{id}',
	'permission' => 'blog/posts',
	'instructions' => 'Here you can manage the blog for your site.',
	'archivable' => TRUE,
	'configuration' => array('blog' => 'blog'),
	'nav_selected' => 'blog/posts',
	'language' => array('blog' => 'blog'),
	'default_col' => 'date_added',
	'default_order' => 'desc',
);

$config['modules']['blog_categories'] = array(
	'module_name' => 'Categories',
	'module_uri' => 'blog/categories',
	'model_name' => 'blog_categories_model',
	'model_location' => 'blog',
	'display_field' => 'name',
	'preview_path' => 'blog/categories/{permalink}',
	'permission' => 'blog/categories',
	'instructions' => 'Here you can manage the categories for your site.',
	'archivable' => TRUE,
	'configuration' => array('blog' => 'blog'),
	'nav_selected' => 'blog/categories',
	'language' => array('blog' => 'blog')
	
);

$config['modules']['blog_users'] = array(
	'module_name' => 'Authors',
	'module_uri' => 'blog/users',
	'model_name' => 'blog_users_model',
	'model_location' => 'blog',
	'display_field' => 'display_name',
	'preview_path' => 'blog/authors/{id}',
	'permission' => 'blog/users',
	'instructions' => 'Here you can manage the blog authors for your site.',
	'archivable' => TRUE,
	'configuration' => array('blog' => 'blog'),
	'nav_selected' => 'blog/users',
	'language' => array('blog' => 'blog')
);

$config['modules']['blog_comments'] = array(
	'module_name' => 'Comments',
	'module_uri' => 'blog/comments',
	'model_name' => 'blog_comments_model',
	'model_location' => 'blog',
	'display_field' => 'author_name',
	'default_col' => 'date_added',
	'preview_path' => 'blog/id/{post_id}',
	'permission' => 'blog/comments',
	'instructions' => 'Here you can manage the blog comments for your site.',
	'archivable' => TRUE,
	'configuration' => array('blog' => 'blog'),
	'nav_selected' => 'blog/comments',
	'language' => array('blog' => 'blog')
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
	'instructions' => 'Here you can manage the blog links for your site.',
	'archivable' => TRUE,
	'configuration' => array('blog' => 'blog'),
	'nav_selected' => 'blog/links',
	'language' => array('blog' => 'blog')
);