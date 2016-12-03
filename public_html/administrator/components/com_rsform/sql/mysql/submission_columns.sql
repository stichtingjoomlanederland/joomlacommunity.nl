CREATE TABLE IF NOT EXISTS `#__rsform_submission_columns` (
  `FormId` int(11) NOT NULL,
  `ColumnName` varchar(255) NOT NULL,
  `ColumnStatic` tinyint(1) NOT NULL,
  PRIMARY KEY  (`FormId`,`ColumnName`,`ColumnStatic`)
) DEFAULT CHARSET=utf8;