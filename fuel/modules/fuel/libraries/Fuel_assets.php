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
	
	protected $_data = array(); // holds uploaded file data
	protected $_dirs = array('images', 'pdf');
	protected $_dir_filetypes = array(
										'images' => 'jpg|jpe|jpeg|png|gif',
										'pdf' => 'pdf'
									);
	
	function __construct($params = array())
	{
		parent::__construct($params);
		$this->CI->load->helper('directory');
		$this->CI->load->helper('file');
		$this->CI->load->library('upload');
		$this->CI->lang->load('upload'); // loaded here as well so we can use some of the lang messages
		
	}
	
	function initialize($params)
	{
		parent::initialize($params);
		$this->normalize_files_array();
		$this->_dir_filetypes = $this->CI->fuel->config('editable_asset_filetypes');
		$this->_dirs = list_directories($this->CI->asset->assets_server_path(), $this->CI->fuel->config('assets_excluded_dirs'), FALSE, TRUE);
	}
	
	
	
	function upload($params = array())
	{
		$this->CI->load->library('upload');
		$this->CI->load->library('image_lib');

		$valid = array( 'upload_path' => '',
						'override_post_params' => FALSE,
						'file_name' => '',
						'xss_clean' => FALSE,
						
						// image manipulation parameters must all be FALSE or NULL or else it will trigger the image_lib image processing
						'create_thumb' => NULL,
						'maintain_ratio' => NULL, 
						'master_dim' => NULL, 
						'width' => NULL, 
						'height' => NULL, 
						'resize_and_crop' => FALSE, 
						);

		// set defaults
		foreach($valid as $param => $default)
		{
			$params[$param] = (isset($params[$param])) ? $params[$param] : $default;
		}
		
		// upload the file
		foreach($_FILES as $key => $file)
		{
			if ($file['error'] == 0)
			{
				$ext = end(explode('.', $file['name']));
				$field_name = current(explode('___', $key)); // extract out multi file upload infor
			
				// loop through all the allowed file types that are accepted for the asset directory
				foreach($this->dir_filetypes() as $dir => $types)
				{
					$file_types = explode('|', strtolower($types));
					if (in_array(strtolower($ext), $file_types))
					{
						$default_asset_dir = $dir;
						break;
					}
				}
			
				if (empty($default_asset_dir))
				{
					$this->_add_error(lang('upload_invalid_filetype'));
					return FALSE;
				}
			
				// get params based on the posted variables
				if (empty($params['override_post_params']))
				{
					$posted = array();
					foreach($valid as $param)
					{
						if (!empty($_POST[$param]))
						{
							$posted[$param] = $this->CI->input->post($param);
						}
					}
					$params = array_merge($params, $posted);
				}
				
				$asset_dir = trim(str_replace(assets_server_path(), '', $params['upload_path']), '/');

				// set restrictions 
				$params['max_size'] = $this->fuel->config('assets_upload_max_size');
				$params['max_width'] = $this->fuel->config('assets_upload_max_width');
				$params['max_height'] = $this->fuel->config('assets_upload_max_height');
				if ($this->dir_filetype($asset_dir))
				{
					$params['allowed_types'] = $this->_dir_filetype($asset_dir);
				}
				else if ($this->dir_filetype($default_asset_dir))
				{
					$params['allowed_types'] = $this->_dir_filetype($default_asset_dir);
				}
				else
				{
					$params['allowed_types'] = 'jpg|jpeg|png|gif';
				}
				$params['remove_spaces'] = TRUE;
			
				// set the upload path
				$params['upload_path'] = (!empty($params[$field_name.'_path'])) ? $params[$field_name.'_path'] : assets_server_path().$asset_dir.'/';

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
			
				// set file name
				$params['file_name'] = (!empty($params[$field_name.'_filename'])) ? $params[$field_name.'_filename'] : url_title($file['name'], 'underscore', TRUE);
			
				if (!empty($params['xss_clean']))
				{
					$tmp_file = file_get_contents($file['tmp_name']);
					if (xss_clean($tmp_file, TRUE) === FALSE)
					{
						$this->_add_error(lang('upload_invalid_filetype'));
					}
				}
			
			
				// if errors, then we simply return FALSE at this point and don't continue any further processing'
				if ($this->has_errors())
				{
					return FALSE;
				}
			
				// UPLOAD!!!
				$this->CI->upload->initialize($params);
				if (!$this->CI->upload->do_upload($key))
				{
					$this->_add_error($this->CI->upload->display_errors('', ''));
				}
				else
				{
					$this->_data[] = $this->CI->upload->data();
				}
			}
		}
		
		// now loop through the uploaded files to do any further image processing
		foreach($this->_data as $file)
		{
			if (is_image_file($file['file_name']) AND 
					(isset($params['create_thumb']) OR 
					isset($params['maintain_ratio']) OR 
					!empty($params['width']) OR 
					!empty($params['height']) OR
					!empty($params['master_dim'])
					))
			{
				$params['source_image']	= $file['full_path'];
				
				$this->CI->image_lib->initialize($params); 
				
				// check for if they want just a resize or a resize AND crop
				if (!empty($params['resize_and_crop']))
				{
					$resize = $this->CI->image_lib->resize_and_crop();
				}
				else
				{
					$resize = $this->CI->image_lib->resize();
				}
				
				if (!$resize)
				{
					$this->_add_error($this->CI->image_lib->display_errors());
				}
			}
		}
		
		if ($this->has_errors())
		{
			return FALSE;
		}
		
		return TRUE;
	}
	
	function uploaded_data()
	{
		return $this->_data;
	}
	
	// function get_params_from_post($key)
	// {
	// 	$valid_params = array(
	// 							'file_name',
	// 							'width',
	// 							'height',
	// 							'path',
	// 							'overwrite',
	// 							'create_thumb',
	// 							'maintain_raio',
	// 							'master_dim',
	// 							'resize_and_crop',
	// 						);
	// 	$posted = array();
	// 	foreach($valid_params as $param)
	// 	{
	// 		if (!empty($_POST[$param]))
	// 		{
	// 			$posted[$param] = $this->CI->input->post($param);
	// 		}
	// 	}
	// 	return $posted;
	// 	
	// }
	// 
	function normalize_files_array()
	{
		$i = 0;

		// normalize the $_FILES array so the Upload class can handle it... in particular multiple files with the same name
		if (!empty($_FILES))
		{
			foreach($_FILES as $key => $file)
			{
				if (is_array($file['tmp_name']))
				{
					foreach($file as $f)
					{
						if (!empty($f['tmp_name']))
						{
							$_FILES[$key.'___'.$i]['name'] = $f['name'];
							$_FILES[$key.'___'.$i]['type'] = $f['type'];
							$_FILES[$key.'___'.$i]['tmp_name'] = $f['tmp_name'];
							$_FILES[$key.'___'.$i]['error'] = $f['error'];
							$_FILES[$key.'___'.$i]['size'] = $f['size'];
						}
						$i++;
					}
				}
			}
		}
	}
	
	function dir($dir)
	{
		$dirs = (array) $this->dirs();
		return (isset($dirs[$dir])) ? $dirs[$dir] : $this->image_dir();
	}
	
	function dirs()
	{	
		$dirs = array();
		if (!empty($this->_dirs))
		{
			$dirs = array_combine($this->_dirs, $this->_dirs);
		}
		ksort($dirs);
		return $dirs;
	}
	function image_dir()
	{
		$editable_filetypes = $this->CI->fuel->config('editable_asset_filetypes');
		foreach($editable_filetypes as $folder => $types)
		{
			if (preg_match('#(jp(e){0,1}g|gif|png)#i', $types))
			{
				return $folder;
			}
		}
		return key(reset($editable_filetypes));
		
	}

	function dir_filetypes()
	{
		return $this->_dir_filetypes;
	}

	function dir_filetype($filetype)
	{
		return (isset($this->_dir_filetypes[$filetype])) ? $this->_dir_filetypes[$filetype] : FALSE;
	}
	
	function excluded_asset_server_folders()
	{
		$excluded = array_merge($this->CI->fuel->config('assets_excluded_dirs'), $this->CI->asset->assets_folders);
		$return = array();
		foreach($excluded as $folder)
		{
			$folder_path = assets_server_path($folder);
			if (substr($folder_path, -1, 1) != '/')
			{
				$folder_path = $folder_path.'/';
			}
			
			if (!in_array($folder_path, $return))
			{
				$return[] = $folder_path;
			}
		}
		return $return;
	}
	
	
}

/* End of file Fuel_assets.php */
/* Location: ./modules/fuel/libraries/Fuel_assets.php */