ALTER TABLE `#__plg_pwtseo` ADD `strip_canonical_choice` TINYINT(1) DEFAULT 0;
ALTER TABLE `#__plg_pwtseo` ADD `strip_canonical` VARCHAR(255) DEFAULT '';
ALTER TABLE `#__plg_pwtseo` ADD `facebook_url` VARCHAR(255) NOT NULL DEFAULT '' AFTER `facebook_image`;
ALTER TABLE `#__plg_pwtseo_datalayers` MODIFY COLUMN `template` VARCHAR(255) DEFAULT 0 NOT NULL;
