CREATE TABLE IF NOT EXISTS `#__rsform_submission_values` (
  `SubmissionValueId` int(11) NOT NULL auto_increment,
  `FormId` int(11) NOT NULL,
  `SubmissionId` int(11) NOT NULL default '0',
  `FieldName` text NOT NULL,
  `FieldValue` text NOT NULL,
  PRIMARY KEY  (`SubmissionValueId`),
  KEY `FormId` (`FormId`),
  KEY `SubmissionId` (`SubmissionId`)
) DEFAULT CHARSET=utf8;