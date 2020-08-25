ALTER TABLE `#__jdidealgateway_logs`
    CHANGE COLUMN `language` `language` VARCHAR(5) NOT NULL COMMENT 'The user language' AFTER `paymentId`,
    ADD COLUMN `pid` VARCHAR(50) NOT NULL COMMENT 'A unique identifier for the transaction' AFTER `language`;