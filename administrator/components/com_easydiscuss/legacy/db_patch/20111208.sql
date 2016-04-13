
CREATE TABLE IF NOT EXISTS `jos_discuss_views` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `hash` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `ip` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  KEY `hash` (`hash`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `jos_discuss_migrators` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `internal_id` bigint(20) NOT NULL,
  `external_id` bigint(20) NOT NULL,
  `component` text NOT NULL,
  `type` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `jos_discuss_oauth` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL,
  `request_token` text NOT NULL,
  `access_token` text NOT NULL,
  `message` text NOT NULL,
  `params` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `jos_discuss_oauth_posts` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `post_id` bigint(20) NOT NULL,
  `oauth_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


ALTER TABLE `jos_discuss_users` ADD `location` TEXT NOT NULL;

alter table `jos_discuss_posts` add FULLTEXT `discuss_post_titlecontent` (`title`, `content`);
alter table `jos_discuss_tags` add FULLTEXT `discuss_tags_title` (`title`);
