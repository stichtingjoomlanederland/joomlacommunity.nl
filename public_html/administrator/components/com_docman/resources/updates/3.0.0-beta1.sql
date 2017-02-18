
ALTER TABLE `#__docman_documents` ENGINE=InnoDB;
ALTER TABLE `#__docman_categories` ENGINE=InnoDB;
ALTER TABLE `#__docman_category_relations` ENGINE=InnoDB;
ALTER TABLE `#__docman_category_orderings` ENGINE=InnoDB;
ALTER TABLE `#__docman_levels` ENGINE=InnoDB;

ALTER TABLE `#__files_thumbnails` ENGINE=InnoDB;
ALTER TABLE `#__files_containers` ENGINE=InnoDB;

ALTER TABLE `#__docman_documents` CHANGE `docman_document_id` `docman_document_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `#__docman_categories` CHANGE `docman_category_id` `docman_category_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `#__docman_category_relations` CHANGE `ancestor_id` `ancestor_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `#__docman_category_relations` CHANGE `descendant_id` `descendant_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `#__docman_category_orderings` CHANGE `docman_category_id` `docman_category_id` BIGINT(20) UNSIGNED NOT NULL;

CREATE TABLE IF NOT EXISTS `#__docman_category_folders` (
  `docman_category_id` bigint(20) unsigned NOT NULL,
  `folder` varchar(4096) NOT NULL DEFAULT '',
  `automatic` tinyint(1) NOT NULL DEFAULT '0',
  UNIQUE KEY `docman_category_id` (`docman_category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__docman_files` (
  `docman_file_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `folder` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `modified_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` bigint(20) NOT NULL,
  `created_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` bigint(20) NOT NULL,
  `parameters` text,
  PRIMARY KEY (`docman_file_id`),
  UNIQUE KEY `path` (`folder`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__docman_folders` (
  `docman_folder_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `folder` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `modified_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` bigint(20) NOT NULL,
  `created_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` bigint(20) NOT NULL,
  `parameters` text,
  PRIMARY KEY (`docman_folder_id`),
  UNIQUE KEY `path` (`folder`,`name`)
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
