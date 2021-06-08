DELETE FROM `#__rsform_config` WHERE `SettingName` LIKE 'jdideal.%';
DELETE FROM `#__rsform_component_types` WHERE `ComponentTypeId` IN (5575, 5576, 5577, 5578, 5579);
DELETE FROM `#__rsform_component_type_fields` WHERE `ComponentTypeId` IN (5575, 5576, 5577, 5578, 5579);
