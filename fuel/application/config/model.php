<?php 
$config['auto_validate'] = TRUE;
$config['auto_validate_fields'] = array(
	'email|email_address' => array('valid_email', 'Please enter a valid email address.'),
	'phone|phone_number' => array('valid_phone', 'Please enter a valid phone number.')
);
$config['auto_date_add'] = array('date_added', 'entry_date');
$config['auto_date_update'] = array('last_modified', 'last_updated');
$config['date_format'] = "mm/dd/yyyy";
$config['date_use_gmt'] = FALSE;
$config['default_required_message'] = "Please fill out the required field '%1s'"; // uses sprintf
$config['auto_trim'] = TRUE;
$config['auto_escape_html_entities'] = TRUE;

/* End of file model.php */
/* Location: ./application/config/model.php */