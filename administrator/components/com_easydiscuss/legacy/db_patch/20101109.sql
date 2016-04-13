ALTER TABLE `#__discuss_posts` ADD COLUMN `num_replies` int(11) NULL default 0;
ALTER TABLE `#__discuss_posts` ADD COLUMN `num_likes` int(11) NULL default 0;
ALTER TABLE `#__discuss_posts` ADD COLUMN `num_negvote` int(11) NULL default 0;
ALTER TABLE `#__discuss_posts` ADD COLUMN `sum_totalvote` int(11) NULL default 0;


update #__discuss_posts as `a` set a.sum_totalvote = (select sum(value) from #__discuss_votes as `b` where b.post_id = a.id);
update #__discuss_posts as `a` set a.sum_totalvote = 0 where sum_totalvote is null;
update #__discuss_posts as `a` set a.num_likes = (select count(b.id) from #__discuss_likes as `b` where b.content_id = a.id and b.`type` = 'post');