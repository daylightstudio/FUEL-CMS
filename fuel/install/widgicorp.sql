-- phpMyAdmin SQL Dump
-- version 2.10.2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Nov 07, 2010 at 08:02 PM
-- Server version: 5.0.45
-- PHP Version: 5.3.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- Database: `fuel_widgicorp`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `fuel_archives`
-- 
DROP TABLE IF EXISTS `fuel_archives`;
CREATE TABLE `fuel_archives` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `ref_id` int(10) unsigned NOT NULL,
  `table_name` varchar(50) collate utf8_unicode_ci NOT NULL,
  `data` text collate utf8_unicode_ci NOT NULL,
  `version` smallint(5) unsigned NOT NULL,
  `version_timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `archived_user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- 
-- Dumping data for table `fuel_archives`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `fuel_blocks`
-- 
DROP TABLE IF EXISTS `fuel_blocks`;
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

INSERT INTO `fuel_blocks` (`id`, `name`, `view`, `published`, `date_added`, `last_modified`) VALUES 
(2, 'quote', '{$quote = fuel_model(''quotes'', array(''find'' = ''one'', ''order'' = ''RAND()''))}\n{if ($quote) }\n<div id="block_quote">\n	{quote($quote->content, $quote->name, $quote->title)}\n</div>\n{/if}', 'no', '2010-11-06 18:34:33', '2010-11-07 01:47:36'),
(3, 'showcase', '{$project = fuel_model(''projects'', array(''find'' = ''one'', ''where'' = array(''featured'' = ''yes''), ''order'' = ''RAND()''))}\n\n{if ($project) }\n<div id="block_showcase">\n	<h3>{$project->name}</h3>\n	<img src="{$project->image_path}" />\n</div>\n{/if}', 'yes', '2010-11-06 18:34:58', '2010-11-07 01:14:08');

-- --------------------------------------------------------

-- 
-- Table structure for table `fuel_blog_categories`
-- 
DROP TABLE IF EXISTS `fuel_blog_categories`;
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
(1, 'Uncategorized', 'uncategorized', 'yes'),
(2, 'Widgets', 'widgets', 'yes');

-- --------------------------------------------------------

-- 
-- Table structure for table `fuel_blog_comments`
-- 
DROP TABLE IF EXISTS `fuel_blog_comments`;
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `fuel_blog_comments`
-- 

INSERT INTO `fuel_blog_comments` (`id`, `post_id`, `parent_id`, `author_id`, `author_name`, `author_email`, `author_website`, `author_ip`, `is_spam`, `content`, `published`, `date_added`, `last_modified`) VALUES 
(1, 1, 0, 0, 'Dave', 'dave@thedaylightstudio.com', 'www.thedaylightstudo.com', '::1', 'no', 'I love star wars', 'no', '2010-11-07 19:24:42', '2010-11-07 19:24:42');

-- --------------------------------------------------------

-- 
-- Table structure for table `fuel_blog_links`
-- 

DROP TABLE IF EXISTS `fuel_blog_links`;
CREATE TABLE `fuel_blog_links` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) default NULL,
  `url` varchar(255) default NULL,
  `target` enum('blank','self','parent') default 'blank',
  `description` varchar(100) default NULL,
  `precedence` int(11) NOT NULL default '0',
  `published` enum('yes','no') default 'yes',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `fuel_blog_links`
-- 

INSERT INTO `fuel_blog_links` (`id`, `name`, `url`, `target`, `description`, `precedence`, `published`) VALUES 
(1, 'Star Wars', 'http://www.starwars.com', 'blank', '', 0, 'yes'),
(2, 'The Darth Side', 'http://darthside.blogspot.com/', 'blank', '', 0, 'yes');

-- --------------------------------------------------------

-- 
-- Table structure for table `fuel_blog_posts`
-- 

DROP TABLE IF EXISTS `fuel_blog_posts`;
CREATE TABLE `fuel_blog_posts` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(255) collate utf8_unicode_ci NOT NULL,
  `permalink` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'This is the last part of the url string. If left blank, the permalink will automatically be created for you.',
  `content` text collate utf8_unicode_ci NOT NULL,
  `content_filtered` text collate utf8_unicode_ci NOT NULL,
  `formatting` varchar(100) collate utf8_unicode_ci default NULL,
  `excerpt` text collate utf8_unicode_ci NOT NULL COMMENT 'A condensed version of the content',
  `author_id` int(10) unsigned NOT NULL COMMENT 'If left blank, you will assumed be the author.',
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

