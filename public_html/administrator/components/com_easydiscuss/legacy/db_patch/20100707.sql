ALTER TABLE `#__discuss_posts` ADD COLUMN `islock` TINYINT(1) UNSIGNED DEFAULT '0' AFTER `hits`;
ALTER TABLE `#__discuss_posts` ADD COLUMN `isresolve` TINYINT(1) UNSIGNED DEFAULT '0' AFTER `islock`;