
ALTER TABLE `#__docman_documents` CHANGE `docman_document_id` `docman_document_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `#__docman_documents` CHANGE `docman_category_id` `docman_category_id` BIGINT(20) UNSIGNED NOT NULL;
ALTER TABLE `#__docman_categories` CHANGE `docman_category_id` `docman_category_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `#__docman_category_relations` CHANGE `ancestor_id` `ancestor_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `#__docman_category_relations` CHANGE `descendant_id` `descendant_id` BIGINT(20) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `#__docman_category_folders` CHANGE `docman_category_id` `docman_category_id` BIGINT(20) UNSIGNED NOT NULL;
ALTER TABLE `#__docman_category_orderings` CHANGE `docman_category_id` `docman_category_id` BIGINT(20) UNSIGNED NOT NULL;
