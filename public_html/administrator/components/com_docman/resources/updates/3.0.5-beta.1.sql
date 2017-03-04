
ALTER TABLE `#__docman_files` DROP INDEX `path`;
ALTER TABLE `#__docman_folders` DROP INDEX `path`;

ALTER TABLE `#__docman_files` CHANGE `folder` `folder` varchar(2048) NOT NULL DEFAULT '';
ALTER TABLE `#__docman_files` CHANGE `name` `name` varchar(2048) NOT NULL DEFAULT '';

ALTER TABLE `#__docman_folders` CHANGE `folder` `folder` varchar(2048) NOT NULL DEFAULT '';
ALTER TABLE `#__docman_folders` CHANGE `name` `name` varchar(2048) NOT NULL DEFAULT '';