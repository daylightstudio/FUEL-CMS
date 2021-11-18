<?php

/*
|--------------------------------------------------------------------------
| General
|--------------------------------------------------------------------------
*/
$lang['fuel_page_title'] = 'FUEL CMS';
$lang['logged_in_as'] = 'Logged in as:';
$lang['logout'] = 'Logout';
$lang['fuel_developed_by'] = 'FUEL CMS version %1s is developed by <a href="https://www.thedaylightstudio.com" target="_blank">Daylight Studio</a> and built upon the <a href="https://www.codeigniter.com" target="_blank">CodeIgniter</a> framework.';
$lang['fuel_copyright'] = 'Copyright &copy; %1s Daylight Studio. All Rights Reserved.';


/*
|--------------------------------------------------------------------------
| Error Messages
|--------------------------------------------------------------------------
*/
$lang['error_no_access'] = 'You do not have access to this page. <a href="%1s">Try logging in again</a>.';
$lang['error_missing_module'] = "You are missing the module %1s.";
$lang['error_invalid_login'] = 'Invalid login.';
$lang['error_max_attempts'] = 'Sorry, but your login information was incorrect and you are temporarily locked out. Please try again in %s seconds.';
$lang['error_empty_user_pwd'] = 'Please enter in a user name and password.';
$lang['error_pwd_reset'] = 'There was an error in resetting your password.';
$lang['error_csrf'] = 'Invalid submission.';

$lang['error_pwd_too_short'] = 'Password entered does not meet the %1s character min length requirement.';
$lang['error_pwd_too_long'] = 'Password entered exceeds the %1s character max length requirement.';
$lang['error_pwd_invalid'] = 'Please choose a stronger password. Try a mix of %1s.';


$lang['error_invalid_email'] = 'The email address provided was not in the system.';
$lang['error_invalid_password_match'] = 'The passwords don\'t match.';
$lang['error_empty_email'] = 'Please enter in an email address.';
$lang['error_folder_not_writable'] = 'You must make the %1s folder writable.';
$lang['error_invalid_folder'] = 'Invalid folder %1s';
$lang['error_file_already_exists'] = 'File %1s already exists.';
$lang['error_zip'] = 'There was an error creating the zipped file.';
$lang['error_no_permissions'] = 'You do not have permissions to complete this action. <a href="%1s">Try logging in again</a>.';
$lang['error_no_lib_permissions'] = 'You do not have permission to execute methods on the %1s class.';
$lang['error_page_layout_variable_conflict'] = 'There is an error with this layout because it either doesn\'t exist or contains one or more of the following reserved words: %1s';
$lang['error_no_curl_lib'] = 'You must have the curl php extension to use these tools.';
$lang['error_inline_page_edit'] = 'This variable must either be saved in the admin or edited in the associated views/_variables file.';
$lang['error_saving'] = 'There was an error saving.';
$lang['error_cannot_preview'] = 'There was an error in trying to preview this page.';
$lang['error_cannot_make_api_call'] = 'There was an error making the API call to %1s.';
$lang['error_sending_email'] = 'There was an error sending an email to %1s.';
$lang['error_upload'] = 'There was an error uploading your file. Please make sure the server is setup to upload files of this size and folders are writable.';
$lang['error_create_nav_group'] = 'Please create a Navigation Group';
$lang['error_requires_string_value'] = 'The name field should be a string value';
$lang['error_missing_params'] = 'You are missing parameters to view this page';
$lang['error_invalid_method'] = 'Invalid method name';
$lang['error_curl_page'] = 'Error loading page with CURL';
$lang['error_class_property_does_not_exist'] = 'Class property %1s does not exist';
$lang['error_class_method_does_not_exist'] = 'Class method %1s does not exist';
$lang['error_could_not_create_folder'] = 'Could not create folder %1s';
$lang['error_could_not_create_file'] = 'Could not create file %1s';
$lang['error_no_build'] = "No build setup for this module.\n";
$lang['error_invalid_record'] = "The module record does not exists.";


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
| Login
|--------------------------------------------------------------------------
*/
$lang['logged_in'] = "%s logged in";
$lang['logged_out'] = "%s logged out";
$lang['dev_pwd_instructions'] = 'This site is currently in development and requires a password to view.';
$lang['login_forgot_pwd'] = 'Forgot password?';
$lang['login_reset_pwd'] = 'Set New Password';
$lang['login_btn'] = 'Login';
$lang['logout_restore_original_user'] = 'Restore original user';

