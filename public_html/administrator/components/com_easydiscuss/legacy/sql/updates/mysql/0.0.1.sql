/**
 * @package   EasyDiscuss
 * @copyright Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license   GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

CREATE TABLE IF NOT EXISTS`#__discuss_configs` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(100) ,
  `params` TEXT ,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__discuss_posts` (
  `id` bigint(20) UNSIGNED NOT NULL auto_increment,
  `title` text NULL,
  `alias` text NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime NULL DEFAULT '0000-00-00 00:00:00',
  `replied` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `content` longtext NOT NULL,
  `published` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `ordering` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `vote` int(11) UNSIGNED DEFAULT '0',
  `hits` int(11) UNSIGNED DEFAULT '0',
  `islock` TINYINT(1) UNSIGNED DEFAULT '0',
  `featured` TINYINT(1) UNSIGNED DEFAULT '0',
  `isresolve` TINYINT(1) UNSIGNED DEFAULT '0',
  `isreport` TINYINT(1) UNSIGNED DEFAULT '0',
  `answered` TINYINT(1) UNSIGNED DEFAULT '0',
  `user_id` bigint(20) UNSIGNED DEFAULT '0',
  `parent_id` bigint(20) UNSIGNED DEFAULT '0',
  `user_type` varchar(255) NOT NULL,
  `poster_name` varchar(255) NOT NULL,
  `poster_email` varchar(255) NOT NULL,
  `num_likes` int(11) NULL default 0,
  `num_negvote` int(11) NULL default 0,
  `sum_totalvote` int(11) NULL default 0,
  `params` TEXT NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `discuss_post_published` (`published`),
  KEY `discuss_post_user_id` (`user_id`) ,
  KEY `discuss_post_vote` (`vote`),
  KEY `discuss_post_parentid` (`parent_id`),
  KEY `discuss_post_isreport` (`isreport`),
  KEY `discuss_post_answered` (`answered`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__discuss_comments` (
  `id` bigint(20) UNSIGNED NOT NULL auto_increment,
  `comment` text NULL,
  `name` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT '',
  `url` varchar(255) DEFAULT '',
  `ip` varchar(255) DEFAULT '',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` datetime DEFAULT '0000-00-00 00:00:00',
  `published` tinyint(1) UNSIGNED DEFAULT '0',
  `ordering` tinyint(1) UNSIGNED DEFAULT '0',
  `post_id` bigint(20) UNSIGNED ,
  `user_id` INT(11) UNSIGNED DEFAULT '0',
  PRIMARY KEY  (`id`),
  KEY `discuss_comment_postid` (`post_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS`#__discuss_tags` (
  `id` BIGINT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `title` VARCHAR( 100 ) NOT NULL ,
  `alias` VARCHAR( 100 ) NOT NULL ,
  `created` DATETIME NOT NULL ,
  `published` TINYINT( 1 ) UNSIGNED DEFAULT '0',
  `user_id` INT( 11 ) UNSIGNED,
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


CREATE TABLE IF NOT EXISTS `#__discuss_votes` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED DEFAULT NULL,
  `post_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `ipaddress` varchar(15) DEFAULT NULL,
  `value` TINYINT(2) DEFAULT '0' NULL,
  PRIMARY KEY (`id`),
  KEY `discuss_user_post` (`user_id`, `post_id`),
  KEY `discuss_post_id` (`post_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__discuss_reports` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `post_id` INT( 11 ) NOT NULL ,
  `reason` text NULL,
    `created_by` bigint(20) unsigned NULL DEFAULT 0,
    `created` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `discuss_reports_post` (`post_id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

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


CREATE TABLE IF NOT EXISTS `#__discuss_users` (
  `id` bigint(20) unsigned NOT NULL,
  `nickname` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `description` text,
  `url` varchar(255) DEFAULT NULL,
  `params` text,
  `alias` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `discuss_users_alias` (`alias`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__discuss_subscription` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `userid` bigint(20) NOT NULL,
  `member` tinyint(1) NOT NULL DEFAULT '0',
  `type` varchar(100) NOT NULL DEFAULT 'daily',
  `cid` bigint(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `interval` varchar(100) NOT NULL,
  `created` datetime NOT NULL,
  `sent_out` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__discuss_attachments` (
  `id` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL,
  `title` text NOT NULL,
  `type` varchar(200) NOT NULL,
  `path` text NOT NULL,
  `created` datetime NOT NULL,
  `published` tinyint(3) NOT NULL,
  `mime` text NOT NULL,
  `size` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `uid` (`uid`),
  KEY `type` (`type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__discuss_category` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_by` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL DEFAULT '',
  `alias` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `published` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ordering` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `avatar` varchar(255) DEFAULT NULL,
  `parent_id` int(11) DEFAULT '0',
  `private` int(11) unsigned NOT NULL DEFAULT '0',
  `default` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `level` int(11) NULL,
  `lft` int(11) NULL,
  `rgt` int(11) NULL,
  PRIMARY KEY (`id`),
  KEY `discuss_cat_published` (`published`),
  KEY `discuss_cat_parentid` (`parent_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__discuss_acl` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `action` varchar(255) NOT NULL,
  `default` tinyint(1) NOT NULL DEFAULT '1',
  `description` text NOT NULL,
  `published` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `ordering` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `discuss_post_acl_action` (`action`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__discuss_acl_group` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `content_id` bigint(20) unsigned NOT NULL,
  `acl_id` bigint(20) unsigned NOT NULL,
  `status` tinyint(1) NOT NULL,
  `type` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `discuss_post_acl_content_type` (`content_id`,`type`),
  KEY `discuss_post_acl` (`acl_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
