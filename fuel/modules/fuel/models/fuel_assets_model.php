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
 */

// ------------------------------------------------------------------------

/**
 * Extends CI_Model
 *
 * <strong>Fuel_assets_model</strong> is used for managing asset data with the file system which includes retrieving and deleting images, pdfs, etc.
 * 
 * @package		FUEL CMS
 * @subpackage	Models
 * @category	Models
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/models/fuel_assets_model
 */

// not pulling from the database so just extend the normal model
require_once(FUEL_PATH.'libraries/Validator.php');

class Fuel_assets_model extends CI_Model {
	
	public $filters = array('group_id' => 'images'); // the default list view group value for filtering
	public $filter_value = null; // the default filter value
	public $key_field = 'id'; // placed here to prevent errors since we are not extended Base_module_model
	public $boolean_fields = array(); // placed here to prevent errors since we are not extended Base_module_model
	public $default_file_types = 'jpg|jpeg|jpe|png|gif|mov|mpeg|mp3|wav|aiff|pdf|css'; // default file types to associate to folders without associations
	
	protected $validator = NULL; // the validator object

	private $_encoded = FALSE;

	/**
	 * Constructor
	 *
	 * @access	public
	 * @return	void
	 */
	public function __construct()
	{
		parent::__construct();
		$CI =& get_instance();
		$CI->load->helper('directory');
		$CI->load->helper('file');
		
		$this->validator = new Validator();
		$this->validator->register_to_global_errors = FALSE;
		
	}
	
	// --------------------------------------------------------------------

	/**
	 * Adds search filters
	 *
	 * @access	public
	 * @param	array	Search filters
	 * @return	void
	 */	
	public function add_filters($filters)
	{
		if (empty($this->filters))
		{
			$this->filters = $filters;
		}
		else
		{
			$this->filters = array_merge($this->filters, $filters);
		}
	}
	
	// --------------------------------------------------------------------

	/**
	 * Returns an array of data used for the CMS's list view
	 *
	 * @access	public
	 * @param	int		limit
	 * @param	string	offset
	 * @param	string	column
	 * @param	string	order
	 * @return	array
	 */	
	public function list_items($limit = null, $offset = 0, $col = 'name', $order = 'asc')
	{
		$CI =& get_instance();
		$CI->load->helper('array');
		$CI->load->helper('convert');
		if (!isset($this->filters['group_id'])) return array();
		$group_id = $this->filters['group_id'];

		// not encoded yet... then decode
		if (!$this->_encoded)
		{
			$this->filters['group_id'] = uri_safe_encode($group_id); // to pass the current folder
			$this->_encoded = TRUE;
		}
		else
		{
			$group_id = uri_safe_decode($group_id);
		}

		$asset_dir = $CI->fuel->assets->dir($group_id);
		
		$assets_path = $CI->asset->assets_server_path().$asset_dir.DIRECTORY_SEPARATOR;
		
		$exclude = $CI->fuel->config('assets_excluded_dirs');
		$exclude[] = 'index.html';
		$tmpfiles = directory_to_array($assets_path, TRUE, $exclude, FALSE);
		
		$files = get_dir_file_info($assets_path, FALSE, TRUE);

		$cnt = count($tmpfiles);
		$return = array();
		
		$asset_type_path = WEB_PATH.$CI->config->item('assets_path').$asset_dir.'/';
		
		//for ($i = $offset; $i < $cnt - 1; $i++)
		for ($i = 0; $i < $cnt; $i++)
		{
			if (!empty($tmpfiles[$i]) AND !empty($files[$tmpfiles[$i]]))
			{
				$key = $tmpfiles[$i];
				if (empty($this->filters['name']) || 
					(!empty($this->filters['name']) AND 
					(strpos($files[$key]['name'], $this->filters['name']) !== FALSE || strpos($key, $this->filters['name']) !== FALSE)))
				{

					$file['id'] = uri_safe_encode(assets_server_to_web_path($files[$tmpfiles[$i]]['server_path'], TRUE));

					//$file['filename'] = $files[$key]['name'];
					$file['name'] = $key;
					$file['preview/kb'] = $files[$key]['size'];
					$file['link'] = NULL;
					$file['last_updated'] = english_date($files[$key]['date'], true);
					$return[] = $file;
				}
			}
			
		}
		
		$return = array_sorter($return, $col, $order, TRUE);
		
		// do a check for empty limit values to prevent issues found where an empty $limit value would return nothing in 5.16
		$return = (empty($limit)) ? array_slice($return, $offset) : array_slice($return, $offset, $limit);
		
		// after sorting add the images
		foreach ($return as $key => $val)
		{
			if (is_image_file($return[$key]['name']))
			{
				$return[$key]['preview/kb'] = $return[$key]['preview/kb'].' kb <div class="img_crop"><a href="'.$asset_type_path.$return[$key]['name'].'" target="_blank"><img src="'.$asset_type_path.($return[$key]['name']).'?c='.time().'" border="0"></a></div>';
				$return[$key]['link'] = '<a href="'.$asset_type_path.$return[$key]['name'].'" target="_blank">'.$asset_dir.'/'.$return[$key]['name'].'</a>';
				
			}
			else
			{
				$return[$key]['preview/kb'] = $return[$key]['preview/kb'];
				$return[$key]['link'] = '<a href="'.$asset_type_path.$return[$key]['name'].'" target="_blank">'.$asset_dir.'/'.$return[$key]['name'].'</a>';
			}
		}
		return $return;
	}
	

