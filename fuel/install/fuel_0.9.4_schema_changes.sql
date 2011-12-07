ALTER TABLE `fuel_permissions` CHANGE `name` `name` VARCHAR(50)  NOT NULL  DEFAULT '';
ALTER TABLE `fuel_logs` ADD `type` VARCHAR(30)  NOT NULL  DEFAULT ''  AFTER `level`;
ALTER TABLE `fuel_users` ADD `salt` VARCHAR(32)  NOT NULL  DEFAULT ''  AFTER `reset_key`;
ALTER TABLE `fuel_navigation_groups` ADD UNIQUE INDEX `name` (`name`);
ALTER TABLE `fuel_users` CHANGE `password` `password` VARCHAR(64)  NOT NULL  DEFAULT '';
ALTER TABLE `fuel_page_variables` CHANGE `type` `type` ENUM('string','int','boolean','array')  NOT NULL  DEFAULT 'string';



