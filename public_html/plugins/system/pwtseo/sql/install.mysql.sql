CREATE TABLE IF NOT EXISTS `#__plg_pwtseo` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT,
  `context` VARCHAR(255) NOT NULL DEFAULT '',
  `context_id` INT(11) UNSIGNED NOT NULL,
  `url` VARCHAR(255) NOT NULL DEFAULT '',
  `focus_word` VARCHAR(255) NOT NULL DEFAULT '',
  `pwtseo_score` INT(11) UNSIGNED DEFAULT 0,
  `facebook_title` VARCHAR(255) NOT NULL DEFAULT '',
  `facebook_description` VARCHAR(255) NOT NULL DEFAULT '',
  `facebook_image` VARCHAR(255) NOT NULL DEFAULT '',
  `facebook_url` VARCHAR(255) NOT NULL DEFAULT '',
  `twitter_title` VARCHAR(255) NOT NULL DEFAULT '',
  `twitter_description` VARCHAR(255) NOT NULL DEFAULT '',
  `twitter_image` VARCHAR(255) NOT NULL DEFAULT '',
  `google_title` VARCHAR(255) NOT NULL DEFAULT '',
  `google_description` VARCHAR(255) NOT NULL DEFAULT '',
  `google_image` VARCHAR(255) NOT NULL DEFAULT '',
  `adv_open_graph` LONGTEXT NULL,
  `structureddata` LONGTEXT NULL,
  `override_page_title` TINYINT(1) DEFAULT 0,
  `expand_og` TINYINT(1) DEFAULT 0,
  `page_title` VARCHAR(255) NOT NULL DEFAULT '',
  `serptitle` VARCHAR(255) NOT NULL DEFAULT '',
  `serpurl` VARCHAR(255) NOT NULL DEFAULT '',
  `serpmetadescription` TEXT,
  `override_canonical` TINYINT(1) DEFAULT 0,
  `canonical` VARCHAR(255) NOT NULL DEFAULT '',
  `version` VARCHAR(25) NOT NULL DEFAULT '1.5.2',
  `flag_outdated` TINYINT(1) DEFAULT 0,
  `articletitleselector` VARCHAR(255) DEFAULT '' NOT NULL,
  `twitter_card` VARCHAR(20) DEFAULT '' NOT NULL,
  `twitter_site_username` VARCHAR(150) DEFAULT '' NOT NULL,
  `cascade_settings` TINYINT(1) DEFAULT 4 NOT NULL,
  `google_rank` INT(10) DEFAULT 0 NOT NULL,
  `strip_canonical_choice` TINYINT(1) DEFAULT 0,
  `strip_canonical` VARCHAR(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

/** Since 1.3.0 */
CREATE TABLE IF NOT EXISTS `#__plg_pwtseo_datalayers` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `fields` LONGTEXT NOT NULL,
  `language` CHAR(7) NOT NULL DEFAULT '*',
  `template` VARCHAR(255) NOT NULL,
  `published` TINYINT(4) NOT NULL DEFAULT 1,
  `ordering` int(10) UNSIGNED DEFAULT 0 NOT NULL,

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__plg_pwtseo_datalayers_map` (
  `context_id` INT(11) UNSIGNED NOT NULL,
  `context` VARCHAR(50) NOT NULL DEFAULT '',
  `datalayer_id` INT(11) UNSIGNED NOT NULL,
  `values` LONGTEXT NOT NULL,

  UNIQUE KEY (`context_id`, `datalayer_id`, `context`),

  CONSTRAINT `pwtseo_datalayers_map_datalayer_fk`
  FOREIGN KEY(`datalayer_id`) REFERENCES `#__plg_pwtseo_datalayers` (`id`)
  ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;
