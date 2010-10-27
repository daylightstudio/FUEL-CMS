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
$hook['pre_controller'] = array(
                                'class'    => 'Fuel_hooks',
                                'function' => 'pre_controller',
                                'filename' => 'Fuel_hooks.php',
                                'filepath' => 'hooks',
                                'params'   => array()
                                );
$hook['post_controller_constructor'] = array(
                                'class'    => 'Fuel_hooks',
                                'function' => 'dev_password',
                                'filename' => 'Fuel_hooks.php',
                                'filepath' => 'hooks',
                                'params'   => array()
                                );

$hook['post_controller'] = array(
                                'class'    => 'Fuel_hooks',
                                'function' => 'post_controller',
                                'filename' => 'Fuel_hooks.php',
                                'filepath' => 'hooks',
                                'params'   => array()
                                );

/* End of file hooks.php */
/* Location: ./application/config/hooks.php */