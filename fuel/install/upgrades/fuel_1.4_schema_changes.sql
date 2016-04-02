ALTER TABLE `fuel_categories` CHANGE `slug` `slug` VARCHAR(255)  CHARACTER SET utf8  COLLATE utf8_general_ci  NOT NULL  DEFAULT '';
ALTER TABLE `fuel_categories` CHANGE `name` `name` VARCHAR(255)  CHARACTER SET utf8  COLLATE utf8_general_ci  NOT NULL  DEFAULT '';

ALTER TABLE `fuel_relationships` ADD UNIQUE INDEX (`candidate_table`, `candidate_key`, `foreign_table`, `foreign_key`);