$lang['auth_log_pass_reset_request'] = "Password reset request for '%1s' from %2s";
$lang['auth_log_pass_reset'] = "Password reset for '%1s' from %2s";
$lang['auth_log_cms_pass_reset'] = "Password reset from CMS for '%1s' from %2s";
$lang['auth_log_login_success'] = "Successful login by '%1s' from %2s";
$lang['auth_log_failed_updating_login_info'] = "There was an error updating the login information for by '%1s' from %2s";
$lang['auth_log_failed_login'] = "Failed login by '%1s' from %2s, login attempts: %3s";
$lang['auth_log_account_lockout'] = "Account lockout for '%1s' from %2s";

$lang['pwd_requirements'] = '<p style="text-align: left; margin: 10px 0 5px 0;"><strong>Password must be:</strong></p>';
$lang['pwd_min_length_required'] = 'A minimum of %1s characters';
$lang['pwd_max_length_required'] = 'A maximum of %1s characters';
$lang['pwd_lowercase_required'] = 'One or more lower case letters';
$lang['pwd_uppercase_required'] = 'One or more upper case letters';
$lang['pwd_numbers_required'] = 'One or more numbers';
$lang['pwd_symbols_required'] = 'One or more symbols';

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/
$lang['section_dashboard'] = 'Dashboard';
$lang['dashboard_intro'] = 'Welcome to FUEL CMS.';
$lang['dashboard_hdr_latest_activity'] = 'Latest Activity';
$lang['dashboard_hdr_latest_news'] = 'Latest FUEL News';
$lang['dashboard_hdr_modified'] = 'Recently Modified Pages';
$lang['dashboard_hdr_site_docs'] = 'Site Documentation';
$lang['dashboard_change_pwd'] = 'Change password';
$lang['dashboard_change_pwd_later'] = 'I\'ll change my password later';
$lang['dashboard_subscribe_rss'] = 'Subscribe to the RSS Feed';
$lang['dashboard_view_all_pages'] = 'View all pages';
$lang['dashboard_view_all_activity'] = 'View all activity';


/*
|--------------------------------------------------------------------------
| My Profile
|--------------------------------------------------------------------------
*/
$lang['section_my_profile'] = 'My Profile';
$lang['profile_instructions'] = 'Change your profile information below:';


/*
|--------------------------------------------------------------------------
| My Modules
|--------------------------------------------------------------------------
*/
$lang['section_my_modules'] = 'My Modules';


/*
|--------------------------------------------------------------------------
| Login/Password Reset
|--------------------------------------------------------------------------
*/
//$lang['pwd_reset'] = 'An email to confirm your password reset is on its way.';
$lang['pwd_reset_error'] = 'The email address entered does not have an active reset token, please resubmit the reset password form to get a new reset link.';
$lang['pwd_reset_success'] = 'Your password has been successfully reset, please login.';

$lang['pwd_reset_error_not_match'] = 'The passwords submitted do not match.';

$lang['pwd_reset_missing_token'] = 'Missing or invalid reset token.';
$lang['pwd_reset_success'] = 'Your password has been successfully reset, please login with your user name and password.';
$lang['pwd_reset'] = 'An email with your password reset link has been sent.';
$lang['pwd_reset_subject'] = "FUEL admin password reset request";
$lang['pwd_reset_email'] = "Click the following link to reset your FUEL password:\n%1s";
$lang['pwd_reset_subject_success'] = "FUEL admin password reset success";
//$lang['pwd_reset_email_success'] = "Your FUEL password has been reset to %1s. To change your password, login to the FUEL CMS admin with this password and click on your login name in the upper right to access your profile information.";
$lang['pwd_reset_email_success'] = 'An email with your password reset link has been sent.';
$lang['cache_cleared'] = "Site cache cleared explicitly";


