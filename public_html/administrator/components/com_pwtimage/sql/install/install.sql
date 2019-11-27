CREATE TABLE IF NOT EXISTS `#__pwtimage_profiles` (
  `id`               INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `name`             VARCHAR(100)        NOT NULL,
  `settings`         TEXT                NOT NULL,
  `ordering`         TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
  `published`        TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
  `created`          DATETIME            NOT NULL DEFAULT '1001-01-01 00:00:00',
  `created_by`       INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `modified`         DATETIME            NOT NULL DEFAULT '1001-01-01 00:00:00',
  `modified_by`      INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `checked_out_time` DATETIME            NOT NULL DEFAULT '1001-01-01 00:00:00',
  `checked_out`      INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)
  CHARSET = utf8
  COMMENT = 'Profiles with PWT Image settings';

CREATE TABLE IF NOT EXISTS `#__pwtimage_extensions`
(
  `id`         INT AUTO_INCREMENT PRIMARY KEY,
  `profile_id` INT          NULL,
  `path`       VARCHAR(255) NULL,
  CONSTRAINT `profile_path`
  UNIQUE (`profile_id`, `path`)
)
  CHARSET = utf8
  COMMENT = 'Hold the relations between a profile and the media fields';

INSERT IGNORE INTO `#__pwtimage_profiles` (id, name, settings, ordering, published, created, created_by, modified, modified_by, checked_out_time, checked_out) VALUES (1, 'All', '{"sourcePath":"\\/images","subPath":"{year}\\/{month}","filenameFormat":"{d}_{random}_{name}","ratio":{"ratio0":{"width":"16","height":"9"},"ratio1":{"width":"4","height":"3"}},"freeRatio":"1","width":{"width0":{"width":"200"},"width1":{"width":"400"},"width2":{"width":"600"}},"checkSize":"1","showUpload":"1","showFolder":"1","showSavePath":"1","toCanvas":"0","keepOriginal":"1","showRotationTools":"1","showFlippingTools":"1","showHelp":"1","allMediaFields":"1","chmod":"0755","memoryLimit":""}', 0, 1, '1001-01-01 00:00:00', 0, '1001-01-01 00:00:00', 0, null, 0);
INSERT IGNORE INTO `#__pwtimage_extensions` (id, profile_id, path) VALUES (1, 1, 'all');