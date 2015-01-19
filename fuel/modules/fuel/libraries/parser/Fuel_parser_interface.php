<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */
// ------------------------------------------------------------------------

/**
 * Fuel_parser_interface
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Parser
 * @author		David McReynolds
 */

interface Fuel_parser_interface
{
	public function parse($template, $data = array(), $return = FALSE);
	public function parse_string($string, $data = array(), $return = FALSE);
}