/*
|--------------------------------------------------------------------------
| Menu Titles / Sections
|--------------------------------------------------------------------------
*/

$lang['module_dashboard'] = 'Dashboard';
$lang['module_pages'] = 'Pages';
$lang['module_blocks'] = 'Blocks';
$lang['module_navigation'] = 'Navigation';
$lang['module_categories'] = 'Categories';
$lang['module_tags'] = 'Tags';
$lang['module_assets'] = 'Assets';
$lang['module_sitevariables'] = 'Site Variables';
$lang['module_users'] = 'Users';
$lang['module_permissions'] = 'Permissions';
$lang['module_tools'] = 'Tools';
$lang['module_manage_cache'] = 'Page Cache';
$lang['module_manage_activity'] = 'Activity Log';
$lang['module_manage_settings'] = 'Settings';
$lang['module_generate'] = 'Generated';


$lang['section_site'] = 'Site';
$lang['section_blog'] = 'Blog';
$lang['section_modules'] = 'Modules';
$lang['section_manage'] = 'Manage';
$lang['section_tools'] = 'Tools';
$lang['section_settings'] = 'Settings';
$lang['section_recently_viewed'] = 'Recently Viewed';


/*
|--------------------------------------------------------------------------
| Generic Module
|--------------------------------------------------------------------------
*/
$lang['module_created']= "%1s item <em>%2s</em> created";
$lang['module_edited'] = "%1s item <em>%2s</em> edited";
$lang['module_deleted'] = "%1s item for <em>%2s</em> deleted";
$lang['module_multiple_deleted'] = "Multiple <em>%1s</em> deleted";
$lang['module_restored'] = "%1s item restored from archive";
$lang['module_instructions_default'] = "Here you can manage the %1s for your site.";
$lang['module_restored_success'] = 'Previous version successfully restored.';
$lang['module_replaced_success'] = 'The contents of this record were successfully replaced.';
$lang['module_incompatible'] = 'The version of this module is not compatible with the install FUEL version of '.FUEL_VERSION;

$lang['cannot_determine_module'] = "Cannot determine module.";
$lang['incorrect_route_to_module'] = "Incorrect route to access this module.";
$lang['data_saved'] = 'Data has been saved.';
$lang['data_deleted'] = 'Data has been deleted.';
$lang['data_not_deleted'] = 'Some or all data couldn\'t be deleted.';
$lang['no_data'] = 'No data to display.';
$lang['no_preview_path'] = 'There is no preview path assigned to this module.';
$lang['delete_item_message'] = 'You are about to delete the item:';
$lang['replace_item_message'] = 'Select a record from the list below that you would like to replace. Replacing will transfer the data from one record to the other and then delete the old record.';
$lang['error_select_replacement'] = 'There was an error replacing your module record.';

// command line
$lang['module_install'] = "The '%1s' module has successfully been installed.\n";
$lang['module_install_error'] = "There was an error installing the '%1s' module.\n";

$msg = "The module %1s has been uninstalled in FUEL.\n\n";
$msg .= "However, removing a module from GIT is a little more work that we haven't automated yet. However, the below steps should help.\n\n";
$msg .= "1. Delete the relevant section from the .gitmodules file.\n";
$msg .= "2. Delete the relevant section from .git/config.\n";
$msg .= "3. Run git rm --cached %2s (no trailing slash).\n";
$msg .= "4. Commit and delete the now untracked submodule files.\n";
$lang['module_uninstall'] = $msg;

$lang['module_update'] = "The module %1s has been updated in FUEL.\n";

// build
$lang['module_build_asset'] = "%1s optimized and output to %2s\n";

/*
|--------------------------------------------------------------------------
| Migrations
|--------------------------------------------------------------------------
*/
$lang['migrate_success'] = "You have successfully migrated to version %s.\n";
$lang['migrate_nothing_todo'] = "No migrations were necessary.\n";

/*
|--------------------------------------------------------------------------
| List View
|--------------------------------------------------------------------------
*/
$lang['adv_search'] = 'Advanced Search';
$lang['reset_search'] = 'Reset Search';
$lang['num_items'] = 'item';

/*
|--------------------------------------------------------------------------
| Pages
|--------------------------------------------------------------------------
*/

