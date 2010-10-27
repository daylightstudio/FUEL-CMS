DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_name` varchar(30) collate utf8_unicode_ci NOT NULL,
  `password` varchar(32) collate utf8_unicode_ci NOT NULL,
  `email` varchar(100) collate utf8_unicode_ci NOT NULL,
  `first_name` varchar(30) collate utf8_unicode_ci NOT NULL,
  `last_name` varchar(30) collate utf8_unicode_ci NOT NULL,
  `active` enum('yes','no') collate utf8_unicode_ci NOT NULL default 'yes',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `users` (`id`,`user_name`,`password`,`email`,`first_name`,`last_name`,`active`)
VALUES
	(1,'admin','21232f297a57a5a743894a0e4a801fc3','dvader@deathstar.com','Darth','Vader','yes'),
	(2,'dave','21232f297a57a5a743894a0e4a801fc3','dave@thedaylightstudio.com','Dave','McReynolds','yes'),
	(3,'shawn','21232f297a57a5a743894a0e4a801fc3','shawn@thedaylightstudio.com','Shawn','Mann','no')
	;

