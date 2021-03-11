ALTER TABLE `#__plg_pwtseo` ADD `cascade_settings` TINYINT(1) DEFAULT 0 NOT NULL AFTER `twitter_site_username`;
UPDATE `#__plg_pwtseo` SET `override_canonical` = 4 WHERE `override_canonical` = 0;
ALTER TABLE `#__plg_pwtseo` ADD `google_rank` INT(10) DEFAULT 0 NOT NULL;
