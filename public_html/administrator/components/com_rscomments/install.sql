CREATE TABLE IF NOT EXISTS `#__rscomments_comments` (
  `IdComment` int(15) NOT NULL AUTO_INCREMENT,
  `IdParent` int(15) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `website` varchar(255) NOT NULL,
  `comment` text NOT NULL,
  `uid` int(5) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `date` DATETIME NOT NULL, 
  `modified_by` int(11) NOT NULL,
  `modified` datetime NOT NULL,
  `option` varchar(100) NOT NULL,
  `id` int(5) NOT NULL,
  `url` text NOT NULL,
  `file` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `coordinates` varchar(255) NOT NULL,
  `published` int(2) NOT NULL,
  PRIMARY KEY (`IdComment`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__rscomments_groups` (
  `IdGroup` int(2) NOT NULL AUTO_INCREMENT,
  `GroupName` varchar(255) NOT NULL,
  `gid` int(5) NOT NULL,
  `permissions` text NOT NULL,
  PRIMARY KEY (`IdGroup`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__rscomments_subscriptions` (
  `IdSubscription` int(10) NOT NULL AUTO_INCREMENT,
  `id` int(10) NOT NULL,
  `option` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY (`IdSubscription`),
  KEY `IdComment` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__rscomments_votes` (
  `IdVote` int(5) NOT NULL AUTO_INCREMENT,
  `IdComment` int(15) NOT NULL,
  `uid` int(5) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `value` enum('positive','negative') NOT NULL,
  PRIMARY KEY (`IdVote`),
  KEY `IdComment` (`IdComment`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__rscomments_threads` (
  `IdThread` int(5) NOT NULL AUTO_INCREMENT,
  `option` varchar(255) NOT NULL,
  `id` int(10) NOT NULL,
  PRIMARY KEY (`IdThread`),
  KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__rscomments_messages` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `type` varchar(100) NOT NULL,
  `tag` varchar(10) NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__rscomments_emoticons` (
	`id` INT NOT NULL AUTO_INCREMENT ,
	`replace` VARCHAR( 225 ) NOT NULL ,
	`with` TEXT NOT NULL ,
	PRIMARY KEY ( `id` )
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__rscomments_reports` (
	`id` INT NOT NULL AUTO_INCREMENT ,
	`report` TEXT NOT NULL ,
	`IdComment` INT NOT NULL ,
	`uid` INT NOT NULL ,
	`ip` VARCHAR( 15 ) NOT NULL ,
	`date` DATETIME NOT NULL ,
	PRIMARY KEY ( `id` ) ,
	INDEX ( `IdComment` )
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


INSERT IGNORE INTO `#__rscomments_messages` (`id`, `type`, `tag`, `content`) VALUES(1, 'terms', 'en-GB', '<p>By checking this, you agree with the following: <br /><br />1. To accept full responsibility for the comment that you submit.<br />2. To use this function only for lawful purposes.<br />3. Not to post defamatory, abusive, offensive, racist, sexist, threatening, vulgar, obscene, hateful or otherwise inappropriate comments, or to post comments which will constitute a criminal offense or give rise to civil liability.<br />4. Not to post or make available any material which is protected by copyright, trade mark or other proprietary right without the express permission of the owner of the copyright, trade mark or any other proprietary right.<br />5. To evaluate for yourself the accuracy of any opinion, advice or other content.</p>');
INSERT IGNORE INTO `#__rscomments_messages` (`id`, `type`, `tag`, `content`) VALUES(2, 'comments_closed', 'en-GB', 'Comments closed!');
INSERT IGNORE INTO `#__rscomments_messages` (`id`, `type`, `tag`, `content`) VALUES(3, 'comments_denied', 'en-GB', 'You don`t have permission to comment here!');
INSERT IGNORE INTO `#__rscomments_messages` (`id`, `type`, `tag`, `content`) VALUES(4, 'notification_message', 'en-GB', '<p>User <em><strong>{username}</strong></em> has posted a new comment from <strong>{ip}</strong> with the message :</p>\r\n<p>{message}</p>\r\n<p>If you wish to view this comment please follow this link : {link}</p>');
INSERT IGNORE INTO `#__rscomments_messages` (`id`, `type`, `tag`, `content`) VALUES(5, 'subscription_message', 'en-GB', '<p>Hello {name},</p>\r\n<p>A new comment has been added.</p>\r\n<p>{author} commented :</p>\r\n<p>{message}</p>\r\n<p>For the full commenting thread please go to {link}.</p>\r\n<p>If you would like to unsubscribe from this thread please click on the following link: <br />{unsubscribelink}</p>');

INSERT IGNORE INTO `#__rscomments_emoticons` (`id`, `replace`, `with`) VALUES(1, ':confused:', 'components/com_rscomments/assets/images/emoticons/confused.gif');
INSERT IGNORE INTO `#__rscomments_emoticons` (`id`, `replace`, `with`) VALUES(2, ':cool:', 'components/com_rscomments/assets/images/emoticons/cool.gif');
INSERT IGNORE INTO `#__rscomments_emoticons` (`id`, `replace`, `with`) VALUES(3, ':cry:', 'components/com_rscomments/assets/images/emoticons/cry.gif');
INSERT IGNORE INTO `#__rscomments_emoticons` (`id`, `replace`, `with`) VALUES(4, ':laugh:', 'components/com_rscomments/assets/images/emoticons/laugh.gif');
INSERT IGNORE INTO `#__rscomments_emoticons` (`id`, `replace`, `with`) VALUES(5, ':lol:', 'components/com_rscomments/assets/images/emoticons/lol.gif');
INSERT IGNORE INTO `#__rscomments_emoticons` (`id`, `replace`, `with`) VALUES(6, ':normal:', 'components/com_rscomments/assets/images/emoticons/normal.gif');
INSERT IGNORE INTO `#__rscomments_emoticons` (`id`, `replace`, `with`) VALUES(7, ':blush:', 'components/com_rscomments/assets/images/emoticons/redface.gif');
INSERT IGNORE INTO `#__rscomments_emoticons` (`id`, `replace`, `with`) VALUES(8, ':rolleyes:', 'components/com_rscomments/assets/images/emoticons/rolleyes.gif');
INSERT IGNORE INTO `#__rscomments_emoticons` (`id`, `replace`, `with`) VALUES(9, ':sad:', 'components/com_rscomments/assets/images/emoticons/sad.gif');
INSERT IGNORE INTO `#__rscomments_emoticons` (`id`, `replace`, `with`) VALUES(10, ':shocked:', 'components/com_rscomments/assets/images/emoticons/shocked.gif');
INSERT IGNORE INTO `#__rscomments_emoticons` (`id`, `replace`, `with`) VALUES(11, ':sick:', 'components/com_rscomments/assets/images/emoticons/sick.gif');
INSERT IGNORE INTO `#__rscomments_emoticons` (`id`, `replace`, `with`) VALUES(12, ':sleeping:', 'components/com_rscomments/assets/images/emoticons/sleeping.gif');
INSERT IGNORE INTO `#__rscomments_emoticons` (`id`, `replace`, `with`) VALUES(13, ':smile:', 'components/com_rscomments/assets/images/emoticons/smile.gif');
INSERT IGNORE INTO `#__rscomments_emoticons` (`id`, `replace`, `with`) VALUES(14, ':surprised:', 'components/com_rscomments/assets/images/emoticons/surprised.gif');
INSERT IGNORE INTO `#__rscomments_emoticons` (`id`, `replace`, `with`) VALUES(15, ':tongue:', 'components/com_rscomments/assets/images/emoticons/tongue.gif');
INSERT IGNORE INTO `#__rscomments_emoticons` (`id`, `replace`, `with`) VALUES(16, ':unsure:', 'components/com_rscomments/assets/images/emoticons/unsure.gif');
INSERT IGNORE INTO `#__rscomments_emoticons` (`id`, `replace`, `with`) VALUES(17, ':whistle:', 'components/com_rscomments/assets/images/emoticons/whistling.gif');
INSERT IGNORE INTO `#__rscomments_emoticons` (`id`, `replace`, `with`) VALUES(18, ':wink:', 'components/com_rscomments/assets/images/emoticons/wink.gif');
