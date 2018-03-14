CREATE TABLE IF NOT EXISTS `#__ak_profiles` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`description` varchar(255) NOT NULL,
	`configuration` longtext,
	`filters` longtext,
	PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET utf8;

INSERT IGNORE INTO `#__ak_profiles`
(`id`,`description`, `configuration`, `filters`) VALUES
(1,'Default Backup Profile','','');

CREATE TABLE IF NOT EXISTS `#__ak_stats` (
	`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	`description` varchar(255) NOT NULL,
	`comment` longtext,
	`backupstart` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
	`backupend` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
	`status` enum('run','fail','complete') NOT NULL DEFAULT 'run',
	`origin` varchar(30) NOT NULL DEFAULT 'backend',
	`type` varchar(30) NOT NULL DEFAULT 'full',
	`profile_id` bigint(20) NOT NULL DEFAULT '1',
	`archivename` longtext,
	`absolute_path` longtext,
	`multipart` int(11) NOT NULL DEFAULT '0',
	`tag` varchar(255) DEFAULT NULL,
	`filesexist` tinyint(3) NOT NULL DEFAULT '1',
	`remote_filename` varchar(1000) DEFAULT NULL,
	`total_size` bigint(20) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	KEY `idx_fullstatus` (`filesexist`,`status`),
	KEY `idx_stale` (`status`,`origin`)
) DEFAULT CHARACTER SET utf8;

CREATE TABLE IF NOT EXISTS `#__ak_storage` (
	`tag` varchar(255) NOT NULL,
	`lastupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`data` longtext,
	PRIMARY KEY (`tag`)
) DEFAULT CHARACTER SET utf8;
