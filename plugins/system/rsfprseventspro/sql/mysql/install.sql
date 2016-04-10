INSERT IGNORE INTO `#__rsform_component_types` (`ComponentTypeId`, `ComponentTypeName`) VALUES(30, 'textBox');
INSERT IGNORE INTO `#__rsform_component_types` (`ComponentTypeId`, `ComponentTypeName`) VALUES(31, 'textBox');
INSERT IGNORE INTO `#__rsform_component_types` (`ComponentTypeId`, `ComponentTypeName`) VALUES(32, 'rseprotickets');
INSERT IGNORE INTO `#__rsform_component_types` (`ComponentTypeId`, `ComponentTypeName`) VALUES(33, 'selectList');
INSERT IGNORE INTO `#__rsform_component_types` (`ComponentTypeId`, `ComponentTypeName`) VALUES(34, 'textBox');

DELETE FROM `#__rsform_component_type_fields` WHERE `ComponentTypeId` IN (30, 31, 32, 33, 34);

INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(30, 'NAME', 'hiddenparam', 'RSEProName', 0);
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(30, 'CAPTION', 'textbox', '', 1);
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(30, 'REQUIRED', 'hiddenparam', 'YES', 9);
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(30, 'VALIDATIONRULE', 'select', '//<code>\r\nreturn RSFormProHelper::getValidationRules();\r\n//</code>', 3);
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(30, 'VALIDATIONEXTRA', 'textbox', '', 3);
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(30, 'VALIDATIONMESSAGE', 'textarea', 'INVALIDINPUT', 4);
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(30, 'ADDITIONALATTRIBUTES', 'textarea', '', 5);
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(30, 'DEFAULTVALUE', 'hiddenparam', '//<code>\r\nif(class_exists(''plgSystemRSFPRSEventspro'')) return plgSystemRSFPRSEventspro::getName();\r\n//</code>', 6);

INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(30, 'DESCRIPTION', 'textarea', '', 7);
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(30, 'COMPONENTTYPE', 'hidden', '30', 8);
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(30, 'SIZE', 'textbox', '20', 2);
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(30, 'MAXSIZE', 'hiddenparam', '', 10);


INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(31, 'NAME', 'hiddenparam', 'RSEProEmail', 0);
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(31, 'CAPTION', 'textbox', '', 1);
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(31, 'REQUIRED', 'hiddenparam', 'YES', 2);
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(31, 'SIZE', 'textbox', '20', 3);
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(31, 'VALIDATIONRULE', 'select', '//<code>\r\nreturn RSFormProHelper::getValidationRules();\r\n//</code>', 4);
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(31, 'VALIDATIONEXTRA', 'textbox', '', 4);
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(31, 'VALIDATIONMESSAGE', 'textarea', 'INVALIDINPUT', 5);
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(31, 'ADDITIONALATTRIBUTES', 'textarea', '', 6);
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(31, 'DEFAULTVALUE', 'hiddenparam', '//<code>\r\n$user = JFactory::getUser();\r\nreturn $user->get(''email'');\r\n//</code>', 7);
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(31, 'COMPONENTTYPE', 'hidden', '31', 9);
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(31, 'DESCRIPTION', 'textarea', '', 8);
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(31, 'MAXSIZE', 'hiddenparam', '', 10);


INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(32, 'NAME', 'hiddenparam', 'RSEProTickets', 0);
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(32, 'CAPTION', 'textbox', '', 1);
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(32, 'REQUIRED', 'hiddenparam', 'YES', 2);
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(32, 'VALIDATIONMESSAGE', 'textarea', 'INVALIDINPUT', 3);
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(32, 'ADDITIONALATTRIBUTES', 'hiddenparam', 'onchange="rs_get_ticket(this.value);"', 4);
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(32, 'COMPONENTTYPE', 'hidden', '32', 5);
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(32, 'ITEMS', 'hiddenparam', '//<code>\r\nif(class_exists(''plgSystemRSFPRSEventspro'')) return plgSystemRSFPRSEventspro::getTickets();\r\n//</code>', 6);


INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(33, 'NAME', 'hiddenparam', 'RSEProPayment', 0);
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(33, 'CAPTION', 'textbox', '', 1);
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(33, 'ITEMS', 'hiddenparam', '//<code>\r\nif (class_exists(''plgSystemRSFPRSEventspro'')) return plgSystemRSFPRSEventspro::getPayments();\r\n//</code>', 2);
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(33, 'REQUIRED', 'hiddenparam', 'YES', 4);
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(33, 'ADDITIONALATTRIBUTES', 'textarea', '', 5);
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(33, 'DESCRIPTION', 'textarea', '', 6);
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(33, 'VALIDATIONMESSAGE', 'textarea', 'INVALIDINPUT', 7);
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(33, 'COMPONENTTYPE', 'hidden', '33', 8);
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(33, 'MULTIPLE', 'hiddenparam', 'NO', '9');
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(33, 'SIZE', 'hiddenparam', '', '10');
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(33, 'FLOW', 'select', 'HORIZONTAL\r\nVERTICAL', '11');

INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(34, 'NAME', 'hiddenparam', 'RSEProCoupon', 0);
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(34, 'CAPTION', 'textbox', '', 1);
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(34, 'REQUIRED', 'hiddenparam', 'NO', 9);
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(34, 'ADDITIONALATTRIBUTES', 'textarea', '', 5);
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(34, 'DEFAULTVALUE', 'hiddenparam', '', 6);
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(34, 'DESCRIPTION', 'textarea', '', 7);
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(34, 'COMPONENTTYPE', 'hidden', '34', 8);
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(34, 'SIZE', 'textbox', '20', 2);
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(34, 'MAXSIZE', 'hiddenparam', '', 10);

CREATE TABLE IF NOT EXISTS `#__rsform_rseventspro` (
  `form_id` int(11) NOT NULL,
  `registration` tinyint(1) NOT NULL,
  `registration_subject` varchar(255) NOT NULL,
  `registration_text` text NOT NULL,
  `activation` tinyint(1) NOT NULL,
  `activation_subject` varchar(255) NOT NULL,
  `activation_text` text NOT NULL,
  `unsubscribe` tinyint(1) NOT NULL,
  `unsubscribe_subject` varchar(255) NOT NULL,
  `unsubscribe_text` text NOT NULL,
  `denied` tinyint(1) NOT NULL,
  `denied_subject` varchar(255) NOT NULL,
  `denied_text` text NOT NULL,
  `notify` tinyint(1) NOT NULL,
  `notify_subject` varchar(255) NOT NULL,
  `notify_text` text NOT NULL,
  `ticketpdf` tinyint(1) NOT NULL,
  `ticketpdf_layout` text NOT NULL,
  `published` tinyint(1) NOT NULL,
  PRIMARY KEY (`form_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;