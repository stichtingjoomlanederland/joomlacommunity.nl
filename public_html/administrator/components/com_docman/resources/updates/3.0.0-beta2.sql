
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

ALTER TABLE `#__docman_documents` ADD `ordering` int(11) NOT NULL default 0;

SET @order := 0;

UPDATE `#__docman_documents`
SET `ordering` = (@order := @order + 1)
order by `docman_category_id`, `docman_document_id`;
