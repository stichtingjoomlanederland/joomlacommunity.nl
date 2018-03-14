CREATE TABLE IF NOT EXISTS `#__rsform_properties` (
  `PropertyId` int(11) NOT NULL auto_increment,
  `ComponentId` int(11) NOT NULL default '0',
  `PropertyName` text NOT NULL,
  `PropertyValue` text NOT NULL,
  UNIQUE KEY `PropertyId` (`PropertyId`),
  KEY `ComponentId` (`ComponentId`)
) DEFAULT CHARSET=utf8;