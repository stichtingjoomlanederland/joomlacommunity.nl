CREATE TABLE IF NOT EXISTS `#__discuss_customfields` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(100) NOT NULL,
  `title` varchar(255) NOT NULL,
  `params` text,
  `ordering` bigint(20) NOT NULL DEFAULT '0',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `required` TINYINT(3) NOT NULL,
  `tooltips` TEXT NOT NULL,
  `section` tinyint(1) NOT NULL DEFAULT '1',
  `global` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_published_ordering` (`published`, `ordering`),
  KEY `idx_section` (`section`),
  KEY `idx_published_section_ordering` (`published`, `section`, `ordering`)
) DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__discuss_customfields_acl` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `action` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `acl_published` tinyint(1) unsigned NOT NULL,
  `default` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__discuss_customfields_rule` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `field_id` bigint(20) unsigned NOT NULL,
  `acl_id` bigint(20) NOT NULL,
  `content_id` int(10) NOT NULL,
  `content_type` varchar(25) NOT NULL,
  `status` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `cf_rule_field_id` (`field_id`),
  KEY `cf_rule_acl_types` (`content_type`, `acl_id`, `content_id`),
  KEY `idx_access` (`field_id`, `content_type`, `acl_id`, `content_id`)
) DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `#__discuss_customfields_value` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `field_id` bigint(20) unsigned NOT NULL,
  `value` text NOT NULL,
  `post_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cf_value_field_id` (`field_id`),
  KEY `cf_value_field_post` (`field_id`, `post_id` )
) DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1;

INSERT INTO `#__discuss_customfields_acl` (`id`, `action`, `description`, `acl_published`, `default`) VALUES ('1', 'view', 'Who can view the custom fields at front end.', 1, 1) ON DUPLICATE KEY UPDATE `default` = '1';
INSERT INTO `#__discuss_customfields_acl` (`id`, `action`, `description`, `acl_published`, `default`) VALUES ('2', 'input', 'Who can input the custom fields at front end.', 1, 1) ON DUPLICATE KEY UPDATE `default` = '1';
