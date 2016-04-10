CREATE TABLE IF NOT EXISTS `#__rsform_components` (
  `ComponentId` int(11) NOT NULL auto_increment, 
  `FormId` int(11) NOT NULL default '0', 
  `ComponentTypeId` int(11) NOT NULL default '0',
  `Order` int(11) NOT NULL default '0',
  `Published` tinyint(1) NOT NULL default '1',
  UNIQUE KEY `ComponentId` (`ComponentId`),
  KEY `ComponentTypeId` (`ComponentTypeId`),
  KEY `FormId` (`FormId`)
) DEFAULT CHARSET=utf8;