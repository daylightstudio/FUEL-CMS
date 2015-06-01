<?php 
/*
Example view that can be used to display module archives. 

$config['modules']['articles'] = array(
	'preview_path' => 'articles/{year}/{month}/{day}/{slug}', // put in the preview path on the site e.g products/{slug}
	'model_location' => '', // put in the advanced module name here
	'pages' => array(
		'base_uri' => 'articles',
		'list' => '_posts/posts',
		'post' => '_posts/post', // <-- THIS POINTS TO THE VIEW
		// CAN ALSO BE WRITTEN LIKE THE FOLLOWING:
		'post' => array('view' => '_posts/archives'), 
	)
);
*/
?>
<?php fuel_set_var('h1', 'Archives: '.$display_date) ?>
<?php fuel_set_var('sidemenu', array('categories')) ?>
<?php $CI->load->view('_posts/posts'); ?>
