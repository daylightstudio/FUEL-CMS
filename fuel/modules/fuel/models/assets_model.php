<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

// not pulling from the database so just extend the normal model
require_once(APPPATH.'libraries/Validator.php');


class Assets_model extends CI_Model {
	
	public $filters = array('group_id' => 'images');
	public $filter_value = null;
	public $key_field = 'id';
	
	protected $_dirs = array('images', 'pdf');
	protected $_dir_filetypes = array('images' => 'jpg|jpe|jpeg|png|gif', 'pdf' => 'pdf');
	protected $validator = NULL; // the validator object

	private $_encoded = FALSE;

	function __construct()
	{
		parent::__construct();
		$CI =& get_instance();
		$CI->load->helper('directory');
		$this->_dirs = list_directories($CI->asset->assets_server_path, $CI->config->item('assets_excluded_dirs', 'fuel'), FALSE, TRUE);
		$this->_dir_filetypes = $CI->config->item('editable_asset_filetypes', 'fuel');
		$CI->load->helper('directory');
		$CI->load->helper('file');
		
		$this->validator = new Validator();
		$this->validator->register_to_global_errors = FALSE;
		
	}
	
	function add_filters($filters){
		if (empty($this->filters))
		{
			$this->filters = $filters;
		}
		else
		{
			$this->filters = array_merge($this->filters, $filters);
		}
	}
	
	function list_items($limit = null, $offset = 0, $col = 'name', $order = 'asc')
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

		$asset_dir = $this->get_dir($group_id);
		
		$assets_path = $CI->asset->assets_server_path.$asset_dir.DIRECTORY_SEPARATOR;
		
		$tmpfiles = directory_to_array($assets_path, TRUE, $CI->config->item('assets_excluded_dirs', 'fuel'), FALSE);
		
		$files = get_dir_file_info($assets_path, TRUE);

		$cnt = count($tmpfiles);
		$return = array();
		
		$asset_type_path = WEB_PATH.$CI->config->item('assets_path').$asset_dir.'/';
		
