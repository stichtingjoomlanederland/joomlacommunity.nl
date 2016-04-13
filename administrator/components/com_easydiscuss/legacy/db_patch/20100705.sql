CREATE TABLE `#__discuss_votes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL,
  `post_id` bigint(20) unsigned DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `ipaddress` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8

ALTER TABLE `#__discuss_votes` ADD COLUMN `value` TINYINT(2) DEFAULT '0' NULL AFTER `ipaddress`;