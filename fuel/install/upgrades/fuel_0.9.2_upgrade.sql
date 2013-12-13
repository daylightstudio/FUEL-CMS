ALTER TABLE  `fuel_users` ADD  `language` VARCHAR( 30 ) NOT NULL AFTER  `last_name` ,
ADD  `reset_key` VARCHAR( 64 ) NOT NULL AFTER  `language` ;
ALTER TABLE  `fuel_blocks` ADD  `description` VARCHAR( 255 ) NOT NULL AFTER  `name` ;