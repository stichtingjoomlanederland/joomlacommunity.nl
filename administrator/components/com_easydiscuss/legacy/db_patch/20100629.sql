CREATE TABLE IF NOT EXISTS `#__discuss_configs`(
	`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
	`name` VARCHAR(100) ,
	`params` TEXT ,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__discuss_posts` (
	`id` bigint(20) unsigned NOT NULL auto_increment,
	`title` text NULL,
	`permalink` text NOT NULL,
	`created` datetime NOT NULL default '0000-00-00 00:00:00',
	`modified` datetime NULL default '0000-00-00 00:00:00',
	`content` longtext NOT NULL,
	`published` tinyint(1) unsigned NOT NULL DEFAULT 0,
	`ordering` tinyint(1) unsigned NOT NULL DEFAULT 0,
	`vote` int(11) unsigned NOT NULL default 0,
	`hits` int(11) unsigned NOT NULL default 0,
	`user_id` bigint(20) unsigned NOT NULL,
	`parent_id` bigint(20) unsigned NOT NULL,
	PRIMARY KEY  (`id`),
	KEY `discuss_post_title` (`user_id`) ,
	KEY `discuss_post_published` (`published`),
	KEY `discuss_post_user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__discuss_comments` (
	`id` bigint(20) unsigned NOT NULL auto_increment,
	`comment` text NULL,
	`name` varchar(255) NOT NULL,
	`title` varchar(255) NOT NULL,
	`email` varchar(255) NULL DEFAULT '',
	`url` varchar(255) NULL DEFAULT '',
	`ip` varchar(255) NULL DEFAULT '',
	`created` datetime NOT NULL default '0000-00-00 00:00:00',
	`modified` datetime NULL default '0000-00-00 00:00:00',
	`published` tinyint(1) unsigned NOT NULL DEFAULT 0,
	`ordering` tinyint(1) unsigned NOT NULL DEFAULT 0,
	`post_id` bigint(20) unsigned NOT NULL,
	`user_id` bigint(20) unsigned NULL DEFAULT 0,
	PRIMARY KEY  (`id`),
	KEY `easyblog_comment_postid` (`post_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
