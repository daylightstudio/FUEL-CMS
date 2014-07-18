DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_name` varchar(30) collate utf8_unicode_ci NOT NULL,
  `password` varchar(32) collate utf8_unicode_ci NOT NULL,
  `email` varchar(100) collate utf8_unicode_ci NOT NULL,
  `first_name` varchar(30) collate utf8_unicode_ci NOT NULL,
  `last_name` varchar(30) collate utf8_unicode_ci NOT NULL,
  `bio` text  collate utf8_unicode_ci NOT NULL,
  `role_id` tinyint(2) unsigned NOT NULL,
  `attributes` text COLLATE utf8_unicode_ci NOT NULL,
  `active` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'yes',
  `date_added` datetime DEFAULT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `users` (`id`,`user_name`,`password`,`email`,`first_name`,`last_name`,`bio`, `role_id`, `attributes`, `active`, `date_added`)
VALUES
  (1,'admin','21232f297a57a5a743894a0e4a801fc3','dvader@deathstar.com','Darth','Vader', 'This is my bio.', 1, '', 'yes', '2012-01-02'),
  (2,'dave','21232f297a57a5a743894a0e4a801fc3','dave@thedaylightstudio.com','Dave','McReynolds', 'Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.', 2, '', 'yes', '2012-01-01'),
  (3,'shawn','21232f297a57a5a743894a0e4a801fc3','shawn@thedaylightstudio.com','Shawn','Mann', 'Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', 3, '', 'no', '2012-01-01')
  ;