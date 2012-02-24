ALTER TABLE `fuel_blog_posts` ADD `main_image` VARCHAR(100)  NOT NULL  DEFAULT ''  AFTER `author_id`;
ALTER TABLE `fuel_blog_posts` ADD `thumbnail_image` VARCHAR(100)  NOT NULL  DEFAULT ''  AFTER `main_image`;
ALTER TABLE `fuel_blog_categories` ADD `precedence` INT(11)  UNSIGNED  DEFAULT 0  AFTER `permalink`;
UPDATE `fuel_blog_settings` SET `value` = '0' WHERE `name` = 'use_captchas';
ALTER TABLE `fuel_blog_categories` CHANGE `permalink` `slug` VARCHAR(255)  NOT NULL  DEFAULT ''  COMMENT 'If left blank, the slug will automatically be created for you.';
ALTER TABLE `fuel_blog_posts` CHANGE `permalink` `slug` VARCHAR(255)  NOT NULL  DEFAULT ''  COMMENT 'This is the last part of the url string. If left blank, the slug will automatically be created for you.';
ALTER TABLE `fuel_blog_posts` ADD `list_image` VARCHAR(100)  NOT NULL  DEFAULT ''  AFTER `main_image`;
ALTER TABLE `fuel_blog_posts` ADD `post_date` DATETIME  NOT NULL  AFTER `allow_comments`;
INSERT INTO `fuel_blog_settings` (`name`, `value`) VALUES ('page_title_separator', '&laquo;');
