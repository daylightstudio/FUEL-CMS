<?php

if ( ! empty($error))
{
	$error = str_replace(array('{script}', '{/script}'), array('<script>', '</script>'), $error); // convert script tags
	echo display_errors($error, 'ico error ico_error');
}
else if ($this->session->flashdata('error') && $this->session->flashdata('error') !== TRUE && $this->session->flashdata('success') !== '1')
{
	$error = str_replace(array('{script}', '{/script}'), array('<script>', '</script>'), $this->session->flashdata('error')); // convert script tags
	echo display_errors($this->session->flashdata('error'), 'ico error ico_error');
}
else if ($this->session->flashdata('success') && $this->session->flashdata('success') !== TRUE && $this->session->flashdata('success') !== '1')
{
	$success = str_replace(array('{script}', '{/script}'), array('<script>', '</script>'), $this->session->flashdata('success')); // convert script tags
	echo '<div class="success ico ico_success">'.$success.'</div>';
}
else
{
	echo display_errors(NULL, 'ico error ico_error');
}

if ( ! empty($last_updated))
{
	echo ' <p class="ico ico_info last_updated">'.$last_updated.'</p>';
}
else if ($this->session->flashdata('info'))
{
	echo '<p class="ico ico_info">'.$this->session->flashdata('info').'</p>';
}
else if ( ! empty($info))
{
	echo '<p class="ico ico_info">'.$info.'</p>';
}