/**
 * @package  Komento
 * @copyright Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
 * @license  GNU/GPL, see LICENSE.php
 * Komento is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

CREATE TABLE IF NOT EXISTS `#__komento_comments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `component` varchar(255) NOT NULL,
  `cid` bigint(20) unsigned NOT NULL,
  `comment` text,
  `name` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT '',
  `url` varchar(255) DEFAULT '',
  `ip` varchar(255) DEFAULT '',
  `created_by` bigint(20) unsigned DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` bigint(20) unsigned DEFAULT '0',
  `modified` datetime DEFAULT '0000-00-00 00:00:00',
  `deleted_by` bigint(20) unsigned DEFAULT '0',
  `deleted` datetime DEFAULT '0000-00-00 00:00:00',
  `flag` tinyint(1) DEFAULT '0',
  `published` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `publish_up` datetime DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime DEFAULT '0000-00-00 00:00:00',
  `sticked` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `sent` tinyint(1) DEFAULT '0',
  `parent_id` int(11) unsigned DEFAULT '0',
  `lft` int(11) unsigned NOT NULL DEFAULT '0',
  `rgt` int(11) unsigned NOT NULL DEFAULT '0',
  `depth` int(11) unsigned NOT NULL DEFAULT '0',
  `latitude` VARCHAR(255) NULL,
  `longitude` VARCHAR(255) NULL,
  `address` TEXT NULL,
  `params` TEXT NULL,
  `ratings` int(11) unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `komento_threaded` (`component`, `cid`, `published`, `lft`, `rgt`),
  KEY `komento_threaded_reverse` (`component`, `cid`, `published`, `rgt`),
  KEY `komento_module_comments` (`component`, `cid`, `published`, `created`),
  KEY `komento_backend` (`parent_id`, `component`, `cid`, `created`)
) DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__komento_configs` (
  `component` varchar(255) NOT NULL,
  `params` text NOT NULL
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__komento_acl` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cid` varchar(255) NOT NULL,
  `component` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `rules` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `komento_acl_content_type` (`type`)
) DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__komento_actions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(20) NOT NULL,
  `comment_id` bigint(20) unsigned NOT NULL,
  `action_by` bigint(20) unsigned NOT NULL DEFAULT 0,
  `actioned` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `komento_actions` (`type`, `comment_id`, `action_by`),
  KEY `komento_actions_comment_id` (`comment_id`)
) DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__komento_activities` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `type` varchar(20) NOT NULL,
  `comment_id` bigint(20) NOT NULL,
  `uid` bigint(20) NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `published` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__komento_captcha` (
  `id` int(11) NOT NULL auto_increment,
  `response` varchar(5) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__komento_mailq` (
  `id` int(11) NOT NULL auto_increment,
  `mailfrom` varchar(255) NULL,
  `fromname` varchar(255) NULL,
  `recipient` varchar(255) NOT NULL,
  `subject` text NOT NULL,
  `body` text NOT NULL,
  `created` datetime NOT NULL,
  `type` varchar(10) NOT NULL DEFAULT 'text',
  `status` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY  (`id`),
  KEY `komento_mailq_status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__komento_subscription` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(20) NOT NULL,
  `component` varchar(255) NOT NULL,
  `cid` bigint(20) unsigned NOT NULL,
  `userid` bigint(20) unsigned NOT NULL DEFAULT 0,
  `fullname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `created` DATETIME  NOT NULL DEFAULT '0000-00-00 00:00:00',
  `published` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `komento_subscription` (`type`, `component`, `cid`)
) DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__komento_hashkeys` (
  `id` bigint(11) NOT NULL auto_increment,
  `uid` bigint(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT 0,
  `key` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `uid` (`uid`),
  KEY `type` (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__komento_uploads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NULL,
  `filename` text NOT NULL,
  `hashname` text NOT NULL,
  `path` text NULL,
  `created` datetime NOT NULL,
  `created_by` bigint(20) unsigned DEFAULT '0',
  `published` tinyint(1) NOT NULL,
  `mime` text NOT NULL,
  `size` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__komento_ipfilter` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `component` varchar(255) NOT NULL,
  `ip` varchar(20) NOT NULL,
  `rules` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `komento_ipfilter` (`component`, `ip`)
) DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
