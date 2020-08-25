CREATE TABLE IF NOT EXISTS `#__jdidealgateway_customers` (
  `id` INT(10) NOT NULL AUTO_INCREMENT COMMENT 'Auto increment ID',
  `name` VARCHAR(150) NOT NULL COMMENT 'Customer name',
  `email` VARCHAR(75) NOT NULL COMMENT 'Customer email',
  `customerId` VARCHAR(30) NOT NULL,
  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `checked_out` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
)
  CHARSET = utf8
  COMMENT = 'Recurring payment customers';


CREATE TABLE IF NOT EXISTS `#__jdidealgateway_subscriptions` (
  `id`               INT(10) UNSIGNED NOT NULL AUTO_INCREMENT
  COMMENT 'Auto increment ID',
  `customerId`       INT(10) UNSIGNED NOT NULL
  COMMENT 'The customer id',
  `subscriptionId`   VARCHAR(30)      NOT NULL
  COMMENT 'The subscription ID',
  `status`           VARCHAR(10)      NOT NULL,
  `currency`         VARCHAR(3)       NOT NULL,
  `amount`           VARCHAR(10)      NOT NULL,
  `times`            TINYINT(2)       NOT NULL,
  `interval`         VARCHAR(10)      NOT NULL,
  `description`      VARCHAR(255)     NOT NULL,
  `cancelled`        DATE             NULL     DEFAULT '0000-00-00',
  `created`          DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by`       INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `modified`         DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by`      INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `checked_out`      INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `checked_out_time` DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
)
  CHARSET = utf8
  COMMENT ='Registered subscriptions';
