-- phpMyAdmin SQL Dump
-- version 2.10.2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Jun 27, 2010 at 02:53 PM
-- Server version: 5.0.45
-- PHP Version: 5.3.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- Database: `fuel`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `articles`
-- 

CREATE TABLE `articles` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `slug` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `author_id` tinyint(3) unsigned NOT NULL default '0',
  `content` text collate utf8_unicode_ci NOT NULL,
  `date_added` datetime NOT NULL default '0000-00-00 00:00:00',
  `published` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'yes',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Table structure for table `authors`
-- 

CREATE TABLE `authors` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `email` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `bio` text collate utf8_unicode_ci NOT NULL default '',
  `avatar_image` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `published` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'yes',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;