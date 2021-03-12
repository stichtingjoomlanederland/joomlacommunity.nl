ALTER TABLE `#__plg_pwtseo` ADD `override_canonical` TINYINT(1) DEFAULT 1;
ALTER TABLE `#__plg_pwtseo` ADD `canonical` VARCHAR(255) NOT NULL DEFAULT '';
ALTER TABLE `#__plg_pwtseo` ADD `version` VARCHAR(25) NOT NULL DEFAULT '1.0.2';
ALTER TABLE `#__plg_pwtseo` ADD `flag_outdated` TINYINT(1) NOT NULL DEFAULT 0;