$lang['page_route_warning'] = 'The location specified has the following routes already specified in the routes file (%1s):';
$lang['page_controller_assigned'] = 'There is a controller method already assigned to this page.';
$lang['page_updated_view'] = 'There is an updated view file located at <strong>%1s</strong>. Would you like to upload it into the body of your page (if available)?';
$lang['page_not_published'] = 'This page is not published.';

$lang['page_no_upload'] = 'No, don\'t upload';
$lang['page_yes_upload'] = 'Yes, upload';
$lang['page_information'] = 'Page Information';
$lang['page_layout_vars'] = 'Layout Variables';

$lang['pages_instructions'] = 'Here you can manage the data associated with the page.';
$lang['pages_associated_navigation'] = 'Associated Navigation';
$lang['pages_success_upload'] = 'The page view was successfully uploaded.';
$lang['pages_upload_instructions'] = 'Select a view file and upload to a page below.';
$lang['pages_select_action'] = 'Select';

// page specific form fields
$lang['form_label_layout'] = 'Layout';
$lang['form_label_cache'] = 'Cache';
$lang['pages_last_updated'] = 'Last updated %1s';
$lang['pages_last_updated_by'] = 'Last updated %1s by %2s';
$lang['pages_not_published'] = 'This page is not published.';
$lang['pages_default_location'] = 'example: company/about';

$lang['form_label_page'] = 'Page';
$lang['form_label_target'] = 'Target';
$lang['form_label_class'] = 'Class';

$lang['navigation_related'] = 'Create navigation';
$lang['navigation_quick_add'] = 'This field lets you quickly add a navigation item for this page. It only allows you to create a navigation item during page creation. To edit the navigation item, you must click on the\'Navigation\' link on the left, find the navigation item you want to change and click on the edit link.';

$lang['page_select_pages'] = 'Pages';
$lang['page_select_pdfs'] = 'PDFs';

/*
|--------------------------------------------------------------------------
| Blocks
|--------------------------------------------------------------------------
*/
$lang['blocks_updated_view'] = 'There is an updated view file located at <strong>%1s</strong>. Would you like to import?';
$lang['blocks_success_upload'] = 'The block view was successfully uploaded.';
$lang['blocks_upload_instructions'] = 'Select a block view file and upload it below.';

$lang['form_label_view'] = 'View';

/*
|--------------------------------------------------------------------------
| Navigation
|--------------------------------------------------------------------------
*/
$lang['navigation_import'] = 'Import Navigation';
$lang['navigation_instructions'] = 'Here you create and edit the top menu items of the page.';
$lang['navigation_import_instructions'] = 'The following allows you to import your <code>views/_variables/nav.php</code> file OR a JSON file that represents your navigation array. For a reference of the array format, please consult the <a href="https://docs.getfuelcms.com/general/navigation" target="_blank">user guide</a>.';
$lang['navigation_import_file_comment'] = 'If no file is uploaded, it will default to the views/_variables/nav.php file. The file must be a JSON file';
$lang['navigation_success_upload'] = 'The navigation was successfully uploaded.';
$lang['form_label_navigation_group'] = 'Navigation Group:';
$lang['form_label_nav_key'] = 'Key';
$lang['form_label_parent_id'] = 'Parent';
$lang['form_label_attributes'] = 'Attributes';
$lang['form_label_selected'] = 'Selected';
$lang['form_label_hidden'] = 'Hidden';

$lang['error_location_parents_match'] = 'Location and parents can\'t match.';

// for upload form
$lang['form_label_clear_first'] = 'Clear First';


