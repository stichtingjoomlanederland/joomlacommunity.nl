ALTER TABLE `#__discuss_posts` ADD `issticky` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0' AFTER `islock`;
ALTER TABLE `#__discuss_posts` ADD `isreport` TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT '0';

CREATE TABLE IF NOT EXISTS `#__discuss_reports` (
	`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	`post_id` INT( 11 ) NOT NULL ,
	`reason` text NULL,
  	`created_by` bigint(20) unsigned NULL DEFAULT 0,
  	`created` datetime NOT NULL default '0000-00-00 00:00:00',
	PRIMARY KEY  (`id`),
	KEY `discuss_reports_post` (`post_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;