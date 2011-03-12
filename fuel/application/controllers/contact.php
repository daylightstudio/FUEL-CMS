<?php
class Contact extends CI_Controller {

	function __construct()
	{
		parent::__construct();
	}
	
	function index()
	{
		$this->load->library('session');
		$this->load->library('form_builder');
		
		if (!empty($_POST) AND $_SERVER['SERVER_ADDR'] == $_SERVER['REMOTE_ADDR'])
		{
			// put your processing code here... we show what we do for emailing. You will need to add a correct email address
			if ($this->_process($_POST))
			{
				$this->session->set_flashdata('success', TRUE);
				redirect('contact');
			}
		}
		
		$fields = array();
		$fields['first_name'] = array('required' => TRUE);
		$fields['last_name'] = array('required' => TRUE);
		$fields['email'] = array('required' => TRUE);
		$fields['question'] = array('required' => TRUE, 'type' => 'textarea');
		
		$this->form_builder->set_fields($fields);
		
		 // will set the values of the fields if there is an error... must be after set_fields
		$this->form_builder->set_validator($this->validator);
		$this->form_builder->set_field_values($_POST);
		$this->form_builder->display_errors = TRUE;
		$this->form_builder->required_text = '<span class="required">*</span>required fields';
		$vars['form'] = $this->form_builder->render();
		
		// use Fuel_page to render so it will grab all opt-in variables and do any necessary parsing
		$page_init = array('location' => 'contact');
		$this->load->module_library(FUEL_FOLDER, 'fuel_page', $page_init);
		$this->fuel_page->add_variables($vars);
		$this->fuel_page->render();
	}
	
	function _process($data)
	{
		$this->load->library('validator');
		/*
		Set rules up here so we can pass them to the form_builder to display errors.
		validator_helper contains the valid_email function... validator helper automatically gets' looded with Validation Class'
		*/
		
		
		$this->validator->add_rule('first_name', 'required', 'Please enter in an first name', $this->input->post('first_name'));
		$this->validator->add_rule('last_name', 'required', 'Please enter in an last name', $this->input->post('last_name'));
		$this->validator->add_rule('email', 'valid_email', 'Please enter in a valid email', $this->input->post('email'));
		$this->validator->add_rule('question', 'required', 'Please enter in an question', $this->input->post('question'));
		
		
		if ($this->validator->validate())
		{
			$this->load->library('email');
			$this->load->helper('inflector');

			// send email
			$this->email->from($data['email'], $data['first_name'] . ' ' . $data['last_name']);

			/*********************************************************************
			YOU MUST FILL OUT THE CORRECT dev_email config in application/config/MY_config.php
			AND/OR THE CORRECT TO email address
			*********************************************************************/
			// check config if we are in dev mode
			if ($this->config->item('dev_mode'))
			{
				$this->email->to($this->config->item('dev_email'));
			}
			else
			{
				// need to fill this out to work
				$this->email->to('');
			}
			$this->email->subject('Website Contact Form');
			$msg = "The following information was submitted:\n";
			foreach($data as $key => $val)
			{
				$msg .= humanize($key, 3).": ".$val."\n";
			}
			$this->email->message($msg);
			
			// let her rip
			if (!$this->email->send())
			{
				add_error('There was an error notifying');
				return FALSE;
			} 

			return TRUE;
		}
		
	}
}