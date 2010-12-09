<?php 
$blog_controllers = array('posts', 'comments', 'categories', 'links', 'users');

foreach($blog_controllers as $c)
{
	$route[FUEL_ROUTE.'blog/'.$c] = FUEL_FOLDER.'/module';
	$route[FUEL_ROUTE.'blog/'.$c.'/(.*)'] = FUEL_FOLDER.'/module/$1';
}

$route[FUEL_ROUTE.'blog/settings'] = BLOG_FOLDER.'/settings';