/*
|--------------------------------------------------------------------------
| Assets
|--------------------------------------------------------------------------
*/
$lang['assets_instructions'] = 'Here you can upload new assets. Select overwrite if you would like to overwrite a file with the same name.';
$lang['form_label_preview/kb'] = 'Preview/kb';
$lang['form_label_link'] = 'Link';
$lang['form_label_asset_folder'] = 'Asset folder';
$lang['form_label_new_file_name'] = 'New file name';
$lang['form_label_subfolder'] = 'Subfolder';
$lang['form_label_overwrite'] = 'Overwrite';
$lang['form_label_create_thumb'] = 'Create thumb';
$lang['form_label_resize_method'] = 'Resize method';
$lang['form_label_maintain_ratio'] = 'maintain ratio';
$lang['form_label_resize_and_crop'] = 'crop if needed';
$lang['form_label_overwrite'] = 'Overwrite';
$lang['form_label_width'] = 'Width';
$lang['form_label_height'] = 'Height';
$lang['form_label_alt'] = 'Alt';
$lang['form_label_align'] = 'Align';
$lang['form_label_master_dim'] = 'Master dimension';
$lang['form_label_unzip'] = 'Unzip zip files';
$lang['assets_upload_action'] = 'Upload';
$lang['assets_select_action'] = 'Select';
$lang['assets_comment_asset_folder'] = 'The asset folder that it will be uploaded to';
$lang['assets_comment_filename'] = 'If no name is provided, the filename that already exists will be used.';
$lang['assets_comment_subfolder'] = 'Will attempt to create a new subfolder to place your asset.';
$lang['assets_comment_overwrite'] = 'Overwrite a file with the same name. If unchecked, a new file will be uploaded with a version number appended to the end of it.';
$lang['assets_heading_general'] = 'General';
$lang['assets_heading_image_specific'] = 'Image Specific';
$lang['assets_comment_thumb'] = 'Create a thumbnail of the image.';
$lang['assets_comment_resize_method'] = 'Maintains the aspect ratio or resizes and crops the image to fit the provided dimensions. If "Create thumbnail" is selected, it will only effect the size of the thumbnail.';
$lang['assets_comment_width'] = 'Will change the width of an image to the desired amount. If "Create thumbnail" is selected, then it will only effect the size of the thumbnail.';
$lang['assets_comment_height'] = 'Will change the height of an image to the desired amount. If "Create thumbnail" is selected, it will only effect the size of the thumbnail.';
$lang['assets_comment_master_dim'] = 'Specifies the master dimension to use for resizing. If the source image size does not allow perfect resizing to those dimensions, this setting determines which axis should be used as the hard value. "auto" sets the axis automatically based on whether the image is taller then wider, or vice versa.';
$lang['assets_comment_unzip'] = 'Unzips a zip file';
$lang['assets_encryption_key_missing'] = 'Missing $config[\'encryption_key\'] value to your CI config file.';

/*
|--------------------------------------------------------------------------
| Site Variables
|--------------------------------------------------------------------------
*/
$lang['sitevariables_instructions'] = 'Here you can manage the site variables for your website.';
$lang['sitevariables_scope'] = 'Scope';


/*
|--------------------------------------------------------------------------
| Users
|--------------------------------------------------------------------------
*/
$lang['users_instructions'] = 'Here you can manage the data for users.';
$lang['permissions_heading'] = 'Permissions';
$lang['form_label_language'] = 'Language';
$lang['form_label_send_email'] = 'Send Email';
$lang['btn_send_email'] = 'Send Email';
$lang['new_user_email_subject'] = 'Your FUEL CMS account has been created';
$lang['new_user_email'] = 'Your FUEL CMS account has been created with the user name of "%1s". Click the following link to set your FUEL password: 
%2s';
$lang['new_user_account_email'] = 'Your FUEL CMS account has been created with the user name of "%2s" and the password "%3s". 
Click the following link to login: %1s';
$lang['new_user_created_notification'] = 'The user information was successfully saved and a notification was sent to %1s.';
$lang['error_cannot_deactivate_yourself'] = 'You cannot deactivate yourself.';

/*
|--------------------------------------------------------------------------
| Permissions
|--------------------------------------------------------------------------
*/
$lang['permissions_instructions'] = 'Here you can manage the permissions for FUEL modules and later assign them to users.';
$lang['form_label_other_perms'] = 'Generate related simple<br /> module permissions';

/*
|--------------------------------------------------------------------------
| Manage Cache
|--------------------------------------------------------------------------
*/
$lang['cache_cleared'] = 'The cache has been cleared.';
$lang['cache_instructions'] = 'You are about to clear the page cache of the site.';
$lang['cache_no_clear'] = 'No, don\'t clear cache';
$lang['cache_yes_clear'] = 'Yes, clear cache';