INSERT INTO `fuel_blog_posts` (`id`, `title`, `content`, `content_filtered`, `formatting`, `excerpt`, `permalink`, `author_id`, `sticky`, `allow_comments`, `date_added`, `last_modified`, `published`) VALUES 
(1, 'A long, long time ago, in a galaxy far, far away', 'Episode IV, A NEW HOPE It is a period of civil war. Rebel spaceships, striking from a hidden base, have won their first victory against the evil Galactic Empire. During the battle, Rebel spies managed to steal secret plans to the Empire&rsquo;s ultimate weapon, the DEATH STAR, an armored space station with enough power to destroy an entire planet. Pursued by the Empire&rsquo;s sinister agents, Princess Leia races home aboard her starship, custodian of the stolen plans that can save her people and restore freedom to the galaxy&hellip;.', 'Episode IV, A NEW HOPE It is a period of civil war. Rebel spaceships, striking from a hidden base, have won their first victory against the evil Galactic Empire. During the battle, Rebel spies managed to steal secret plans to the Empire&rsquo;s ultimate weapon, the DEATH STAR, an armored space station with enough power to destroy an entire planet. Pursued by the Empire&rsquo;s sinister agents, Princess Leia races home aboard her starship, custodian of the stolen plans that can save her people and restore freedom to the galaxy&hellip;.', 'auto_typography', '', 'a-long-long-time-ago-in-a-galaxy-far-far-away', 1, 'no', 'yes', '2010-11-06 15:27:00', '2010-11-07 19:16:35', 'yes');

-- --------------------------------------------------------

-- 
-- Table structure for table `fuel_blog_posts_to_categories`
-- 

