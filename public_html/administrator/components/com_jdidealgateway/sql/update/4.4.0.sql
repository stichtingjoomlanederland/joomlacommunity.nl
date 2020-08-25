ALTER TABLE `#__jdidealgateway_messages`
	CHANGE COLUMN `message_text_id` `message_text_id` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'The message ID' AFTER `message_type`;