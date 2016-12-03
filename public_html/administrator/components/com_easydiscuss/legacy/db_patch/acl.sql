
truncate table jos_discuss_acl;

INSERT INTO `jos_discuss_acl` (`id`, `action`, `default`, `description`, `published`, `ordering`, `public`) VALUES
(1, 'add_reply', 1, 'Can add new reply?', 1, 0, 1),
(2, 'add_question', 0, 'Can add new post?', 1, 0, 1),
(3, 'add_attachment', 0, 'Can add attachment?', 1, 0, 1),
(4, 'add_tag', 0, 'Can add tag?', 1, 0, 1),
(5, 'edit_reply', 0, 'Can edit reply?', 1, 0, 0),
(6, 'delete_reply', 0, 'Can delete reply?', 1, 0, 0),
(7, 'mark_answered', 0, 'Can mark or unmark reply as answered?', 1, 0, 0),
(8, 'lock_discussion', 0, 'Can lock or unlock discussion?', 1, 0, 0),
(9, 'edit_question', 0, 'Can edit post?', 1, 0, 0),
(10, 'delete_question', 0, 'Can delete post?', 1, 0, 0),
(11, 'delete_attachment', '0', 'Allows user to remove a file attachment from the reply or questions.', '1', '0', 0),
(12, 'add_comment', 0, 'Determines whether the user is allowed to comment.', 1, 0, 1),
(13, 'delete_comment', 0, 'Determines whether the user is allowed to delete comments.', 1, 0, 0),
(14, 'feature_post', 0, 'Determines if the user or user group is allowed to feature discussions on the site.', 1, 0, 0);