ALTER TABLE `fuel_categories` ADD `language` VARCHAR(30)  NOT NULL  DEFAULT ''  AFTER `context`;
ALTER TABLE `fuel_tags` ADD `language` VARCHAR(30)  NOT NULL  DEFAULT ''  AFTER `slug`;