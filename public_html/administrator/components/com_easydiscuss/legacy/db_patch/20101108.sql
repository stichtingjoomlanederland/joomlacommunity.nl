ALTER TABLE `#__discuss_posts` ADD INDEX `discuss_post_parentid` ( `parent_id` ) ;

ALTER TABLE `#__discuss_votes` ADD INDEX `discuss_user_post` (`user_id`, `post_id`);
ALTER TABLE `#__discuss_votes` ADD INDEX `discuss_post_id` (`post_id`);