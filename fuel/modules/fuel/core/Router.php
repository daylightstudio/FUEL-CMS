<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
/**
 * FUEL CMS
 * http://www.getfuelcms.com
 *
 * An open source Content Management System based on the 
 * Codeigniter framework (http://codeigniter.com)
 *
 * @package		FUEL CMS
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com
 */

// ------------------------------------------------------------------------

/**
 * Some additions to the Awesome Modular Extension Library mostly for Matchbox compatibility
 *
 * This Library overides the original MX Loader library
 *
 * @package		FUEL CMS
 * @subpackage	Third Party
 * @category	Third Party
 * @author		Changes by David McReynolds @ Daylight Studio. Original Author info is below
 */


/**
 * Modular Extensions - HMVC
 *
 * Adapted from the CodeIgniter Core Classes
 * @link	http://codeigniter.com
 *
 * Description:
 * This library extends the CodeIgniter router class.
 *
 * Install this file as application/third_party/MX/Router.php
 *
 * @copyright	Copyright (c) Wiredesignz 2010-11-12
 * @version 	5.3.5
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 **/

require_once(APPPATH.'third_party/MX/Router.php');

class Fuel_Router extends MX_Router
{
	private $module;
	private $_no_controller;
	
	public function fetch_module() {
		return $this->module;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Set the Route
	 *
	 * This function takes an array of URI segments as
	 * input, and sets the current class/method
	 *
	 * @access	private
	 * @param	array
	 * @param	bool
	 * @return	void
	 */
	public function _set_request($segments = array())
	{
		$segments = $this->_validate_request($segments);

		// <-- FUEL
		if ($this->_no_controller)
		{
			$fuel_path = explode('/', $this->routes['404_override']);
			$this->set_class($fuel_path[1]);
			$this->uri->rsegments = $segments;
			return;
		}
		// FUEL -->

		if (count($segments) == 0)
		{
			return $this->_set_default_controller();
		}

		$this->set_class($segments[0]);

		if (isset($segments[1]))
		{
			// A standard method request
			$this->set_method($segments[1]);
		}
		else
		{
			// This lets the "routed" segment array identify that the default
			// index method is being used.
			$segments[1] = 'index';
		}

		// Update our "routed" segment array to contain the segments.
		// Note: If there is no custom routing, this array will be
		// identical to $this->uri->segments
		$this->uri->rsegments = $segments;
	}
	
	
	public function _validate_request($segments) {		
		
		/* locate module controller */
		if ($located = $this->locate($segments)) return $located;
		
		/* use a default 404 controller */
		if (isset($this->routes['404']) AND $segments = explode('/', $this->routes['404'])) {
			if ($located = $this->locate($segments)) return $located;
		}	
			
		/* use a default 404_override controller CI 2.0 */
		// <-- FUEL changed
		if (isset($this->routes['404_override']) AND $segments404 = explode('/', $this->routes['404_override'])) {
			$this->_no_controller = TRUE;
			if ($located = $this->locate($segments404)) return $segments;
		}
		// FUEL -->
		
		/* no controller found */
		show_404();
	}
	
	/** Locate the controller **/
	public function locate($segments) {		
		
		$this->module = '';
		$this->directory = '';
		$ext = $this->config->item('controller_suffix').EXT;
		
		/* use module route if available */
		if (isset($segments[0]) AND $routes = Modules::parse_routes($segments[0], implode('/', $segments))) {
			$segments = $routes;
		}
	
		/* get the segments array elements */
		list($module, $directory, $controller) = array_pad($segments, 3, NULL);
		foreach (Modules::$locations as $location => $offset) {
			/* module exists? */
			if (is_dir($source = $location.$module.'/controllers/')) {
				
				$this->module = $module;
				$this->directory = $offset.$module.'/controllers/';
				
				/* module sub-controller exists? */
				if($directory AND is_file($source.$directory.$ext)) {
					return array_slice($segments, 1);
				}
					
				/* module sub-directory exists? */
				if($directory AND is_dir($module_subdir = $source.$directory.'/')) {
							
					$this->directory .= $directory.'/';

					/* module sub-directory controller exists? */
					if(is_file($module_subdir.$directory.$ext)) {
						return array_slice($segments, 1);
					}
				
					/* module sub-directory sub-controller exists? */
					if($controller AND is_file($module_subdir.$controller.$ext))	{
						return array_slice($segments, 2);
					}
				}
			
				/* module controller exists? */			
				if(is_file($source.$module.$ext)) {
					return $segments;
				}
			}
		}
		
		/* application controller exists? */			
		if(is_file(APPPATH.'controllers/'.$module.$ext)) {
			return $segments;
		}
		
		/* application sub-directory controller exists? */
		if(is_file(APPPATH.'controllers/'.$module.'/'.$directory.$ext)) {
			$this->directory = $module.'/';
			return array_slice($segments, 1);
		}

		/* application sub-directory default controller exists? */
		if(is_file(APPPATH.'controllers/'.$module.'/'.$this->default_controller.$ext)) {
			$this->directory = $module.'/';
			return array($this->default_controller);
		}
	}
	
	public function set_class($class) {
		$this->class = $class.$this->config->item('controller_suffix');
	}
}