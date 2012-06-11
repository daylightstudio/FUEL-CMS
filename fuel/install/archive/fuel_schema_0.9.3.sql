SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- --------------------------------------------------------

-- 
-- Table structure for table `fuel_archives`
-- 

CREATE TABLE `fuel_archives` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `ref_id` int(10) unsigned NOT NULL,
  `table_name` varchar(50) collate utf8_unicode_ci NOT NULL,
  `data` text collate utf8_unicode_ci NOT NULL,
  `version` smallint(5) unsigned NOT NULL,
  `version_timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `archived_user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `fuel_archives`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `fuel_blocks`
-- 

CREATE TABLE `fuel_blocks` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `name` varchar(100) collate utf8_unicode_ci NOT NULL,
  `description` varchar(255) collate utf8_unicode_ci NOT NULL,
  `view` text collate utf8_unicode_ci NOT NULL,
  `published` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'yes',
  `date_added` datetime default NULL,
  `last_modified` timestamp NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `fuel_blocks`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `fuel_blog_categories`
-- 

CREATE TABLE `fuel_blog_categories` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `permalink` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'If left blank, the permalink will automatically be created for you.',
  `published` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'yes',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `permalink` (`permalink`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `fuel_blog_categories`
-- 

INSERT INTO `fuel_blog_categories` (`id`, `name`, `permalink`, `published`) VALUES 
(1, 'Uncategorized', 'uncategorized', 'yes');

-- --------------------------------------------------------

-- 
-- Table structure for table `fuel_blog_comments`
-- 

CREATE TABLE `fuel_blog_comments` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `post_id` int(10) unsigned NOT NULL,
  `parent_id` int(10) unsigned NOT NULL,
  `author_id` int(10) unsigned NOT NULL,
  `author_name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `author_email` varchar(255) collate utf8_unicode_ci NOT NULL,
  `author_website` varchar(255) collate utf8_unicode_ci NOT NULL,
  `author_ip` varchar(32) collate utf8_unicode_ci NOT NULL,
  `is_spam` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'no',
  `content` text collate utf8_unicode_ci NOT NULL,
  `published` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'yes',
  `date_added` datetime NOT NULL,
  `last_modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `fuel_blog_comments`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `fuel_blog_links`
-- 

CREATE TABLE `fuel_blog_links` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `url` varchar(255) default NULL,
  `target` enum('blank','self','parent') default 'blank',
  `description` varchar(100) default NULL,
  `precedence` int(11) NOT NULL default '0',
  `published` enum('yes','no') default 'yes',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `fuel_blog_links`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `fuel_blog_posts`
-- 

CREATE TABLE `fuel_blog_posts` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(255) collate utf8_unicode_ci NOT NULL,
  `permalink` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'This is the last part of the URL string. If left blank, the permalink will automatically be created for you.',
  `content` text collate utf8_unicode_ci NOT NULL,
  `content_filtered` text collate utf8_unicode_ci NOT NULL,
  `formatting` varchar(100) collate utf8_unicode_ci default NULL,
  `excerpt` text collate utf8_unicode_ci NOT NULL COMMENT 'A condensed version of the content.',
  `author_id` int(10) unsigned NOT NULL COMMENT 'If left blank, you will be assumed the author.',
  `sticky` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'no',
  `allow_comments` enum('yes','no') collate utf8_unicode_ci default 'no',
  `date_added` datetime default NULL,
  `last_modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `published` enum('yes','no') collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `permalink` (`permalink`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `fuel_blog_posts`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `fuel_blog_posts_to_categories`
-- 

CREATE TABLE `fuel_blog_posts_to_categories` (
  `post_id` int(10) unsigned NOT NULL,
  `category_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`post_id`,`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `fuel_blog_posts_to_categories`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `fuel_blog_settings`
-- 

CREATE TABLE `fuel_blog_settings` (
  `name` varchar(100) collate utf8_unicode_ci NOT NULL,
  `value` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `fuel_blog_settings`
-- 

INSERT INTO `fuel_blog_settings` (`name`, `value`) VALUES 
('title', 'My Blog'),
('uri', 'blog/'),
('theme_layout', 'blog'),
('theme_path', 'themes/default/'),
('theme_module', 'blog'),
('use_cache', '0'),
('cache_ttl', '3600'),
('per_page', '2'),
('description', ''),
('use_captchas', '1'),
('monitor_comments', '1'),
('save_spam', '1'),
('allow_comments', '1'),
('akismet_api_key', ''),
('comments_time_limit', ''),
('multiple_comment_submission_time_limit', '30'),
('asset_upload_path', 'images/blog/');

-- --------------------------------------------------------

-- 
-- Table structure for table `fuel_blog_users`
-- 

CREATE TABLE `fuel_blog_users` (
  `fuel_user_id` int(10) unsigned NOT NULL,
  `display_name` varchar(50) NOT NULL,
  `website` varchar(100) NOT NULL,
  `about` text NOT NULL,
  `avatar_image` varchar(255) NOT NULL,
  `twitter` varchar(255) NOT NULL,
  `facebook` varchar(255) NOT NULL,
  `date_added` datetime default NULL,
  `active` enum('yes','no') NOT NULL default 'yes',
  PRIMARY KEY  (`fuel_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

-- 
-- Table structure for table `fuel_logs`
-- 

CREATE TABLE `fuel_logs` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `entry_date` datetime NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `fuel_logs`
-- 



-- 
-- Table structure for table `fuel_navigation`
-- 

CREATE TABLE `fuel_navigation` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `group_id` int(5) unsigned NOT NULL default '1',
  `location` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'The part of the path after the domain name that you want the link to go to (e.g. comany/about)',
  `nav_key` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'The nav key is a friendly ID that you can use for setting the selected state. If left blank, a default value will be set for you',
  `label` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'The name you want to appear in the menu',
  `parent_id` int(10) unsigned NOT NULL default '0' COMMENT 'Used for creating menu hierarchies. No value means it is a root level menu item',
  `precedence` int(10) unsigned NOT NULL default '0' COMMENT 'The higher the number, the greater the precedence and farther up the list the navigational element will appear',
  `attributes` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'Extra attributes that can be used for navigation implementation',
  `selected` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'The pattern to match for the active state. Most likely you leave this field blank',
  `hidden` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'no' COMMENT 'A hidden value can be added to the rendered output. This is not always necessary',
  `published` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'yes' COMMENT 'Determines whether the item is displayed or not',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `group_id` (`group_id`,`location`,`parent_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `fuel_navigation`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `fuel_navigation_groups`
-- 

CREATE TABLE `fuel_navigation_groups` (
  `id` int(3) unsigned NOT NULL auto_increment,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `published` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'yes',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `fuel_navigation_groups`
-- 

INSERT INTO `fuel_navigation_groups` (`id`, `name`, `published`) VALUES 
(1, 'main', 'yes');


-- --------------------------------------------------------

-- 
-- Table structure for table `fuel_pages`
-- 

CREATE TABLE `fuel_pages` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `location` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'Add the part of the URL after the root of your site (usually after the domain name). For the homepage, just put the word ''home''',
  `layout` varchar(50) collate utf8_unicode_ci NOT NULL COMMENT 'The name of the template to associate with this page',
  `published` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'yes' COMMENT 'A ''yes'' value will display the page and a ''no'' value will give a 404 error message',
  `cache` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'yes' COMMENT 'Cache controls whether the page will pull from the database, or from a saved file (which is more efficient). If a page has content that is dynamic, it''s best to set cache to ''no'', to prevent new content not being displayed.',
  `date_added` datetime default NULL,
  `last_modified` timestamp NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `last_modified_by` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `location` (`location`),
  KEY `layout` (`layout`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `fuel_pages`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `fuel_page_variables`
-- 

CREATE TABLE `fuel_page_variables` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `page_id` int(10) unsigned NOT NULL,
  `name` varchar(50) collate utf8_unicode_ci NOT NULL,
  `scope` varchar(255) collate utf8_unicode_ci NOT NULL,
  `value` text collate utf8_unicode_ci NOT NULL,
  `type` enum('string','int','boolean','array','template') collate utf8_unicode_ci NOT NULL default 'string',
  `active` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'yes',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `page_id` (`page_id`,`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `fuel_page_variables`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `fuel_permissions`
-- 

CREATE TABLE `fuel_permissions` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(50) collate utf8_unicode_ci NOT NULL COMMENT 'Permissions beginning with ''Manage'' will allow items to appear on the left menu',
  `description` varchar(255) collate utf8_unicode_ci NOT NULL,
  `active` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'yes',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `fuel_permissions`
-- 

INSERT INTO `fuel_permissions` (`id`,`name`,`description`,`active`) VALUES
	(1,'pages','Manage pages','yes'),
	(2,'pages_publish','Publish Pages','yes'),
	(3,'pages_delete','Delete Pages','yes'),
	(4,'navigation','Manage navigation','yes'),
	(5,'users','Manage users','yes'),
	(6,'tools/backup','Manage database backup','yes'),
	(7,'manage/cache','Manage the page cache','yes'),
	(8,'manage/activity','View activity logs','yes'),
	(9,'myPHPadmin','myPHPadmin','yes'),
	(10,'google_analytics','Google Analytics','yes'),
	(11,'tools/user_guide','Access the User Guide','yes'),
	(12,'manage','View the Manage Dashboard Page','yes'),
	(13,'permissions','Manage Permissions','yes'),
	(14,'tools','Manage Tools','yes'),
	(15,'tools/seo/google_keywords','Google Keywords','yes'),
	(16,'sitevariables','Site Variables','yes'),
	(17,'blog/posts','Blog Posts','yes'),
	(18,'blog/categories','Blog Categories','yes'),
	(19,'blog/comments','Blog Comments','yes'),
	(20,'blog/links','Blog Links','yes'),
	(21,'blog/users','Blog Authors','yes'),
	(22,'blog/settings','Blog Settings','yes'),
	(23,'assets','Assets','yes'),
	(24,'tools/validate','Validate','yes'),
	(25,'tools/seo','Page Analysis','yes'),
	(26,'tools/tester','Tester Module','yes'),
	(27,'blocks','Manage Blocks','yes'),
	(28,'site_docs','Site Documentation','yes'),
	(29,'tools/cronjobs','Cronjobs','yes');

-- --------------------------------------------------------

-- 
-- Table structure for table `fuel_site_variables`
-- 

CREATE TABLE `fuel_site_variables` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(100) collate utf8_unicode_ci NOT NULL,
  `value` text collate utf8_unicode_ci NOT NULL,
  `scope` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'Leave blank if you want the variable to be available to all pages',
  `active` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'yes',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `fuel_site_variables`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `fuel_users`
-- 

CREATE TABLE `fuel_users` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_name` varchar(30) collate utf8_unicode_ci NOT NULL,
  `password` varchar(64) collate utf8_unicode_ci NOT NULL,
  `email` varchar(100) collate utf8_unicode_ci NOT NULL,
  `first_name` varchar(30) collate utf8_unicode_ci NOT NULL,
  `last_name` varchar(30) collate utf8_unicode_ci NOT NULL,
  `language` varchar(30) collate utf8_unicode_ci NOT NULL default 'english',
  `reset_key` varchar(64) collate utf8_unicode_ci NOT NULL,
  `super_admin` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'no',
  `active` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'yes',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `fuel_users`
-- 

INSERT INTO `fuel_users` (`id`, `user_name`, `password`, `email`, `first_name`, `last_name`, `super_admin`, `active`) VALUES 
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', '', 'Admin', '', 'yes', 'yes');

-- --------------------------------------------------------

-- 
-- Table structure for table `fuel_user_to_permissions`
-- 

CREATE TABLE `fuel_user_to_permissions` (
  `user_id` int(10) unsigned NOT NULL,
  `permission_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`user_id`,`permission_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `fuel_user_to_permissions`
-- 

