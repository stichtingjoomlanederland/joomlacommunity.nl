
ALTER TABLE `jos_discuss_notifications` ADD INDEX `discuss_notification` (`target`, `state`, `cid`, `created`, `id`);
ALTER TABLE `jos_discuss_notifications` ADD INDEX `discuss_notification_created` (`created`);
ALTER TABLE `jos_discuss_badges` ADD INDEX `discuss_badges_alias` (`alias`);
ALTER TABLE `jos_discuss_badges` ADD INDEX `discuss_badges_published` (`published`);
ALTER TABLE `jos_discuss_points` ADD INDEX `discuss_points_rule` (`rule_id`);
ALTER TABLE `jos_discuss_points` ADD INDEX `discuss_points_published` (`published`);
ALTER TABLE `jos_discuss_rules` ADD INDEX `discuss_rules_command` (`command` (255) );
ALTER TABLE `jos_discuss_ranks` ADD INDEX `discuss_ranks_range` (`start`, `end`);
ALTER TABLE `jos_discuss_oauth` ADD INDEX `discuss_oauth_type` (`type`);