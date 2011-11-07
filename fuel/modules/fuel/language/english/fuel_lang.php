<?php

/*
|--------------------------------------------------------------------------
| General
|--------------------------------------------------------------------------
*/
$lang['fuel_page_title'] = 'FUEL CMS';
$lang['logged_in_as'] = 'Logged in as:';
$lang['logout'] = 'Logout';
$lang['fuel_developed_by'] = 'FUEL CMS version %1s is developed by <a href="http://www.thedaylightstudio.com" target="_blank">Daylight Studio</a> and built upon the <a href="http://www.codeigniter.com" target="_blank">CodeIgniter</a> framework.';
$lang['fuel_copyright'] = 'Copyright &copy; %1s Daylight Studio. All Rights Reserved.';


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
$lang['error_cache_folder_not_writable'] = 'You must make the cache folder %1s writable.';
$lang['error_exporting_view'] = 'There was an error in creating the associated view file for the location %1s because the variable with the name <strong><em>%2s</em></strong> does not exist.';
$lang['error_no_curl_lib'] = 'You must have the curl php extension to use these tools.';
$lang['error_inline_page_edit'] = 'This variable must be edited in the associated views/_variables file.';
$lang['error_saving'] = 'There was an error saving.';
$lang['error_cannot_preview'] = 'There was an error in trying to preview this page.';
$lang['error_cannot_make_api_call'] = 'There was an error making the API call to %1s.';
$lang['error_sending_email'] = 'There was an error sending an email to %1s.';
$lang['error_upload'] = 'There was an error uploading the your file.';
$lang['error_create_nav_group'] = 'Please create a Navigation Group';
$lang['error_requires_string_value'] = 'The name field should be a string value';
$lang['error_missing_params'] = 'You are missing parameters to view this page';
$lang['error_invalid_method'] = 'Invalid method name';


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
$lang['login_reset_pwd'] = 'Reset Password';
$lang['login_btn'] = 'Login';


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
| Login/Password Reset
|--------------------------------------------------------------------------
*/

$lang['pwd_reset'] = 'An email to confirm your password reset is on its way.';
$lang['pwd_reset_subject'] = "FUEL admin password reset request";
$lang['pwd_reset_email'] = "Click the following link to confirm the reset of your FUEL password:\n%1s";
$lang['pwd_reset_subject_success'] = "FUEL admin password reset success";
$lang['pwd_reset_email_success'] = "Your FUEL password has been reset to %1s. To change your password, login to the FUEL CMS admin with this password and click on your login name in the upper right to access your profile information.";
$lang['pwd_reset_success'] = 'Your password was successfully reset and an email has been sent to you with the new password.';
$lang['cache_cleared'] = "Site cache cleared explicitly";
$lang['module_restored_success'] = 'Previous version successfully restored.';


/*
|--------------------------------------------------------------------------
| Menu Titles / Sections
|--------------------------------------------------------------------------
*/

$lang['module_dashboard'] = 'Dashboard';
$lang['module_pages'] = 'Pages';
$lang['module_blocks'] = 'Blocks';
$lang['module_navigation'] = 'Navigation';
$lang['module_assets'] = 'Assets';
$lang['module_sitevariables'] = 'Site Variables';
$lang['module_users'] = 'Users';
$lang['module_permissions'] = 'Permissions';
$lang['module_manage_cache'] = 'Page Cache';
$lang['module_manage_activity'] = 'Activity Log';

$lang['section_site'] = 'Site';
$lang['section_blog'] = 'Blog';
$lang['section_modules'] = 'Modules';
$lang['section_manage'] = 'Manage';
$lang['section_tools'] = 'Tools';
$lang['section_recently_viewed'] = 'Recently Viewed';

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
$lang['data_not_deleted'] = 'Some or all data couldn\'t be deleted.';
$lang['no_data'] = 'No data to display.';
$lang['no_preview_path'] = 'There is no preview path assigned to this module.';
$lang['delete_item_message'] = 'You are about to delete the item:';


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

$lang['pages_instructions'] = 'Here you can manage the data associated with the page.';
$lang['pages_associated_navigation'] = 'Associated Navigation';
$lang['pages_success_upload'] = 'The page view was successfully uploaded.';
$lang['pages_upload_instructions'] = 'Select a view file and upload to a page below.';


