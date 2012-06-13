ALTER TABLE `fuel_permissions` CHANGE `name` `name` VARCHAR(50)  NOT NULL  DEFAULT '';
ALTER TABLE `fuel_logs` ADD `type` VARCHAR(30)  NOT NULL  DEFAULT ''  AFTER `message`;
ALTER TABLE `fuel_users` MODIFY `password` VARCHAR(64)  NOT NULL  DEFAULT ''  AFTER `user_name`;
ALTER TABLE `fuel_users` ADD `salt` VARCHAR(32)  NOT NULL  DEFAULT ''  AFTER `reset_key`;
ALTER TABLE `fuel_navigation_groups` ADD UNIQUE INDEX `name` (`name`);
ALTER TABLE `fuel_users` CHANGE `password` `password` VARCHAR(64)  NOT NULL  DEFAULT '';
ALTER TABLE `fuel_page_variables` CHANGE `type` `type` ENUM('string','int','boolean','array')  NOT NULL  DEFAULT 'string';
ALTER TABLE `fuel_page_variables` ADD `language` VARCHAR(30)  NOT NULL  DEFAULT 'english'  AFTER `type`;
ALTER TABLE  `fuel_page_variables` DROP INDEX  `page_id` ,ADD UNIQUE  `page_id` (  `page_id` ,  `name` ,  `language` );
ALTER TABLE `fuel_page_variables` CHANGE `value` `value` LONGTEXT  NOT NULL;
ALTER TABLE `fuel_blog_posts` DROP `image`;
ALTER TABLE `fuel_blog_posts` ADD `main_image` VARCHAR(100)  NOT NULL  DEFAULT ''  AFTER `author_id`;
ALTER TABLE `fuel_blog_posts` ADD `list_image` VARCHAR(100)  NOT NULL  DEFAULT ''  AFTER `main_image`;
ALTER TABLE `fuel_blog_posts` ADD `thumbnail_image` VARCHAR(100)  NOT NULL  DEFAULT ''  AFTER `list_image`;

ALTER TABLE `fuel_permissions` MODIFY COLUMN `name` VARCHAR(50) NOT NULL COMMENT 'Permissions beginning with \'Manage \' will allow items to appear on the left menu' AFTER `description`;
ALTER TABLE `fuel_permissions` CHANGE `name` `name` VARCHAR(50)  NOT NULL  DEFAULT ''  COMMENT 'In most cases, this should be the name of the module (e.g. news)';


UPDATE TABLE `fuel_users` SET password = 'f4c99eae874755b97610d650be565f1ac42019d1' WHERE id = 1;
UPDATE TABLE `fuel_users` SET salt = '429c6e14342dd7a63c510007a1858c26' WHERE id = 1;
UPDATE TABLE `fuel_users` SET first_name = 'admin' WHERE id = 1;
UPDATE TABLE `fuel_users` SET last_name = 'admin' WHERE id = 1;


CREATE TABLE `fuel_relationships` (
`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
`candidate_table` varchar(100) DEFAULT '',
`candidate_key` int(11) NOT NULL,
`foreign_table` varchar(100) DEFAULT NULL,
`foreign_key` int(11) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `fuel_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `module` varchar(50) NOT NULL DEFAULT '',
  `key` varchar(50) NOT NULL DEFAULT '',
  `value` longtext,
  PRIMARY KEY (`id`),
  UNIQUE KEY `module` (`module`,`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


INSERT INTO `fuel_permissions` (`id`, `description`, `name`, `active`) VALUES (NULL, 'Settings', 'settings', 'yes');
