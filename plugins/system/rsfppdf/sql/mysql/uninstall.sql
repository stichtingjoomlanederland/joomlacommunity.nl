DROP TABLE IF EXISTS `#__rsform_pdfs`;

DELETE FROM #__rsform_config WHERE SettingName = 'pdf.font';
DELETE FROM #__rsform_config WHERE SettingName = 'pdf.orientation';
DELETE FROM #__rsform_config WHERE SettingName = 'pdf.paper';
DELETE FROM #__rsform_config WHERE SettingName = 'pdf.remote';
