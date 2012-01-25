<?php
require_once(FUEL_PATH.'/libraries/Fuel_base_controller.php');

class Settings extends Fuel_base_controller {
	public $view_location = 'blog';
	public $nav_selected = 'blog/settings';
	
	function __construct()
	{
		parent::__construct();
		$this->config->module_load('blog', 'blog');
		$this->_validate_user('blog/settings');
	}
	
	function index()
	{
		$this->load->module_model(BLOG_FOLDER, 'blog_settings_model');
		$this->load->module_library(BLOG_FOLDER, 'fuel_blog');
		$this->js_controller_params['method'] = 'add_edit';
		
		$field_values = $this->blog_settings_model->find_all_array_assoc('name');
		
		if (!empty($_POST['settings']))
		{
			// format data for saving
			$save = array();
			foreach($field_values as $field => $value)
			{
				$settings = $this->input->post('settings', TRUE);
				$val = (isset($settings[$field])) ? $settings[$field] : '';
				$save[] = array('name' => $field, 'value' => trim($val));
			}
			$this->fuel_blog->remove_cache();
			$this->blog_settings_model->save($save);
			$this->session->set_flashdata('success', lang('data_saved'));
			redirect($this->uri->uri_string());
			
		}
		
		$this->load->library('form_builder');
		
		$fields = array();
		$fields['title'] = array();
		$fields['description'] = array('size' => '80');
		$fields['uri'] = array('value' => 'blog');
		$fields['theme_path'] = array('value' => 'default');
		$fields['theme_layout'] = array('value' => 'blog', 'size' => '20');
		$fields['theme_module'] = array('value' => 'blog', 'size' => '20');
		$fields['use_cache'] = array('type' => 'checkbox', 'value' => '1');
		$fields['allow_comments'] = array('type' => 'checkbox', 'value' => '1');
		$fields['monitor_comments'] = array('type' => 'checkbox', 'value' => '1');
		$fields['use_captchas'] = array('type' => 'checkbox', 'value' => '1');
		$fields['save_spam'] = array('type' => 'checkbox', 'value' => '1');
		$fields['akismet_api_key'] = array('value' => '', 'size' => '80');
		
		$fields['multiple_comment_submission_time_limit'] = array('size' => '5', 'after_html' => lang('form_label_multiple_comment_submission_time_limit_after_html'));
		$fields['comments_time_limit'] = array('size' => '5', 'after_html' => lang('form_label_comments_time_limit_after_html'));
		$fields['cache_ttl'] = array('value' => 3600, 'size' => 5);
		$fields['asset_upload_path'] = array('default' => 'images/blog/');
		$fields['per_page'] = array('value' => 1, 'size' => 3);


	//	$this->form_builder->id = 'form';
		$this->form_builder->label_layout = 'left';
		$this->form_builder->form->validator = &$this->blog_settings_model->get_validation();
		//$this->form_builder->submit_value = null;
		$this->form_builder->use_form_tag = FALSE;
		$this->form_builder->set_fields($fields);
		$this->form_builder->display_errors = FALSE;
		$this->form_builder->name_array = 'settings';
		$this->form_builder->submit_value = 'Save';
		$this->form_builder->set_field_values($field_values);
		
		
		$vars = array();
		$vars['form'] = $this->form_builder->render();
		$vars['warn_using_config'] = !$this->config->item('blog_use_db_table_settings');
		
		$this->_render('settings', $vars);

	}

}