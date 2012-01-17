<?php
require_once('module.php');

class Assets extends Module {
	
	var $module = '';
	
	function __construct()
	{
		parent::__construct();
//		$this->views['create_edit'] = 'assets/assets_create_edit';
	}
	
	function items($inline = FALSE)
	{
		$dirs = $this->fuel->assets->dirs();
		$this->filters['group_id']['options'] = $dirs;
		parent::items($inline);
	}

	function create($dir = NULL, $inline = FALSE)
	{
		$id = NULL;
		if (!empty($_FILES))
		{

			$this->model->on_before_post();

			if ($this->input->post('asset_folder')) $dir = $this->input->post('asset_folder');
			if (!in_array($dir, array_keys($this->fuel->assets->dirs()))) show_404();
			
			$subfolder = ($this->config->item('assets_allow_subfolder_creation', 'fuel')) ? str_replace('..'.DIRECTORY_SEPARATOR, '', $this->input->post('subfolder')) : ''; // remove any going down the folder structure for protections
			$upload_path = $this->config->item('assets_server_path').$this->fuel->assets->dir($dir).DIRECTORY_SEPARATOR.$subfolder; //assets_server_path is in assets config

			$posted['upload_path'] = $upload_path;
			$posted['overwrite'] = ($this->input->post('overwrite')) ? TRUE : FALSE;
			$posted['create_thumb'] = ($this->input->post('create_thumb')) ? TRUE : FALSE;
			$posted['maintain_ratio'] = ($this->input->post('maintain_ratio')) ? TRUE : FALSE;
			$posted['width'] = $this->input->post('width');
			$posted['height'] = $this->input->post('height');
			$posted['master_dim'] = $this->input->post('master_dim');
			$posted['file_name'] = $this->input->post('userfile_file_name');
			$id = $posted['file_name'];
			
			if ($this->fuel->assets->upload($posted))
			{
				foreach($_FILES as $filename => $fileinfo)
				{
					$msg = lang('module_edited', $this->module_name, $fileinfo['name']);
					$this->fuel->logs->write($msg);
				}
				$flashdata = $_POST;
				$uploaded_data = $this->fuel->assets->uploaded_data();
				$first_file = current($uploaded_data);

				// set the uploaded file name to the first file
				$flashdata['uploaded_file_name'] = trim(str_replace(assets_server_path(), '', $first_file['full_path']), '/');

				$this->session->set_flashdata('uploaded_post', $flashdata);
				$this->session->set_flashdata('success', lang('data_saved'));
			}
			else
			{
				add_errors($this->fuel->assets->errors());
			}
			
			$this->model->on_after_post($posted);
			
			if ($inline === TRUE)
			{
				$url = fuel_uri($this->module.'/inline_create/'.$dir);
			}
			else
			{
				$url = fuel_uri($this->module.'/create/'.$dir);
			}
			redirect($url);
		}
		$vars = $this->_form($dir, $inline);

		$list_view = ($inline) ? $this->module_uri.'/inline_items/' : $this->module_uri;
		$crumbs = array($list_view => $this->module_name, lang('assets_upload_action'));
		$this->fuel->admin->set_titlebar($crumbs);
		$this->fuel->admin->set_inline($inline);
		
		if ($inline === TRUE)
		{
			$this->fuel->admin->set_display_mode(Fuel_admin::DISPLAY_COMPACT_TITLEBAR);
		}
		else
		{
			$vars['actions'] = $this->load->module_view(FUEL_FOLDER, '_blocks/module_create_edit_actions', $vars, TRUE);
		}
		$this->fuel->admin->render($this->views['create_edit'], $vars);
		return $id;
		
		
	}
	
	function inline_create($field = NULL)
	{
		$this->create($field, TRUE);
	}
	
	function select_ajax($dir = NULL)
	{
		if (!is_numeric($dir))
		{
			$dir = fuel_uri_string(1, NULL, TRUE);
			$dirs = $this->fuel->assets->dirs();
			foreach($dirs as $key => $d)
			{
				if ($d == $dir)
				{
					$dir = $key;
					break;
				}
			}
		}

		$this->load->helper('array');
		$this->load->helper('form');
		$this->load->library('form_builder');
		$this->model->add_filters(array('group_id' => $dir));
		$options = options_list($this->model->list_items(), 'name', 'name');
		
		$preview = '<div id="asset_preview"></div>';
		$field_values['asset_folder']['value'] = $dir;
		$fields['asset_select'] = array('value' => '', 'label' => 'Select', 'type' => 'select', 'options' => $options, 'after_html' => $preview);
		$this->form_builder->css_class = 'asset_select';
		$this->form_builder->submit_value = NULL;
		$this->form_builder->use_form_tag = FALSE;
		$this->form_builder->set_fields($fields);
		$this->form_builder->display_errors = FALSE;
		$this->form_builder->set_field_values($field_values);
		$vars['form'] = $this->form_builder->render();
		$this->load->view('assets/assets_select_ajax', $vars);
	}
	
	function edit($dir = NULL)
	{
		redirect(fuel_uri('assets/create/'.$dir));
	}
	
