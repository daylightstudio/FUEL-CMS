<?php
require_once('module.php');

class Blocks extends Module {
	
	public function __construct()
	{
		parent::__construct();
	}

	public function _form_vars($id = NULL, $fields = NULL, $log_to_recent = TRUE, $display_normal_submit_cancel = TRUE)
	{
		$vars = parent::_form_vars($id, $fields, $log_to_recent, $display_normal_submit_cancel);
		$saved = $vars['data'];
		$import_view = FALSE;
		$warning_window = '';
		if (!empty($saved['name'])) {
			$view_twin = APPPATH.'views/_blocks/'.$saved['name'].EXT;
			if (file_exists($view_twin))
			{
				$this->load->helper('file');
				$view_twin_info = get_file_info($view_twin);
				if (!empty($saved)) 
				{
					$tz = date('T');
					if ($view_twin_info['date'] > strtotime($saved['last_modified'].' '.$tz) OR 
						$saved['last_modified'] == $saved['date_added'] AND !$this->input->get('uploaded'))
					{
						$warning_window = lang('blocks_updated_view', $view_twin);
					}
				}
			}
		}
		$vars['warning_window'] = $warning_window;
		return $vars;
	}
	
	public function import_view_cancel()
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
	
	public function import_view()
	{
		$out = 'error';
		if (!empty($_POST['id']))
		{
			$out = $this->fuel->blocks->import($this->input->post('id'), $this->sanitize_input);
		}
		$this->output->set_output($out);
	}
	
	public function upload($inline = FALSE)
	{
		$this->load->helper('file');
		$this->load->helper('security');
		$this->load->library('form_builder');
		$this->load->library('upload');

		$this->js_controller_params['method'] = 'upload';
		
		if (!empty($_POST) AND !empty($_FILES))
		{
			$params['upload_path'] = sys_get_temp_dir();
			$params['allowed_types'] = 'php|html|txt';

			// to ensure we check the proper mime types
			$this->upload->initialize($params);

			// Hackery to ensure that a proper php mimetype is set. 
			// Would set in mimes.php config but that may be updated with the next version of CI which does not include the text/plain
			$this->upload->mimes['php'] =  array(
				'application/x-httpd-php', 
				'application/php', 
				'application/x-php', 
				'text/php', 
				'text/x-php', 
				'application/x-httpd-php-source', 
				'text/plain');

			if ($this->upload->do_upload('file'))
			{
				$upload_data = $this->upload->data();
				$error = FALSE;
				
				// read in the file so we can filter it
				$file = read_file($upload_data['full_path']);
				
				// sanitize the file before saving
				$file = $this->_sanitize($file);
				$id = $this->input->post('id', TRUE);
				$name =  $this->input->post('name', TRUE);
				$language =  $this->input->post('language', TRUE);

				if (empty($name))
				{
					$name = current(explode('.', $file_info['name']));
				}

				if ($id)
				{
					$save['id'] = $id;
				}

				$save['name'] = $name;
				$save['view'] = $file;
				$save['language'] = $language;
				$save['date_added'] = datetime_now();
				$save['last_modified'] = date('Y-m-d H:i:s', (time() + 1)); // to prevent window from popping up after upload

				$id  = $this->model->save($save);
				if (!$id)
				{
					add_error(lang('error_upload'));
				}
				else
				{
					// change list view page state to show the selected group id
					$this->fuel->admin->set_notification(lang('blocks_success_upload'), Fuel_admin::NOTIFICATION_SUCCESS);
					
					redirect(fuel_url('blocks/edit/'.$id));
				}
				
			}
			else
			{
				$error_msg = $this->upload->display_errors('', '');
				add_error($error_msg);
			}
		}

		$fields = array();
		$blocks = $this->model->options_list('id', 'name', array('published' => 'yes'), 'name');
		
		$fields['name'] = array('label' => lang('form_label_name'), 'type' => 'inline_edit', 'options' => $blocks, 'module' => 'blocks');
		$fields['file'] = array('type' => 'file', 'accept' => '', 'required' => TRUE);
		$fields['id'] = array('type' => 'hidden');
		$fields['language'] = array('type' => 'hidden');
		
		$field_values = $_POST;
		$common_fields = $this->_common_fields($field_values);
		$fields = array_merge($fields, $common_fields);
		
		
		$this->form_builder->hidden = array();
		$this->form_builder->set_fields($fields);
		$this->form_builder->set_field_values($_POST);
		$this->form_builder->submit_value = '';
		$this->form_builder->use_form_tag = FALSE;
		$vars['instructions'] = lang('blocks_upload_instructions');
		$vars['form'] = $this->form_builder->render();
		$vars['back_action'] = ($this->fuel->admin->last_page() AND $this->fuel->admin->is_inline()) ? $this->fuel->admin->last_page() : fuel_uri($this->module_uri);
		//$vars['back_action'] = fuel_uri($this->module_uri);
		
		$crumbs = array($this->module_uri => $this->module_name, lang('action_upload'));
		$this->fuel->admin->set_titlebar($crumbs);
		
		$this->fuel->admin->render('upload', $vars, Fuel_admin::DISPLAY_NO_ACTION);
	}


	public function layout_fields($layout, $id = NULL, $lang = NULL, $_context = NULL, $_name = NULL)
	{

		// add back in slash 
		$layout = str_replace(':', '/', $layout);
		
		// check to make sure there is no conflict between page columns and layout vars
		$layout = $this->fuel->layouts->get($layout, 'block');
		if (!$layout)
		{
			return;
		}

		// sort of kludgy but not wanting to encode/unencode brackets
		if (empty($_context))
		{
			$_context = $this->input->get('context', TRUE);
		}

		if (empty($_name))
		{
			$_name = $this->input->get('name', TRUE);
		}

		if (empty($_name))
		{
			$_name = $_context;
		}

		if (!empty($_context))
		{
			$layout->set_context($_context);
		}
		
		if (!empty($id))
		{
			$this->load->module_model(FUEL_FOLDER, 'fuel_pagevariables_model');
			$page_vars = $this->fuel_pagevariables_model->find_all_by_page_id($id, $lang);

			// the following will pre-populate fields of a different language to the default values
			if (empty($page_vars) AND $this->fuel->language->has_multiple() AND $lang != $this->fuel->language->default_option())
			{
				$page_vars = $this->fuel_pagevariables_model->find_all_by_page_id($id, $this->fuel->language->default_option());
			}

			// extract variables
			extract($page_vars);
			$_name = end(explode('--', $_name));
			$_name_var = str_replace(array('[', ']'), array('["', '"]'), $_name);
			if (!empty($_name_var))
			{
				$_name_var_eval = '@$_name = (isset($'.$_name_var.')) ? $'.$_name_var.' : "";';
				@eval($_name_var_eval);
			}

			if (isset($_name))
			{
				$block_vars = $_name;
				$layout->set_field_values($block_vars);
			}

		}
		$fields = $layout->fields();


		$this->load->library('form_builder');
		$this->form_builder->load_custom_fields(APPPATH.'config/custom_fields.php');
		
		$this->form_builder->question_keys = array();
		$this->form_builder->submit_value = '';
		$this->form_builder->cancel_value = '';
		$this->form_builder->use_form_tag = FALSE;
		//$this->form_builder->name_prefix = 'vars';
		$this->form_builder->set_fields($fields);
		$this->form_builder->display_errors = FALSE;
		if (isset($block_vars))
		{
			$this->form_builder->set_field_values($block_vars);
		}
		$form = $this->form_builder->render();
		$this->output->set_output($form);
	}
}