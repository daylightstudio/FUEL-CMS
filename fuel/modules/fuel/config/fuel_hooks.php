<?php
/*
|--------------------------------------------------------------------------
| FUEL HOOKS
|--------------------------------------------------------------------------
|
| The following are hooks used by FUEL specifically. This file is included
| in the fuel/application/config/hooks.php file
|
*/

$hook['pre_controller'][] = array(
								'class'    => 'Fuel_hooks',
								'function' => 'pre_controller',
								'filename' => 'Fuel_hooks.php',
								'filepath' => 'hooks',
								'params'   => array(),
								'module' => FUEL_FOLDER,
								);

$hook['post_controller_constructor'][] = array(
								'class'    => 'Fuel_hooks',
								'function' => 'dev_password',
								'filename' => 'Fuel_hooks.php',
								'filepath' => 'hooks',
								'params'   => array(),
								'module' => FUEL_FOLDER,
								);

$hook['post_controller_constructor'][] = array(
								'class'    => 'Fuel_hooks',
								'function' => 'offline',
								'filename' => 'Fuel_hooks.php',
								'filepath' => 'hooks',
								'params'   => array(),
								'module' => FUEL_FOLDER,
								);

$hook['post_controller_constructor'][] = array(
								'class'    => 'Fuel_hooks',
								'function' => 'redirects',
								'filename' => 'Fuel_hooks.php',
								'filepath' => 'hooks',
								'params'   => array(),
								'module'   => FUEL_FOLDER,
								);

$hook['post_controller'][] = array(
								'class'    => 'Fuel_hooks',
								'function' => 'post_controller',
								'filename' => 'Fuel_hooks.php',
								'filepath' => 'hooks',
								'params'   => array(),
								'module' => FUEL_FOLDER,
								);

/* End of file fuel_hooks.php */
/* Location: ./modules/fuel/config/fuel_hooks.php */