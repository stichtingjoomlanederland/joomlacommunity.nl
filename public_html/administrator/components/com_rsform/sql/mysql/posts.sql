CREATE TABLE IF NOT EXISTS `#__rsform_posts` (
  `form_id` int(11) NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `method` tinyint(1) NOT NULL,
  `fields` MEDIUMTEXT NOT NULL,
  `headers` MEDIUMTEXT NOT NULL,
  `silent` tinyint(1) NOT NULL,
  `url` text NOT NULL,
  PRIMARY KEY (`form_id`)
) DEFAULT CHARSET=utf8;