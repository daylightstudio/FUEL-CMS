ALTER TABLE `fuel_navigation` DROP INDEX `group_id`;
ALTER TABLE `fuel_navigation` ADD UNIQUE INDEX (`group_id`, `nav_key`, `language`);