/*
|--------------------------------------------------------------------------
| Settings
|--------------------------------------------------------------------------
*/
$lang['settings_none'] = 'There are no settings for any advanced modules to manage.';
$lang['settings_manage'] = 'Manage the settings for the following advanced modules:';
$lang['settings_problem'] = 'There was a problem with the settings for the advanced module <strong>%1s</strong>. <br />Check that <strong>/fuel/modules/%1s/config/%1s.php</strong> config is configured to handle settings.';


/*
|--------------------------------------------------------------------------
| Generate
|--------------------------------------------------------------------------
*/
$lang['error_not_cli_request'] = 'This is not a CLI request.';
$lang['error_not_in_dev_mode'] = 'This will only run in dev mode.';
$lang['error_missing_generation_files'] = 'There are no generation files to create for %1s.';


/*
|--------------------------------------------------------------------------
| Table Actions
|--------------------------------------------------------------------------
*/
$lang['table_action_edit'] = 'EDIT';
$lang['table_action_delete'] = 'DELETE';
$lang['table_action_view'] = 'VIEW';
$lang['click_to_toggle'] = 'click to toggle';
$lang['table_action_login_as'] = 'LOGIN AS';


/*
|--------------------------------------------------------------------------
| Labels
|--------------------------------------------------------------------------
*/
$lang['label_show'] = 'Show:';
$lang['label_language'] = 'Language:';
$lang['label_restore_from_prev'] = 'Restore from previous version...';
$lang['label_select_another'] = 'Select another...';
$lang['label_select_one'] = 'Select one...';
$lang['label_belongs_to'] = 'Belongs to';
$lang['label_select_a_language'] = 'Select a language...';


/*
|--------------------------------------------------------------------------
| Buttons
|--------------------------------------------------------------------------
*/
$lang['btn_list'] = 'List';
$lang['btn_tree'] = 'Tree';
$lang['btn_create'] = 'Create';
$lang['btn_delete_multiple'] = 'Delete Multiple';
$lang['btn_rearrange'] = 'Rearrange';
$lang['btn_search'] = 'Search';
$lang['btn_view'] = 'View';
$lang['btn_publish'] = 'Publish';
$lang['btn_unpublish'] = 'Unpublish';
$lang['btn_activate'] = 'Activate';
$lang['btn_deactivate'] = 'Deactivate';
$lang['btn_delete'] = 'Delete';
$lang['btn_duplicate'] = 'Duplicate';
$lang['btn_replace'] = 'Replace';
$lang['btn_ok'] = 'OK';
$lang['btn_upload'] = 'Upload';
$lang['btn_download'] = 'Download';
$lang['btn_export_data'] = 'Export Data';

$lang['btn_no'] = 'No';
$lang['btn_yes'] = 'Yes';

$lang['btn_no_upload'] = 'No, don\'t upload it';
$lang['btn_yes_upload'] = 'Yes, upload it';

$lang['btn_no_dont_delete'] = 'No, don\'t delete it';
$lang['btn_yes_dont_delete'] = 'Yes,  delete it';


