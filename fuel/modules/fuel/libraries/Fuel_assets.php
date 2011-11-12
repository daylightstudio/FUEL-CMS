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
 * FUEL assets object
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/libraries/fuel_assets
 */

// --------------------------------------------------------------------

class Fuel_assets extends Fuel_base_library {
	
	public $name;
	public $path;
	public $create_thumb;
	public $maintain_ratio;
	public $width;
	public $height;
	public $master_dim;
	
	protected $_data;
	
	function upload($params)
	{
		$this->CI->load->library('upload');
		$this->CI->load->library('image_lib');

		// set any parameters passed
		$this->set_params($params);
		
		if (empty($params['upload_path'])) return;
		
		// normalize the $_FILES array so the Upload class can handle it
		$i = 0;
		foreach($_FILES as $key => $file)
		{
			if (is_array($file['tmp_name']))
			{
				foreach($file as $f)
				{
					if (!empty($f['tmp_name']))
					{
						$_FILES['file___'.$i]['name'] = $f['name'];
						$_FILES['file___'.$i]['type'] = $f['type'];
						$_FILES['file___'.$i]['tmp_name'] = $f['tmp_name'];
						$_FILES['file___'.$i]['error'] = $f['error'];
						$_FILES['file___'.$i]['size'] = $f['size'];
					}
					$i++;
				}
			}
		}
		
		// upload the file
		foreach($_FILES as $file)
		{
			$params['max_size'] = $this->fuel->config('assets_upload_max_size');
			$params['max_width'] = $this->fuel->config('assets_upload_max_width');
			$params['max_height'] = $this->fuel->config('assets_upload_max_height');
			
			// a little heavy handed here to do this... but it removes spaces and possible problematic characters... as well as helps normalized file names
			$params['file_name'] = url_title($file['name'], 'underscore', TRUE);
			
			// make directory if it doesn't exist and subfolder creation is allowed'
			if (!is_dir($params['upload_path']) AND $this->fuel->config('assets_allow_subfolder_creation'))
			{
				// will recursively create folder
				@mkdir($params['upload_path'], 0777, TRUE);
				if (!file_exists($params['upload_path']))
				{
					$this->_add_error(lang('upload_not_writable'));
				}
				else
				{
					chmodr($params['upload_path'], 0777);
				}
			} 
			
			$this->upload->initialize($params);
			
			if (!$this->upload->do_upload($file['name']))
			{
				$valid = FALSE;
				$this->_add_error($this->upload->display_errors('', ''));
			}
			
			
		}
		
		
		// process any uploaded images files that have been specified
		foreach($_FILES as $file)
		{
			if (is_image_file($file['name']) AND 
					(!empty($params['create_thumb']) OR 
					!empty($params['maintain_ratio']) OR 
					!empty($params['width']) OR 
					!empty($params['height'])))
			{
	

				$config['max_size']	= $this->fuel->config('assets_upload_max_size');
				$config['max_width']  = $this->fuel->config('assets_upload_max_width');
				$config['max_height']  = $this->fuel->config('assets_upload_max_height');
				
				// if a new file is uploaded, then we need to process it and associate nice_name and file type to it
				if (!empty($_FILES['file___'.$i]))
				{

					$this->upload->initialize($config);
					
					if (!$this->upload->do_upload())
					{
						$valid = FALSE;
						$this->validator->catch_error($this->upload->display_errors('', ''), 'files[]');
					}
					else
					{
						$data = $this->upload->data();
				
				
				$config['upload_path'] = $params['upload_path'];
		
		
				if (is_image_file($file['name']) AND 
						(!empty($params['create_thumb']) OR 
						!empty($params['maintain_ratio']) OR 
						!empty($params['width']) OR 
						!empty($params['height'])))
				{
				$config['source_image']	= $params['path'].$file['name'];
				$config['create_thumb'] = $params['create_thumb'];
				$config['maintain_ratio'] = $params['maintain_ratio'];
				
				if (!empty($params['width'])) $config['width'] = $params['width'];
				if (!empty($params['height'])) $config['height'] = $params['height'];
				if (!empty($params['master_dim'])) $config['master_dim'] = $params['master_dim'];
				
				$this->CI->image_lib->initialize($config); 

				if (!$this->CI->image_lib->resize())
				{
					$error = $this->CI->image_lib->display_errors();
					$this->CI->validator->catch_error($error);
				}
			}
		}
	}

}

/* End of file Fuel_assets.php */
/* Location: ./modules/fuel/libraries/Fuel_assets.php */