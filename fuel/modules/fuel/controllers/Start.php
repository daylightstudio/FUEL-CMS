<?php

class Start extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
	}

	public function _remap($segment)
	{
		redirect($this->fuel->config('login_redirect'));
	}
}