ALTER TABLE `fuel_permissions` CHANGE `name` `name` VARCHAR(50)  NOT NULL  DEFAULT '';
ALTER TABLE `fuel_logs` ADD `type` VARCHAR(30)  NOT NULL  DEFAULT ''  AFTER `message`;
ALTER TABLE `fuel_users` MODIFY `password` VARCHAR(64)  NOT NULL  DEFAULT ''  AFTER `user_name`;
ALTER TABLE `fuel_users` ADD `salt` VARCHAR(32)  NOT NULL  DEFAULT ''  AFTER `reset_key`;
ALTER TABLE `fuel_navigation_groups` ADD UNIQUE INDEX `name` (`name`);
ALTER TABLE `fuel_users` CHANGE `password` `password` VARCHAR(64)  NOT NULL  DEFAULT '';
ALTER TABLE `fuel_page_variables` CHANGE `type` `type` ENUM('string','int','boolean','array')  NOT NULL  DEFAULT 'string';
ALTER TABLE `fuel_blog_posts` ADD `image` VARCHAR(100)  NOT NULL  DEFAULT ''  AFTER `permalink`;
ALTER TABLE `fuel_blog_posts` ADD `thumb_image` VARCHAR(100)  NOT NULL  DEFAULT ''  AFTER `image`;
ALTER TABLE `fuel_page_variables` CHANGE `value` `value` LONGTEXT  NOT NULL;


UPDATE TABLE `fuel_users` SET password = 'f4c99eae874755b97610d650be565f1ac42019d1' WHERE user_id = 1;
UPDATE TABLE `fuel_users` SET salt = '429c6e14342dd7a63c510007a1858c26' WHERE user_id = 1;
UPDATE TABLE `fuel_users` SET first_name = 'admin' WHERE user_id = 1;
UPDATE TABLE `fuel_users` SET last_name = 'admin' WHERE user_id = 1;

