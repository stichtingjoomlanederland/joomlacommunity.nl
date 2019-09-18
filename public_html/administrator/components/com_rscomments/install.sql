CREATE TABLE IF NOT EXISTS `#__rscomments_comments` (
  `IdComment` int(15) NOT NULL AUTO_INCREMENT,
  `IdParent` int(15) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `website` varchar(255) NOT NULL DEFAULT '',
  `comment` text NOT NULL,
  `uid` int(5) NOT NULL DEFAULT '0',
  `ip` varchar(15) NOT NULL DEFAULT '',
  `hash` varchar(32) NOT NULL DEFAULT '',
  `sid` varchar(255) NOT NULL DEFAULT '',
  `date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00', 
  `modified_by` int(11) NOT NULL DEFAULT '0',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `option` varchar(100) NOT NULL DEFAULT '',
  `id` int(5) NOT NULL DEFAULT '0',
  `url` text NOT NULL,
  `file` varchar(255) NOT NULL DEFAULT '',
  `location` varchar(255) NOT NULL DEFAULT '',
  `coordinates` varchar(255) NOT NULL DEFAULT '',
  `anonymous` tinyint(2) NOT NULL DEFAULT '0',
  `emails` tinyint(2) NOT NULL DEFAULT '0',
  `published` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`IdComment`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__rscomments_groups` (
  `IdGroup` int(2) NOT NULL AUTO_INCREMENT,
  `GroupName` varchar(255) NOT NULL DEFAULT '',
  `gid` int(5) NOT NULL DEFAULT '0',
  `permissions` text NOT NULL,
  PRIMARY KEY (`IdGroup`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__rscomments_subscriptions` (
  `IdSubscription` int(10) NOT NULL AUTO_INCREMENT,
  `id` int(10) NOT NULL DEFAULT '0',
  `option` varchar(255) NOT NULL DEFAULT '',
  `name` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`IdSubscription`),
  KEY `IdComment` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__rscomments_votes` (
  `IdVote` int(5) NOT NULL AUTO_INCREMENT,
  `IdComment` int(15) NOT NULL DEFAULT '0',
  `uid` int(5) NOT NULL DEFAULT '0',
  `ip` varchar(32) NOT NULL DEFAULT '',
  `value` enum('positive','negative') NOT NULL DEFAULT 'positive',
  PRIMARY KEY (`IdVote`),
  KEY `IdComment` (`IdComment`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__rscomments_threads` (
  `IdThread` int(5) NOT NULL AUTO_INCREMENT,
  `option` varchar(255) NOT NULL DEFAULT '',
  `id` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`IdThread`),
  KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__rscomments_messages` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `type` varchar(100) NOT NULL DEFAULT '',
  `tag` varchar(10) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__rscomments_emoticons` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`replace` VARCHAR( 225 ) NOT NULL DEFAULT '',
	`with` TEXT NOT NULL,
	PRIMARY KEY ( `id` )
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__rscomments_reports` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`report` TEXT NOT NULL,
	`IdComment` INT NOT NULL DEFAULT '0',
	`uid` INT NOT NULL DEFAULT '0',
	`ip` VARCHAR( 15 ) NOT NULL DEFAULT '',
	`date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY ( `id` ),
	INDEX ( `IdComment` )
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


INSERT IGNORE INTO `#__rscomments_messages` (`id`, `type`, `tag`, `content`) VALUES(1, 'terms', 'en-GB', '<p>By checking this, you agree with the following: <br /><br />1. To accept full responsibility for the comment that you submit.<br />2. To use this function only for lawful purposes.<br />3. Not to post defamatory, abusive, offensive, racist, sexist, threatening, vulgar, obscene, hateful or otherwise inappropriate comments, or to post comments which will constitute a criminal offense or give rise to civil liability.<br />4. Not to post or make available any material which is protected by copyright, trade mark or other proprietary right without the express permission of the owner of the copyright, trade mark or any other proprietary right.<br />5. To evaluate for yourself the accuracy of any opinion, advice or other content.</p>');
INSERT IGNORE INTO `#__rscomments_messages` (`id`, `type`, `tag`, `content`) VALUES(2, 'comments_closed', 'en-GB', 'Comments closed!');
INSERT IGNORE INTO `#__rscomments_messages` (`id`, `type`, `tag`, `content`) VALUES(3, 'comments_denied', 'en-GB', 'You don`t have permission to comment here!');
INSERT IGNORE INTO `#__rscomments_messages` (`id`, `type`, `tag`, `content`) VALUES(4, 'notification_message', 'en-GB', '<p>User <em><strong>{username}</strong></em> has posted a new comment from <strong>{ip}</strong> with the message :</p>\r\n<p>{message}</p>\r\n<p>If you wish to view this comment please follow this link : {link}</p>');
INSERT IGNORE INTO `#__rscomments_messages` (`id`, `type`, `tag`, `content`) VALUES(5, 'subscription_message', 'en-GB', '<p>Hello {name},</p>\r\n<p>A new comment has been added.</p>\r\n<p>{author} commented :</p>\r\n<p>{message}</p>\r\n<p>For the full commenting thread please go to {link}.</p>\r\n<p>If you would like to unsubscribe from this thread please click on the following link: <br />{unsubscribelink}</p>');

INSERT IGNORE INTO `#__rscomments_emoticons` (`id`, `replace`, `with`) VALUES(1, ':confused:', 'media/com_rscomments/images/emoticons/confused.gif');
INSERT IGNORE INTO `#__rscomments_emoticons` (`id`, `replace`, `with`) VALUES(2, ':cool:', 'media/com_rscomments/images/emoticons/cool.gif');
INSERT IGNORE INTO `#__rscomments_emoticons` (`id`, `replace`, `with`) VALUES(3, ':cry:', 'media/com_rscomments/images/emoticons/cry.gif');
INSERT IGNORE INTO `#__rscomments_emoticons` (`id`, `replace`, `with`) VALUES(4, ':laugh:', 'media/com_rscomments/images/emoticons/laugh.gif');
INSERT IGNORE INTO `#__rscomments_emoticons` (`id`, `replace`, `with`) VALUES(5, ':lol:', 'media/com_rscomments/images/emoticons/lol.gif');
INSERT IGNORE INTO `#__rscomments_emoticons` (`id`, `replace`, `with`) VALUES(6, ':normal:', 'media/com_rscomments/images/emoticons/normal.gif');
INSERT IGNORE INTO `#__rscomments_emoticons` (`id`, `replace`, `with`) VALUES(7, ':blush:', 'media/com_rscomments/images/emoticons/redface.gif');
INSERT IGNORE INTO `#__rscomments_emoticons` (`id`, `replace`, `with`) VALUES(8, ':rolleyes:', 'media/com_rscomments/images/emoticons/rolleyes.gif');
INSERT IGNORE INTO `#__rscomments_emoticons` (`id`, `replace`, `with`) VALUES(9, ':sad:', 'media/com_rscomments/images/emoticons/sad.gif');
INSERT IGNORE INTO `#__rscomments_emoticons` (`id`, `replace`, `with`) VALUES(10, ':shocked:', 'media/com_rscomments/images/emoticons/shocked.gif');
INSERT IGNORE INTO `#__rscomments_emoticons` (`id`, `replace`, `with`) VALUES(11, ':sick:', 'media/com_rscomments/images/emoticons/sick.gif');
INSERT IGNORE INTO `#__rscomments_emoticons` (`id`, `replace`, `with`) VALUES(12, ':sleeping:', 'media/com_rscomments/images/emoticons/sleeping.gif');
INSERT IGNORE INTO `#__rscomments_emoticons` (`id`, `replace`, `with`) VALUES(13, ':smile:', 'media/com_rscomments/images/emoticons/smile.gif');
INSERT IGNORE INTO `#__rscomments_emoticons` (`id`, `replace`, `with`) VALUES(14, ':surprised:', 'media/com_rscomments/images/emoticons/surprised.gif');
INSERT IGNORE INTO `#__rscomments_emoticons` (`id`, `replace`, `with`) VALUES(15, ':tongue:', 'media/com_rscomments/images/emoticons/tongue.gif');
INSERT IGNORE INTO `#__rscomments_emoticons` (`id`, `replace`, `with`) VALUES(16, ':unsure:', 'media/com_rscomments/images/emoticons/unsure.gif');
INSERT IGNORE INTO `#__rscomments_emoticons` (`id`, `replace`, `with`) VALUES(17, ':whistle:', 'media/com_rscomments/images/emoticons/whistling.gif');
INSERT IGNORE INTO `#__rscomments_emoticons` (`id`, `replace`, `with`) VALUES(18, ':wink:', 'media/com_rscomments/images/emoticons/wink.gif');