/*
|--------------------------------------------------------------------------
| Common Form Labels
|--------------------------------------------------------------------------
*/
$lang['form_label_name'] = 'Name';
$lang['form_label_title'] = 'Title';
$lang['form_label_label'] = 'Label';
$lang['form_label_location'] = 'Location';
$lang['form_label_published'] = 'Published';
$lang['form_label_active'] = 'Active';
$lang['form_label_precedence'] = 'Precedence';
$lang['form_label_date_added'] = 'Date added';
$lang['form_label_last_updated'] = 'Last updated';
$lang['form_label_file'] = 'File';
$lang['form_label_value'] = 'Value';
$lang['form_label_email'] = 'Email';
$lang['form_label_user_name'] = 'User name';
$lang['form_label_first_name'] = 'First name';
$lang['form_label_last_name'] = 'Last name';
$lang['form_label_super_admin'] = 'Super admin';
$lang['form_label_password'] = 'Password';
$lang['form_label_confirm_password'] = 'Confirm password';
$lang['form_label_new_password'] = 'New password';
$lang['form_label_new_invite'] = 'Send new user invite';
$lang['form_label_description'] = 'Description';
$lang['form_label_entry_date'] = 'Entry date';
$lang['form_label_message'] = 'Message';
$lang['form_label_image'] = 'Image';
$lang['form_label_upload_image'] = 'Upload image';
$lang['form_label_upload_images'] = 'Upload images';
$lang['form_label_content'] = 'Content';
$lang['form_label_excerpt'] = 'Excerpt';
$lang['form_label_permalink'] = 'Permalink';
$lang['form_label_slug'] = 'Slug';
$lang['form_label_url'] = 'URL';
$lang['form_label_link'] = 'Link';
$lang['form_label_pdf'] = 'PDF';
$lang['form_label_canonical'] = 'Canonical';
$lang['form_label_og_title'] = 'Open Graph title';
$lang['form_label_og_description'] = 'Open Graph description';
$lang['form_label_og_image'] = 'Open Graph image';
$lang['form_label_category_id'] = 'Category';
$lang['form_label_context'] = 'Context';

$lang['form_label_group_id'] = 'Group';
$lang['form_label_or_select'] = 'OR select';

$lang['form_enum_option_yes'] = 'yes';
$lang['form_enum_option_no'] = 'no';

$lang['required_text'] = 'required fields';


/*
|--------------------------------------------------------------------------
| Layouts
|--------------------------------------------------------------------------
*/
$lang['layout_field_main_copy'] = 'This is the main layout to be used for your site.';
$lang['layout_field_page_title'] = 'Page title';
$lang['layout_field_meta_description'] = 'Meta description';
$lang['layout_field_meta_keywords'] = 'Meta keywords';
$lang['layout_field_body'] = 'Body';
$lang['layout_field_heading'] = 'Heading';
$lang['layout_field_body_description'] = 'Main content of the page';
$lang['layout_field_body_class'] = 'Body class';
$lang['layout_field_redirect_to'] = 'Redirect to';
$lang['layout_field_alias'] = 'Alias';

$lang['layout_field_301_redirect_copy'] = 'This layout will do a 301 redirect to another page.';
$lang['layout_field_alias_copy'] = 'This layout is similar to a 301 redirect but the location of the page does not change and <br />the page content from the specified location is used to render the page.';
$lang['layout_field_sitemap_xml_copy'] = 'This layout is used to generate a sitemap. For this page to appear, a sitemap.xml must not exist on the server.';
$lang['layout_field_robots_txt_copy'] = 'This layout is used to generate a robots.txt file. For this page to appear, a robots.txt must not exist on the server.';
$lang['layout_field_none_copy'] = 'This layout is the equivalent of having no layout assigned.';

$lang['layout_field_frequency'] = 'Frequency';
$lang['layout_field_frequency_always'] = 'always';
$lang['layout_field_frequency_hourly'] = 'hourly';
$lang['layout_field_frequency_daily'] = 'daily';
$lang['layout_field_frequency_weekly'] = 'weekly';
$lang['layout_field_frequency_monthly'] = 'monthly';
$lang['layout_field_frequency_yearly'] = 'yearly';
$lang['layout_field_frequency_never'] = 'never';


/*
|--------------------------------------------------------------------------
| Tooltips
|--------------------------------------------------------------------------
*/
$lang['tooltip_dbl_click_to_open'] = 'Double click to open';


/*
|--------------------------------------------------------------------------
| Pagination
|--------------------------------------------------------------------------
*/

$lang['pagination_prev_page'] = '&lt;';
$lang['pagination_next_page'] = '&gt;';
$lang['pagination_first_link'] = '&lsaquo; First';
$lang['pagination_last_link'] = 'Last &rsaquo;';


/*
|--------------------------------------------------------------------------
| Actions
|--------------------------------------------------------------------------
*/
$lang['action_edit'] = 'Edit';
$lang['action_create'] = 'Create';
$lang['action_delete'] = 'Delete';
$lang['action_upload'] = 'Upload';
$lang['action_replace'] = 'Replace';

