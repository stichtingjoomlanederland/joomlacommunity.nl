DROP TABLE IF EXISTS `#__rsform_rseventspro`;

DELETE FROM #__rsform_component_types WHERE ComponentTypeId IN (30, 31, 32, 33, 34);
DELETE FROM #__rsform_component_type_fields WHERE ComponentTypeId IN (30, 31, 32, 33, 34);