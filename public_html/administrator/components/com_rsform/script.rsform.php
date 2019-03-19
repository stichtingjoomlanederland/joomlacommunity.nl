<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2017 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class com_rsformInstallerScript
{
	protected $source;
	
	public function update($parent) {
		$db = JFactory::getDbo();
		$this->source = $parent->getParent()->getPath('source');

		/**
		 * Create column here, so we can run the SQL immediately after
		 */
		$columns = $db->getTableColumns('#__rsform_component_type_fields', false);
		if (!isset($columns['Properties'])) {
			$db->setQuery("ALTER TABLE `#__rsform_component_type_fields` ADD `Properties` TEXT NOT NULL AFTER `FieldValues`");
			$db->execute();
		}
		if ($columns['FieldType']->Type != "varchar(32)") {
			$db->setQuery("ALTER TABLE `#__rsform_component_type_fields` CHANGE `FieldType` `FieldType` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'hidden'");
			$db->execute();
		}

		// Add config data
		$this->runSQL('config.data.sql');
		
		// Run all SQL queries to create missing data
		$this->runSQL('component_type_fields.data.sql');
		$this->runSQL('component_types.data.sql');
		$this->runSQL('conditions.sql');
		$this->runSQL('condition_details.sql');
		$this->runSQL('emails.sql');
		$this->runSQL('posts.sql');
		$this->runSQL('submission_columns.sql');
		$this->runSQL('translations.sql');
		$this->runSQL('calculations.sql');
		$this->runSQL('directory.sql');
		$this->runSQL('directory_fields.sql');
		
		// #__rsform_forms updates
		$columns = $db->getTableColumns('#__rsform_forms');
		if (!isset($columns['UserEmailAttach'])) {
			$db->setQuery("ALTER TABLE #__rsform_forms ADD `UserEmailAttach` TINYINT NOT NULL AFTER `UserEmailMode`");
			$db->execute();
		}
		if (!isset($columns['UserEmailAttachFile'])) {
			$db->setQuery("ALTER TABLE #__rsform_forms ADD `UserEmailAttachFile` VARCHAR (255) NOT NULL AFTER `UserEmailAttach`");
			$db->execute();
		}
		if (!isset($columns['ScriptProcess2'])) {
			$db->setQuery("ALTER TABLE #__rsform_forms ADD `ScriptProcess2` TEXT NOT NULL AFTER `ScriptProcess`");
			$db->execute();
		}
		if (!isset($columns['UserEmailCC'])) {
			$db->setQuery("ALTER TABLE #__rsform_forms ADD `UserEmailCC` VARCHAR (255) NOT NULL AFTER `UserEmailTo`");
			$db->execute();
		}
		if (!isset($columns['UserEmailBCC'])) {
			$db->setQuery("ALTER TABLE #__rsform_forms ADD `UserEmailBCC` VARCHAR (255) NOT NULL AFTER `UserEmailCC`");
			$db->execute();
		}
		if (!isset($columns['UserEmailReplyTo'])) {
			$db->setQuery("ALTER TABLE #__rsform_forms ADD `UserEmailReplyTo` VARCHAR (255) NOT NULL AFTER `UserEmailBCC`");
			$db->execute();
		}
		if (!isset($columns['AdminEmailCC'])) {
			$db->setQuery("ALTER TABLE #__rsform_forms ADD `AdminEmailCC` VARCHAR (255) NOT NULL AFTER `AdminEmailTo`");
			$db->execute();
		}
		if (!isset($columns['AdminEmailBCC'])) {
			$db->setQuery("ALTER TABLE #__rsform_forms ADD `AdminEmailBCC` VARCHAR (255) NOT NULL AFTER `AdminEmailCC`"); 
			$db->execute();
		}
		if (!isset($columns['AdminEmailReplyTo'])) {
			$db->setQuery("ALTER TABLE #__rsform_forms ADD `AdminEmailReplyTo` VARCHAR (255) NOT NULL AFTER `AdminEmailBCC`");
			$db->execute();
		}
		if (!isset($columns['LoadFormLayoutFramework'])) {
			$db->setQuery("ALTER TABLE `#__rsform_forms` ADD `LoadFormLayoutFramework` TINYINT( 1 ) NOT NULL default '1' AFTER `FormLayoutName`");
			$db->execute();
		}
		if (!isset($columns['FormLayoutFlow'])) {
			$db->setQuery("ALTER TABLE `#__rsform_forms` ADD `FormLayoutFlow` TINYINT( 1 ) NOT NULL default '0' AFTER `FormLayoutAutogenerate`");
			$db->execute();
		}
		if (!isset($columns['MetaTitle'])) {
			$db->setQuery("ALTER TABLE `#__rsform_forms` ADD `MetaTitle` TINYINT( 1 ) NOT NULL");
			$db->execute();
			$db->setQuery("ALTER TABLE `#__rsform_forms` ADD `MetaDesc` TEXT NOT NULL");
			$db->execute();
			$db->setQuery("ALTER TABLE `#__rsform_forms` ADD `MetaKeywords` TEXT NOT NULL");
			$db->execute();
			$db->setQuery("ALTER TABLE `#__rsform_forms` ADD `Required` VARCHAR( 255 ) NOT NULL DEFAULT '(*)'");
			$db->execute();
			$db->setQuery("ALTER TABLE `#__rsform_forms` ADD `ErrorMessage` TEXT NOT NULL");
			$db->execute();
			
			$db->setQuery("SELECT FormId FROM #__rsform_forms WHERE FormId='1' AND FormName='RSformPro example' AND ErrorMessage=''");
			if ($db->loadResult())
			{
				$db->setQuery("UPDATE #__rsform_forms SET MetaTitle=0, MetaDesc='This is the meta description of your form. You can use it for SEO purposes.', MetaKeywords='rsform, contact, form, joomla', Required='(*)', ErrorMessage='<p class=\"formRed\">Please complete all required fields!</p>' WHERE FormId='1' LIMIT 1");
				$db->execute();
			}
		}
		if (!isset($columns['CSS'])) {
			$db->setQuery("ALTER TABLE `#__rsform_forms` ADD `CSS` TEXT NOT NULL AFTER `FormLayoutAutogenerate` ,".
						  " ADD `JS` TEXT NOT NULL AFTER `CSS` ,".
						  " ADD `ShowThankyou` TINYINT( 1 ) NOT NULL DEFAULT '1' AFTER `ReturnUrl` ,".
						  " ADD `UserEmailScript` TEXT NOT NULL AFTER `ScriptDisplay` ,".
						  " ADD `AdminEmailScript` TEXT NOT NULL AFTER `UserEmailScript` ,".
						  " ADD `MultipleSeparator` VARCHAR( 64 ) NOT NULL AFTER `ErrorMessage` ,".
						  " ADD `TextareaNewLines` TINYINT( 1 ) NOT NULL AFTER `MultipleSeparator`");
			$db->execute();
		}
		if (!isset($columns['CSSClass'])) {
			$db->setQuery("ALTER TABLE `#__rsform_forms` ADD `CSSClass` VARCHAR( 255 ) NOT NULL AFTER `TextareaNewLines` ,".
						  " ADD `CSSId` VARCHAR( 255 ) NOT NULL DEFAULT 'userForm' AFTER `CSSClass` ,".
						  " ADD `CSSName` VARCHAR( 255 ) NOT NULL AFTER `CSSId` ,".
						  " ADD `CSSAction` TEXT NOT NULL AFTER `CSSName` ,".
						  " ADD `CSSAdditionalAttributes` TEXT NOT NULL AFTER `CSSAction`,".
						  " ADD `AjaxValidation` TINYINT( 1 ) NOT NULL AFTER `CSSAdditionalAttributes`");
			$db->execute();
		}
		if (isset($columns['UserEmailConfirmation'])) {
			$db->setQuery("ALTER TABLE `#__rsform_forms` DROP `UserEmailConfirmation`");
			$db->execute();
		}
		if (!isset($columns['ShowContinue'])) {
			$db->setQuery("ALTER TABLE `#__rsform_forms` ADD `ShowContinue` TINYINT( 1 ) NOT NULL DEFAULT '1' AFTER `Thankyou`");
			$db->execute();
		}
		if (!isset($columns['ShowSystemMessage'])) {
			$db->setQuery("ALTER TABLE `#__rsform_forms` ADD `ShowSystemMessage` TINYINT( 1 ) NOT NULL DEFAULT '1' AFTER `ReturnUrl`");
			$db->execute();
		}
		if (!isset($columns['Keepdata'])) {
			$db->setQuery("ALTER TABLE `#__rsform_forms` ADD `Keepdata` TINYINT( 1 ) NOT NULL DEFAULT '1'");
			$db->execute();
			$db->setQuery("UPDATE `#__rsform_forms` SET `Keepdata` = 1");
			$db->execute();
		} else {
			$db->setQuery("ALTER TABLE `#__rsform_forms` CHANGE `Keepdata` `Keepdata` TINYINT( 1 ) NOT NULL DEFAULT '1'");
			$db->execute();
		}
		if (!isset($columns['KeepIP'])) {
			$db->setQuery("ALTER TABLE `#__rsform_forms` ADD `KeepIP` TINYINT( 1 ) NOT NULL DEFAULT '1' AFTER `Keepdata`");
			$db->execute();
		}
        if (!isset($columns['DeleteSubmissionsAfter'])) {
            $db->setQuery("ALTER TABLE `#__rsform_forms` ADD `DeleteSubmissionsAfter` INT( 11 ) NOT NULL DEFAULT '0' AFTER `KeepIP`");
            $db->execute();
        }
		if (!isset($columns['Backendmenu'])) {
			$db->setQuery("ALTER TABLE `#__rsform_forms` ADD `Backendmenu` TINYINT( 1 ) NOT NULL");
			$db->execute();
		}
		if (!isset($columns['ConfirmSubmission'])) {
			$db->setQuery("ALTER TABLE `#__rsform_forms` ADD `ConfirmSubmission` TINYINT( 1 ) NOT NULL DEFAULT '0'");
			$db->execute();
		}
		if (!isset($columns['AdditionalEmailsScript'])) {
			$db->setQuery("ALTER TABLE `#__rsform_forms` ADD `AdditionalEmailsScript` TEXT NOT NULL AFTER `AdminEmailScript`");
			$db->execute();
		}
		if (!isset($columns['ShowFormTitle'])) {
			$db->setQuery("ALTER TABLE `#__rsform_forms` ADD `ShowFormTitle` TINYINT( 1 ) NOT NULL DEFAULT '1' AFTER `FormTitle`");
			$db->execute();
		}
		if (!isset($columns['Access'])) {
			$db->setQuery("ALTER TABLE `#__rsform_forms` ADD `Access` VARCHAR( 5 ) NOT NULL");
			$db->execute();
		}
		if (!isset($columns['ScrollToThankYou'])) {
			$db->setQuery("ALTER TABLE #__rsform_forms ADD `ScrollToThankYou` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `ShowThankyou`");
			$db->execute();
		}
		if (!isset($columns['ThankYouMessagePopUp'])) {
			$db->setQuery("ALTER TABLE #__rsform_forms ADD `ThankYouMessagePopUp` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `ScrollToThankYou`");
			$db->execute();
		}
		if (!isset($columns['ScrollToError'])) {
			$db->setQuery("ALTER TABLE #__rsform_forms ADD `ScrollToError` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `AjaxValidation`");
			$db->execute();
		}
		if (!isset($columns['DisableSubmitButton'])) {
			$db->setQuery("ALTER TABLE #__rsform_forms ADD `DisableSubmitButton` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `FormLayoutAutogenerate`");
			$db->execute();
		}
		if (!isset($columns['RemoveCaptchaLogged'])) {
			$db->setQuery("ALTER TABLE #__rsform_forms ADD `RemoveCaptchaLogged` TINYINT( 1 ) NOT NULL DEFAULT '0' AFTER `DisableSubmitButton`");
			$db->execute();
		}
		if (!isset($columns['GridLayout'])) {
			$db->setQuery("ALTER TABLE #__rsform_forms ADD `GridLayout` MEDIUMTEXT NOT NULL AFTER `FormLayout`");
			$db->execute();
		}
        if (!isset($columns['DeletionEmailText']))
        {
            $db->setQuery("ALTER TABLE #__rsform_forms ADD `DeletionEmailText` text NOT NULL AFTER `AdminEmailMode`");
            $db->execute();
        }
        if (!isset($columns['DeletionEmailTo']))
        {
            $db->setQuery("ALTER TABLE #__rsform_forms ADD `DeletionEmailTo` text NOT NULL AFTER `DeletionEmailText`");
            $db->execute();
        }
        if (!isset($columns['DeletionEmailCC']))
        {
            $db->setQuery("ALTER TABLE #__rsform_forms ADD  `DeletionEmailCC` varchar(255) NOT NULL AFTER `DeletionEmailTo`");
            $db->execute();
        }
        if (!isset($columns['DeletionEmailBCC']))
        {
            $db->setQuery("ALTER TABLE #__rsform_forms ADD `DeletionEmailBCC` varchar(255) NOT NULL AFTER `DeletionEmailCC`");
            $db->execute();
        }
        if (!isset($columns['DeletionEmailFrom']))
        {
            $db->setQuery("ALTER TABLE #__rsform_forms ADD `DeletionEmailFrom` varchar(255) NOT NULL default '' AFTER `DeletionEmailBCC`");
            $db->execute();
        }
        if (!isset($columns['DeletionEmailReplyTo']))
        {
            $db->setQuery("ALTER TABLE #__rsform_forms ADD `DeletionEmailReplyTo` varchar(255) NOT NULL AFTER `DeletionEmailFrom`");
            $db->execute();
        }
        if (!isset($columns['DeletionEmailFromName']))
        {
            $db->setQuery("ALTER TABLE #__rsform_forms ADD `DeletionEmailFromName` varchar(255) NOT NULL default '' AFTER `DeletionEmailReplyTo`");
            $db->execute();
        }
        if (!isset($columns['DeletionEmailSubject']))
        {
            $db->setQuery("ALTER TABLE #__rsform_forms ADD `DeletionEmailSubject` varchar(255) NOT NULL default '' AFTER `DeletionEmailFromName`");
            $db->execute();
        }
        if (!isset($columns['DeletionEmailMode']))
        {
            $db->setQuery("ALTER TABLE #__rsform_forms ADD `DeletionEmailMode` tinyint(4) NOT NULL default '1' AFTER `DeletionEmailSubject`");
            $db->execute();
        }
        if (!isset($columns['ScriptBeforeDisplay'])) {
            $db->setQuery("ALTER TABLE #__rsform_forms ADD `ScriptBeforeDisplay` TEXT NOT NULL AFTER `ScriptProcess2`");
            $db->execute();
        }
        if (!isset($columns['ScriptBeforeValidation'])) {
            $db->setQuery("ALTER TABLE #__rsform_forms ADD `ScriptBeforeValidation` TEXT NOT NULL AFTER `ScriptBeforeDisplay`");
            $db->execute();
        }
		if ($columns['FormLayout'] == 'text') {
			$db->setQuery("ALTER TABLE `#__rsform_forms` CHANGE `FormLayout` `FormLayout` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
			$db->execute();
		}
		
		// #__rsform_emails updates
		$columns = $db->getTableColumns('#__rsform_emails', false);
		if (!isset($columns['type'])) {
			$db->setQuery("ALTER TABLE `#__rsform_emails` ADD `type` VARCHAR( 255 ) NOT NULL AFTER `formId`");
			$db->execute();
			$db->setQuery("UPDATE `#__rsform_emails` SET `type` = 'additional'");
			$db->execute();
		}
		
		// #__rsform_config updates
		$columns = $db->getTableColumns('#__rsform_config', false);
		if (isset($columns['ConfigId'])) {
			$db->setQuery("ALTER TABLE `#__rsform_config` DROP `ConfigId`");
			$db->execute();
		}
		if (!$columns['SettingName']->Key) {
			// remove duplicates
			$query = $db->getQuery(true);
			$query->select($db->qn('SettingName'))->from('#__rsform_config');
			$db->setQuery($query);
			$results = $db->loadColumn();
			
			$counts = array_count_values($results);
			foreach ($counts as $key => $num) {
				if ($num > 1) {
					$db->setQuery("DELETE FROM #__rsform_config WHERE ".$db->qn('SettingName').'='.$db->q($key)." LIMIT ".($num-1));
					$db->execute();
				}
			}
			
			$db->setQuery("ALTER TABLE `#__rsform_config` ADD PRIMARY KEY (`SettingName`)");
			$db->execute();
		}
		
		// #__rsform_submission_values updates
		$columns = $db->getTableColumns('#__rsform_submission_values', false);
		if ($columns['FormId']->Key != 'MUL') {
			$db->setQuery("ALTER TABLE #__rsform_submission_values ADD INDEX (`FormId`)"); 
			$db->execute();
		}
		if ($columns['SubmissionId']->Key != 'MUL') {
			$db->setQuery("ALTER TABLE #__rsform_submission_values ADD INDEX (`SubmissionId`)");
			$db->execute();
		}
		if (!isset($columns['FormId'])) {
			$db->setQuery("ALTER TABLE #__rsform_submission_values ADD `FormId` INT NOT NULL AFTER `SubmissionValueId`");
			$db->execute();
			$db->setQuery("UPDATE #__rsform_submission_values sv, #__rsform_submissions s SET sv.FormId=s.FormId WHERE sv.SubmissionId = s.SubmissionId");
			$db->execute();
		}
		
		// #__rsform_submissions updates
		$columns = $db->getTableColumns('#__rsform_submissions', false);
		if ($columns['FormId']->Key != 'MUL') {
			$db->setQuery("ALTER TABLE #__rsform_submissions ADD INDEX (`FormId`)");
			$db->execute();
		}
		if (!isset($columns['Lang'])) {
			$db->setQuery("ALTER TABLE `#__rsform_submissions` ADD `Lang` VARCHAR( 255 ) NOT NULL AFTER `UserId`");
			$db->execute();
		}
		if (!isset($columns['confirmed'])) {
			$db->setQuery("ALTER TABLE `#__rsform_submissions` ADD `confirmed` TINYINT( 1 ) NOT NULL");
			$db->execute();
		}
        if (!isset($columns['SubmissionHash'])) {
            $db->setQuery("ALTER TABLE `#__rsform_submissions` ADD `SubmissionHash` VARCHAR( 32 ) NOT NULL," .
                "ADD KEY `SubmissionId` (`SubmissionId`,`FormId`,`DateSubmitted`)," .
                "ADD KEY `SubmissionHash` (`SubmissionHash`)");
            $db->execute();
        }
		$columns = $db->getTableColumns('#__rsform_submissions', false);
		if ($columns['UserIp']->Type == 'varchar(15)') {
			$db->setQuery("ALTER TABLE `#__rsform_submissions` CHANGE `UserIp` `UserIp` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
			$db->execute();
		}
		// #__rsform_component_type_fields updates
		$query = $db->getQuery(true);
		$query->update('#__rsform_component_type_fields')
			  ->set($db->qn('FieldType').'='.$db->q('textarea'))
			  ->where($db->qn('FieldName').'='.$db->q('DEFAULTVALUE'))
			  ->where($db->qn('ComponentTypeId').'='.$db->q(1));
		$db->setQuery($query);
		$db->execute();

		$columns = $db->getTableColumns('#__rsform_component_type_fields', false);
		if (isset($columns['ComponentTypeFieldId'])) {
			$db->setQuery("ALTER TABLE `#__rsform_component_type_fields` DROP `ComponentTypeFieldId`");
			$db->execute();
		}
		if ($columns['ComponentTypeId']->Key != 'MUL') {
			$db->setQuery("ALTER TABLE #__rsform_component_type_fields ADD INDEX (`ComponentTypeId`)");
			$db->execute();
		}

		// add the VALIDATIONMULTIPLE to the textBox field
		$db->setQuery("SELECT COUNT(`FieldName`) FROM #__rsform_component_type_fields  WHERE `ComponentTypeId` = 1 AND `FieldName` = 'VALIDATIONMULTIPLE'");
		if (!$db->loadResult()) {
			$db->setQuery("INSERT INTO #__rsform_component_type_fields SET `ComponentTypeId` = 1, `FieldName` = 'VALIDATIONMULTIPLE' , `FieldType` = 'selectmultiple', `FieldValues` = '".$db->escape("//<code>\r\nreturn RSFormProHelper::getValidationRules(false, true);\r\n//</code>")."', `Ordering`= 6");
			$db->execute();
		}
		// add the VALIDATIONMULTIPLE to the textArea field
		$db->setQuery("SELECT COUNT(`FieldName`) FROM #__rsform_component_type_fields  WHERE `ComponentTypeId` = 2 AND `FieldName` = 'VALIDATIONMULTIPLE'");
		if (!$db->loadResult()) {
			$db->setQuery("INSERT INTO #__rsform_component_type_fields SET `ComponentTypeId` = 2, `FieldName` = 'VALIDATIONMULTIPLE' , `FieldType` = 'selectmultiple', `FieldValues` = '".$db->escape("//<code>\r\nreturn RSFormProHelper::getValidationRules(false, true);\r\n//</code>")."', `Ordering`= 6");
			$db->execute();
		}
		
		// add the VALIDATIONMULTIPLE to the password field
		$db->setQuery("SELECT COUNT(`FieldName`) FROM #__rsform_component_type_fields  WHERE `ComponentTypeId` = 14 AND `FieldName` = 'VALIDATIONMULTIPLE'");
		if (!$db->loadResult()) {
			$db->setQuery("INSERT INTO #__rsform_component_type_fields SET `ComponentTypeId` = 14, `FieldName` = 'VALIDATIONMULTIPLE' , `FieldType` = 'selectmultiple', `FieldValues` = '".$db->escape("//<code>\r\nreturn RSFormProHelper::getValidationRules(false, true);\r\n//</code>")."', `Ordering`= 9");
			$db->execute();
		}
		
		// rename old RSadapter function to new one
		$db->setQuery("UPDATE #__rsform_component_type_fields SET FieldValues='".$db->escape("//<code>\r\nreturn JPATH_SITE.'/components/com_rsform/uploads/';\r\n//</code>")."' WHERE FieldName='DESTINATION' AND ComponentTypeId=9 AND FieldValues LIKE '%RSadapter%'");
		$db->execute();
		// remove old "ATTACHUSEREMAIL" and "ATTACHADMINEMAIL" fields
		$db->setQuery("SELECT * FROM #__rsform_component_type_fields WHERE `ComponentTypeId` = 9 AND `FieldName`='ATTACHUSEREMAIL' OR `FieldName`='ATTACHADMINEMAIL'");
		if ($db->loadResult()) {
			$db->setQuery("DELETE FROM `#__rsform_component_type_fields` WHERE `ComponentTypeId` = 9 AND `FieldName` ='ATTACHUSEREMAIL'");
			$db->execute();
			$db->setQuery("DELETE FROM `#__rsform_component_type_fields` WHERE `ComponentTypeId` = 9 AND `FieldName` ='ATTACHADMINEMAIL'");
			$db->execute();
			
			// if we deleted the fields, then we need to migrate the old information
			$db->setQuery("SELECT `ComponentId` FROM `#__rsform_components` WHERE `ComponentTypeId` = 9 ");
			if ($uploadcomponents = $db->loadColumn()) {
				$db->setQuery("SELECT * FROM #__rsform_properties WHERE ComponentId IN (".implode(",", $uploadcomponents).") AND PropertyName IN ('ATTACHADMINEMAIL', 'ATTACHUSEREMAIL') AND PropertyValue='YES'");
				$properties = array();
				if ($tmp = $db->loadObject()) {
					if (!isset($properties[$tmp->ComponentId])) {
						$properties[$tmp->ComponentId] = array();
					}
					$properties[$tmp->ComponentId][$tmp->PropertyName] = 1;
				}
				
				foreach ($properties as $ComponentId => $property) {
					$updateemailattach = array();
					
					if (isset($property['ATTACHADMINEMAIL'])) {
						$updateemailattach[] = 'adminemail';
					}
					if (isset($property['ATTACHUSEREMAIL'])) {
						$updateemailattach[] = 'useremail';
					}
					
					if ($updateemailattach) {
						$db->setQuery("INSERT INTO #__rsform_properties SET ComponentId = '".$ComponentId."' , PropertyName = 'EMAILATTACH', PropertyValue = '".$db->escape(implode(",", $updateemailattach))."' ");
						$db->execute();
					}
				}
				
				// delete them
				$db->setQuery("DELETE FROM #__rsform_properties WHERE PropertyName IN ('ATTACHADMINEMAIL', 'ATTACHUSEREMAIL')");
				$db->execute();
			}
		}
		$db->setQuery("UPDATE `#__rsform_component_type_fields` SET `FieldType` = 'textarea' WHERE `ComponentTypeId` = 6 AND `FieldName` IN ('MINDATE', 'MAXDATE') AND `FieldType` = 'textbox'");
		$db->execute();
		
		$db->setQuery("UPDATE `#__rsform_component_type_fields` SET `FieldValues` = '//<code>\r\nreturn RSFormProHelper::getOtherCalendars(6);\r\n//</code>' WHERE `ComponentTypeId` = 6 AND `FieldName` = 'VALIDATIONCALENDAR'");
		$db->execute();
		
		// replace old ImageButton with Submits buttons fields
		$db->setQuery("SELECT `ComponentId` FROM `#__rsform_components` WHERE `ComponentTypeId` = 12 ");
		if ($imagebuttons = $db->loadColumn()) {
			$db->setQuery("SELECT `FieldName` FROM `#__rsform_component_type_fields` WHERE `ComponentTypeId` = 13 ");
			$submitButtonProperties = $db->loadColumn();
			
			$db->setQuery("SELECT * FROM #__rsform_properties WHERE ComponentId IN (".implode(",", $imagebuttons).")");
			if ($tmp = $db->loadObjectList()) {
				$newProperties = array();
				// handle common properties
				foreach ($tmp as $property) {
					if (!isset($newProperties[$property->ComponentId])) {
						$newProperties[$property->ComponentId] = array();
					}
					if (in_array($property->PropertyName, $submitButtonProperties)) {
						if ($property->PropertyName == 'ADDITIONALATTRIBUTES' && isset($newProperties[$property->ComponentId]['ADDITIONALATTRIBUTES'])) {
							$newProperties[$property->ComponentId]['ADDITIONALATTRIBUTES'] = $property->PropertyValue."\r\n".$newProperties[$property->ComponentId]['ADDITIONALATTRIBUTES'];
						} else {
							$newProperties[$property->ComponentId][$property->PropertyName] = $property->PropertyValue;
						}
					} else if ($property->PropertyName == 'IMAGEBUTTON' && !empty($property->PropertyValue)) {
						$additional = 'type="image"'."\r\n".'src="'.$property->PropertyValue.'"';
						if (isset($newProperties[$property->ComponentId]['ADDITIONALATTRIBUTES']) && !empty($newProperties[$property->ComponentId]['ADDITIONALATTRIBUTES'])) {
							$additional = $newProperties[$property->ComponentId]['ADDITIONALATTRIBUTES']."\r\n".$additional;
						}
						$newProperties[$property->ComponentId]['ADDITIONALATTRIBUTES'] = $additional;
					}
				}
				// add the submit button extra properties
				foreach ($newProperties as $ComponentId => $property) {
					foreach ($submitButtonProperties as $submitProperty) {
						$value = '';
						switch ($submitProperty) {
							case 'DISPLAYPROGRESS':
								$value = 'NO';
							break;
							case 'BUTTONTYPE':
								$value = 'TYPEINPUT';
							break;
							case 'DISPLAYPROGRESSMSG':
								$value = '<div>'."\r\n".' <p><em>Page <strong>{page}</strong> of {total}</em></p>'."\r\n".' <div class="rsformProgressContainer">'."\r\n".'  <div class="rsformProgressBar" style="width: {percent}%;"></div>'."\r\n".' </div>'."\r\n".'</div>';
							break;
						}
						
						if (!empty($value)) {
							$newProperties[$ComponentId][$submitProperty] = $value;
						}
					}
				}
				
				foreach ($newProperties as $ComponentId => $property) {
					// delete the old image button specific properties
					$db->setQuery("DELETE FROM `#__rsform_properties` WHERE `ComponentId` = '".$ComponentId."'");
					$db->execute();
					
					// add the new submit button properties
					foreach ($property as $propertyName => $propertyValue) {
						$db->setQuery("INSERT INTO #__rsform_properties SET ComponentId = '".$ComponentId."' , PropertyName = '".$db->escape($propertyName)."', PropertyValue = '".$db->escape($propertyValue)."'");
						$db->execute();
					}
				}
			}
			
			// change the ComponentTypeId from the image button to the submit one
			$db->setQuery("UPDATE `#__rsform_components` SET `ComponentTypeId` = 13 WHERE `ComponentTypeId` = 12");
			$db->execute();
			
			// delete the image button component type
			$db->setQuery("DELETE FROM #__rsform_component_types WHERE `ComponentTypeId` = 12");
			$db->execute();
		}
	
		
		// #__rsform_components updates
		$columns = $db->getTableColumns('#__rsform_components', false);
		if ($columns['FormId']->Key != 'MUL') {
			$db->setQuery("ALTER TABLE #__rsform_components ADD INDEX (`FormId`)");
			$db->execute();
		}
		if ($columns['ComponentTypeId']->Key != 'MUL') {
			$db->setQuery("ALTER TABLE #__rsform_components ADD INDEX (`ComponentTypeId`)");
			$db->execute();
		}
		
		// #__rsform_properties
		$columns = $db->getTableColumns('#__rsform_properties', false);
		if ($columns['ComponentId']->Key != 'MUL') {
			$db->setQuery("ALTER TABLE #__rsform_properties ADD INDEX (`ComponentId`)");
			$db->execute();
		}
		
		// #__rsform_mappings migration
		$columns = $db->getTableColumns('#__rsform_mappings');
		if (isset($columns['MappingTable'])) {
			$db->setQuery("SELECT * FROM #__rsform_mappings");
			$mappings = $db->loadObjectList();

			$mtables = array();
			if (!empty($mappings))
			{
				foreach ($mappings as $mapping)
				{		
					$db->setQuery("SELECT p.PropertyValue FROM #__rsform_properties p LEFT JOIN #__rsform_components c ON (p.ComponentId = c.ComponentId) WHERE c.ComponentId='".$mapping->ComponentId."' AND p.PropertyName='NAME' AND c.Published='1' ORDER BY c.Order");
					$component = $db->loadResult();
					
					$db->setQuery("SELECT FormId FROM #__rsform_components WHERE ComponentId = '".$mapping->ComponentId."'");
					$formId = $db->loadResult();
					
					if (!empty($component))
					{
						$object = new stdClass();
						$object->column = $mapping->MappingColumn;
						$object->component = '{'.$component.':value}';
						$mtables[$mapping->MappingTable][$formId][] = $object;
					}
				}
			}
			
			$db->setQuery("DROP TABLE `#__rsform_mappings`");
			$db->execute();
			
			$this->runSQL('mappings.sql');

			$data = array();
			if (!empty($mtables))
			{
				foreach ($mtables as $table => $details)
				{
					if (!empty($details))
					foreach ($details as $formId => $columns)
					{
						if (!empty($columns))
						foreach ($columns as $column)
						{
							$data[$column->column] = $column->component;
						}
						
						if (!empty($data))
						{
							$data = serialize($data);
							
							$db->setQuery("INSERT INTO `#__rsform_mappings` SET `formId` = '".$db->escape($formId)."', `connection` = 0, `port` = '3306', `method` = 0, `table` = '".$db->escape($table)."', `data` = '".$db->escape($data)."' ");
							$db->execute();
						}
						unset($data);
					}
				}
			}
		}
		
		if (!isset($columns['driver'])) {
			$db->setQuery('ALTER TABLE `#__rsform_mappings` ADD `driver` VARCHAR( 16 ) NOT NULL AFTER `host`');
			$db->execute();
			
			$query = $db->getQuery(true)
						->update($db->qn('#__rsform_mappings'))
						->set($db->qn('driver').'='.$db->q(JFactory::getConfig()->get('dbtype')))
						->where($db->qn('driver').'='.$db->q(''));
			$db->setQuery($query)->execute();
		}

		// Add filename field to #__rsform_directory table
		$columns = $db->getTableColumns('#__rsform_directory');
		if (!isset($columns['filename'])) {
			$db->setQuery("ALTER TABLE `#__rsform_directory` ADD `filename` VARCHAR(255) NOT NULL DEFAULT 'export.pdf' AFTER `formId`");
			$db->execute();
		}
		if (!isset($columns['EmailsCreatedScript'])) {
			$db->setQuery("ALTER TABLE `#__rsform_directory` ADD `EmailsCreatedScript` TEXT NOT NULL AFTER `EmailsScript`");
			$db->execute();
		}
        if (!isset($columns['DeletionGroups'])) {
            $db->setQuery("ALTER TABLE `#__rsform_directory` ADD `DeletionGroups` TEXT NOT NULL AFTER `groups`");
            $db->execute();
        }

		// #__rsform_posts updates
		$columns = $db->getTableColumns('#__rsform_posts');
		if (!isset($columns['fields'])) {
			$db->setQuery("ALTER TABLE `#__rsform_posts` ADD `fields` MEDIUMTEXT NOT NULL AFTER `method`");
			$db->execute();
		}
		
		// Update DESTINATION to relative path format.
		$query = $db->getQuery(true);
		$query->update($db->qn('#__rsform_component_type_fields'))
			  ->set($db->qn('FieldValues').' = '.$db->q("//<code>\r\nreturn 'components/com_rsform/uploads/';\r\n//</code>"))
			  ->where($db->qn('FieldName').' = '.$db->q('DESTINATION'))
			  ->where($db->qn('ComponentTypeId').' = '.$db->q(9))
			  ->where($db->qn('FieldValues').' = '.$db->q('%JPATH_SITE%'));
		$db->setQuery($query);
		$db->execute();
		
		// Change RSgetValidationRules() to the new format
		$query = $db->getQuery(true);
		$query->update($db->qn('#__rsform_component_type_fields'))
			  ->set($db->qn('FieldValues').' = '.$db->q("//<code>\r\nreturn RSFormProHelper::getValidationRules();\r\n//</code>"))
			  ->where($db->qn('FieldName').' = '.$db->q('VALIDATIONRULE'))
			  ->where($db->qn('FieldValues').' = '.$db->q('%RSgetValidationRules%'));
		$db->setQuery($query);
		$db->execute();

		if (!empty($this->migrateResponsiveLayoutFramework)) {
			$query = $db->getQuery(true);
			$query->update($db->qn('#__rsform_forms'))
				->set($db->qn('LoadFormLayoutFramework').'='.$db->q(1))
				->where($db->qn('FormLayoutName').'='.$db->q('responsive'));

			$db->setQuery($query)
				->execute();
		}
	}
	
	public function uninstall($parent) {
		$db = JFactory::getDbo();

		// Uninstall the Installer - RSForm! Pro Plugin
		$query = $db->getQuery(true);
		$query->select($db->qn('extension_id'))
			  ->from($db->qn('#__extensions'))
			  ->where($db->qn('element').'='.$db->q('rsform'))
			  ->where($db->qn('type').'='.$db->q('plugin'))
			  ->where($db->qn('folder').'='.$db->q('installer'));
		$db->setQuery($query);
		$plg_installer_id = (int) $db->loadResult();
		
		if (!empty($plg_installer_id)) {
			// Get a new installer
			$installer = new JInstaller();
			$installer->uninstall('plugin', $plg_installer_id, 1);
		}

		// Uninstall the System - RSForm! Pro Delete Submissions Plugin
        $query = $db->getQuery(true);
        $query->select($db->qn('extension_id'))
            ->from($db->qn('#__extensions'))
            ->where($db->qn('element').'='.$db->q('rsformdeletesubmissions'))
            ->where($db->qn('type').'='.$db->q('plugin'))
            ->where($db->qn('folder').'='.$db->q('system'));
        $db->setQuery($query);
        $plg_installer_id = (int) $db->loadResult();

        if (!empty($plg_installer_id)) {
            // Get a new installer
            $installer = new JInstaller();
            $installer->uninstall('plugin', $plg_installer_id, 1);
        }
	}
	
	public function preflight($type, $parent) {
		$app 		= JFactory::getApplication();
		$jversion 	= new JVersion();
		
		// Running Joomla! 2.5
		if (!$jversion->isCompatible('3.0.0'))
		{
			$app->enqueueMessage('Your version of Joomla! has reached end of life. RSForm! Pro can no longer be installed on older Joomla! versions. Please consider updating to the latest version of Joomla! if you\'d like to still use RSForm! Pro.', 'error');
			return false;
		}
		
		// Running 3.x
		if (!$jversion->isCompatible('3.7.0'))
		{
			$app->enqueueMessage('Please upgrade to at least Joomla! 3.7.0 before continuing!', 'error');
			return false;
		}

		// Flag to check if we should set 'Load Layout Framework' to 'Yes' for 'Responsive' layout forms now that front.css is missing responsive declarations
		if ($type == 'update' && !file_exists(JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/formlayouts/responsive.php'))
		{
			$this->migrateResponsiveLayoutFramework = true;
		}
		
		return true;
	}
	
	public function postflight($type, $parent) {
		if ($type == 'uninstall') {
			return true;
		}
		
		$this->source = $parent->getParent()->getPath('source');
		
		$db = JFactory::getDbo();
		
		$messages = array(
			'lib_tcpdf' 					=> false,
			'plg_installer' 				=> false,
			'plg_rsformdeletesubmissions' 	=> false,
			'plugins' 						=> array(),
			'modules' 						=> array()
		);
		// update plugins, modules as necessary
		
		// Check if we don't have TCPDF installed.
        $installer = new JInstaller();
		if (is_dir(JPATH_SITE.'/libraries/tcpdf')) {
			$messages['lib_tcpdf'] = 'skip';
		} elseif ($installer->install($this->source.'/other/lib_tcpdf')) {
			$messages['lib_tcpdf'] = true;
		}
		
		// Get a new installer
		$installer = new JInstaller();
		if ($installer->install($this->source.'/other/plg_installer')) {
			$query = $db->getQuery(true);
			$query->update('#__extensions')
				  ->set($db->qn('enabled').'='.$db->q(1))
				  ->where($db->qn('element').'='.$db->q('rsform'))
				  ->where($db->qn('type').'='.$db->q('plugin'))
				  ->where($db->qn('folder').'='.$db->q('installer'));
			$db->setQuery($query);
			$db->execute();
			
			$messages['plg_installer'] = true;
		}
		
		// Get a new installer
		$installer = new JInstaller();
		if ($installer->install($this->source.'/other/plg_rsformdeletesubmissions')) {
			$query = $db->getQuery(true);
			$query->update('#__extensions')
				  ->set($db->qn('enabled').'='.$db->q(1))
				  ->where($db->qn('element').'='.$db->q('rsformdeletesubmissions'))
				  ->where($db->qn('type').'='.$db->q('plugin'))
				  ->where($db->qn('folder').'='.$db->q('system'));
			$db->setQuery($query);
			$db->execute();
			
			$messages['plg_rsformdeletesubmissions'] = true;
		}
		
		$this->showInstallMessage($messages);
	}
	
	protected function runSQL($file) {
		$db = JFactory::getDbo();
		$driver = strtolower($db->name);
		if (strpos($driver, 'mysql') !== false) {
			$driver = 'mysql';
		} elseif ($driver == 'sqlsrv') {
			$driver = 'sqlazure';
		}
		
		$sqlfile = $this->source.'/admin/sql/'.$driver.'/'.$file;
		
		if (file_exists($sqlfile)) {
			$buffer = file_get_contents($sqlfile);
			if ($buffer !== false) {
				$queries = $db->splitSql($buffer);
				foreach ($queries as $query) {
					$query = trim($query);
					if ($query != '' && $query{0} != '#') {
						$db->setQuery($query);
						try
                        {
                            $db->execute();
                        }
                        catch (Exception $e)
                        {
                            JFactory::getApplication()->enqueueMessage($e->getMessage());
                        }
					}
				}
			}
		}
	}
	
	protected function escape($string) {
		return htmlentities($string, ENT_COMPAT, 'utf-8');
	}
	
	protected function showInstallMessage($messages=array()) {
		$app			= JFactory::getApplication();
		$isUpdateScreen = $app->input->get('option') == 'com_installer' && $app->input->get('view') == 'update';
?>
<style type="text/css">
.version-history {
	margin: 0 0 2em 0;
	padding: 0;
	list-style-type: none;
}
.version-history > li {
	margin: 0 0 0.5em 0;
	padding: 0 0 0 4em;
}
.version-new,
.version-fixed,
.version-upgraded {
	float: left;
	font-size: 0.8em;
	margin-left: -4.9em;
	width: 4.5em;
	color: white;
	text-align: center;
	font-weight: bold;
	text-transform: uppercase;
	-webkit-border-radius: 4px;
	-moz-border-radius: 4px;
	border-radius: 4px;
}

.version-new {
	background: #7dc35b;
}
.version-fixed {
	background: #e9a130;
}
.version-upgraded {
	background: #61b3de;
}

.install-ok {
	background: #7dc35b;
	color: #fff;
	padding: 3px;
}

.install-not-ok {
	background: #E9452F;
	color: #fff;
	padding: 3px;
}

.install-warning {
	background: #EFBB67;
	color: #fff;
	padding: 3px;
}
.big-warning {
	background: #FAF0DB;
	border: solid 1px #EBC46F;
	padding: 5px;
	font-size: 22px;
	line-height: 22px;
}

.big-warning b {
	color: red;
}

.red {
	color: red;
}
</style>
	<div class="row-fluid">
	<div class="span2">
		<img src="components/com_rsform/assets/images/box.png" alt="RSForm! Pro Box" />
	</div>
	<div class="span10">
		<p>TCP Library ...
			<?php if ($messages['lib_tcpdf'] === true) { ?>
			<b class="install-ok">Installed</b>
			<?php } elseif ($messages['lib_tcpdf'] === false) { ?>
			<b class="install-not-ok">Error installing! Please make sure /libraries/ and/or /libraries/tcpdf/ is writable!</b>
			<?php } else { ?>
			<b class="install-warning">Skipped installing TCPDF - it appears there's already a TCPDF library in place.</b>
			<?php } ?>
		</p>
		<p>Installer Plugin ...
			<?php if ($messages['plg_installer']) { ?>
			<b class="install-ok">Installed</b>
			<?php } else { ?>
			<b class="install-not-ok">Error installing!</b>
			<?php } ?>
		</p>
		<p>System - RSForm! Pro Delete Submissions Plugin ...
			<?php if ($messages['plg_rsformdeletesubmissions']) { ?>
			<b class="install-ok">Installed</b>
			<?php } else { ?>
			<b class="install-not-ok">Error installing!</b>
			<?php } ?>
		</p>
		<h2>Changelog v2.1.4</h2>
		<ul class="version-history">
            <li><span class="version-fixed">Fix</span> When setting 'Allow Only Images' to 'Yes' the generated placeholders pointed to a non-existent file.</li>
		</ul>
		<a class="btn btn-large btn-primary" href="index.php?option=com_rsform">Start using RSForm! Pro</a>
		<a class="btn" href="https://www.rsjoomla.com/support/documentation/rsform-pro.html" target="_blank">Read the RSForm! Pro User Guide</a>
		<a class="btn" href="https://www.rsjoomla.com/support.html" target="_blank">Get Support!</a>
	</div>
	</div>
		<?php
	}
}