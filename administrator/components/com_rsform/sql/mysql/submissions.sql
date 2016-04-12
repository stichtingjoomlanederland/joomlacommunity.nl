CREATE TABLE IF NOT EXISTS `#__rsform_submissions` (
  `SubmissionId` int(11) NOT NULL auto_increment,
  `FormId` int(11) NOT NULL default '0',
  `DateSubmitted` datetime NOT NULL ,
  `UserIp` varchar(255) NOT NULL default '',
  `Username` varchar(255) NOT NULL default '',
  `UserId` text NOT NULL,
  `Lang` varchar(255) NOT NULL,
  `confirmed` tinyint(1) NOT NULL,
  PRIMARY KEY  (`SubmissionId`),
  KEY `FormId` (`FormId`)
) DEFAULT CHARSET=utf8;