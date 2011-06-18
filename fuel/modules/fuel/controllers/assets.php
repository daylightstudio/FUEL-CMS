<?php
require_once('module.php');

class Assets extends Module {
	
	var $module = '';
	
	function __construct()
	{
		parent::__construct();
		$this->views['create_edit'] = 'assets/assets_create_edit';
	}
	
	function items()
	{
		$dirs = $this->model->get_dirs();
		$this->filters['group_id']['options'] = $dirs;
		parent::items();
	}

	function create($dir = NULL)
	{
		if (!empty($_FILES))
		{
			if ($this->input->post('asset_folder')) $dir = $this->input->post('asset_folder');
			if (!in_array($dir, array_keys($this->model->get_dirs()))) show_404();
			
			$subfolder = ($this->config->item('assets_allow_subfolder_creation', 'fuel')) ? str_replace('..'.DIRECTORY_SEPARATOR, '', $this->input->post('subfolder')) : ''; // remove any going down the folder structure for protections
			$upload_path = $this->config->item('assets_server_path').$this->model->get_dir($dir).DIRECTORY_SEPARATOR.$subfolder; //assets_server_path is in assets config

			$overwrite = ($this->input->post('overwrite')) ? TRUE : FALSE;
			$create_thumb = ($this->input->post('create_thumb')) ? TRUE : FALSE;
			$maintain_ratio = ($this->input->post('maintain_ratio')) ? TRUE : FALSE;

			$posted['userfile_width'] = $this->input->post('width');
			$posted['userfile_height'] = $this->input->post('height');
			
			$posted['userfile_path'] = $upload_path;
			$posted['userfile_overwrite'] = $overwrite;
			$posted['userfile_create_thumb'] = $create_thumb;
			$posted['userfile_maintain_ratio'] = $maintain_ratio;
			$posted['userfile_master_dim'] = $this->input->post('master_dim');
			
			$posted['userfile_filename'] = $this->input->post('userfile_filename');
			
			if ($this->_process_uploads($posted))
			{
				foreach($_FILES as $filename => $fileinfo)
				{
					$msg = lang('module_edited', $this->module_name, $fileinfo['name']);
					$this->logs_model->logit($msg);
				}
				$this->session->set_flashdata('uploaded_post', $_POST);
				$this->session->set_flashdata('success', lang('data_saved'));
			}
			
			$this->model->on_after_post($posted);
			
			redirect(fuel_uri($this->module.'/create/'));
		}
		$vars = $this->_form($dir);
		$this->_render($this->views['create_edit'], $vars);
	}
	
	function select_ajax($dir = NULL)
	{
		if (!is_numeric($dir))
		{
			$dir = fuel_uri_string(1, NULL, TRUE);
			$dirs = $this->model->get_dirs();
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
		$this->form_builder->submit_value = null;
		$this->form_builder->use_form_tag = false;
		$this->form_builder->set_fields($fields);
		$this->form_builder->display_errors = false;
		$this->form_builder->set_field_values($field_values);
		$vars['form'] = $this->form_builder->render();
		$this->load->view('assets/assets_select_ajax', $vars);
	}
	
	function edit($dir = NULL)
	{
		redirect(fuel_uri('assets/create/'.$dir));
	}
	
	// seperated to make it easier in subclasses to use the form without rendering the page
	function _form($dir = NULL)
	{
		$this->load->library('form_builder');
		$this->load->helper('convert');
		if (!empty($dir)) $dir = uri_safe_decode($dir);
		
		$model = $this->model;
		$this->js_controller_params['method'] = 'add_edit';
		
		$field_values = ($this->session->flashdata('uploaded_post')) ? $this->session->flashdata('uploaded_post') : array('asset_folder' => $dir);
		$fields = $this->model->form_fields();
		
		
		$this->form_builder->submit_value = 'Save';
		$this->form_builder->use_form_tag = false;
		$this->form_builder->set_fields($fields);
		$this->form_builder->display_errors = false;
		$this->form_builder->set_field_values($field_values);
		$vars['form'] = $this->form_builder->render();
		
		// other variables
		$vars['id'] = null;
		$vars['data'] = array();
		$vars['action'] =  'create';
		$preview_key = preg_replace('#^(.*)\{(.+)\}(.*)$#', "\\2", $this->preview_path);
		if (!empty($vars['data'][$preview_key])) $this->preview_path = preg_replace('#^(.*)\{(.+)\}(.*)$#e', "'\\1'.\$vars['data']['\\2'].'\\3'", $this->preview_path);
		
		// active or publish fields
		//$vars['publish'] = (!empty($saved['published']) && ($saved['published'] == 'yes')) ? 'Unpublish' : 'Publish';
		$vars['module'] = $this->module;
		$vars['actions'] = $this->load->view('_blocks/module_create_edit_actions', $vars, TRUE);
		$vars['notifications'] = $this->load->view('_blocks/notifications', $vars, TRUE);

		// do this after rendering so it doesn't render current page'
		if (!empty($vars['data'][$this->display_field])) $this->_recent_pages($this->uri->uri_string(), $vars['data'][$this->display_field], $this->module);
		return $vars;
	}
	
	function delete($id = NULL)
	{
		if (!$this->fuel_auth->has_permission($this->permission, 'delete')) 
		{
			show_error(lang('error_no_permissions'));
		}
		
		if (!empty($_POST['id']))
		{
			$posted = explode('|', $this->input->post('id'));
			foreach($posted as $id)
			{
				$this->model->delete(uri_safe_decode($id));
			}
			$this->session->set_flashdata('success', lang('data_deleted'));
			$this->_clear_cache();
			$this->logs_model->logit('Multiple module '.$this->module.' data deleted');
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
			$vars['notifications'] = $this->load->module_view(FUEL_FOLDER, '_blocks/notifications', $vars, TRUE);
			$this->_render($this->views['delete'], $vars);
		}
	}

	function view($id = null)
	{
		$url = $this->preview_path.'/'.$id;
		redirect($url);
	}
	

}