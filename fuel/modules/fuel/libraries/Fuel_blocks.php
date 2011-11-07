<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * FUEL CMS
 * http://www.getfuelcms.com
 *
 * An open source Content Management System based on the 
 * Codeigniter framework (http://codeigniter.com)
 *
 * @package		FUEL CMS
 * @author		David McReynolds @ Daylight Studio
 * @copyright	Copyright (c) 2011, Run for Daylight LLC.
 * @license		http://www.getfuelcms.com/user_guide/general/license
 * @link		http://www.getfuelcms.com
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * FUEL blocks object
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/libraries/fuel_blocks
 */

// --------------------------------------------------------------------

class Fuel_blocks extends Fuel_module {
	

	function upload($block, $sanitize = TRUE)
	{
		$this->CI->load->helper('file');
		
		$model = $this->model();
		if (!is_numeric($block))
		{
			$block_data = $model->find_by_name($block, 'array');
		}
		else
		{
			$block_data = $model->find_by_key($block, 'array');
		}
		
		$this->load->helper('file');
		$view_twin = APPPATH.'views/_blocks/'.$block_data['name'].EXT;

		if (file_exists($view_twin))
		{
			$view_twin_info = get_file_info($view_twin);
			
			$tz = date('T');
			if ($view_twin_info['date'] > strtotime($block_data['last_modified'].' '.$tz) OR
				$block_data['last_modified'] == $block_data['date_added'])
			{
				// must have content in order to not return error
				$output = file_get_contents($view_twin);
				
				// replace PHP tags with template tags... comments are replaced because of xss_clean()
				if ($sanitize)
				{
					$output = php_to_template_syntax($output);
				}
			}
		}
		return $output;
	}
	
	

}

/* End of file Fuel_blocks.php */
/* Location: ./modules/fuel/libraries/Fuel_blocks.php */