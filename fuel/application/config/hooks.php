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

/* Example of a module hook for a module named projects
$hook['before_create_projects'] = array(
								'class'    => 'Test_hooks',
								'function' => 'before_create_projects',
								'filename' => 'Test_hooks.php',
								'filepath' => 'hooks',
								'params'   => array(),
								'module' => 'app',
*/
// include hooks specific to FUEL
include(FUEL_PATH.'config/fuel_hooks.php');


/* End of file hooks.php */
/* Location: ./application/config/hooks.php */