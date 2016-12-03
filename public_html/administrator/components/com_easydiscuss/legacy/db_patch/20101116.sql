ALTER TABLE `#__discuss_posts` ADD `replied` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `modified`;

CREATE TABLE IF NOT EXISTS `#__discuss_mailq` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `mailfrom` varchar(255) NULL,
  `fromname` varchar(255) NULL,
  `recipient` varchar(255) NOT NULL,
  `subject` text NOT NULL,
  `body` text NOT NULL,
  `created` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`id`),
  KEY `discuss_mailq_status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;