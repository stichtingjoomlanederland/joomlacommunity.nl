CREATE TABLE IF NOT EXISTS `#__discuss_likes` (
	`id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
	`type` VARCHAR( 20 ) NOT NULL ,
	`content_id` INT( 11 ) NOT NULL ,
  	`created_by` bigint(20) unsigned NULL DEFAULT 0,
  	`created` datetime NOT NULL default '0000-00-00 00:00:00',
	PRIMARY KEY  (`id`),
	KEY `discuss_content_type` (`type`, `content_id`),
	KEY `discuss_contentid` (`content_id`),
	KEY `discuss_createdby` (`created_by`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8