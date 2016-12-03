CREATE TABLE IF NOT EXISTS `#__rsform_component_type_fields` (
  `ComponentTypeId` int(11) NOT NULL default '0',
  `FieldName` text NOT NULL,
  `FieldType` varchar(32) NOT NULL default 'hidden',
  `FieldValues` text NOT NULL,
  `Properties` text NOT NULL,
  `Ordering` int(11) NOT NULL default '0',
  KEY `ComponentTypeId` (`ComponentTypeId`)
) DEFAULT CHARSET=utf8;