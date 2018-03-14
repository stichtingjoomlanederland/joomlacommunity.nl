CREATE TABLE IF NOT EXISTS `#__docman_documents` (
  `docman_document_id` SERIAL,
  `uuid` char(36) NOT NULL UNIQUE,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL UNIQUE,
  `docman_category_id` bigint(20) UNSIGNED NOT NULL,
  `description` longtext,
  `image` varchar(512) NOT NULL default '',
  `storage_type` varchar(64) NOT NULL default '',
  `storage_path` varchar(512) NOT NULL default '',
  `hits` int(11) NOT NULL default 0,
  `enabled` tinyint(1) NOT NULL default 1,
  `access` int(11) NOT NULL default 0,
  `publish_on` datetime NOT NULL default '0000-00-00 00:00:00',
  `unpublish_on` datetime NOT NULL default '0000-00-00 00:00:00',
  `locked_on` datetime NOT NULL default '0000-00-00 00:00:00',
  `locked_by` bigint(20) NOT NULL default 0,
  `created_on` datetime NOT NULL default '0000-00-00 00:00:00',
  `created_by` bigint(20) NOT NULL default 0,
  `modified_on` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified_by` bigint(20) NOT NULL default 0,
  `params` text,
  `asset_id` INTEGER UNSIGNED NOT NULL DEFAULT 0,
	`ordering` int(11) NOT NULL default 0,
  KEY `category_index` (`docman_category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__docman_categories` (
    `docman_category_id` SERIAL,
  	`uuid` char(36) NOT NULL UNIQUE,
    `title` varchar(255) NOT NULL,
    `slug` varchar(255) NOT NULL UNIQUE,
    `description` text,
    `image` varchar(512) NOT NULL default '',
    `params` text,
	  `access` int(11) NOT NULL default 1,
    `access_raw` int(11) NOT NULL default 0,
    `enabled` tinyint(1) NOT NULL default 1,
    `locked_on` datetime NOT NULL default '0000-00-00 00:00:00',
    `locked_by` bigint(20) NOT NULL default 0,
    `created_on` datetime NOT NULL default '0000-00-00 00:00:00',
    `created_by` bigint(20) NOT NULL default 0,
    `modified_on` datetime NOT NULL default '0000-00-00 00:00:00',
    `modified_by` bigint(20) NOT NULL default 0,
    `asset_id` INTEGER UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__docman_category_relations` (
  `ancestor_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `descendant_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `level` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ancestor_id`, `descendant_id`, `level`),
  KEY `path_index` (`descendant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__docman_category_orderings` (
  `docman_category_id` bigint(20) unsigned NOT NULL,
  `title` int(11) NOT NULL DEFAULT '0',
  `custom` int(11) NOT NULL DEFAULT '0',
  `created_on` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`docman_category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__docman_levels` (
  `docman_level_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `entity` char(36) DEFAULT NULL,
  `groups` varchar(1024) NOT NULL DEFAULT '',
  UNIQUE KEY `docman_level_id` (`docman_level_id`),
  UNIQUE KEY `uuid` (`entity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__docman_category_folders` (
  `docman_category_id` bigint(20) unsigned NOT NULL,
  `folder` varchar(4096) NOT NULL DEFAULT '',
  `automatic` tinyint(1) NOT NULL DEFAULT '0',
  UNIQUE KEY `docman_category_id` (`docman_category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__docman_files` (
  `docman_file_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `folder` varchar(2048) NOT NULL DEFAULT '',
  `name` varchar(2048) NOT NULL DEFAULT '',
  `modified_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` bigint(20) NOT NULL,
  `created_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` bigint(20) NOT NULL,
  `parameters` text,
  PRIMARY KEY (`docman_file_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__docman_folders` (
  `docman_folder_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `folder` varchar(2048) NOT NULL DEFAULT '',
  `name` varchar(2048) NOT NULL DEFAULT '',
  `modified_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` bigint(20) NOT NULL,
  `created_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` bigint(20) NOT NULL,
  `parameters` text,
  PRIMARY KEY (`docman_folder_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE OR REPLACE VIEW `#__docman_nodes`
AS
  SELECT *, 'folder' AS `type` FROM `#__docman_folders`
  UNION
  SELECT *, 'file' AS `type` FROM `#__docman_files`;

CREATE OR REPLACE VIEW `#__docman_file_counts`
AS
  SELECT `storage_path`, COUNT(0) AS `count`
  FROM `#__docman_documents`
  WHERE `storage_type` = 'file'
  GROUP BY `storage_path`;

CREATE TABLE IF NOT EXISTS `#__docman_tags` (
  `tag_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
	`count` int(11) DEFAULT '0',
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `modified_by` int(10) UNSIGNED DEFAULT NULL,
  `modified_on` datetime DEFAULT NULL,
  `locked_by` int(10) UNSIGNED DEFAULT NULL,
  `locked_on` datetime DEFAULT NULL,
  `params` text NOT NULL,
  PRIMARY KEY (`tag_id`),
  UNIQUE KEY `slug` (`slug`),
  UNIQUE KEY `title` (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__docman_tags_relations` (
  `tag_id` bigint(20) UNSIGNED NOT NULL,
  `row` varchar(36) NOT NULL,
  PRIMARY KEY (`tag_id`, `row`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__docman_scans` (
  `docman_scan_id` SERIAL,
  `identifier` varchar(64) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `ocr` tinyint(1) NOT NULL DEFAULT '0',
  `thumbnail` tinyint(1) NOT NULL DEFAULT '0',
  `modified_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `response` varchar(2048) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Duplicated from update.sql to here to make sure 2.1 -> 3.x updates work
# Otherwise foreign key constraint fails
ALTER TABLE `#__docman_documents` CHANGE `docman_document_id` `docman_document_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT;

CREATE TABLE IF NOT EXISTS `#__docman_document_contents` (
  `docman_document_id` bigint(20) unsigned NOT NULL,
  `contents` longtext,
  UNIQUE KEY `docman_document_id` (`docman_document_id`),
  FOREIGN KEY (`docman_document_id`) REFERENCES `#__docman_documents`(`docman_document_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;