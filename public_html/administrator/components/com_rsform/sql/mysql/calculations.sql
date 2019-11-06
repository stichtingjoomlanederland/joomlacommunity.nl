CREATE TABLE IF NOT EXISTS `#__rsform_calculations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `formId` int(11) NOT NULL,
  `total` varchar(255) NOT NULL,
  `expression` text NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `formId` (`formId`),
  KEY `ordering` (`ordering`),
  KEY `formId_2` (`formId`, `ordering`)
) DEFAULT CHARSET=utf8;