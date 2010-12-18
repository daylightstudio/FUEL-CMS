<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once(FUEL_PATH.'models/base_module_model.php');

class Projects_model extends Base_module_model {

	public $filters = array('name');
	public $required = array('name');
	
	function __construct()
	{
		parent::__construct('projects'); // table name
	}

	function list_items($limit = null, $offset = null, $col = 'precedence', $order = 'desc')
	{
		$this->db->select('id, name, client, precedence, published', FALSE);
		$data = parent::list_items($limit, $offset, $col, $order);
		return $data;
	}
	
	function form_fields($values = array())
	{
		$fields = parent::form_fields($values);
		
		// to limit the image folder to just the projects folder for selection
		$fields['image']['class'] = 'asset_select images/projects';
		
		// put all project images into a projects subfolder
		$fields['image_upload']['upload_path'] = assets_server_path('projects', 'images');
		
		// fix the preview by adding projects in front of the image path since we are saving it in a subfolder
		if (!empty($values['image']))
		{
			$fields['image_upload']['before_html'] = '<div class="img_display"><img src="'.img_path('projects/'.$values['image']).'" style="float: right;"/></div>';
		}
		return $fields;
	}
	
	function on_before_validate($values)
	{
		// if slug is left blank, then we generate it based on the name
		if (empty($values['slug'])) $values['slug'] = url_title($values['name'], 'dash', TRUE);
		return $values;
	}
	
	function on_after_post($values)
	{
		$CI =& get_instance();
		$CI->load->library('image_lib');
		
		// create the thumbnail if an image is uploaded
		if (!empty($CI->upload))
		{
			$data = $CI->upload->data();
			if (!empty($data['full_path']))
			{
				$thumb_img = assets_server_path('projects/'.$this->thumb_name($data['file_name']), 'images');
				
				// resize to proper dimensions
				$config = array();
				$config['source_image'] = $data['full_path'];
				$config['create_thumb'] = FALSE;
				//$config['new_image'] = $thumb_img;
				$config['width'] = 240;
				$config['height'] = 140;
				$config['master_dim'] = 'auto';
				$config['maintain_ratio'] = TRUE;
				$CI->image_lib->clear();
				$CI->image_lib->initialize($config);
				if (!$CI->image_lib->resize())
				{
					$this->add_error($CI->image_lib->display_errors());
				}
				
				// create thumb
				$config = array();
				$config['source_image'] = $data['full_path'];
				$config['create_thumb'] = FALSE;
				$config['new_image'] = $thumb_img;
				$config['width'] = 100;
				$config['height'] = 80;
				$config['master_dim'] = 'auto';
				$config['maintain_ratio'] = TRUE;
				$CI->image_lib->clear();
				$CI->image_lib->initialize($config);
				if (!$CI->image_lib->resize())
				{
					$this->add_error($CI->image_lib->display_errors());
				}
			}
		}

		return $values;
	}
	
	// delete the images as well
	function on_before_delete($where)
	{
		$id = $this->_determine_key_field_value($where);
		$data = $this->find_by_key($id);
		$files[] = assets_server_path('projects/'.$data->image, 'images');
		$files[] = assets_server_path('projects/'.$this->thumb_name($data->image), 'images');
		foreach($files as $file)
		{
			if (file_exists($file))
			{
				@unlink($file);
			}
		}
	}
	
	function thumb_name($image)
	{
		return preg_replace('#(.+)(\.jpg|\.png)#U', '$1_thumb$2', $image);
	}

}

class Project_model extends Base_module_record {
	
	function get_url()
	{
		return site_url('showcase/project/'.$this->slug);
	}

	function get_image_path()
	{
		return img_path('projects/'.$this->image);
	}

	function get_thumb()
	{
		$thumb = $this->_parent_model->thumb_name($this->image);
		return img_path('projects/'.$thumb);
	}
	
}
?>