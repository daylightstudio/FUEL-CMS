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
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `fuel_logs`;

CREATE TABLE `fuel_logs` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `entry_date` datetime NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;




DROP TABLE IF EXISTS `fuel_navigation`;

CREATE TABLE `fuel_navigation` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `group_id` int(5) unsigned NOT NULL default '1',
  `location` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'The part of the path after the domain name (e.g. comany/about_us)',
  `label` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'The name you want to appear in the menu',
  `precedence` int(10) unsigned NOT NULL default '0' COMMENT 'The higher the number, the greater the precedence and farther up the list the navigational element will appear',
  `attributes` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'Extra attributes that can be used for navigation implementation',
  `parent_id` int(10) unsigned NOT NULL default '0' COMMENT 'Used for creating menu hierarchies. No value means it is a root level menu item',
  `hidden` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'no',
  `published` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'yes' COMMENT 'Determines whether the item is displayed or not',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `group_id` (`group_id`,`location`,`parent_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



DROP TABLE IF EXISTS `fuel_navigation_groups`;

CREATE TABLE `fuel_navigation_groups` (
  `id` int(3) unsigned NOT NULL auto_increment,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `published` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'yes',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


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
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


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
  KEY `template_id` (`layout`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `fuel_permissions`;

CREATE TABLE `fuel_permissions` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(50) collate utf8_unicode_ci NOT NULL COMMENT 'Permissions beginning with ''Manage '' will allow items to appear on the left menu',
  `description` varchar(255) collate utf8_unicode_ci NOT NULL,
  `active` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'yes',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `fuel_permissions` (`id`,`name`,`description`,`active`)
VALUES
	(1,'pages','Manage pages','yes'),
	(2,'navigation','Manage navigation','yes'),
	(3,'users','Manage users','yes'),
	(4,'tools/backup','Manage database backup','yes'),
	(5,'cache','Manage the page cache','yes'),
	(6,'logs','View activity logs','yes'),
	(7,'myPHPadmin','myPHPadmin','yes'),
	(8,'google_analytics','Google Analytics','yes'),
	(9,'Sphider search','','yes'),
	(10,'manage','View the manage dashboard page','yes'),
	(11,'permissions','Manage permissions','yes'),
	(12,'tools','Manage tools','yes'),
	(13,'Wordpress','','yes'),
	(14,'cronjobs','Manage cronjobs','yes'),
	(15,'tools/seo/google_keywords','Google Keywords','yes'),
	(16,'pages_published','Ability to publish pages','yes'),
	(17,'sitevariables','Site Variables','yes'),
	(19,'blog/posts','Blog Posts','yes'),
	(20,'blog/categories','Blog Categories','yes'),
	(21,'blog/comments','Blog Comments','yes'),
	(22,'blog/users','Blog Authors','yes'),
	(23,'blog/settings','Blog Settings','yes'),
	(24,'assets','Assets','yes'),
	(25,'tools/validate','Validate','yes'),
	(26,'tools/seo','Page Analysis','yes');

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


DROP TABLE IF EXISTS `fuel_user_to_permissions`;

CREATE TABLE `fuel_user_to_permissions` (
  `user_id` int(10) unsigned NOT NULL,
  `permission_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`user_id`,`permission_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



DROP TABLE IF EXISTS `fuel_users`;

CREATE TABLE `fuel_users` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_name` varchar(30) collate utf8_unicode_ci NOT NULL,
  `password` varchar(32) collate utf8_unicode_ci NOT NULL,
  `email` varchar(100) collate utf8_unicode_ci NOT NULL,
  `first_name` varchar(30) collate utf8_unicode_ci NOT NULL,
  `last_name` varchar(30) collate utf8_unicode_ci NOT NULL,
  `super_admin` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'no',
  `active` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'yes',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `fuel_users` (`id`,`user_name`,`password`,`email`,`first_name`,`last_name`,`super_admin`,`active`)
VALUES
	(1,'admin','21232f297a57a5a743894a0e4a801fc3','','','','yes','yes');