DROP TABLE IF EXISTS `fuel_blog_posts_to_categories`;
CREATE TABLE `fuel_blog_posts_to_categories` (
  `post_id` int(10) unsigned NOT NULL,
  `category_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`post_id`,`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `fuel_blog_posts_to_categories`
-- 

INSERT INTO `fuel_blog_posts_to_categories` (`post_id`, `category_id`) VALUES 
(1, 1);

-- --------------------------------------------------------

-- 
-- Table structure for table `fuel_blog_settings`
-- 

DROP TABLE IF EXISTS `fuel_blog_settings`;
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

DROP TABLE IF EXISTS `fuel_blog_users`;
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

-- 
-- Dumping data for table `fuel_blog_users`
-- 

INSERT INTO `fuel_blog_users` (`fuel_user_id`, `display_name`, `website`, `about`, `avatar_image`, `twitter`, `facebook`, `date_added`, `active`) VALUES 
(1, '', '', '', 'team_placeholder.png', '', '', '2010-11-07 12:14:53', 'yes');

-- --------------------------------------------------------

-- 
-- Table structure for table `fuel_logs`
-- 

DROP TABLE IF EXISTS `fuel_logs`;
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

-- --------------------------------------------------------

-- 
-- Table structure for table `fuel_navigation`
-- 

DROP TABLE IF EXISTS `fuel_navigation`;
CREATE TABLE `fuel_navigation` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `group_id` int(5) unsigned NOT NULL default '1',
  `location` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'The part of the path after the domain name that you want the link to go to (e.g. comany/about_us)',
  `nav_key` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'The nav key is a friendly ID that you can use for setting the selected state. If left blank, a default value will be set for you.',
  `label` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'The name you want to appear in the menu',
  `parent_id` int(10) unsigned NOT NULL default '0' COMMENT 'Used for creating menu hierarchies. No value means it is a root level menu item',
  `precedence` int(10) unsigned NOT NULL default '0' COMMENT 'The higher the number, the greater the precedence and farther up the list the navigational element will appear',
  `attributes` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'Extra attributes that can be used for navigation implementation',
  `selected` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'The pattern to match for the active state. Most likely you leave this field blank',
  `hidden` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'no' COMMENT 'A hidden value can be used in rendering the menu. In some areas, the menu item may not want to be displayed',
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

DROP TABLE IF EXISTS `fuel_navigation_groups`;
CREATE TABLE `fuel_navigation_groups` (
  `id` int(3) unsigned NOT NULL auto_increment,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `published` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'yes',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `fuel_navigation_groups`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `fuel_pages`
-- 

DROP TABLE IF EXISTS `fuel_pages`;
CREATE TABLE `fuel_pages` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `location` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'Add the part of the url after the root of your site (usually after the domain name). For the homepage, just put the word ''home''',
  `layout` varchar(50) collate utf8_unicode_ci NOT NULL COMMENT 'The name of the template to associate with this page',
  `published` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'yes' COMMENT 'A ''yes'' value will display the page and an ''no'' value will give a 404 error message',
  `cache` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'yes' COMMENT 'Cache controls whether the page will pull from the database or from a saved file which is more effeicent. If a page has content that is dynamic, it''s best to set cache to ''no''',
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

INSERT INTO `fuel_pages` (`id`, `location`, `layout`, `published`, `cache`, `date_added`, `last_modified`, `last_modified_by`) VALUES 
(1, 'about', 'main', 'yes', 'yes', '2010-11-06 17:43:03', '2010-11-07 10:14:14', 1);

-- --------------------------------------------------------

-- 
-- Table structure for table `fuel_page_variables`
-- 

DROP TABLE IF EXISTS `fuel_page_variables`;
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

INSERT INTO `fuel_page_variables` (`id`, `page_id`, `name`, `scope`, `value`, `type`, `active`) VALUES 
(18, 1, 'meta_keywords', '', '', 'string', 'yes'),
(19, 1, 'body', '', '<h1>About WidgiCorp </h1>\n<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nulla tincidunt condimentum nulla. Sed ut elit. Morbi lectus. Sed iaculis lacus eget elit. In hac habitasse platea dictumst. Nullam semper semper risus.</p>\n\n<p>Nulla tincidunt condimentum nulla. Sed ut elit. Morbi lectus. Sed iaculis lacus eget elit. In hac habitasse platea dictumst. Nullam semper semper risus.\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Nulla tincidunt condimentum nulla. Sed ut elit. Morbi lectus. Sed iaculis lacus eget elit. In hac habitasse platea dictumst. Nullam semper semper risus.\n</p>\n<ul>\n <li>Lorem ipsum dolor sit amet</li>\n <li>Consectetuer adipiscing elit</li>\n <li>Sed ut elit. Morbi lectus. Sed</li>\n</ul>', 'string', 'yes'),
(20, 1, 'body_class', '', '', 'string', 'yes'),
(17, 1, 'meta_description', '', '', 'string', 'yes'),
(16, 1, 'page_title', '', '', 'string', 'yes');

-- --------------------------------------------------------

-- 
-- Table structure for table `fuel_permissions`
-- 

DROP TABLE IF EXISTS `fuel_permissions`;
CREATE TABLE `fuel_permissions` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(50) collate utf8_unicode_ci NOT NULL COMMENT 'Permissions beginning with ''Manage '' will allow items to appear on the left menu',
  `description` varchar(255) collate utf8_unicode_ci NOT NULL,
  `active` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'yes',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `fuel_permissions`
-- 

INSERT INTO `fuel_permissions` (`id`, `name`, `description`, `active`) VALUES 
(1, 'pages', 'Manage pages', 'yes'),
(2, 'pages_publish', 'Ability to Publish Pages', 'yes'),
(3, 'pages_delete', 'Ability to Delete Pages', 'yes'),
(4, 'navigation', 'Manage navigation', 'yes'),
(5, 'users', 'Manage users', 'yes'),
(6, 'tools/backup', 'Manage database backup', 'yes'),
(7, 'manage/cache', 'Manage the page cache', 'yes'),
(8, 'manage/activity', 'View activity logs', 'yes'),
(9, 'myPHPadmin', 'myPHPadmin', 'yes'),
(10, 'google_analytics', 'Google Analytics', 'yes'),
(11, 'tools/user_guide', 'Access the User Guide', 'yes'),
(12, 'manage', 'View the Manage Dashboard Page', 'yes'),
(13, 'permissions', 'Manage Permissions', 'yes'),
(14, 'tools', 'Manage Tools', 'yes'),
(15, 'tools/seo/google_keywords', 'Google Keywords', 'yes'),
(16, 'sitevariables', 'Site Variables', 'yes'),
(17, 'blog/posts', 'Blog Posts', 'yes'),
(18, 'blog/categories', 'Blog Categories', 'yes'),
(19, 'blog/comments', 'Blog Comments', 'yes'),
(20, 'blog/links', 'Blog Comments', 'yes'),
(21, 'blog/users', 'Blog Authors', 'yes'),
(22, 'blog/settings', 'Blog Settings', 'yes'),
(23, 'assets', 'Assets', 'yes'),
(24, 'tools/validate', 'Validate', 'yes'),
(25, 'tools/seo', 'Page Analysis', 'yes'),
(26, 'tools/tester', 'Tester Module', 'yes'),
(27, 'blocks', 'Manage Blocks', 'yes'),
(28,'site_docs','Site Documentation','yes'),
(29,'tools/cronjobs','Cronjobs','yes'),
(30, 'quotes', 'Quotes', 'yes'),
(31, 'projects', 'Projects', 'yes');
-- --------------------------------------------------------

-- 
-- Table structure for table `fuel_site_variables`
-- 

DROP TABLE IF EXISTS `fuel_site_variables`;
CREATE TABLE `fuel_site_variables` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(100) collate utf8_unicode_ci NOT NULL,
  `value` text collate utf8_unicode_ci NOT NULL,
  `scope` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'leave blank if you want the variable to be available to all pages',
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

DROP TABLE IF EXISTS `fuel_users`;
CREATE TABLE `fuel_users` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_name` varchar(30) collate utf8_unicode_ci NOT NULL,
  `password` varchar(32) collate utf8_unicode_ci NOT NULL,
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

DROP TABLE IF EXISTS `fuel_user_to_permissions`;
CREATE TABLE `fuel_user_to_permissions` (
  `user_id` int(10) unsigned NOT NULL,
  `permission_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`user_id`,`permission_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `fuel_user_to_permissions`
-- 
INSERT INTO `fuel_user_to_permissions` (`user_id`, `permission_id`) VALUES 
(2, 1),
(2, 2),
(2, 3),
(2, 4),
(2, 6),
(2, 7),
(2, 8),
(2, 11),
(2, 12),
(2, 14),
(2, 15),
(2, 16),
(2, 17),
(2, 18),
(2, 19),
(2, 20),
(2, 21),
(2, 22),
(2, 23),
(2, 24),
(2, 25),
(2, 26),
(2, 27),
(2, 28),
(2, 29),
(2, 30),
(2, 31);


-- --------------------------------------------------------

-- 
-- Table structure for table `projects`
-- 

DROP TABLE IF EXISTS `projects`;
CREATE TABLE `projects` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'The name of the project',
  `slug` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'If left blank, one will automatically be generated for you',
  `client` varchar(255) collate utf8_unicode_ci NOT NULL,
  `description` text collate utf8_unicode_ci NOT NULL,
  `website` varchar(255) collate utf8_unicode_ci NOT NULL,
  `launch_date` date default NULL,
  `image` varchar(100) collate utf8_unicode_ci NOT NULL,
  `featured` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'yes',
  `precedence` int(11) NOT NULL default '999' COMMENT 'The higher the number, the more important',
  `published` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'yes',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `projects`
-- 

INSERT INTO `projects` (`id`, `name`, `slug`, `client`, `description`, `website`, `launch_date`, `image`, `featured`, `precedence`, `published`) VALUES 
(1, 'Nuts Over Bolts', 'nuts-over-bolts', 'Yoda', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', 'getfuelcms.com', '2010-11-06', 'showcase1.png', 'yes', 999, 'yes'),
(2, 'Cubed', 'cubed', 'Chewy', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '', '0000-00-00', 'showcase2.png', 'yes', 999, 'yes');

-- --------------------------------------------------------

-- 
-- Table structure for table `quotes`
-- 

DROP TABLE IF EXISTS `quotes`;
CREATE TABLE `quotes` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `content` text collate utf8_unicode_ci NOT NULL,
  `name` varchar(50) collate utf8_unicode_ci NOT NULL,
  `title` varchar(100) collate utf8_unicode_ci NOT NULL,
  `precedence` int(11) NOT NULL default '0',
  `published` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'yes',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- 
-- Dumping data for table `quotes`
-- 

INSERT INTO `quotes` (`id`, `content`, `name`, `title`, `precedence`, `published`) VALUES 
(1, 'Do or do not... there is no try.', 'Yoda', 'Jedi', 0, 'yes'),
(2, 'Laugh it up fuzzball.', 'Han Solo', 'Mercenary', 0, 'yes');
