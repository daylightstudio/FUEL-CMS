<?php
require_once('module.php');

class Blocks extends Module {
	
	function __construct()
	{
		parent::__construct();
	}

	function _form($id = NULL, $fields = NULL, $log_to_recent = TRUE, $display_normal_submit_cancel = TRUE)
	{
		$vars = parent::_form($id, $fields, $log_to_recent, $display_normal_submit_cancel);
		$saved = $vars['data'];
		$import_view = FALSE;
		$warning_window = '';
		if (!empty($saved['name'])) {
			$view_twin = APPPATH.'views/_blocks/'.$saved['name'].EXT;
			if (file_exists($view_twin))
			{
				$view_twin_info = get_file_info($view_twin);
				if (!empty($saved)) 
				{
					$tz = date('T');
					if ($view_twin_info['date'] > strtotime($saved['last_modified'].' '.$tz) OR 
						$saved['last_modified'] == $saved['date_added'])
					{
						$warning_window = lang('blocks_updated_view', $view_twin);
					}
				}
			}
		}
		$vars['warning_window'] = $warning_window;
		return $vars;
	}
	
	function import_view_cancel()
	{
		if ($this->input->post('id')){

			// don't need to pass anything because it will automatically update last_modified'
			$save['id'] = $this->input->post('id');
			$save['name'] = $this->input->post('name');
			$save['last_modified'] = datetime_now();

			if ($this->model->save($save))
			{
				$this->output->set_output('success');
				return;
			}
		}
		$this->output->set_output('error');
	}
	
	function import_view()
	{
		$out = 'error';
		if ($this->input->post('id')){
			$block_data = $this->model->find_by_key($this->input->post('id'), 'array');
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
					$out = file_get_contents($view_twin);
					
					// replace PHP tags with template tags... comments are replaced because of xss_clean()
					if ($this->sanitize_input)
					{
						$out = php_to_template_syntax($out);
					}
				}
			}
		}
		$this->output->set_output($out);
	}
	
	function upload()
	{
		$this->load->helper('file');
		$this->load->helper('security');
		$this->load->library('form_builder');
		
		$this->js_controller_params['method'] = 'upload';
		
		if (!empty($_POST))
		{
			if (!empty($_FILES['file']['name']))
			{
				
				$error = FALSE;
				$file_info = $_FILES['file'];
				
				// read in the file so we can filter it
				$file = read_file($file_info['tmp_name']);
				
				// sanitize the file before saving
				$file = $this->_sanitize($file);
				$id =  $this->input->post('id', TRUE);

				$save['id'] = $id;
				$save['view'] = $file;
				
				if (!$this->model->save($save))
				{
					add_error(lang('error_upload'));
				}
				else
				{
					// change list view page state to show the selected group id
					$this->session->set_flashdata('success', lang('blocks_success_upload'));
					redirect(fuel_url('blocks/edit/'.$id));
				}
				
			}
			else if (!empty($_FILES['file']['error']))
			{
				add_error(lang('error_upload'));
			}
		}
		
		$fields = array();
		$blocks = $this->model->options_list('id', 'name', array('published' => 'yes'), 'name');
		
		$fields['id'] = array('label' => lang('form_label_name'), 'type' => 'select', 'options' => $blocks, 'class' => 'add_edit blocks');
		$fields['file'] = array('type' => 'file', 'accept' => '');
		$this->form_builder->hidden = array();
		$this->form_builder->set_fields($fields);
		$this->form_builder->set_field_values($_POST);
		$this->form_builder->submit_value = '';
		$this->form_builder->use_form_tag = FALSE;
		$vars['instructions'] = lang('blocks_upload_instructions');
		$vars['form'] = $this->form_builder->render();
		$this->_render('upload', $vars);
	}
}