INSERT IGNORE INTO `#__rsform_component_types` (`ComponentTypeId`, `ComponentTypeName`) VALUES
(5575, 'jdidealSingleProduct'),
(5576, 'jdidealMultipleProducts'),
(5577, 'jdidealTotal'),
(5578, 'jdidealInputbox'),
(5579, 'jdidealButton');

INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Properties`, `Ordering`) VALUES
/* Single Product */
(5575, 'COMPONENTTYPE', 'hidden', '5575', '', 0),
(5575, 'NAME', 'textbox', '', '', 0),
(5575, 'CAPTION', 'textbox', '', '', 1),
(5575, 'DESCRIPTION', 'textarea', '', '', 2),
(5575, 'SHOW', 'select', 'YES\r\nNO', '', 3),
(5575, 'PRICE', 'textbox', '', '', 4),
(5575, 'CURRENCY', 'textbox', '', '', 5),
/* Multiple Products */
(5576, 'COMPONENTTYPE', 'hidden', '5576', '', 0),
(5576, 'NAME', 'textbox', '', '', 0),
(5576, 'CAPTION', 'textbox', '', '', 1),
(5576, 'SIZE', 'textbox', '', '', 2),
(5576, 'MULTIPLE', 'select', 'NO\r\nYES', '', 3),
(5576, 'FLOW', 'select', 'HORIZONTAL\r\nVERTICAL', '', 4),
(5576, 'ITEMS', 'textarea', '', '', 5),
(5576, 'REQUIRED', 'select', 'NO\r\nYES', '', 6),
(5576, 'ADDITIONALATTRIBUTES', 'textarea', '', '', 7),
(5576, 'DESCRIPTION', 'textarea', '', '', 8),
(5576, 'VALIDATIONMESSAGE', 'textarea', 'INVALIDINPUT', '', 9),
(5576, 'CURRENCY', 'textbox', '', '', 10),
(5576, 'HIDE_DESCRIPTION', 'select', 'NO\r\nYES', '', 11),
(5576, 'VIEW_TYPE', 'select', 'DROPDOWN\r\nCHECKBOX\r\nRADIOGROUP', '{"case":{"DROPDOWN":{"show":[],"hide":["CHECKBOX_CHECKED","CHECKBOX_INVISIBLE"]},"CHECKBOX":{"show":["CHECKBOX_CHECKED","CHECKBOX_INVISIBLE"],"hide":[]},"RADIOGROUP":{"show":[],"hide":["CHECKBOX_CHECKED","CHECKBOX_INVISIBLE"]}}}', 11),
(5576, 'CHECKBOX_CHECKED', 'select', 'NO\r\nYES', '', 12),
(5576, 'CHECKBOX_INVISIBLE', 'select', 'NO\r\nYES', '', 13),
(5576, 'QUANTITYBOX', 'select', 'NO\r\nYES', '{"case":{"YES":{"show":["BOXTYPE","DEFAULTQUANTITY","BOXMIN","BOXMAX","BOXSTEP"],"hide":[]},"NO":{"show":[],"hide":["BOXTYPE","DEFAULTQUANTITY","BOXMIN","BOXMAX","BOXSTEP"]}}}',14),
(5576, 'BOXTYPE', 'select', 'INPUT\r\nDROPDOWN\r\nNUMBER', '{"case":{"INPUT":{"show":["DEFAULTQUANTITY"],"hide":["BOXMIN","BOXMAX","BOXSTEP"]}, "DROPDOWN":{"show":["DEFAULTQUANTITY","BOXMIN","BOXMAX","BOXSTEP"],"hide":[]},"NUMBER":{"show":["DEFAULTQUANTITY","BOXMIN","BOXMAX","BOXSTEP"],"hide":[]}}}', 15),
(5576, 'DEFAULTQUANTITY', 'textbox', '', '', 16),
(5576, 'BOXMIN', 'textbox', '', '', 17),
(5576, 'BOXMAX', 'textbox', '', '', 18),
(5576, 'BOXSTEP', 'textbox', '', '', 19),
/* Total field */
(5577, 'COMPONENTTYPE', 'hidden', '5577', '', 0),
(5577, 'NAME', 'textbox', '', '', 0),
(5577, 'CAPTION', 'textbox', '', '', 1),
(5577, 'CURRENCY', 'textbox', '', '', 2),
(5577, 'SHOW', 'select', 'YES\r\nNO', '', 3),
(5577, 'PLACEHOLDER', 'select', 'YES\r\nNO', '', 4),
/* Input field */
(5578, 'COMPONENTTYPE', 'hidden', '5578', '', 5),
(5578, 'NAME', 'textbox', '', '', 0),
(5578, 'CAPTION', 'textbox', '', '', 1),
(5578, 'VIEW_TYPE', 'hiddenparam', 'INPUTBOX', '', 2),
(5578, 'DEFAULTVALUE', 'textarea', '', '', 3),
(5578, 'DESCRIPTION', 'textarea', '', '', 4),
(5578, 'SIZE', 'textbox', '5', '', 5),
(5578, 'REQUIRED', 'select', 'NO\r\nYES', '', 6),
(5578, 'VALIDATIONMESSAGE', 'textarea', 'INVALIDINPUT', '', 7),
(5578, 'CURRENCY', 'textbox', '', '', 8),
(5578, 'BOXTYPE', 'select', 'INPUT\r\nNUMBER', '{"case":{"INPUT":{"show":["DEFAULTQUANTITY"],"hide":["BOXMIN","BOXMAX","BOXSTEP"]}, "NUMBER":{"show":["DEFAULTQUANTITY","BOXMIN","BOXMAX","BOXSTEP"],"hide":[]}}}', 9),
(5578, 'BOXMIN', 'textbox', '', '', 10),
(5578, 'BOXMAX', 'textbox', '', '', 11),
(5578, 'BOXSTEP', 'textbox', '', '', 12),
/* RO Payments button */
(5579, 'NAME', 'textbox', '', '', 0),
(5579, 'LABEL', 'textbox', '', '', 1),
(5579, 'COMPONENTTYPE', 'hidden', '5579', '', 2),
(5579, 'LAYOUTHIDDEN', 'hiddenparam', 'YES', '', 3);

CREATE TABLE IF NOT EXISTS `#__rsform_jdideal` (
	`form_id` INT(11) NOT NULL,
	`params` TEXT NOT NULL,
	PRIMARY KEY (`form_id`)
) CHARSET=utf8 COMMENT='RO Payments settings';