	// seperated to make it easier in subclasses to use the form without rendering the page
	function _form($dir = NULL, $inline = FALSE)
	{
		$this->load->library('form_builder');
		$this->load->helper('convert');
		if (!empty($dir)) $dir = uri_safe_decode($dir);
		
		$model = $this->model;
		$this->js_controller_params['method'] = 'add_edit';
		
		
		$fields = $this->model->form_fields();
		
		if ($this->session->flashdata('uploaded_post'))
		{
			$field_values = $this->session->flashdata('uploaded_post');
		}
		else
		{
			$field_values = array('asset_folder' => $dir);
		}
		
		$this->form_builder->submit_value = 'Save';
		$this->form_builder->use_form_tag = false;
		$this->form_builder->set_fields($fields);
		$this->form_builder->display_errors = false;
		$this->form_builder->set_field_values($field_values);
		$vars['form'] = $this->form_builder->render();
		
		// other variables
		$vars['id'] = NULL;
		$vars['data'] = array();
		$vars['action'] =  'create';
		$preview_key = preg_replace('#^(.*)\{(.+)\}(.*)$#', "\\2", $this->preview_path);
		if (!empty($vars['data'][$preview_key])) $this->preview_path = preg_replace('#^(.*)\{(.+)\}(.*)$#e', "'\\1'.\$vars['data']['\\2'].'\\3'", $this->preview_path);
		
		// active or publish fields
		//$vars['publish'] = (!empty($saved['published']) && ($saved['published'] == 'yes')) ? 'Unpublish' : 'Publish';
		$vars['module'] = $this->module;
		$vars['actions'] = $this->load->view('_blocks/module_create_edit_actions', $vars, TRUE);
		$vars['notifications'] = $this->load->view('_blocks/notifications', $vars, TRUE);
		
		if ($inline === TRUE)
		{
			$vars['form_action'] = $this->module_uri.'/inline_create/'.$vars['id'];
		}
		else
		{
			$vars['form_action'] = $this->module_uri.'/create/'.$vars['id'];
		}

		return $vars;
	}
	
	/*function delete($id = NULL, $inline = FALSE)
	{
		if (!$this->fuel_auth->has_permission($this->permission, 'delete')) 
		{
			show_error(lang('error_no_permissions'));
		}
		
		if (!empty($_POST['id']))
		{
			$posted = explode('|', $this->input->post('id'));
		
			// run before_delete hook
			$this->_run_hook('before_delete', $posted);
			
			// Flags
			$any_success = $any_failure = FALSE;
			foreach($posted as $id)
			{
				if ($this->model->delete(uri_safe_decode($id)))
				{
					$any_success = TRUE;
				}
				else
				{
					$any_failure = TRUE;
				}
			}
			
			// run after_delete hook
			$this->_run_hook('after_delete', $posted);
			
			$this->_clear_cache();
			$this->fuel->logs->write(lang('module_multiple_deleted', $this->module));
			//$this->session->set_flashdata('success', lang('data_deleted'));
			if ($inline === TRUE)
			{
				//$vars['layout'] = FALSE;
				$this->fuel->admin->render('modules/module_close_modal', $vars);
				$this->fuel->admin->set_display_mode(Fuel_admin::DISPLAY_COMPACT_NO_ACTION, TRUE);
				$this->fuel->admin->render($this->views['delete'], $vars);
				
			}
			else
			{
				
				// set a success delete message
				if ($any_success)
				{
					$this->session->set_flashdata('success', lang('data_deleted'));
				}

				// set an error delete message
				if ($any_failure)
				{
					// first try to get an error added in model by $this->add_error('...')
					$msg = $this->model->get_validation()->get_last_error();

					// if there is none like that, lets use default message
					if (is_null($msg))
					{
						$msg = lang('data_not_deleted');
					}

					$this->session->set_flashdata('error', $msg);
				}
				
				$url = fuel_uri($this->module_uri);
				redirect($url);
			}


			redirect(fuel_uri($this->module_uri));
		}
		else
		{
			$this->js_controller_params['method'] = 'deleteItem';
			$vars = array();
			if (!empty($_POST['delete']) AND is_array($_POST['delete'])) 
			{
				$data = array();
				foreach($this->input->post('delete') as $key => $val)
				{
					$d = $this->model->find_by_key(uri_safe_decode($key), 'array');
					if (!empty($d)) $data[] = $d[$this->display_field];
				}
				$vars['id'] = implode('|', array_keys($_POST['delete']));
				$vars['title'] = implode(', ', $data);
			}
			else
			{
				$data = $this->model->find_by_key(uri_safe_decode($id));
				$vars['id'] = $id;
				if (isset($data[$this->display_field])) $vars['title'] = $data[$this->display_field];
			}
			if (empty($data) OR (!empty($data['server_path']) AND empty($data['name']))) show_404();
			$vars['error'] = $this->model->get_errors();
			
			if ($inline === TRUE)
			{
				$this->fuel->admin->set_display_mode(Fuel_admin::DISPLAY_COMPACT_NO_ACTION, TRUE);
				$vars['back_action'] = fuel_url($this->module_uri.'/inline_edit/'.$id);
			}
			else
			{
				$this->fuel->admin->set_display_mode(Fuel_admin::DISPLAY_NO_ACTION, TRUE);
				$vars['back_action '] = fuel_url($this->module_uri);
			}
			
			$this->fuel->admin->render($this->views['delete'], $vars);
		}
	}
	
	function inline_delete($id)
	{
		$this->delete($id, TRUE);
	}*/
	
	

	function view($id = null)
	{
		$url = $this->preview_path.'/'.$id;
		redirect($url);
	}
	

}