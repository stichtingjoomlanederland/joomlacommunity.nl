alter table `#__discuss_posts` add index `discuss_post_category` (`category_id`);
alter table `#__discuss_posts` add index `discuss_post_query1` (`published`, `parent_id`, `answered`, `id`);
alter table `#__discuss_posts` add index `discuss_post_query2` (`published`, `parent_id`, `answered`, `replied`);
alter table `#__discuss_posts` add index `discuss_post_query3` (`published`, `parent_id`, `category_id`, `created`);
alter table `#__discuss_posts` add index `discuss_post_query4` (`published`, `parent_id`, `category_id`, `id`);
alter table `#__discuss_posts` add index `discuss_post_query5` (`published`, `parent_id`, `created`);
alter table `#__discuss_posts` add index `discuss_post_query6` (`published`, `parent_id`, `id`);
alter table `#__discuss_votes` add index `discuss_user_id` (`user_id`);
alter table `#__discuss_category` add index `discuss_cat_mod_categories1` (`published`, `private`, `id`);
alter table `#__discuss_category` add index `discuss_cat_mod_categories2` (`published`, `private`, `ordering`);

alter table `#__discuss_tags` add index `discuss_tags_query1` (`published`, `id`);