CREATE TABLE IF NOT EXISTS `#__rsform_mappings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `formId` int(11) NOT NULL,
  `connection` tinyint(1) NOT NULL,
  `host` varchar(255) NOT NULL,
  `driver` varchar(16) NOT NULL,
  `port` int(10) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `database` varchar(255) NOT NULL,
  `method` tinyint(1) NOT NULL,
  `table` varchar(255) NOT NULL,
  `data` text NOT NULL,
  `wheredata` text NOT NULL,
  `extra` text NOT NULL,
  `andor` text NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;