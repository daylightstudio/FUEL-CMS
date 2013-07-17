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
 * @copyright	Copyright (c) 2013, Run for Daylight LLC.
 * @license		http://docs.getfuelcms.com/general/license
 * @link		http://www.getfuelcms.com
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * FUEL assets object
 *
 * Ths class is used to help manage asset files
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/libraries/fuel_assets
 */

// --------------------------------------------------------------------

class Fuel_assets extends Fuel_base_library {
	
	public $dir_filetypes = array('images' => 'jpg|jpe|jpeg|png|gif', 'pdf' => 'pdf'); // the default file types to associate with asset different asset folders
	protected $_data = array(); // holds uploaded file data
	protected $_dirs = array('images', 'pdf'); // the directories the CMS has access to upload files
	
	// --------------------------------------------------------------------
	
	/**
	 * Constructor
	 *
	 * Accepts an associative array as input, containing preferences (optional)
	 *
	 * @access	public
	 * @param	array	config preferences
	 * @return	void
	 */	
	public function __construct($params = array())
	{
		parent::__construct();
		$this->CI->load->helper('directory');
		$this->CI->load->helper('file');
		$this->CI->load->library('upload');
		$this->CI->lang->load('upload'); // loaded here as well so we can use some of the lang messages
		$this->CI->fuel->load_model('fuel_assets');
		$this->initialize($params);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Initialize the object and set object parameters
	 *
	 * Accepts an associative array as input, containing object preferences.
	 * Also will set the values in the parameters array as properties of this object
	 *
	 * @access	public
	 * @param	array	Array of initalization parameters  (optional)
	 * @return	void
	 */	
	public function initialize($params = array())
	{
		parent::initialize($params);
		$this->normalize_files_array();
		$this->dir_filetypes = $this->CI->fuel->config('editable_asset_filetypes');
		$this->_dirs = list_directories($this->CI->asset->assets_server_path(), $this->CI->fuel->config('assets_excluded_dirs'), FALSE, TRUE);
	}
	
	
	// --------------------------------------------------------------------
	
	/**
	 * Uploads the files in the $_FILES array
	 *
	 * Accepts an associative array as which can have the following parameters:
	 *
		<ul>
		 	<li><strong>upload_path</strong>: the server path to upload the file</li>
		 	<li><strong>override_post_params</strong>: determines whether post parameters (e.g. {$_FILES_key}_{param}) take precedence over parameters passed to the method</li>
		 	<li><strong>file_name</strong>: the name of the file to change to</li>
		 	<li><strong>overwrite</strong>: boolean value that determines whether to overwrite the file or create a new file which will append a number at the end</li>
		 	<li><strong>xss_clean</strong>: boolean value that determines whether to try and run the xss_clean function on any images that are uploaded</li>
		 	<li><strong>encrypt_name</strong>: boolean value that determines whether to encrypt the file name and make it unique</li>
		 	<li><strong>create_thumb</strong>: image specific boolean value that determines whether to create a thumbnail image based on the original uploaded image</li>
		 	<li><strong>thumb_marker</strong>: the default suffix to use on a generated thumbnail. The default is "_thumb"</li>
		 	<li><strong>maintain_ratio</strong>:image specific boolean value that determines whether to maintain the aspect ratio of the image upon resize</li>
		 	<li><strong>master_dim</strong>: image specific boolean value that determines which dimension should be used when resizing and maintaining the aspect ratio. Options are height, width, auto</li>
		 	<li><strong>width</strong>: sets the width of the uploaded image</li>
		 	<li><strong>height</strong>: sets the height of the uploaded image</li>
		 	<li><strong>resize_and_crop</strong>: image specific boolean value that determines whether to both resize and crop the image to the specified height and width</li>
		 </ul>
	 *
	 * @access	public
	 * @param	array	upload parameters (optional)
	 * @return	boolean
	 */	
	public function upload($params = array())
	{
		$this->CI->load->library('upload');
		$this->CI->load->library('image_lib');
		$this->CI->load->library('encrypt');

		$valid = array( 'upload_path' => '',
						'file_name' => '',
						'overwrite' => FALSE,
						'xss_clean' => FALSE,
						'encrypt_name' => FALSE,
						'unzip' => FALSE,
						'override_post_params' => FALSE,
						'posted' => $_POST,
						
						// image manipulation parameters must all be FALSE or NULL or else it will trigger the image_lib image processing
						'create_thumb' => NULL,
						'thumb_marker' => '_thumb',
						'maintain_ratio' => NULL, 
						'master_dim' => NULL, 
						'width' => NULL, 
						'height' => NULL, 
						'resize_and_crop' => FALSE, 
						);

		// used later
		$has_empty_filename = (empty($params['file_name'])) ? TRUE : FALSE;


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
			
				$non_multi_key = current(explode('___', $key));

				// get params based on the posted variables
				if (empty($params['override_post_params']))
				{
					$posted = array();
					foreach($valid as $param => $default)
					{
						if ($param != 'posted')
						{
							$input_key = $non_multi_key.'_'.$param;
							// decode encrypted file path values
							if (isset($params['posted'][$input_key]))
							{
								if ($input_key == $field_name.'_upload_path')
								{
									$posted['upload_path'] = $this->CI->encrypt->decode($params['posted'][$input_key]);
									foreach($params['posted'] as $k => $p)
									{
										if (is_string($p))
										{
											$posted['upload_path'] = str_replace('{'.$k.'}', $p ,$posted['upload_path']);	
										}
									}
								}
								else
								{
									$posted[$param] = $params['posted'][$input_key];
								}

								if ($param == 'file_name')
								{
									$has_empty_filename = FALSE;
								}
							}
						}
					}
					$params = array_merge($params, $posted);
					unset($params['override_post_params'], $params['posted']);
				}
				$asset_dir = trim(str_replace(assets_server_path(), '', $params['upload_path']), '/');

				// set restrictions 
				$params['max_size'] = $this->fuel->config('assets_upload_max_size');
				$params['max_width'] = $this->fuel->config('assets_upload_max_width');
				$params['max_height'] = $this->fuel->config('assets_upload_max_height');
				if ($this->dir_filetype($asset_dir))
				{
					$params['allowed_types'] = $this->dir_filetype($asset_dir);
				}
				else if ($this->dir_filetype($default_asset_dir))
				{
					$params['allowed_types'] = $this->dir_filetype($default_asset_dir);
					$asset_dir = $default_asset_dir;
				}
				else
				{
					$params['allowed_types'] = 'jpg|jpeg|png|gif';
					$asset_dir = $default_asset_dir;
				}
			
				// set the upload path
				if (empty($params['upload_path']))
				{
					$params['upload_path'] = (!empty($params[$field_name.'_path'])) ? $params[$field_name.'_path'] : assets_server_path().$asset_dir.'/';
				}

				$params['remove_spaces'] = TRUE;

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
				if ($has_empty_filename AND !empty($params[$field_name.'_file_name']))
				{
					$params['file_name'] = $params[$field_name.'_file_name'];
				}
				else if ($has_empty_filename)
				{
					$file_name = pathinfo($file['name'], PATHINFO_FILENAME);
					$params['file_name'] = url_title($file_name, 'underscore', FALSE);	
				}
			
				// set overwrite
				$params['overwrite'] = (is_true_val($params['overwrite']));

				if (is_image_file($params['file_name']) AND !empty($params['xss_clean']))
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
					$this->_data[$key] = $this->CI->upload->data();

					// set the file perm if necessary
					if (($this->fuel->config('set_upload_file_perms') !== FALSE) AND function_exists('chmod')
						AND is_integer($this->fuel->config('set_upload_file_perms')))
					{
						chmod($this->_data[$key]['full_path'], $this->fuel->config('set_upload_file_perms'));
					}
				}
				
			}
		}

		// set maintain ration if it is set to maintain_ratio
		if ((!empty($params['resize_method']) AND $params['resize_method'] == 'maintain_ratio'))
		{
			$params['maintain_ratio'] = TRUE;
		}

		// now loop through the uploaded files to do any further image processing
		foreach($this->_data as $file)
		{
			if (is_image_file($file['file_name']) AND 
					(isset($params['create_thumb']) OR 
					isset($params['maintain_ratio']) OR 
					!empty($params['width']) OR 
					!empty($params['height']) OR
					!empty($params['master_dim']) OR
					!empty($params['resize_and_crop']) OR
					!empty($params['resize_method'])
					))
			{

				$params['source_image']	= $file['full_path'];

				// to fix issues with resize and crop
				if (empty($params['create_thumb']))
				{
					$params['thumb_marker'] = '';
				}

				$this->CI->image_lib->initialize($params); 
				
				// check for if they want just a resize or a resize AND crop
				if (!empty($params['resize_and_crop']) OR (!empty($params['resize_method']) AND $params['resize_method'] == 'resize_and_crop'))
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
			
			// unzip any zip files
			else if (is_true_val($params['unzip']) AND $file['file_ext'] == '.zip')
			{
				// unzip the contents
				$this->unzip($file['full_path']);
				
				// then delete the zip file
				$this->delete($file['full_path']);
			}
		}
		
		if ($this->has_errors())
		{
			return FALSE;
		}
		
		return TRUE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the <a href="http://codeigniter.com/user_guide/libraries/file_uploading.html" target="_blank">uploaded file information</a>.
	 *
	 * @access	public
	 * @param	string	The uploaded $_FILE key value (optional)
	 * @return	array
	 */	
	public function uploaded_data($key = NULL)
	{
		if (isset($key))
		{
			return $this->_data[$key];
		}
		return $this->_data;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Normalizes the $_FILES array so that the <a href="http://codeigniter.com/user_guide/libraries/file_uploading.html" target="_blank">CI File Upload Class</a> will work correctly
	 *
	 * @access	public
	 * @return	void
	 */	
	public function normalize_files_array()
	{
		$i = 0;
		if (!empty($_FILES))
		{
			foreach($_FILES as $key => $file)
			{
				if (is_array($file['tmp_name']))
				{
					foreach($file as $k => $f)
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
	
	// --------------------------------------------------------------------
	
	/**
	 * Alias to the model's find_by_key method
	 *
	 * @access	public
	 * @param	string
	 * @return	object
	 */	
	public function file_info($file)
	{
		return $this->CI->fuel_assets_model->find_by_key($file);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Alias to the model's delete method
	 *
	 * @access	public
	 * @param	string
	 * @return	object
	 */	
	public function delete($file)
	{
		return $this->CI->fuel_assets_model->delete($file);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns the asset model
	 *
	 * @access	public
	 * @return	object
	 */	
	public function &model()
	{
		return $this->CI->fuel_assets_model;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns an asset folder name
	 *
	 * @access	public
	 * @param	string	The key associated with the asset folder (usually the same as the asset folder's name)
	 * @return	string
	 */	
	public function dir($dir)
	{
		$dirs = (array) $this->dirs();
		return (isset($dirs[$dir])) ? $dirs[$dir] : $this->image_dir();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns an array of all the asset folders
	 *
	 * @access	public
	 * @return	array
	 */	
	public function dirs()
	{	
		$dirs = array();
		if (!empty($this->_dirs))
		{
			$dirs = array_combine($this->_dirs, $this->_dirs);
		}
		ksort($dirs);
		return $dirs;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns an array of the main root asset folders
	 *
	 * @access	public
	 * @return	array
	 */	
	public function root_dirs($full_path = FALSE)
	{	
		$asset_server_path = $this->CI->asset->assets_server_path();
		$dirs = list_directories($asset_server_path, array(), $full_path, FALSE, FALSE);
		if (!empty($dirs))
		{
			$dirs = array_combine($dirs, $dirs);
		}

		ksort($dirs);
		return $dirs;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns the image asset folder name
	 *
	 * @access	public
	 * @return	string
	 */	
	public function image_dir()
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

	// --------------------------------------------------------------------
	
	/**
	 * Returns an array of pipe delimited file types associated with assets
	 *
	 * @access	public
	 * @param	boolean	returns an array instead of the pipe delimited list. Default is FALSE
	 * @return	mixed
	 */	
	public function dir_filetypes($return_array = FALSE)
	{
		if ($return_array)
		{
			$return = array();
			foreach($this->dir_filetypes as $key => $filetype)
			{
				$return[$key] = $this->dir_filetype($file_type, TRUE);
			}
			return $return;
		}
		return $this->dir_filetypes;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Returns a list of file types that are allowed for a particular folder
	 *
	 * @access	public
	 * @param	string	folder name
	 * @param	boolean	returns an array instead of the pipe delimited list. Default is FALSE
	 * @return	mixed
	 */	
	public function dir_filetype($filetype, $return_array = FALSE)
	{
		if (!isset($this->dir_filetypes[$filetype]))
		{
			return FALSE;
		}
		
		$filetypes = $this->dir_filetypes[$filetype];
		if ($return_array)
		{
			return explode('|', $filetypes);
		}
		return $filetypes;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Returns a list of file types that are allowed for a particular folder
	 *
	 * @access	public
	 * @param	string	folder name
	 * @param	boolean	will recursively look inside nested folders. Default is FALSE
	 * @param	boolean	will include the server path. Default is FALSE
	 * @return	mixed
	 */	
	public function dir_files($folder, $recursive = FALSE, $append_path = FALSE)
	{
		$dir = assets_server_path($folder);
		return directory_to_array($dir, $recursive, array(), $append_path);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * An array of folders excluded from being viewed
	 *
	 * @access	public
	 * @return	array
	 */	
	public function excluded_asset_server_folders()
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
	
	// --------------------------------------------------------------------
	
	/**
	 * Zips up an asset folder
	 *
	 * @access	public
	 * @param	mixed	If a string, it should be the folder path. If an array, it will be extracted and must at least contain a key of "dir" that specifies the folder path
	 * @param	string	File name
	 * @param	boolean	Determines whether to send download headers to the browser
	 * @param	boolean	Allow a file with the same name as the zip to be overwritten
	 * @return	string
	 */	
	public function zip($dir, $file_name = NULL, $download = FALSE, $allow_overwrite = TRUE)
	{
		$this->CI->load->library('zip');

		// first we clear out any of the data
		$this->CI->zip->clear_data();
		
		// if you decide to pass the parameters as an array instead
		if (is_array($dir))
		{
			extract($dir);
		}
		
		if (is_string($dir))
		{
			// if string is a directory path, then we read the directory... 
			// may be too presumptious but it's convenient'
			if (is_dir($dir))
			{
				$this->CI->zip->read_dir($dir);
			}
			else
			{
				$this->_add_error(lang('error_folder_not_writable', $dir));
				return FALSE;
			}
		}
		else
		{
			$this->_add_error(lang('error_folder_not_writable', $dir));
			return FALSE;
		}
		
		// check if folder is writable
		if (!is_really_writable($dir))
		{
			$this->_add_error(lang('error_folder_not_writable', $dir));
			return FALSE;
		}
		
		// the assets server path
		$assets_dir = assets_server_path();
		
		// normalize dir path
		$dir = trim(str_replace($assets_dir, '', $dir), '/');
		
		// add .zip extension so file_name is correct
		if (empty($file_name))
		{
			$file_name = end(explode('/', rtrim($dir, '/')));
		}
		
		$file_name = $file_name.'.zip';
		
		// path to download file
		$full_path = $assets_dir.$dir.'/'.$file_name;
		
		if (file_exists($full_path) AND !$allow_overwrite)
		{
			$this->_add_error(lang('error_file_already_exists', $full_path));
			return FALSE;
		}
		
		// write the zip file to a folder on your server. 
		$archived = $this->CI->zip->archive($full_path); 
		if (!$archived) 
		{
			$this->_add_error(lang('error_zip'));
			return FALSE;
		}
	
		// download the file to your desktop. 
		if ($download)
		{
			$this->CI->zip->download($file_name);
		}
		
		return $full_path;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Unzips a zip file
	 *
	 * @access	public
	 * @param	string	The path to the zip file to unzip relative to the assets folder
	 * @param	string	The destination path
	 * @return	array 	An array of unzipped file names
	 */	
	public function unzip($zip_file, $destination = NULL)
	{
		$this->CI->load->library('unzip');

		// the assets server path
		$assets_dir = assets_server_path();

		//Only take out these files, anything else is ignored
		$path_parts = pathinfo($zip_file);
		$filetype_folder = rtrim(str_replace($assets_dir, '', $path_parts['dirname']), '/');
		$allowed = $this->dir_filetype($filetype_folder);
		
		if (!$allowed)
		{
			$this->_add_error(lang('error_invalid_folder'));
			return FALSE;
		}
		
		// normalize file path
		$zip_file = rtrim(str_replace($assets_dir, '', $zip_file), '/');
		
		$this->CI->unzip->allow($allowed);
		
		$zip_file = $assets_dir.$zip_file;
		
		// or specify a destination directory
		$unzipped = $this->CI->unzip->extract($zip_file, $destination);
		$errors = $this->CI->unzip->error_string('', '');
		if (!$unzipped OR !empty($errors))
		{
			$this->_add_error($this->CI->unzip->error_string('', ' '));
			return FALSE;
		}
		return $unzipped;
	}
}


/* End of file Fuel_assets.php */
/* Location: ./modules/fuel/libraries/Fuel_assets.php */