#!/usr/bin/php
<?php
/* original idea from here:
By Jonathon Hill (http://jonathonhill.net)
| CodeIgniter forum member "compwright" (http://codeigniter.com/forums/member/60942/)
*/
$CI = './../../../../index.php';
$script = array_shift($argv);
$cmdline = implode(' ', $argv);
$usage = "Usage: Controller_runner.php --run=/controller/method [--ci-path][-CI] [--domain][-D]  [--port][-P]  [--post][-X]\n\n";
$required = array('--run' => FALSE);
$_SERVER['SERVER_NAME'] = 'localhost';
$_SERVER['SERVER_PORT'] = 80;
foreach($argv as $arg)
{
	list($param, $value) = explode('=', $arg);
	switch($param)
	{
		case '--run':
			// Simulate an HTTP request
			$_SERVER['PATH_INFO'] = $value;
			$_SERVER['REQUEST_URI'] = $value;
			$required['--run'] = TRUE;
			break;
		case '-CI': case '--ci-path':
			$CI = $value;
			break;
		case '-D': case '--domain':
			$_SERVER['SERVER_NAME'] = $value;
			break;
		case '-P': case '--port':
			$_SERVER['SERVER_PORT'] = $value;
			break;
		case '-X': case '--post':
			$_POST = unserialize(base64_decode($value));
			break;
		default:
			die($usage);
	}
}

foreach($required as $arg => $present)
{
	if(!$present) die($usage);
}
// Run CI
ob_start();
//chdir(dirname(realpath($CI)));
require($CI);
$output = ob_get_contents();
while(@ob_end_flush());

?>