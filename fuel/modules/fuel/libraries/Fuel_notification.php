<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * FUEL CMS
 * http://www.getfuelcms.com
 *
 * An open source Content Management System based on the 
 * Codeigniter framework (http://codeigniter.com)
 *
 * @package		FUEL CMS
 * @author		David McReynolds @ Daylight Studio
 * @copyright	Copyright (c) 2011, Run for Daylight LLC.
 * @license		http://www.getfuelcms.com/user_guide/general/license
 * @link		http://www.getfuelcms.com
 */

// ------------------------------------------------------------------------

/**
 * FUEL notification library object
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://www.getfuelcms.com/user_guide/libraries/fuel_notification
 */

// --------------------------------------------------------------------

class Fuel_notification extends Fuel_base_library {

	public $to = '';
	public $from = '';
	public $from_name = '';
	public $subject = '';
	public $message = '';
	public $attachments = array();
	public $use_dev_mode = TRUE;

	function __construct($params = array())
	{
		parent::__construct($params);
	}
	
	function send($params = array())
	{
		// set defaults for from and from name
		if (empty($params['from']))
		{
			$params['from'] = $this->fuel->config('from_email');
		}

		if (empty($params['from_name']))
		{
			$params['from_name'] = $this->fuel->config('site_name');
		}

		// set any parameters passed
		$this->set_params($params);
		
		// load email and set notification properties
		$this->CI->load->library('email');
		$this->CI->email->set_wordwrap(TRUE);
		$this->CI->email->from($this->from, $this->from_name);
		$this->CI->email->subject($this->subject);
		$this->CI->email->message($this->message);
		if (!empty($this->attachments))
		{
			if (is_array($this->attachments))
			{
				foreach($this->attachments as $attachment)
				{
					$this->CI->email->attach($attachment);
				}
			}
			else
			{
				$this->CI->email->attach($this->attachments);
			}
		}
		
		// if in dev mode then we send it to the dev email if specified
		if ($this->is_dev_mode())
		{
			$this->CI->email->to($this->CI->config->item('dev_email'));
		}
		else
		{
			$this->CI->email->to($this->to);
		}
		
		if (!$this->CI->email->send())
		{
			$this->_errors[] = $this->CI->email->print_debugger();
			return FALSE;
		}
		return TRUE;
		
	}

	function data_message($data, $intro = '')
	{
		$msg = $intro."\n\n";
		if (!empty($data))
		{
			if (is_object($data) AND is_a($data, 'Data_record'))
			{
				$data = $data->values();
			}

			foreach ($data as $key => $val)
			{
				$msg .= humanize($key) . ': ' . $val . "\n";
			}
		}
		return $msg;
	}
	

	function is_dev_mode()
	{
		return $this->use_dev_mode == TRUE AND (is_dev_mode());
	}
	
	function errors()
	{
		return $this->_errors;
	}
	
	function has_errors()
	{
		return (count($this->_errors) > 0);
	}

}