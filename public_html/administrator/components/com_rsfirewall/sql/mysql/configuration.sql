CREATE TABLE IF NOT EXISTS `#__rsfirewall_configuration` (
  `name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  `type` varchar(16) NOT NULL,
  PRIMARY KEY (`name`)
) DEFAULT CHARSET=utf8;