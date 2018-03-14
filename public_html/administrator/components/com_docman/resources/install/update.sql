
# 2.1.0
ALTER TABLE `#__docman_documents` CHANGE `access` `access` int(11) NOT NULL default 0;
ALTER TABLE `#__docman_categories` CHANGE `access_raw` `access_raw` int(11) NOT NULL default 0;

UPDATE `#__docman_categories` SET `access_raw` = 0 WHERE `access_raw` = -1;
UPDATE `#__docman_documents` SET `access` = 0 WHERE `access` = -1;

# 3.0.0-beta.1
ALTER TABLE `#__docman_documents` ENGINE=InnoDB;
ALTER TABLE `#__docman_categories` ENGINE=InnoDB;
ALTER TABLE `#__docman_category_relations` ENGINE=InnoDB;
ALTER TABLE `#__docman_category_orderings` ENGINE=InnoDB;
ALTER TABLE `#__docman_levels` ENGINE=InnoDB;

ALTER TABLE `#__files_containers` ENGINE=InnoDB;


ALTER TABLE `#__docman_documents` CHANGE `docman_document_id` `docman_document_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `#__docman_documents` CHANGE `docman_category_id` `docman_category_id` BIGINT(20) UNSIGNED NOT NULL;
ALTER TABLE `#__docman_categories` CHANGE `docman_category_id` `docman_category_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `#__docman_category_relations` CHANGE `ancestor_id` `ancestor_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `#__docman_category_relations` CHANGE `descendant_id` `descendant_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `#__docman_category_folders` CHANGE `docman_category_id` `docman_category_id` BIGINT(20) UNSIGNED NOT NULL;
ALTER TABLE `#__docman_category_orderings` CHANGE `docman_category_id` `docman_category_id` BIGINT(20) UNSIGNED NOT NULL;


# 3.0.0
DROP TABLE IF EXISTS `#__docman_mimetypes`;

# 3.0.5-beta.1
ALTER TABLE `#__docman_files` CHANGE `folder` `folder` varchar(2048) NOT NULL DEFAULT '';
ALTER TABLE `#__docman_files` CHANGE `name` `name` varchar(2048) NOT NULL DEFAULT '';

ALTER TABLE `#__docman_folders` CHANGE `folder` `folder` varchar(2048) NOT NULL DEFAULT '';
ALTER TABLE `#__docman_folders` CHANGE `name` `name` varchar(2048) NOT NULL DEFAULT '';

# 3.1.0-rc.2
DROP TABLE IF EXISTS `#__files_thumbnails`;