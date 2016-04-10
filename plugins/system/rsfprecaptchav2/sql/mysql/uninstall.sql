DELETE FROM #__rsform_component_types WHERE ComponentTypeId = 2424;
DELETE FROM #__rsform_component_type_fields WHERE ComponentTypeId = 2424;

DELETE FROM #__rsform_config WHERE SettingName = 'recaptchav2.site.key';
DELETE FROM #__rsform_config WHERE SettingName = 'recaptchav2.secret.key';
DELETE FROM #__rsform_config WHERE SettingName = 'recaptchav2.language';