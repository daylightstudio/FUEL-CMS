<?php
require_once(FUEL_PATH.'/libraries/Fuel_base_controller.php');

class Recent extends Fuel_base_controller {
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function index()
	{
		$session_key = $this->fuel->auth->get_session_namespace();
		$user_data = $this->fuel->auth->user_data();
		if (!empty($user_data['last_page']))
		{
			
			$redirect_to = $user_data['last_page'];
		}
		else
		{
			$redirect_to = $this->config->item('fuel_path', 'fuel').'dashboard';
		}
		redirect($redirect_to);
	}
}