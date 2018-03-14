
ALTER TABLE `#__docman_documents` ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `#__docman_scans` (
  `docman_scan_id` SERIAL,
  `identifier` varchar(64) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `ocr` tinyint(1) NOT NULL DEFAULT '0',
  `thumbnail` tinyint(1) NOT NULL DEFAULT '0',
  `modified_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__docman_document_contents` (
  `docman_document_id` bigint(20) unsigned NOT NULL,
  `contents` longtext,
  UNIQUE KEY `docman_document_id` (`docman_document_id`),
  FOREIGN KEY (`docman_document_id`) REFERENCES `#__docman_documents` (`docman_document_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__docman_mimetypes`;
