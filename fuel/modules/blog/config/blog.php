<?php 
/*
|--------------------------------------------------------------------------
| FUEL NAVIGATION: An array of navigation items for the left menu
|--------------------------------------------------------------------------
*/
$config['nav']['blog'] = array(
	'blog/posts' => lang('module_blog_posts'), 
	'blog/categories' => lang('module_blog_categories'),  
	'blog/comments' => lang('module_blog_comments'), 
	'blog/links' => lang('module_blog_links'), 
	'blog/users' => lang('module_blog_authors'), 
	'blog/settings' => lang('module_blog_settings')
);

/*
|--------------------------------------------------------------------------
| Configurable in settings if blog_use_db_table_settings is set
|--------------------------------------------------------------------------
*/

// deterines whether to use this configuration below or the database for controlling the blogs behavior
$config['blog_use_db_table_settings'] = TRUE;

$config['blog'] = array();
$config['blog']['title'] = '';
$config['blog']['akismet_api_key'] = '';
$config['blog']['uri'] = 'blog/';
$config['blog']['theme_path'] = 'themes/default/';
$config['blog']['use_cache'] = '';
$config['blog']['cache_ttl'] = 3600;
$config['blog']['per_page'] = 5;
$config['blog']['description'] = '';
$config['blog']['use_captchas'] = 0;
$config['blog']['monitor_comments'] = 1;
$config['blog']['theme_layout'] = 'blog';
$config['blog']['save_spam'] = 1;
$config['blog']['allow_comments'] = 1;
$config['blog']['comments_time_limit'] = '';
$config['blog']['theme_module'] = 'blog';
$config['blog']['multiple_comment_submission_time_limit'] = 30;
$config['blog']['asset_upload_path'] = 'images/blog/';


// the cache folder to hold blog cache files
$config['blog_cache_group'] = 'blog';


/*
|--------------------------------------------------------------------------
| Programmer specific config (not exposed in settings)
|--------------------------------------------------------------------------
*/
// content formatting options
$config['blog']['formatting'] = array(
	'auto_typography' => 'Automatic',
	'Markdown' => 'Markdown',
	'' => 'None'
	);

// captcha options
$config['blog']['captcha'] = array(
				'img_width'	 => 120,
				'img_height' => 26,
				'expiration' => 600, // 10 minutes
				'bg_color' => '#4b4b4b',
				'char_color' => '#ffffff,#cccccc,#ffffff,#999999,#ffffff,#cccccc',
				'line_color' => '#ff9900,#414141,#ea631d,#aaaaaa,#f0a049,#ff9900'
			);

// comment form 
$config['blog']['comment_form'] = array();
$config['blog']['comment_form']['fields'] = array();



// tables for blog
$config['tables']['blog_posts'] = 'fuel_blog_posts';
$config['tables']['blog_categories'] = 'fuel_blog_categories';
$config['tables']['blog_users'] = 'fuel_blog_users';
$config['tables']['blog_comments'] = 'fuel_blog_comments';
$config['tables']['blog_links'] = 'fuel_blog_links';
$config['tables']['blog_posts_to_categories'] = 'fuel_blog_posts_to_categories';
$config['tables']['blog_settings'] = 'fuel_blog_settings';