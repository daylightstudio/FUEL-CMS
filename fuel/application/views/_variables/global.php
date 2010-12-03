<?php 

// declared here so we don't have to in controller variable files'
$CI =& get_instance();

// generic global page variables used for all pages
$vars = array();
$vars['layout'] = 'main';
$vars['page_title'] = 'FUEL CMS : A Rapid Development CodeIgniter CMS';
$vars['meta_keywords'] = 'CodeIgniter Content Management System, CodeIgniter CMS, ExpressionEngine Alternative';
$vars['meta_description'] = 'FUEL CMS is a CodeIgniter-based easy to use Content Management System.';
$vars['js'] = '';
$vars['css'] = '';
$vars['body_class'] = $CI->uri->segment(1).' '.$CI->uri->segment(2);

// page specific variables
$pages = array();
?>