CREATE TABLE IF NOT EXISTS `#__discuss_tags` (
	`id` BIGINT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
	`title` VARCHAR( 100 ) NOT NULL ,
	`alias` VARCHAR( 100 ) NOT NULL ,
	`created` DATETIME NOT NULL ,
	`published` TINYINT( 1 ) NOT NULL DEFAULT '0',
	`user_id` INT( 11 ) NOT NULL,
	PRIMARY KEY (`id`) ,
	KEY `discuss_tags_alias` (`alias`) ,
	KEY `discuss_tags_user_id` (`user_id`) ,
	KEY `discuss_tags_published` (`published`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__discuss_posts_tags` (
	`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT ,
	`post_id` BIGINT(20) UNSIGNED ,
	`tag_id` BIGINT(20) UNSIGNED ,
	PRIMARY KEY (`id`) ,
	KEY `discuss_posts_tags_tagid` (`tag_id`) ,
	KEY `discuss_posts_tags_postid` (`post_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
