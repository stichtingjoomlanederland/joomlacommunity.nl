CREATE TABLE IF NOT EXISTS `#__jdidealgateway_profiles` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `psp` VARCHAR(25) NOT NULL,
  `alias` VARCHAR(100) NOT NULL,
  `paymentInfo` TEXT NOT NULL,
  `ordering` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `checked_out` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) CHARSET=utf8 COMMENT='Payments received in RO Payments';

CREATE TABLE IF NOT EXISTS `#__jdidealgateway_messages` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Auto increment ID',
  `subject` VARCHAR(150) NOT NULL COMMENT 'The subject',
  `orderstatus` VARCHAR(25) NOT NULL COMMENT 'The status to which the message applies',
  `profile_id` INT(10) NOT NULL COMMENT 'The ID of the profile',
  `message_type` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Set which text to show',
  `message_text_id` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'The message ID',
  `message_text` TEXT NOT NULL COMMENT 'The body text',
  `language` CHAR(7) NOT NULL DEFAULT '',
  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `checked_out` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) CHARSET=utf8 COMMENT='Messages to show to customers';

RENAME TABLE `#__jdidealgateway_pay` TO `#__jdidealgateway_pays`;

ALTER TABLE `#__jdidealgateway_logs`
	ADD COLUMN `profile_id` INT(10) NOT NULL DEFAULT '0' COMMENT 'The payment provider' AFTER `id`;

ALTER TABLE `#__jdidealgateway_logs`
	ADD COLUMN `paymentReference` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Overboeking reference' AFTER `cancel_url`;

ALTER TABLE `#__jdidealgateway_logs`
	ADD COLUMN `paymentId` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Payment ID for checking transaction' AFTER `paymentReference`;
