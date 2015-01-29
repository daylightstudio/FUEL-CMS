<?php 
/*
Example view that can be used to display categories associated with a module posts. 

$config['modules']['articles'] = array(
	'preview_path' => 'articles/{year}/{month}/{day}/{slug}', // put in the preview path on the site e.g products/{slug}
	'model_location' => '', // put in the advanced module name here
	'pages' => array(
		'base_uri' => 'articles',
		'list' => '_posts/posts',
		'post' =>'_posts/post',

		'category' => '_posts/category', // <-- THIS POINTS TO THE VIEW
		// CAN ALSO BE WRITTEN LIKE THE FOLLOWING:
		'category' => array('view' => '_posts/category'), 
	)
);
*/
?>
<?php fuel_set_var('h1', $category->name.' '.$module->model()->friendly_name()) ?>
<?php fuel_set_var('sidemenu', array('categories')) ?>
<?php $CI->load->view('_posts/posts'); ?>
