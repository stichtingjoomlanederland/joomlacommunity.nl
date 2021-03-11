ALTER TABLE `#__plg_pwtseo` ADD `url` VARCHAR(255) NOT NULL DEFAULT "" AFTER `context_id`;

UPDATE `#__plg_pwtseo` SET `context` = 'com_content.article' WHERE `context` = "";