ALTER TABLE `#__discuss_users` CHANGE `permalink` `alias` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL

ALTER TABLE `#__discuss_posts` CHANGE `permalink` `alias` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL