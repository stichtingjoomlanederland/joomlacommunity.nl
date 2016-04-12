INSERT IGNORE INTO `#__rsform_config` (`SettingName`, `SettingValue`) VALUES ('pdf.font', 'times'), ('pdf.orientation', 'portrait'), ('pdf.paper', 'a4'), ('pdf.remote', '0');

CREATE TABLE IF NOT EXISTS `#__rsform_pdfs` (
  `form_id` int(11) NOT NULL,
  `useremail_send` tinyint(1) NOT NULL,
  `useremail_filename` varchar(255) NOT NULL,
  `useremail_php` text NOT NULL,
  `useremail_layout` text NOT NULL,
  `useremail_userpass` varchar(255) NOT NULL,
  `useremail_ownerpass` varchar(255) NOT NULL,
  `useremail_options` varchar(32) NOT NULL DEFAULT 'print,modify,copy,add',
  `adminemail_send` tinyint(1) NOT NULL,
  `adminemail_filename` varchar(255) NOT NULL,
  `adminemail_php` text NOT NULL,
  `adminemail_layout` text NOT NULL,
  `adminemail_userpass` varchar(255) NOT NULL,
  `adminemail_ownerpass` varchar(255) NOT NULL,
  `adminemail_options` varchar(32) NOT NULL DEFAULT 'print,modify,copy,add',
  PRIMARY KEY (`form_id`)
) DEFAULT CHARSET=utf8;