		//for ($i = $offset; $i < $cnt - 1; $i++)
		for ($i = 0; $i < $cnt; $i++)
		{
			if (!empty($tmpfiles[$i]) && !empty($files[$tmpfiles[$i]]))
			{
				$key = $tmpfiles[$i];
				if (empty($this->filters['name']) || 
					(!empty($this->filters['name']) && 
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
				$return[$key]['preview/kb'] = $return[$key]['preview/kb'].' kb <div class="img_crop"><a href="'.$asset_type_path.$return[$key]['name'].'" target="_blank"><img src="'.$asset_type_path.($return[$key]['name']).'" border="0"></a></div>';
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
	
	function list_items_total()
	{
		return count($this->list_items());
	}
	
	function get_dir($dir)
	{
		$dirs = (array) $this->get_dirs();
		return (isset($dirs[$dir])) ? $dirs[$dir] : $this->get_image_dir();
	}
	
	function get_dirs()
	{	
		$dirs = array();
		if (!empty($this->_dirs))
		{
			$dirs = array_combine($this->_dirs, $this->_dirs);
		}
		ksort($dirs);
		return $dirs;
	}
	function get_image_dir()
	{
		$CI =& get_instance();
		$editable_filetypes = $CI->config->item('editable_asset_filetypes', 'fuel');
		foreach($editable_filetypes as $folder => $types)
		{
			if (preg_match('#(jp(e){0,1}g|gif|png)#i', $types))
			{
				return $folder;
			}
		}
		return key(reset($editable_filetypes));
		
	}

	function get_dir_filetypes()
	{
		return $this->_dir_filetypes;
	}

	function get_dir_filetype($filetype)
	{
		return (isset($this->_dir_filetypes[$filetype])) ? $this->_dir_filetypes[$filetype] : FALSE;
	}
	
	function find_by_key($file)
	{
		$CI =& get_instance();
		$asset_path = WEB_ROOT.$CI->config->item('assets_path').$file;
		$asset_path = str_replace('/', DIRECTORY_SEPARATOR, $asset_path); // for windows
		return get_file_info($asset_path);
	}
	
	function record_count($dir = 'images')
	{
		$CI =& get_instance();
		$assets_path = WEB_ROOT.$CI->config->item('assets_path').$dir.'/';
		$files = dir_files($assets_path, false, false);
		return count($files);
	}
	
	function delete($file)
	{
		$CI =& get_instance();
		$CI->load->helper('convert');
		
		// cleanup beginning slashes
		if (substr($file, 0, 1) == '/')
		{
			$file = substr($file, 1);
		}
		$filepath = WEB_ROOT.$CI->config->item('assets_path').$file;
		
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
			if (!in_array($parent_folder, $this->_get_excluded_asset_server_folders()))
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
	
	private function _get_excluded_asset_server_folders()
	{
		$CI =& get_instance();
		$excluded = array_merge($CI->config->item('assets_excluded_dirs', 'fuel'), $CI->asset->assets_folders);
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
	
	function key_field()
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
	
	function form_fields($values = array())
	{
		$CI =& get_instance();
		$fields = array();
		$editable_asset_types = $this->config->item('editable_asset_filetypes', 'fuel');
		$accepts = (!empty($editable_asset_types['media']) ? $editable_asset_types['media'] : 'jpg|jpe|jpeg|gif|png');
		$fields['userfile'] = array('label' => lang('form_label_file'), 'type' => 'file', 'class' => 'multifile', 'accept' => $accepts); // key is userfile because that is what CI looks for in Upload Class
		$fields['asset_folder'] = array('label' => lang('form_label_asset_folder'), 'type' => 'select', 'options' => $this->get_dirs(), 'comment' => lang('assets_comment_asset_folder'));
		$fields['userfile_filename'] = array('label' => lang('form_label_new_file_name'), 'comment' => lang('assets_comment_filename'));
		if ($CI->config->item('assets_allow_subfolder_creation', 'fuel'))
		{
			$fields['subfolder'] = array('label' => lang('form_label_subfolder'), 'comment' => lang('assets_comment_filename'));
		}
		$fields['overwrite'] = array('label' => lang('form_label_overwrite'), 'type' => 'checkbox', 'comment' => lang('assets_comment_overwrite'), 'checked' => true, 'value' => '1');

		$fields[lang('assets_heading_image_specific')] = array('type' => 'section');
		$fields['create_thumb'] = array('label' => lang('form_label_create_thumb'), 'type' => 'checkbox', 'comment' => lang('assets_comment_thumb'), 'value' => '1');
		$fields['maintain_ratio'] = array('label' => lang('form_label_maintain_ratio'), 'type' => 'checkbox', 'comment' => lang('assets_comment_aspect_ratio'), 'value' => '1');
		$fields['width'] = array('label' => lang('form_label_width'), 'comment' => lang('assets_comment_width'), 'size' => '3');
		$fields['height'] = array('label' => lang('form_label_height'), 'comment' => lang('assets_comment_height'), 'size' => '3');
		$fields['master_dimension'] = array('type' => 'select', 'label' => lang('form_label_master_dimension'), 'options' => array('auto' => 'auto', 'width' => 'width', 'height' => 'height'), 'comment' => lang('assets_comment_master_dim'));
		return $fields;
	}
	
	function on_after_post($values)
	{
		if (empty($values['userfile_path'])) return;

		// process any uploaded images files that have been specified
		foreach($_FILES as $file)
		{
			if (is_image_file($file['name']) AND 
					(!empty($values['userfile_create_thumb']) OR 
					!empty($values['userfile_maintain_ratio']) OR 
					!empty($values['userfile_width']) OR 
					!empty($values['userfile_height'])))
			{
	
				$CI =& get_instance();
				$CI->load->library('image_lib');

				$config['source_image']	= $values['userfile_path'].$file['name'];
				$config['create_thumb'] = $values['userfile_create_thumb'];
				$config['maintain_ratio'] = $values['userfile_maintain_ratio'];
				if (!empty($values['userfile_width'])) $config['width'] = $values['userfile_width'];
				if (!empty($values['userfile_height'])) $config['height'] = $values['userfile_height'];
				if (!empty($values['userfile_master_dim'])) $config['master_dim'] = $values['userfile_master_dim'];
				
				$this->image_lib->initialize($config); 

				if ( ! $CI->image_lib->resize())
				{
					$error = $CI->image_lib->display_errors();
					$CI->validator->catch_error($error);
				}
			}
		}
	}
}