CREATE TABLE IF NOT EXISTS `jos_discuss_notifications` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` text NOT NULL,
  `cid` bigint(20) NOT NULL,
  `type` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `target` bigint(20) NOT NULL,
  `author` bigint(20) NOT NULL,
  `permalink` text NOT NULL,
  `state` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `jos_discuss_category_acl_item` (
  `id` bigint(20) NOT NULL auto_increment,
  `action` varchar(255) NOT NULL,
  `description` text,
  `published` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `default` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



CREATE TABLE IF NOT EXISTS `jos_discuss_category_acl_map` (
  `id` bigint(20) NOT NULL auto_increment,
  `category_id` bigint(20) NOT NULL,
  `acl_id` bigint(20) NOT NULL,
  `type` varchar(25) NOT NULL,
  `content_id` bigint(20) NOT NULL,
  `status` tinyint(1) default 0,
  PRIMARY KEY  (`id`),
  KEY `discuss_category_acl` (`category_id`),
  KEY `discuss_category_acl_id` (`acl_id`),
  KEY `discuss_content_type` (`content_id`, `type`),
  KEY `discuss_category_content_type` (`category_id`, `content_id`, `type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


INSERT INTO `jos_discuss_category_acl_item` (`id`, `action`, `description`, `published`, `default`) values ('1', 'select', 'can select the category during question creation', 1, 1) ON DUPLICATE KEY UPDATE `default` = '1';
INSERT INTO `jos_discuss_category_acl_item` (`id`, `action`, `description`, `published`, `default`) values ('2', 'view', 'can view the category posts.', 1, 1) ON DUPLICATE KEY UPDATE `default` = '1';
INSERT INTO `jos_discuss_category_acl_item` (`id`, `action`, `description`, `published`, `default`) values ('3', 'reply', 'can reply to category posts', 1, 1) ON DUPLICATE KEY UPDATE `default` = '1';