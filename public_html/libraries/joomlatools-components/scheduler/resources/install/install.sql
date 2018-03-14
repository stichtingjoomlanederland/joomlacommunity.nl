CREATE TABLE IF NOT EXISTS `#__scheduler_jobs` (
  `uuid` char(36) NOT NULL,
  `identifier` varchar(255) NOT NULL DEFAULT '',
  `package` varchar(64) NOT NULL DEFAULT '',
  `frequency` varchar(128) NOT NULL DEFAULT '',
  `status` int(11) NOT NULL DEFAULT '0',
  `queue` tinyint(4) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `state` text,
  `modified_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `completed_on` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  UNIQUE KEY `name` (`identifier`),
  UNIQUE KEY `uuid` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__scheduler_metadata` (
  `type` varchar(32) NOT NULL DEFAULT '',
  `sleep_until` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_run` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  UNIQUE KEY `unique_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;