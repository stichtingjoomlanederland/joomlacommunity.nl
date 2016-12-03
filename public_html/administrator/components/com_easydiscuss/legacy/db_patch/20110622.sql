ALTER TABLE `#__discuss_posts` ADD `answered` TINYINT( 1 ) NULL DEFAULT '0';
ALTER TABLE `#__discuss_posts` ADD INDEX `discuss_post_answered` ( `answered` );


-- ALTER TABLE `#__discuss_posts` drop INDEX `discuss_post_isaccept`;
-- ALTER TABLE `#__discuss_posts` drop column `isaccept`;