// page specific form fields
$lang['form_label_layout'] = 'Layout';
$lang['form_label_cache'] = 'Cache';
$lang['pages_last_updated_by'] = 'Last updated %1s by %2s';
$lang['pages_not_published'] = 'This page is not published.';
$lang['pages_default_location'] = 'example: company/about';


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
$lang['navigation_instructions'] = 'Here you create and edit the top menu items of the page.';
$lang['navigation_import_instructions'] = 'Select a navigation group and upload a file to import below. The file should contain the PHP array variable <strong>$nav</strong>. For a reference of the array format, please consult the <a href="http://www.getfuelcms.com/user_guide/modules/fuel/navigation" target="_blank">user guide</a>';
$lang['navigation_success_upload'] = 'The navigation was successfully uploaded.';
$lang['form_label_navigation_group'] = 'Navigation Group';
$lang['form_label_nav_key'] = 'Nav Key';
$lang['form_label_parent_id'] = 'Parent';
$lang['form_label_attributes'] = 'Attributes';
$lang['form_label_selected'] = 'Selected';
$lang['form_label_hidden'] = 'Hidden';

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
$lang['form_label_asset_folder'] = 'Asset Folder';
$lang['form_label_new_file_name'] = 'New file name';
$lang['form_label_subfolder'] = 'Subfolder';
$lang['form_label_overwrite'] = 'Overwrite';
$lang['form_label_create_thumb'] = 'Create thumb';
$lang['form_label_maintain_ratio'] = 'Maintain ratio';
$lang['form_label_overwrite'] = 'Overwrite';
$lang['form_label_width'] = 'Width';
$lang['form_label_height'] = 'Height';
$lang['form_label_master_dimension'] = 'Master dimension';
$lang['assets_upload_action'] = 'Upload';
$lang['assets_comment_asset_folder'] = 'The asset folder that it will be uploaded to';
$lang['assets_comment_filename'] = 'If no name is provided, the filename that already exists will be used.';
$lang['assets_comment_subfolder'] = 'Will attempt to create a new subfolder to place your asset.';
$lang['assets_comment_overwrite'] = 'Overwrite a file with the same name. If unchecked, a new file will be uploaded with a version number appended to the end of it.';
$lang['assets_heading_image_specific'] = 'Image Specific';
$lang['assets_comment_thumb'] = 'Create a thumbnail of the image.';
$lang['assets_comment_aspect_ratio'] = 'Maintain the aspect ratio of the image if resized.';
$lang['assets_comment_width'] = 'Will change the width of an image to the desired amount.';
$lang['assets_comment_height'] = 'Will change the height of an image to the desired amount.';
$lang['assets_comment_master_dim'] = 'Specifies the master dimension to use for resizing. If the source image size does not allow perfect resizing to those dimensions, this setting determines which axis should be used as the hard value. "auto" sets the axis automatically based on whether the image is taller then wider, or vice versa.';




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
$lang['new_user_email'] = 'Your FUEL CMS account has been created. You can login with the following information:
Login URL:'.site_url('fuel/login').'
User name: %1s
Password: %2s';
$lang['new_user_created_notification'] = 'The user information was successfully saved and a notification was sent to %1s.';


/*
|--------------------------------------------------------------------------
| Permissions
|--------------------------------------------------------------------------
*/
$lang['permissions_instructions'] = 'Here you can manage the permissions for FUEL modules and later assign them to users.';

