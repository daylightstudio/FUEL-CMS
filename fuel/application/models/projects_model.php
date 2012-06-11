<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once(FUEL_PATH.'models/base_module_model.php');

class Projects_model extends Base_module_model {

	//public $filters = array('name');
	public $required = array('name', 'image_upload');
	//public $linked_fields = array('slug' => array('slug' => 'url_title'), 'client' => array('name' => 'strtoupper'));
	//public $linked_fields = array('slug' => array('name' => 'url_title'), 'client' => array('name' => 'strtoupper'));
	//public $linked_fields = array('slug' => 'name', 'client' => 'name');
	public $boolean_fields = array('published');
	public $belongs_to = array('relationships' => 'relationships_test');
	public $serialized_fields = array('serialized_test');
	public $default_serialization_method = 'json';
	public $filter_join = array(
								'name' => 'and',
								'language' => 'and',
								);
	
	
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
		$CI =& get_instance();
		$CI->load->module_model(BLOG_FOLDER, 'blog_categories_model');
	//	$CI->load->module_model(BLOG_FOLDER, 'blog_posts_to_categories_model');
		
		$fields = parent::form_fields($values);
		
		//$fields['name']['pre_process'] = array('func' => 'url_title', 'params' => array('underscore', TRUE));
		//$fields['name']['pre_process'] = array('url_title', 'dash', TRUE);
		//$fields['name']['post_process'] = array('url_title', 'underscore', TRUE);
		
		// to limit the image folder to just the projects folder for selection
		// $fields['image']['type'] = 'asset';
		$fields['multi'] = array('type'=> 'multi', 'sorting' => TRUE, 'options' => array('opt1' => 'opt1', 'opt2' => 'opt2'), 'mode' => 'select');
		$fields['image']['class'] = 'asset_select images/projects';
		$fields['image']['multiple'] = FALSE;
		$fields['image']['multiline'] = FALSE;
		$fields['image']['folder'] = 'images/blog';
		$fields['image']['overwrite'] = TRUE;
		$fields['image']['file_name'] = 'xxx';
		$fields['image']['height'] = '100';
		$fields['image']['width'] = '200';
		$fields['image']['master_dimension'] = 'width';
		//$fields['image']['hide_options'] = '1';
		$fields['image']['create_thumb'] = 'width';
		$fields['image']['subfolder'] = 'test';
		$fields['image']['maintain_ratio'] = TRUE;
		$fields['description']['class'] = 'wysiwyg';
		$fields['fieldset1'] = array('type' => 'fieldset', 'order' => 1, 'value' => 'Tab 1', 'class' => 'tab');
		$fields['fieldset2'] = array('type' => 'fieldset', 'order' => 10, 'value' => 'Tab 2', 'class' => 'tab');
		
		// put all project images into a projects subfolder
		$fields['image_upload']['upload_path'] = assets_server_path('projects', 'images');
		$fields['image_upload']['class'] = 'multifile';
		// fix the preview by adding projects in front of the image path since we are saving it in a subfolder
		if (!empty($values['image']))
		{
			$fields['image_upload']['before_html'] = '<div class="img_display"><img src="'.img_path('projects/'.$values['image']).'" style="float: right;"/></div>';
		}
		$fields['image_upload']['overwrite'] = TRUE;
		
		//test
		//$fields = array();
		//$fields['image'] = array('type' => 'asset');
		unset($fields['image_upload']);
		
		// $category_options = $CI->blog_categories_model->options_list('id', 'name', array('published' => 'yes'), 'name');
		// $category_values = (!empty($values['id'])) ? array_keys($CI->blog_posts_to_categories_model->find_all_array_assoc('category_id', array('post_id' => $values['id'], 'fuel_blog_categories.published' => 'yes'))) : array();
		// 
		// $fields['inline_edit'] = array('type' => 'inline_edit', 'module' => 'projects', 'options' => $category_options, 'multiple' => TRUE);

		//$fields['name'] = array();
		$fields['linked'] = array('type' => 'linked', 'linked_to' => array('name' => 'url_title'));
		$fields['fillin'] = array('type' => 'fillin', 'placeholder' => 'yo');
		$fields['datetime'] = array('type' => 'datetime', 'first_day' => 2, 'min_date' => '01-01-2012', 'max_date' => '31-12-2012');
		//$fields['time'] = array('type' => 'time');
		
		$fields['test_image'] = array('upload' => TRUE);
		$fields['numeric'] = array('type' => 'number', 'represents' => 'int|smallint|mediumint|bigint', 'negative' => TRUE, 'decimal' => TRUE);
		$fields['currency'] = array('type' => 'currency', 'negative' => TRUE, 'decimal' => '.');
		$fields['phone'] = array('type' => 'phone', 'required' => TRUE);
		$fields['file_test'] = array('type' => 'file', 'overwrite' => TRUE, 'display_overwrite' => TRUE, 'multiple' => TRUE);
		$fields['state'] = array('type' => 'state');
		$fields['list_items'] = array('type' => 'list_items');
		$fields['sections'] = array(
						'display_label' => FALSE,
						'type' => 'template', 
						'label' => 'Page sections', 
						'fields' => array(
								'layout' => array('type' => 'select', 'options' => array('img_right' => 'Image Right', 'img_left' => 'Image Left', 'img_right_50' => 'Image Right 50%', 'img_left_50' => 'Image Left 50%')),
								'title' => '',
								'action' => '',
								'content' => array('type' => 'textarea'),
								'image' => array('type' => 'asset', 'multiple' => FALSE, 'img_styles' => 'float: left; width: 100px;'),
								'images' => array('type' => 'template', 'repeatable' => TRUE, 'view' => '_admin/fields/images', 'limit' => 3, 'fields' => 
																		array('image' => array('type' => 'asset', 'multiple' => TRUE))
																		),
							),

						'view' => '_admin/fields/section', 
	//					'post_process' =>  array($custom_fields, 'section_post_process'), 
						//'js' => array('jquery.repeatable'),
						'class' => 'repeatable',
						'add_extra' => FALSE,
						'repeatable' => TRUE,
						//'fieldset' => 'Sections',
						);
		// temporary
		unset($fields['_published']);
		
		
		if (isset($values['id']))
		{
			$serialized_test = $this->find_by_key($values['id']);
		}
		return $fields;
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
		if (!empty($data))
		{
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
	}
	
	function thumb_name($image)
	{
		return preg_replace('#(.+)(\.jpg|\.png)#U', '$1_thumb$2', $image);
	}
	
	function on_before_save($values)
	{
		$values['serialized_test'] = array('This is a test', 'This is a #2');
		return $values;
	}
	
	function _common_query($display_unpublished_if_logged_in = FALSE)
	{
		parent::_common_query(TRUE);
	}
	
}

class Project_model extends Base_module_record {
	
	function get_url()
	{
		return site_url('showcase/project/'.$this->slug);
	}

	function get_image_path()
	{
		if (is_array($this->image))
		{
			return img_path('projects/'.$this->image[0]);
		}
		else
		{
			return img_path('projects/'.$this->image);
		}
	}

	function get_thumb()
	{
		$thumb = $this->_parent_model->thumb_name($this->image);
		return img_path('projects/'.$thumb);
	}
	
}
?>