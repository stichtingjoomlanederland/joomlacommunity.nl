CREATE TABLE IF NOT EXISTS `#__jdidealgateway_statuses` (
  `id`               INT(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `name`             VARCHAR(100)        NOT NULL,
  `jdideal`          VARCHAR(1)         NOT NULL,
  `extension`        VARCHAR(1)        NOT NULL,
  `created`          DATETIME            NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by`       INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `modified`         DATETIME            NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by`      INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  `checked_out_time` DATETIME            NOT NULL DEFAULT '0000-00-00 00:00:00',
  `checked_out`      INT(10) UNSIGNED    NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE INDEX `extension` (`extension`)
)
  CHARSET = utf8
  COMMENT = 'Statuses to use for extensions in RO Payments';
