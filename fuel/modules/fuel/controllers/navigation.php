<?php
require_once('module.php');

class Navigation extends Module {
	
	function __construct()
	{
		parent::__construct();
	}
	
	function items()
	{
		$this->load->module_model(FUEL_FOLDER, 'navigation_groups_model');
		if (!empty($this->filters['group_id'])) $this->filters['group_id']['options'] = $this->navigation_groups_model->options_list('id', 'name', array(), false);
		parent::items();
	}
	
	function upload()
	{
		$this->load->helper('file');
		$this->load->helper('security');
		$this->load->library('form_builder');
		$this->load->module_model(FUEL_FOLDER, 'navigation_groups_model');
		$this->load->module_model(FUEL_FOLDER, 'navigation_model');
		
		$this->js_controller_params['method'] = 'upload';
		
		if (!empty($_POST))
		{
			$this->load->library('menu');
			
			if (!empty($_FILES['file']['name']))
			{
				$error = FALSE;
				$file_info = $_FILES['file'];
				
				// read in the file so we can filter it
				$file = read_file($file_info['tmp_name']);
				
				// strip any php tags
				$file = str_replace('<?php', '', $file);
				
				// run xss_clean on it 
				$file = xss_clean($file);
				
				// now evaluate the string to get the nav array
				@eval($file);
				
				//@include($file_info['tmp_name']);
				
				if (!empty($nav))
				{
					$nav = $this->menu->normalize_items($nav);
					
					$group_id = $this->input->post('group_id');
					if (is_true_val($this->input->post('clear_first')))
					{
						$this->navigation_model->delete(array('group_id' => $this->input->post('group_id')));
					}
					
					// save navigation group
					$group = $this->navigation_groups_model->find_by_key($this->input->post('group_id'));
					
					// set default navigation group if it doesn't exist'
					if (!isset($group->id))
					{
						$save['name'] = 'main';
						$id = $this->navigation_groups_model->save($save);
						$group_id = $id;
					}
					// convert string ids to numbers so we can save... must start at last id in db
					$ids = array();
					$i = $this->navigation_model->max_id() + 1;
					foreach($nav as $key => $item)
					{
						// if the id is empty then we assume it is the homepage
						if (empty($item['id']))
						{
							$item['id'] = 'home';
							$nav[$key]['id'] = 'home';
						}
						$ids[$item['id']] = $i;
						$i++;
					}
					// now loop through and save
					$cnt = 0;

					foreach($nav as $key => $item)
					{
						$save = array();
						$save['id'] = $ids[$item['id']];
						$save['nav_key'] = (empty($key)) ? 'home' : $key;
						$save['group_id'] = $group_id;
						$save['label'] = $item['label'];
						$save['parent_id'] = (empty($ids[$item['parent_id']])) ? 0 : $ids[$item['parent_id']];
						$save['location'] = $item['location'];
						$save['selected'] = (!empty($item['selected'])) ? $item['selected'] : $item['active']; // must be different because "active" has special meaning in FUEL
							
						// fix for homepage links
						if (empty($save['selected']) AND $save['nav_key'] == 'home')
						{
							$save['selected'] = 'home$';
						}
						
						$save['hidden'] = (is_true_val($item['hidden'])) ? 'yes' : 'no';
						$save['published'] = 'yes';
						$save['precedence'] = $cnt;
						if (is_array($item['attributes']))
						{
							$attr = '';
							foreach($item['attributes'] as $key => $val)
							{
								$attr .= $key .'="'.$val.'" ';
							}
							$attr = trim($attr);
						}
						else
						{
							$save['attributes'] = $item['attributes'];
						}
						
						if (!$this->navigation_model->save($save))
						{
							$error = TRUE;
							break;
						}
						$cnt++;
					}
				}
				else
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
					$page_state = $this->_get_page_state($this->module_uri);
					$page_state['group_id'] = $group_id;
					$this->_save_page_state($page_state);
					$this->session->set_flashdata('success', lang('navigation_success_upload'));
					redirect(fuel_url('navigation'));
				}
				
			}
			else
			{
				add_error(lang('error_upload'));
			}
		}
		
		$fields = array();
		$nav_groups = $this->navigation_groups_model->options_list('id', 'name', array('published' => 'yes'), 'id asc');
		if (empty($nav_groups)) $nav_groups = array('1' => 'main');
		
		$fields['group_id'] = array('type' => 'select', 'options' => $nav_groups, 'class' => 'add_edit navigation_group');
		$fields['file'] = array('type' => 'file', 'accept' => '');
		$fields['clear_first'] = array('type' => 'enum', 'options' => array('yes' => 'yes', 'no' => 'no'));
		$this->form_builder->set_fields($fields);
		$this->form_builder->submit_value = '';
		$this->form_builder->use_form_tag = FALSE;
		$vars['instructions'] = lang('navigation_import_instructions');
		$vars['form'] = $this->form_builder->render();
		$this->_render('upload', $vars);
	}
	
	function parents($group_id = NULL, $parent_id = NULL, $id = NULL)
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