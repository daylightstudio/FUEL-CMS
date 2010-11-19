<?php

class Dwoowelcome extends CI_Controller {

    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
    	$this->load->library('Dwootemplate');
    	$this->dwootemplate->assign('itshowlate', date('H:i:s'));
    	$this->dwootemplate->display('dwoowelcome.tpl');
    }
}