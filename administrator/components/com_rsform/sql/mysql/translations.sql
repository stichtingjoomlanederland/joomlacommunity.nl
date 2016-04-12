CREATE TABLE IF NOT EXISTS `#__rsform_translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `form_id` int(11) NOT NULL,
  `lang_code` varchar(32) NOT NULL,
  `reference` varchar(255) NOT NULL,
  `reference_id` varchar(255) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `form_id` (`form_id`)
) DEFAULT CHARSET=utf8;