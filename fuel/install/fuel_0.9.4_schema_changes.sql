ALTER TABLE `fuel_permissions` CHANGE `name` `name` VARCHAR(50)  NOT NULL  DEFAULT '';
ALTER TABLE `fuel_logs` ADD `type` VARCHAR(30)  NOT NULL  DEFAULT ''  AFTER `level`;
