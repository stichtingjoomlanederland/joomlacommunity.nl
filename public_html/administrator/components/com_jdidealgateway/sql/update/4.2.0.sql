ALTER TABLE `#__jdidealgateway_profiles`
  ADD UNIQUE INDEX `alias` (`alias`);

UPDATE `#__jdidealgateway_messages`
SET `orderstatus` = 'FAILURE'
WHERE `orderstatus` = 'FAILED';