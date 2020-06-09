DELETE FROM #__rsform_component_types WHERE ComponentTypeId = 2422;
DELETE FROM #__rsform_component_type_fields WHERE ComponentTypeId = 2422;

DELETE FROM #__rsform_config WHERE SettingName LIKE 'hcaptcha.%';