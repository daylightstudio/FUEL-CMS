CREATE TABLE IF NOT EXISTS `careers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `job_title` varchar(255) NOT NULL,
  `location` varchar(150) NOT NULL,
  `job_description` text NOT NULL,
  `skills_needed` text NOT NULL,
  `desired_skills` text NOT NULL,
  `education` text NOT NULL,
  `experience` text NOT NULL,
  `publish_date` date NOT NULL,
  `published` enum('yes','no') NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
