<?php
/*
|--------------------------------------------------------------------------
| FUEL NAVIGATION: An array of navigation items for the left menu
|--------------------------------------------------------------------------
*/
$config['nav']['tools']['tools/backup'] = lang('module_backup');


/*
|--------------------------------------------------------------------------
| TOOL SETTING: DB Backup
|--------------------------------------------------------------------------
*/

$config['backup'] = array();

// used for the name of the backup file. A value of AUTO will automatically create the name
$config['backup']['backup_file_prefix'] = 'AUTO';

// date format to append to file name
$config['backup']['backup_file_date_format'] = 'Y-m-d';

// ZIP up the file or not
$config['backup']['backup_zip'] = FALSE;

// ZIP up the file or not
$config['backup']['backup_download'] = TRUE;

// number of days to hold backups. A value of 0 or FALSE will be forever
$config['backup']['backup_days_to_keep'] = 30;

// determines whether to backup assets by default
$config['backup']['backup_assets'] = FALSE;

// determines whether to backup assets by default
$config['backup']['backup_cron_email'] = '';

// determines whether to backup assets by default
$config['backup']['backup_cron_email_file'] = TRUE;

// used for the admin/manage/backup. Beow default looks for folder called 
//data_backup at the same level as the system and application folder
$config['backup']['db_backup_path'] = INSTALL_ROOT.'data_backup/';
$config['backup']['db_backup_prefs'] = array(
				'ignore'      => array(),           // List of tables to omit from the backup
				'add_drop'    => TRUE,              // Whether to add DROP TABLE statements to backup file
				'add_insert'  => TRUE              // Whether to add INSERT data to backup file
				);

