-- phpMyAdmin SQL Dump
-- version 2.10.2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Jun 10, 2010 at 11:19 AM
-- Server version: 5.0.45
-- PHP Version: 5.3.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- --------------------------------------------------------

-- 
-- Table structure for table `fuel_blog_categories`
-- 

CREATE TABLE `fuel_blog_categories` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `permalink` varchar(255) collate utf8_unicode_ci NOT NULL default '', COMMENT 'If left blank, the permalink will automatically be created for you.', 
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
  `author_id` int(10) unsigned NOT NULL,
  `author_name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `author_email` varchar(255) collate utf8_unicode_ci NOT NULL,
  `author_website` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `author_ip` varchar(32) collate utf8_unicode_ci NOT NULL,
  `is_spam` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'no',
  `content` text collate utf8_unicode_ci NOT NULL,
  `published` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'yes',
  `date_added` datetime NOT NULL,
  `last_modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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

-- --------------------------------------------------------

-- 
-- Table structure for table `fuel_blog_posts`
-- 

CREATE TABLE `fuel_blog_posts` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(255) collate utf8_unicode_ci NOT NULL,
  `permalink` varchar(255) collate utf8_unicode_ci NOT NULL COMMENT 'This is the last part of the url string. If left blank, the permalink will automatically be created for you.',
  `content` text collate utf8_unicode_ci NOT NULL,
  `content_filtered` text collate utf8_unicode_ci NOT NULL,
  `formatting` varchar(100) collate utf8_unicode_ci NOT NULL default 'auto_typography',
  `excerpt` text collate utf8_unicode_ci NOT NULL COMMENT 'A condensed version of the content',
  `author_id` int(10) unsigned NOT NULL COMMENT 'If left blank, you will assumed be the author.',
  `sticky` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'no' COMMENT 'Sticky posts will always be sorted toward the top on the homepage regardless of date',
  `allow_comments` enum('yes','no') collate utf8_unicode_ci default 'no' COMMENT 'Checking yes will allow comments to be displayed but only if they are enabled in the global settings',
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

INSERT INTO `fuel_blog_posts_to_categories` (`post_id`, `category_id`) VALUES 
(1, 1);

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
  `display_name` varchar(50) NOT NULL default '',
  `avatar` varchar(255) NOT NULL,
  `website` varchar(100) NOT NULL,
  `about` text NOT NULL,
  `date_added` datetime default NULL,
  `active` enum('yes','no') NOT NULL default 'yes',
  PRIMARY KEY  (`fuel_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `fuel_blog_users`
-- 

