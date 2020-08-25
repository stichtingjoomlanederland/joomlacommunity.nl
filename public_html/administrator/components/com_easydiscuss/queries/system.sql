CREATE TABLE IF NOT EXISTS`#__discuss_configs` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(100) ,
  `params` TEXT ,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__discuss_mailq` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `mailfrom` varchar(255) NULL,
  `fromname` varchar(255) NULL,
  `recipient` varchar(255) NOT NULL,
  `subject` text NOT NULL,
  `body` text NOT NULL,
  `created` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `ashtml` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `discuss_mailq_status` (`status`),
  KEY `idx_mailq_pending` (`status`, `created`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__discuss_hashkeys` (
  `id` bigint(11) NOT NULL auto_increment,
  `uid` bigint(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `key` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `uid` (`uid`),
  KEY `type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__discuss_migrators` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `internal_id` bigint(20) NOT NULL,
  `external_id` bigint(20) NOT NULL,
  `component` text NOT NULL,
  `type` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_external_id` ( `external_id` )
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__discuss_views` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `hash` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `ip` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  KEY `hash` (`hash`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__discuss_favourites` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `created_by` bigint(20) NOT NULL,
  `post_id` bigint(20) NOT NULL,
  `type` varchar(20) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_fav_postid` (`post_id`)
) ENGINE=MYISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__discuss_roles` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `usergroup_id` int(10) unsigned NOT NULL,
  `colorcode` VARCHAR( 255 ) NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `ordering` bigint(20) NOT NULL DEFAULT '0',
  `created_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_user_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__discuss_captcha` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `response` varchar(5) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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
  `sort` VARCHAR(25) NOT NULL DEFAULT 'recent',
  `count` INT(11) NOT NULL DEFAULT '10',
  `state` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_cron` (`state`, `type`, `interval`),
  KEY `idx_email_cron` (`state`, `type` (25), `interval` (25), `email`),
  KEY `idx_sentout` (`sent_out`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__discuss_languages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `locale` varchar(255) NOT NULL,
  `updated` datetime NOT NULL,
  `state` tinyint(3) NOT NULL,
  `translator` varchar(255) NOT NULL,
  `progress` int(11) NOT NULL,
  `params` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__discuss_external_groups` (
  `id` bigint(20) NOT NULL auto_increment,
  `source` varchar(255) NOT NULL,
  `post_id` bigint(20) NOT NULL,
  `group_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `external_groups_post_id` (`post_id`),
  KEY `external_groups_group_id` (`group_id`),
  KEY `external_groups_posts` (`group_id`, `post_id`),
  KEY `external_groups` (`source`, `group_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__discuss_download` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `state` tinyint(3) NOT NULL default 0,
  `params` longtext NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_userid` (`userid`),
  KEY `idx_state` (`state`),
  KEY `idx_created` (`created`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__discuss_sync_request` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `command` varchar(255) NOT NULL,
  `params` TEXT NULL,
  `total` int(11) NOT NULL default 0,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_command` (`command` (191))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
