<?php

/**
 * Dwoo base class for ZendFramework
 *
 * This software is provided 'as-is', without any express or implied warranty.
 * In no event will the authors be held liable for any damages arising from the
 * use of this software.
 *
 * @author     Jordi Boggiano <j.boggiano@seld.be>
 * @author     Marc Hodgins <mjh@hodginsmedia.com>
 * @copyright  Copyright (c) 2010, Jordi Boggiano
 * @license    http://dwoo.org/LICENSE   Modified BSD License
 * @link       http://dwoo.org/
 * @version    1.2.0
 * @date       2010-02-28
 * @package    Dwoo
 */
class Dwoo_Adapters_ZendFramework_Dwoo extends Dwoo_Core
{
	/**
	 * Redirects all unknown properties to plugin proxy
	 * to support $this->viewVariable from within templates
	 *
	 * @param string $name Property name
	 * @return mixed
	 */
	public function __get($name)
	{
		if (isset($this->getPluginProxy()->view->$name)) {
			return $this->getPluginProxy()->view->$name;
		}
		$trace = debug_backtrace();
		trigger_error('Undefined property via __get(): ' . $name .
					  ' in ' . $trace[0]['file'] .
					  ' on line ' . $trace[0]['line'], E_USER_NOTICE);
		return null;
	}
}