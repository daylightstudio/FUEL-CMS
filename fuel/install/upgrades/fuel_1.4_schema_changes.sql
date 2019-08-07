ALTER TABLE `fuel_categories` CHANGE `slug` `slug` VARCHAR(255)  CHARACTER SET utf8  COLLATE utf8_general_ci  NOT NULL  DEFAULT '';
ALTER TABLE `fuel_categories` CHANGE `name` `name` VARCHAR(255)  CHARACTER SET utf8  COLLATE utf8_general_ci  NOT NULL  DEFAULT '';

# TO FIX POTENTIAL DUPLICATES CAUSED BY NOT HAVING THE UNIQUE INDEX
DELETE n1 FROM fuel_relationships n1, fuel_relationships n2 WHERE n1.id > n2.id AND n1.candidate_table = n2.candidate_table AND n1.candidate_key = n2.candidate_key AND n1.foreign_table = n2.foreign_table AND n1.foreign_key = n2.foreign_key;
ALTER TABLE `fuel_relationships` ADD UNIQUE INDEX (`candidate_table`, `candidate_key`, `foreign_table`, `foreign_key`);
