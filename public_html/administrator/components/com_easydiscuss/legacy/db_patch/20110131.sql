
CREATE TABLE IF NOT EXISTS `#__discuss_users` (
  `id` bigint(20) unsigned NOT NULL,
  `nickname` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `description` text,
  `url` varchar(255) DEFAULT NULL,
  `params` text,
  `permalink` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `discuss_users_permalink` (`permalink`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;