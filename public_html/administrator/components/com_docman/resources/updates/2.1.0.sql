
CREATE TABLE IF NOT EXISTS `#__docman_levels` (
  `docman_level_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `entity` char(36) DEFAULT NULL,
  `groups` varchar(1024) NOT NULL DEFAULT '',
  UNIQUE KEY `docman_level_id` (`docman_level_id`),
  UNIQUE KEY `uuid` (`entity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `#__docman_documents` CHANGE `access` `access` int(11) NOT NULL default 0;

ALTER TABLE `#__docman_categories` CHANGE `access_raw` `access_raw` int(11) NOT NULL default 0;

UPDATE `#__docman_categories` SET `access_raw` = 0 WHERE `access_raw` = -1;

UPDATE `#__docman_documents` SET `access` = 0 WHERE `access` = -1;