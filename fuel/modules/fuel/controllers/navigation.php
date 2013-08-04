<?php
require_once('module.php');

class Navigation extends Module {
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function upload()
	{
		$this->load->library('form_builder');
		$this->load->module_model(FUEL_FOLDER, 'fuel_navigation_groups_model');
		$this->load->module_model(FUEL_FOLDER, 'fuel_navigation_model');
		$this->js_controller_params['method'] = 'upload';
		
		if (!empty($_POST))
		{
			$params = $this->input->post();
			
			if (!empty($_FILES['file']['name']))
			{
				$error = FALSE;
				$file_info = $_FILES['file'];
				$params['file_path'] = $file_info['tmp_name'];
				$params['var'] = $this->input->post('variable') ? $this->input->post('variable', TRUE) : 'nav';
				$params['language'] = $this->input->post('language', TRUE);
				
				if (!$this->fuel->navigation->upload($params))
				{
					$error = TRUE;
				}

				if ($error)
				{
					add_error(lang('error_upload'));
				}
				else
				{
					// change list view page state to show the selected group id
					$this->fuel->admin->set_notification(lang('navigation_success_upload'), Fuel_admin::NOTIFICATION_SUCCESS);
					
					redirect(fuel_url('navigation?group_id='.$params['group_id']));
				}
				
			}
			else
			{
				add_error(lang('error_upload'));
			}
		}
		
		$fields = array();
		$nav_groups = $this->fuel_navigation_groups_model->options_list('id', 'name', array('published' => 'yes'), 'id asc');
		if (empty($nav_groups)) $nav_groups = array('1' => 'main');
		
		// load custom fields
		$this->form_builder->load_custom_fields(APPPATH.'config/custom_fields.php');

		$fields['group_id'] = array('type' => 'select', 'options' => $nav_groups, 'module' => 'navigation_group');
		$fields['file'] = array('type' => 'file', 'accept' => '');
		$fields['variable'] = array('label' => 'Variable', 'value' => (($this->input->post('variable')) ? $this->input->post('variable', TRUE) : 'nav'), 'size' => 10);
		$fields['language'] = array('type' => 'select', 'options' => $this->fuel->language->options(), 'first_option' => lang('label_select_one'));
		$fields['clear_first'] = array('type' => 'enum', 'options' => array('yes' => 'yes', 'no' => 'no'));
		$fields['__fuel_module__'] = array('type' => 'hidden');
		$fields['__fuel_module__']['value'] = $this->module;
		$fields['__fuel_module__']['class'] = '__fuel_module__';

		$fields['__fuel_module_uri__'] = array('type' => 'hidden');
		$fields['__fuel_module_uri__']['value'] = $this->module_uri;
		$fields['__fuel_module_uri__']['class'] = '__fuel_module_uri__';

		$this->form_builder->set_fields($fields);
		$this->form_builder->submit_value = '';
		$this->form_builder->use_form_tag = FALSE;
		$this->form_builder->set_field_values($_POST);
		
		$vars['instructions'] = lang('navigation_import_instructions');
		$vars['form'] = $this->form_builder->render();
		$vars['back_action'] = ($this->fuel->admin->last_page() AND $this->fuel->admin->is_inline()) ? $this->fuel->admin->last_page() : fuel_uri($this->module_uri);

		$crumbs = array($this->module_uri => $this->module_name, lang('action_upload'));
		$this->fuel->admin->set_titlebar($crumbs);
		
		$this->fuel->admin->render('upload', $vars, Fuel_admin::DISPLAY_NO_ACTION);
	}	
	
	public function download()
	{
		if (!empty($_POST['group_id']))
		{
			$this->load->helper('download');
			$where['group_id'] = $this->input->post('group_id', TRUE);
			$where['published'] = 'yes';
			$data = $this->model->find_all_array_assoc('nav_key', $where, 'parent_id asc, precedence asc');
			$var = '$nav';
			$str = "<?php \n";
			foreach($data as $key => $val)
			{
				// add label
				$str .= $var."['".$key."'] = array('label' => '".$val['label']."', ";

				// add location
				if ($key != $val['location'])
				{
					$str .= "'location' => '".$val['location']."', ";
				}

				if (!empty($val['parent_id']))
				{
					$parent_data  = $this->model->find_one_array(array('id' => $val['parent_id']));
					$str .= "'parent_id' => '".$parent_data['nav_key']."', ";
				}

				if (is_true_val($val['hidden']))
				{
					$str .= "'hidden' => 'yes', ";
				}

				if (!empty($val['attributes']))
				{
					$str .= "'attributes' => '".$val['attributes']."', ";
				}

				if (!empty($val['selected']))
				{
					$str .= "'selected' => '".$val['selected']."', ";
				}
				$str = substr($str, 0, -2);
				$str .= ");\n";
			}
			force_download('nav.php', $str);
		}
	}
	
	public function parents($group_id = NULL, $parent_id = NULL, $id = NULL)
	{
		if (is_ajax() AND !empty($group_id))
		{
			$this->load->library('form');
			$where = array();
			if (!empty($group_id)) $where['group_id'] = $group_id;
			if (!empty($id)) $where['id !='] = $id;
			if (!empty($id)) $where['parent_id !='] = $id;
			
			$parent_options = $this->model->options_list('id', 'nav_key', $where);
			$select = $this->form->select('parent_id', $parent_options, $parent_id, '', 'None');
			$this->output->set_output($select);
		}
		else if ($parent_id != 0)
		{
			show_error(lang('error_missing_params'));
		}
	}
	
}