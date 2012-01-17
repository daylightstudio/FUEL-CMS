<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	http://codeigniter.com/user_guide/general/hooks.html
|
*/

$hook['after_save_module'] = array(
								'class'    => 'Fuel_search',
								'function' => 'after_save_hook',
								'filename' => 'Fuel_search.php',
								'filepath' => 'libraries',
								'params'   => array(),
								'module' => 'search',
								);
$hook['after_delete_module'] = array(
								'class'    => 'Fuel_search',
								'function' => 'after_delete_hook',
								'filename' => 'Fuel_search.php',
								'filepath' => 'libraries',
								'params'   => array(),
								'module' => 'search',
								);
/* End of file hooks.php */
/* Location: ./application/config/hooks.php */