/*
|--------------------------------------------------------------------------
| Migrations
|--------------------------------------------------------------------------
*/
$lang['database_migration_success'] = 'Successful database migration to version %1s';

//$lang['import'] = 'Import';

/*
|--------------------------------------------------------------------------
| Installation
|--------------------------------------------------------------------------
*/
$lang['install_cli_intro'] = "The FUEL CMS installer is an easy way to setup the CMS with common configurations. It will do the following:\n";
$lang['install_cli_intro'] .= "1) Automatically generate an encryption key in fuel/application/config/config.php.\n";
$lang['install_cli_intro'] .= "2) Set the session save path in fuel/application/config/config.php.\n";
$lang['install_cli_intro'] .= "3) Enable the CMS admin by changing the 'admin_enabled' config value in fuel/application/config/MY_fuel.php.\n";
$lang['install_cli_intro'] .= "4) Change the 'fuel_mode' config value in in fuel/application/config/MY_fuel.php to allow for pages to be created in the CMS.\n";
$lang['install_cli_intro'] .= "5) Change the 'site_name' config value in the fuel/application/config/MY_fuel.php.\n";
$lang['install_cli_intro'] .= "6) Setup your environments fuel/application/config/environments.php.\n";
$lang['install_cli_intro'] .= "7) Will make the fuel/application/logs, fuel/application/cache and assets/images folders writable.\n";
$lang['install_cli_intro'] .= "8) Update the fuel/application/config/database.php file with the inputted values.\n";
$lang['install_cli_intro'] .= "9) Create a database and install the fuel_schema.sql file using your local MySQL connection.\n";

$lang['install_session_path'] = 'By default, FUEL CMS saves sessions using the default "file" setting in the fuel/application/config/config.php file. Where would you like the session files be stored (leave blank to keep it as the default)?';
$lang['install_site_name'] = 'What would you like the site name to be for this FUEL CMS installation?';
$lang['install_environments_testing'] = 'What are the domains for your TESTING environment (e.g. myserver.com *.mystagingserver.com)?';
$lang['install_environments_production'] = 'What are the domains for your PRODUCTION environment (e.g. myserver.com *.myserver.com)?';
$lang['install_permissions'] = 'What permissions do you want for your writable folders (e.g. 0755, 0775, 0777)?';
$lang['install_db_name'] = 'What do you want the name of your database to be?';
$lang['install_db_user'] = 'What is the user name to connect to your database?';
$lang['install_db_pwd'] = 'What is the password for that user?';

$lang['install_success'] = 'Your FUEL CMS installation is complete!';
$lang['install_success_with_errors'] = "Your FUEL CMS installation is complete but the follow ERRORS did occur:\n%1s";
$lang['install_further_info'] = "Now, to access the FUEL CMS admin, browse to your installation folder in your browser enter '/fuel' (e.g. localhost/fuel) in your location bar.\n";
$lang['install_further_info'] .= "For additional configuration options, go to https://docs.getfuelcms.com/installation/configuration.\n";
$lang['install_further_info'] .= "For questions, or bug reports, go to https://github.com/daylightstudio/FUEL-CMS/issues or visit us at https://forums.getfuelcms.com.\n";

$lang['update_cli_intro'] = "FUEL CMS 1.4x is built on CodeIgniter 3. If you are upgrading from 1.3x or earlier, this updater will help fix some of the common issues when upgrading including:\n";
$lang['update_cli_intro'] .= "1) Upper-case first letter for models, libraries and controller file names.\n";
$lang['update_cli_intro'] .= "2) Will upper case common references to Base_module_model.php within model files.\n";
$lang['update_cli_intro'] .= "3) Update common method signatures in models and libraries like form_fields, _common_query and initialize to match their parent's signature.\n";
$lang['update_cli_intro'] .= "WARNING: Run this ONLY if you are using GIT in case you need to roll back!\n";
$lang['update_cli_intro'] .= "Do you wish to continue (y/n)";
$lang['update_success'] = 'Update complete!';

// now include the Javascript specific ones since there is some crossover
include('fuel_js_lang.php');

/* End of file fuel_lang.php */
/* Location: ./modules/fuel/language/english/fuel_lang.php */