// permissions
$lang['perm_assets'] = 'Assets';
$lang['perm_blocks'] = 'Manage Blocks';
$lang['perm_blog/categories'] = 'Blog Categories';
$lang['perm_blog/comments'] = 'Blog Comments';
$lang['perm_blog/links'] = 'Blog Links';
$lang['perm_blog/posts'] = 'Blog Posts';
$lang['perm_blog/settings'] = 'Blog Settings';
$lang['perm_blog/users'] = 'Blog Authors';
$lang['perm_google_analytics'] = 'Google Analytics';
$lang['perm_manage'] = 'View the Manage Dashboard Page';
$lang['perm_manage/activity'] = 'View activity logs';
$lang['perm_manage/cache'] = 'Manage the page cache';
$lang['perm_myPHPadmin'] = 'myPHPadmin';
$lang['perm_navigation'] = 'Manage navigation';
$lang['perm_pages'] = 'Manage pages';
$lang['perm_pages_delete'] = 'Ability to Delete Pages';
$lang['perm_pages_publish'] = 'Ability to Publish Pages';
$lang['perm_permissions'] = 'Manage Permissions';
$lang['perm_projects'] = 'Projects';
$lang['perm_projects_delete'] = 'Delete Projects';
$lang['perm_projects_publish'] = 'Publish Projects';
$lang['perm_quotes'] = 'Quotes';
$lang['perm_site_docs'] = 'Site Documentation';
$lang['perm_sitevariables'] = 'Site Variables';
$lang['perm_tools'] = 'Manage Tools';
$lang['perm_tools/backup'] = 'Manage database backup';
$lang['perm_tools/cronjobs'] = 'Cronjobs';
$lang['perm_tools/seo'] = 'Page Analysis';
$lang['perm_tools/seo/google_keywords'] = 'Google Keywords';
$lang['perm_tools/tester'] = 'Tester Module';
$lang['perm_tools/user_guide'] = 'Access the User Guide';
$lang['perm_tools/validate'] = 'Validate';
$lang['perm_users'] = 'Manage users';


/*
|--------------------------------------------------------------------------
| Manage Cache
|--------------------------------------------------------------------------
*/
$lang['cache_instructions'] = 'You are about to clear the page cache of the site.';
$lang['cache_no_clear'] = 'No, don\'t clear cache';
$lang['cache_yes_clear'] = 'Yes, clear cache';


/*
|--------------------------------------------------------------------------
| Table Actions
|--------------------------------------------------------------------------
*/
$lang['table_action_edit'] = 'EDIT';
$lang['table_action_delete'] = 'DELETE';
$lang['table_action_view'] = 'VIEW';


/*
|--------------------------------------------------------------------------
| Labels
|--------------------------------------------------------------------------
*/
$lang['label_show'] = 'Show:';
$lang['label_restore_from_prev'] = 'Restore from previous version...';
$lang['label_select_another'] = 'Select another...';


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
$lang['btn_ok'] = 'OK';
$lang['btn_upload'] = 'Upload';

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
$lang['form_label_date_added'] = 'Date Added';
$lang['form_label_last_updated'] = 'Last Updated';
$lang['form_label_file'] = 'File';
$lang['form_label_value'] = 'Value';
$lang['form_label_email'] = 'Email';
$lang['form_label_user_name'] = 'User Name';
$lang['form_label_first_name'] = 'First Name';
$lang['form_label_last_name'] = 'Last Name';
$lang['form_label_super_admin'] = 'Super Admin';
$lang['form_label_password'] = 'Password';
$lang['form_label_confirm_password'] = 'Confirm Password';
$lang['form_label_new_password'] = 'New Password';
$lang['form_label_description'] = 'Description';
$lang['form_label_entry_date'] = 'Entry Date';
$lang['form_label_message'] = 'Message';
$lang['form_label_image'] = 'Upload Image';
$lang['form_label_upload_image'] = 'Upload Image';
$lang['form_label_upload_images'] = 'Upload Images';
$lang['form_label_content'] = 'Content';
$lang['form_label_excerpt'] = 'Excerpt';
$lang['form_label_permalink'] = 'Permalink';
$lang['form_label_slug'] = 'Slug';
$lang['form_label_url'] = 'URL';
$lang['form_label_group_id'] = 'Group';

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
$lang['layout_field_body_description'] = 'Main content of the page';
$lang['layout_field_body_class'] = 'Body class';
$lang['layout_field_redirect_to'] = 'Redirect to';

$lang['layout_field_301_redirect_copy'] = 'This layout will do a 301 redirect to another page.';
$lang['layout_field_sitemap_xml_copy'] = 'This layout is used to generate a sitemap.';
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
| MISC
|--------------------------------------------------------------------------
*/

$lang['pagination_prev_page'] = '&lt;';
$lang['pagination_next_page'] = '&gt;';
$lang['pagination_first_link'] = '&lsaquo; First';
$lang['pagination_last_link'] = 'Last &rsaquo;';

$lang['action_edit'] = 'Edit';
$lang['action_create'] = 'Create';


// now include the Javascript specific ones since there is some crossover
include('fuel_js_lang.php');

/* End of file fuel_lang.php */
/* Location: ./modules/fuel/language/english/fuel_lang.php */