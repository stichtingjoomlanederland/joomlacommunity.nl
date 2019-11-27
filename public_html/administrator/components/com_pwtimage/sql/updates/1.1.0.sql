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
  COMMENT = 'Hold the relations between a profile and the media fields'
