ALTER TABLE `#__discuss_posts` ADD `user_type` VARCHAR( 255 ) NOT NULL AFTER `parent_id` ,
ADD `poster_name` VARCHAR( 255 ) NOT NULL AFTER `user_type` ,
ADD `poster_email` VARCHAR( 255 ) NOT NULL AFTER `poster_name`