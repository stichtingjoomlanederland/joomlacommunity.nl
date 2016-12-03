
alter table jos_discuss_users add column `posts_read` TEXT NULL;

ALTER TABLE `jos_discuss_votes` DROP `ipaddress`;

ALTER TABLE `jos_discuss_votes` ADD `session_id` VARCHAR( 200 ) DEFAULT NULL;

alter table jos_discuss_posts add column `legacy` tinyint(1) default 1;

alter table jos_discuss_posts add index `unread_category_posts` (legacy, category_id, id );

ALTER TABLE `jos_discuss_posts` ADD `address` TEXT NOT NULL , ADD `latitude` VARCHAR( 255 ) NOT NULL , ADD `longitude` VARCHAR( 255 ) NOT NULL;