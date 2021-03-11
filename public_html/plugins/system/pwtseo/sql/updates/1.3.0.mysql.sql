CREATE TABLE IF NOT EXISTS `#__plg_pwtseo_datalayers` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `fields` LONGTEXT NOT NULL,
  `language` CHAR(7) NOT NULL DEFAULT '*',
  `template` INT(10) UNSIGNED NOT NULL DEFAULT 0,
  `published` TINYINT(4) NOT NULL DEFAULT 1,
  `ordering` int(10) UNSIGNED DEFAULT 0 NOT NULL,

  PRIMARY KEY (`id`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__plg_pwtseo_datalayers_map` (
  `context_id` INT(11) UNSIGNED NOT NULL,
  `context` VARCHAR(50) NOT NULL DEFAULT '',
  `datalayer_id` INT(11) UNSIGNED NOT NULL,
  `values` LONGTEXT NOT NULL,

  UNIQUE KEY (`context_id`, `datalayer_id`),

  CONSTRAINT `pwtseo_datalayers_map_datalayer_fk`
  FOREIGN KEY(`datalayer_id`) REFERENCES `#__plg_pwtseo_datalayers` (`id`)
  ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `#__plg_pwtseo` ADD `structureddata` LONGTEXT NOT NULL AFTER `adv_open_graph`;
ALTER TABLE `#__plg_pwtseo` ADD `serptitle` VARCHAR(255) NOT NULL DEFAULT '' AFTER `page_title`;
ALTER TABLE `#__plg_pwtseo` ADD `serpurl` VARCHAR(255) NOT NULL DEFAULT '' AFTER `serptitle`;
ALTER TABLE `#__plg_pwtseo` ADD `serpmetadescription` TEXT AFTER `serpurl`;