	// --------------------------------------------------------------------

	/**
	 * Returns the number of asset files
	 *
	 * @access	public
	 * @return	int
	 */	
	public function list_items_total()
	{
		return count($this->list_items());
	}

	// --------------------------------------------------------------------

	/**
	 * Returns an array of information about a particular asset file
	 *
	 * @access	public
	 * @param	string	An asset file
	 * @return	array
	 */	
	public function find_by_key($file)
	{
		$file = $this->get_file($file);

		$CI =& get_instance();
		$assets_folder = WEB_ROOT.$CI->config->item('assets_path');
		
		// normalize file path
		$file = trim(str_replace($assets_folder, '', $file), '/');
		
		$asset_path = $assets_folder.$file;
		$asset_path = str_replace('/', DIRECTORY_SEPARATOR, $asset_path); // for windows
		return get_file_info($asset_path);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Returns the name of the file and will decode it if necessary
	 *
	 * @access	public
	 * @param	string	An asset file
	 * @return	string
	 */	
	public function get_file($file)
	{
		// if no extension is provided, then we determine that it needs to be decoded
		if (strpos($file, '.') === FALSE)
		{
			$file = uri_safe_decode($file);
		}
		return $file;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Returns the number of assets in a given folder 
	 *
	 * @access	public
	 * @param	string	An asset folder
	 * @return	int
	 */	
	public function record_count($dir = 'images')
	{
		$CI =& get_instance();
		$assets_path = WEB_ROOT.$CI->config->item('assets_path').$dir.'/';
		$files = dir_files($assets_path, false, false);
		return count($files);
	}
	
	// --------------------------------------------------------------------

	/**
	 * Deletes an asset
	 *
	 * @access	public
	 * @param	string	An asset file to delete
	 * @return	string
	 */	
	public function delete($file)
	{
		$CI =& get_instance();

		if (is_array($file))
		{
			$valid = TRUE;
			foreach($file as $f)
			{
				if (!$this->_delete($f))
				{
					$valid = FALSE;
				}
			}
			return $valid;
		}
		else
		{
			return $this->_delete($file);
		}
	}
	
	// --------------------------------------------------------------------

	/**
	 * Deletes an asset and will perform any necessary folder cleanup
	 *
	 * @access	protected
	 * @param	string	An asset file to delete
	 * @return	string
	 */	
	protected function _delete($file)
	{
		$CI =& get_instance();

		$file = $this->get_file($file);

		$deleted = FALSE;
		
		// cleanup beginning slashes
		$assets_folder = WEB_ROOT.$CI->config->item('assets_path');
		$file = trim(str_replace($assets_folder, '', $file), '/');
		
		// normalize file path
		$filepath = $assets_folder.$file;
		$parent_folder = dirname($filepath).'/';

		if (file_exists($filepath))
		{
			$deleted = unlink($filepath);
		}
		
		$max_depth = 5;
		$i = 0;
		$end = FALSE;
		while(!$end)
		{
			// if it is the last file in a subfolder (not one of the main asset folders), then we recursively remove the folder to clean things up
			$excluded_asset_folders = $CI->fuel->assets->excluded_asset_server_folders();
			if (!in_array($parent_folder, $excluded_asset_folders))
			{
				$dir_files = directory_to_array($parent_folder);

				// if empty, now remove
				if (empty($dir_files))
				{
					@rmdir($parent_folder);
				}
				else
				{
					$end = TRUE;
				}
			}
			else
			{
				$end = TRUE;
			}
			$parent_folder = dirname($parent_folder).'/';
			
		}
		$i++;
		if ($max_depth == $i) $end = TRUE;
		return $deleted;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Returns the field to be used as the key for a record
	 *
	 * @access	public
	 * @return	string
	 */	
	public function key_field()
	{
		return $this->key_field;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Get the validation object
	 *
	 * @access	public
	 * @return	object
	 */	
	public function &get_validation()
	{
		return $this->validator;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Return validation errors
	 *
	 * @access	public
	 * @return	array
	 */	
	public function get_errors()
	{
		return $this->validator->get_errors();
	}
	
	
	// --------------------------------------------------------------------

	/**
	 * Returns an array of form field parameters that can be used by Form_builder
	 *
	 * @access	public
	 * @param	array 	An array of values to be passed to the form fields
	 * @return	array
	 */	
	public function form_fields($values = array())
	{
		$CI =& get_instance();
		$fields = array();
		$editable_asset_types = $CI->fuel->config('editable_asset_filetypes');
		$accepts = (!empty($editable_asset_types['assets']) ? $editable_asset_types['assets'] : $this->default_file_types);
		$fields[lang('assets_heading_general')] = array('type' => 'fieldset', 'class' => 'tab');
		$fields['userfile'] = array('label' => lang('form_label_file'), 'type' => 'file', 'class' => 'multifile', 'accept' => $accepts); // key is userfile because that is what CI looks for in Upload Class
		$fields['asset_folder'] = array('label' => lang('form_label_asset_folder'), 'type' => 'select', 'options' => $CI->fuel->assets->dirs(), 'comment' => lang('assets_comment_asset_folder'));
		$fields['userfile_file_name'] = array('label' => lang('form_label_new_file_name'), 'comment' => lang('assets_comment_filename'));
		if ($CI->config->item('assets_allow_subfolder_creation', 'fuel'))
		{
			$fields['subfolder'] = array('label' => lang('form_label_subfolder'), 'comment' => lang('assets_comment_filename'));
		}
		$fields['overwrite'] = array('label' => lang('form_label_overwrite'), 'type' => 'checkbox', 'comment' => lang('assets_comment_overwrite'), 'checked' => true, 'value' => '1');
		$fields['unzip'] = array('type' => 'checkbox', 'label' => lang('form_label_unzip'), 'comment' => lang('assets_comment_unzip'), 'value' => 1);

		$fields[lang('assets_heading_image_specific')] = array('type' => 'fieldset', 'class' => 'tab');
		$fields['create_thumb'] = array('label' => lang('form_label_create_thumb'), 'type' => 'checkbox', 'comment' => lang('assets_comment_thumb'), 'value' => '1');
		
		$resize_type_options = array('maintain_ratio' => lang('form_label_maintain_ratio'), 'resize_and_crop' => lang('form_label_resize_and_crop'));
		$fields['resize_method'] =  array('label' => lang('form_label_resize_method'), 'type' => 'select', 'options' => $resize_type_options, 'comment' => lang('assets_comment_resize_method'));
		$fields['width'] = array('label' => lang('form_label_width'), 'comment' => lang('assets_comment_width'), 'size' => '3');
		$fields['height'] = array('label' => lang('form_label_height'), 'comment' => lang('assets_comment_height'), 'size' => '3');
		$fields['master_dim'] = array('type' => 'select', 'label' => lang('form_label_master_dim'), 'options' => array('auto' => 'auto', 'width' => 'width', 'height' => 'height'), 'comment' => lang('assets_comment_master_dim'));
		$fields['uploaded_file_name'] = array('type' => 'hidden');
		
		return $fields;
	}
	
		
	/**
	 * Placeholder function (not used)
	 *
	 * @access	public
	 * @return	void
	 */
	public function on_before_post()
	{
		
	}

		
	/**
	 * Placeholder function (not used)
	 *
	 * @access	public
	 * @param   array Posted values
	 * @return	void
	 */
	public function on_after_post($values)
	{
	}

		
	/**
	 * Displays the most recently uplloaded 
	 *
	 * @access	public
	 * @param	array View variable data (optional)
	 * @return	mixed Can be an array of items or a string value
	 */
	public function related_items($params)
	{
		$CI =& get_instance();
		$uploaded_post = $CI->session->flashdata('uploaded_post');
		if (!empty($uploaded_post['uploaded_file_webpath']) AND is_image_file($uploaded_post['uploaded_file_webpath']))
		{
			$img = '<a href="'.$uploaded_post['uploaded_file_webpath'].'" target="_blank"><img src="'.$uploaded_post['uploaded_file_webpath'].'?c='.time().'" alt="" style="max-width: 100%;" /></a>';
			return $img;
		}
		return '';
	}
}