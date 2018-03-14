DROP TABLE IF EXISTS `bf_core_hashes`;
DROP TABLE IF EXISTS `bf_folders_to_scan`;
DROP TABLE IF EXISTS `bf_files`;
DROP TABLE IF EXISTS `bf_folders`;
DROP TABLE IF EXISTS `bf_scan_state`;

CREATE TABLE `bf_core_hashes` (
  `id`           INT(11)    NOT NULL AUTO_INCREMENT,
  `filewithpath` MEDIUMTEXT NOT NULL,
  `hash`         VARCHAR(32)         DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `filewithpath` (`filewithpath`(255)),
  KEY `hash` (`hash`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

CREATE TABLE `bf_folders_to_scan` (
  `id`             INT(11) NOT NULL AUTO_INCREMENT,
  `folderwithpath` TEXT,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

CREATE TABLE `bf_files` (
  `id`             INT(11) NOT NULL AUTO_INCREMENT,
  `filewithpath`   TEXT,
  `fileperms`      VARCHAR(255)     DEFAULT NULL,
  `filemtime`      VARCHAR(255)     DEFAULT NULL,
  `toggler`        INT(11)          DEFAULT NULL,
  `currenthash`    VARCHAR(32)      DEFAULT NULL,
  `lasthash`       VARCHAR(255)     DEFAULT NULL,
  `iscorefile`     INT(11)          DEFAULT NULL,
  `hashfailed`     INT(1)           DEFAULT NULL,
  `hashchanged`    INT(1)           DEFAULT NULL,
  `hacked`         INT(1)           DEFAULT NULL,
  `suspectcontent` INT(1)           DEFAULT NULL,
  `falsepositive`  INT(1)           DEFAULT NULL,
  `mailer`         INT(1)           DEFAULT NULL,
  `uploader`       INT(1)           DEFAULT NULL,
  `encrypted`      INT(1)           DEFAULT NULL,
  `queued`         INT(11)          DEFAULT NULL,
  `size`           INT(11)          DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `currenthash` (`currenthash`),
  KEY `iscorefile` (`iscorefile`),
  KEY `filewithpath` (`filewithpath`(200)),
  KEY `size` (`size`),
  KEY `hashfailed` (`hashfailed`),
  KEY `size_2` (`size`, `hashfailed`, `filewithpath`(255), `iscorefile`, `currenthash`),
  KEY `encrypted` (`encrypted`),
  KEY `queued` (`queued`),
  KEY `mailer` (`mailer`),
  KEY `uploader` (`uploader`),
  KEY `hacked` (`hacked`),
  KEY `suspectcontent` (`suspectcontent`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

CREATE TABLE `bf_folders` (
  `id`             INT(11) NOT NULL AUTO_INCREMENT,
  `folderwithpath` TEXT,
  `folderinfo`     VARCHAR(255)     DEFAULT NULL,
  `foldermtime`    VARCHAR(255)     DEFAULT NULL,
  `filesinfolder`  INT(11)          DEFAULT NULL,
  `queued`         INT(11)          DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `folderwithpath` (`folderwithpath`(255)),
  KEY `folderinfo` (`folderinfo`),
  KEY `foldermtime` (`foldermtime`),
  KEY `queued` (`queued`),
  KEY `filesinfolder` (`filesinfolder`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;