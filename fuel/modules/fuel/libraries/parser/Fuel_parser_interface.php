<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * FUEL CMS
 * http://www.getfuelcms.com
 *
 * An open source Content Management System based on the 
 * Codeigniter framework (http://codeigniter.com)
 *
 * @package		FUEL CMS
 * @author		David McReynolds @ Daylight Studio
 * @copyright	Copyright (c) 2018, Daylight Studio LLC.
 * @license		http://docs.getfuelcms.com/general/license
 * @link		http://www.getfuelcms.com
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
