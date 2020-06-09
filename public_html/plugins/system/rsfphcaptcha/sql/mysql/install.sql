INSERT IGNORE INTO `#__rsform_config` (`SettingName`, `SettingValue`) VALUES
('hcaptcha.sitekey', ''),
('hcaptcha.secret', ''),
('hcaptcha.language', 'auto');

INSERT IGNORE INTO `#__rsform_component_types` (`ComponentTypeId`, `ComponentTypeName`) VALUES (2422, 'hcaptcha');

DELETE FROM `#__rsform_component_type_fields` WHERE ComponentTypeId = 2422;

INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Properties`, `Ordering`) VALUES
(2422, 'NAME', 'textbox', '', '', 0),
(2422, 'CAPTION', 'textbox', '', '', 1),
(2422, 'ADDITIONALATTRIBUTES', 'textarea', '', '', 2),
(2422, 'DESCRIPTION', 'textarea', '', '', 3),
(2422, 'VALIDATIONMESSAGE', 'textarea', 'INVALIDINPUT', '', 4),
(2422, 'THEME', 'select', 'LIGHT\r\nDARK', '', 5),
(2422, 'SIZE', 'select', 'NORMAL\r\nCOMPACT\r\nINVISIBLE', '', 7),
(2422, 'COMPONENTTYPE', 'hidden', '2422', '', 8);