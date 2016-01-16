CREATE TABLE IF NOT EXISTS `#__slider` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`published` tinyint(4) NOT NULL DEFAULT '1',
	`title` varchar(250) NOT NULL DEFAULT '',
	`image` text NOT NULL,
	`image_alt` text NOT NULL,
	`text` varchar(250) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`)
);
