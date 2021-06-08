ALTER TABLE #__jdidealgateway_customers ADD profileId INT(10) UNSIGNED NOT NULL;
ALTER TABLE #__jdidealgateway_subscriptions CHANGE customerId customer_id INT(10) unsigned NOT NULL COMMENT 'The customer ID';
ALTER TABLE #__jdidealgateway_subscriptions ADD customerId varchar(30) NOT NULL COMMENT 'The PSP Customer ID';
ALTER TABLE #__jdidealgateway_subscriptions ADD profileId INT(10) UNSIGNED NOT NULL;
