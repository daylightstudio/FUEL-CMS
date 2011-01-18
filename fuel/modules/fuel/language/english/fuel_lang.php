<?php

/*
|--------------------------------------------------------------------------
| Login/
|--------------------------------------------------------------------------
*/
$lang['logged_in'] = "%s logged in";
$lang['logged_out'] = "%s logged out";
$lang['dev_pwd_instructions'] = 'This site is currently in development and requires a password to view.';

/*
|--------------------------------------------------------------------------
| Generic Module
|--------------------------------------------------------------------------
*/
$lang['module_created']= "%1s item <em>%2s</em> created";
$lang['module_edited'] = "%1s item <em>%2s</em> edited";
$lang['module_deleted'] = "%1s item for <em>%2s</em> deleted";
$lang['module_restored'] = "%1s item restored from archive";
$lang['module_instructions_default'] = "Here you can manage the %1s for your site.";

$lang['cannot_determine_module'] = "Cannot determine module.";
$lang['incorrect_route_to_module'] = "Incorrect route to access this module.";
$lang['data_saved'] = 'Data has been saved.';
$lang['data_deleted'] = 'Data has been deleted.';
$lang['no_data'] = 'No data to display.';
$lang['no_preview_path'] = 'There is no preview path assigned to this module.';


/*
|--------------------------------------------------------------------------
| Login/Password Reset
|--------------------------------------------------------------------------
*/

$lang['pwd_reset'] = 'An email to confirm your password reset is on its way.';
$lang['pwd_reset_subject'] = "FUEL admin password reset request";
$lang['pwd_reset_email'] = "Click the following link to confirm the reset of your FUEL password:\n%1s";
$lang['pwd_reset_subject_success'] = "FUEL admin password reset success";
$lang['pwd_reset_email_success'] = "Your FUEL password has been reset to %1s.";
$lang['pwd_reset_success'] = 'Your password was successfully reset and an email has been sent to you with the new password.';
$lang['cache_cleared'] = "Site cache cleared explicitly";
$lang['module_restored_success'] = 'Previous version successfully restored.';


/*
|--------------------------------------------------------------------------
| Nav Menu Titles
|--------------------------------------------------------------------------
*/

$lang['nav_dashboard'] = 'Dashboard';
$lang['nav_pages'] = 'Pages';
$lang['nav_blocks'] = 'Blocks';
$lang['nav_navigation'] = 'Navigation';
$lang['nav_assets'] = 'Assets';
$lang['nav_sitevariables'] = 'Site Variables';
$lang['nav_users'] = 'Users';
$lang['nav_permissions'] = 'Permissions';
$lang['nav_manage/cache'] = 'Page Cache';
$lang['nav_manage/activity'] = 'Activity Log';


/*
|--------------------------------------------------------------------------
| Error Messages
|--------------------------------------------------------------------------
*/
$lang['error_no_access'] = "You do not have access to this page.";
$lang['error_missing_module'] = "You are missing the module %1s.";
$lang['error_invalid_login'] = 'Invalid login.';
$lang['error_max_attempts'] = 'Sorry, but your login information was incorrect and you are temporarily locked out. Please try again in %s seconds.';
$lang['error_empty_user_pwd'] = 'Please enter in a user name and password.';
$lang['error_pwd_reset'] = 'There was an error in resetting your password.';
$lang['error_invalid_email'] = 'The email address provided was not in the system.';
$lang['error_invalid_password_match'] = 'The passwords don\'t match.';
$lang['error_empty_email'] = 'Please enter in an email address.';
$lang['error_no_permissions'] = 'You do not have permissions to complete this action.';
$lang['error_page_layout_variable_conflict'] = 'There is an error with this layout because it contains one or more of the following reserved words: %1s';
$lang['error_invalid_export_dir'] = 'The directory %1s is not a valid export directory. Make sure it is writable.';
$lang['error_exporting_to_directory'] = 'There was an error writing to one or more directories. View the output below for more information.';
$lang['error_cache_folder_not_writable'] = 'You must make the cache folder '.BASEPATH.'cache/ writable.';
$lang['error_exporting_view'] = 'There was an error in creating the associated view file for the location %1s because the variable with the name <strong><em>%2s</em></strong> does not exist.';
$lang['error_no_curl_lib'] = 'You must have the curl php extension to use these tools.';
$lang['error_inline_page_edit'] = 'This variable must be edited in the associated views/_variables file.';
$lang['error_saving'] = 'There was an error saving.';
$lang['error_cannot_preview'] = 'There was an error in trying to preview this page.';
$lang['error_cannot_make_api_call'] = 'There was an error making the API call to %1s.';
$lang['error_sending_email'] = 'There was an error sending an email to %1s.';
$lang['error_nav_upload'] = 'There was an error uploading the navigation file.';
$lang['error_create_nav_group'] = 'Please create a Navigation Group';
$lang['error_requires_string_value'] = 'The name field should be a string value';
$lang['error_missing_params'] = 'You are missing parameters to view this page';


/*
|--------------------------------------------------------------------------
| Warnings
|--------------------------------------------------------------------------
*/
$lang['warn_change_default_pwd'] = '<strong>It is highly recommend that you change your password from the default of <em>%1s</em></strong>.';
$lang['warn_not_published'] = 'This item is not published.';
$lang['warn_not_active'] = 'This %1s is not active.';


/*
|--------------------------------------------------------------------------
| H2 Values
|--------------------------------------------------------------------------
*/
$lang['h2_pages'] = 'Pages';
$lang['h2_navigation'] = 'Navigation';
$lang['h2_blocks'] = 'Blocks';
$lang['h2_assets'] = 'Assets';
$lang['h2_sitevariables'] = 'Site Variables';

$lang['h2_manage'] = 'Manage';
$lang['h2_activty'] = 'Activity';
$lang['h2_page_cache'] = 'Page Cache';
$lang['h2_my_modules'] = 'MY Modules';
$lang['h2_tools'] = 'Tools';


/*
|--------------------------------------------------------------------------
| Instructions
|--------------------------------------------------------------------------
*/
$lang['page_cache_instructions'] = 'You are about to clear the page cache of the site.';


/*
|--------------------------------------------------------------------------
| Pages
|--------------------------------------------------------------------------
*/

$lang['page_route_warning'] = 'The location specified has the following routes already specified in the routes file (%1s):';
$lang['page_controller_assigned'] = 'There is a controller method already assigned to this page.';
$lang['page_updated_view'] = 'There is an updated view file located at <strong>%1s</strong>. Would you like to import it into the body of your page (if available)?';
$lang['page_not_published'] = 'This page is not published.';
$lang['page_no_import'] = 'No, don\'t import';
$lang['page_yes_import'] = 'Yes, import';
$lang['page_information'] = 'Page Information';
$lang['page_layout_vars'] = 'Layout Variables';

/*
|--------------------------------------------------------------------------
| Blocks
|--------------------------------------------------------------------------
*/
$lang['blocks_updated_view'] = 'There is an updated view file located at <strong>%1s</strong>. Would you like to import?';


/*
|--------------------------------------------------------------------------
| Modal
|--------------------------------------------------------------------------
*/
$lang['modal_no_btn'] = 'No';
$lang['modal_yes_btn'] = 'Yes';


/*
|--------------------------------------------------------------------------
| Misc
|--------------------------------------------------------------------------
*/
$lang['success_nav_upload'] = 'The navigation was successfully uploaded.';





/* End of file fuel_lang.php */
/* Location: ./modules/fuel/language/english/fuel_lang.php */