CREATE TABLE `#__pwtsitemap_menu_types` (
  `menu_types_id` int(10) unsigned NOT NULL,
  `ordering`      int(11) unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY `menu_types_id` (`menu_types_id`),
  CONSTRAINT `Menu_types` FOREIGN KEY (`menu_types_id`)
    REFERENCES `#__menu_types` (`id`)
      ON DELETE CASCADE
      ON UPDATE CASCADE
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;