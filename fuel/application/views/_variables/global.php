<?php 

// declared here so we don't have to in controller variable files'
$CI =& get_instance();

// generic global page variables used for all pages
$vars = array();
$vars['layout'] = array('body' => '_layouts/main');
$vars['page_title'] = fuel_nav(array('render_type' => 'page_title', 'delimiter' => ' : ', 'order' => 'desc', 'home_link' => 'WidgiCorp - Fine Makers of Widgets'));
$vars['meta_keywords'] = 'Widgest, Sprockets';
$vars['meta_description'] = 'Fine maker of widgets';
$vars['js'] = '';
$vars['css'] = '';
$vars['body_class'] = $CI->uri->segment(1).' '.$CI->uri->segment(2);

$vars['blocks'] = array('showcase', 'quote');
$vars['sidemenu'] = fuel_nav(array('container_tag_id' => 'sidemenu', 'parent' => uri_segment(1)));

// page specific variables
$pages = array();
?>