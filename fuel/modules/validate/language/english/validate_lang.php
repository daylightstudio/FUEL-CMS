<?php
$lang['module_validate'] = 'Validate';

$lang['error_html_validation'] = 'There are one ore more HTML errors detected.';
$lang['error_no_pages_selected'] = 'Please select or input the pages you would like to validate.';
$lang['error_public_accessible_server'] = 'Your site must be on a publicly accessible server for the link validation to work correctly.';
$lang['error_checking_page_links'] = 'There was an error checking the links of this page';

$lang['validate_instructions'] = 'Select the pages on the left to validate. Then select whether you want to validate the HTML or the page links for each page. 
Processing time may take seconds to several minutes depending on the number of pages selected.
For HTML validation, it is recommended that you either <a href="http://developer.apple.com/internet/opensource/validator.html" target="blank"><strong>setup a local validation server</strong></a>, 
or validate only several at a time to avoid being temporarily blocked from the w3c.org.';

$lang['btn_validate_links'] = 'Validate Links';
$lang['btn_validate_html'] = 'Validate HTML';
$lang['btn_view_size_report'] = 'View Size Report';
$lang['btn_reload_all'] = 'Reload All';

$lang['validate_link_back_to_page_selection'] = 'Back to page selection';

$lang['validate_pages_input'] = 'OR, input the page(s) manually relative to this domain, separated by a return';

$lang['validate_type_html'] = 'HTML';
$lang['validate_type_links'] = 'Links';
$lang['validate_type_size_report'] = 'Size Report';

// js localization
include('validate_js_lang'.EXT);

/* End of file validate_lang.php */
/* Location: ./modules/validate/language/english/validate_lang.php */
