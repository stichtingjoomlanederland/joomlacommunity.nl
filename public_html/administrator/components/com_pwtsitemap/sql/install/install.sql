CREATE TABLE IF NOT EXISTS `#__pwtsitemap_menu_types` (
  `menu_types_id` int(10) unsigned NOT NULL,
  `ordering`      int(11) unsigned NOT NULL DEFAULT '0',
  `custom_title`  varchar(200) null,
  UNIQUE KEY `menu_types_id` (`menu_types_id`)
)
  DEFAULT CHARSET = utf8;
