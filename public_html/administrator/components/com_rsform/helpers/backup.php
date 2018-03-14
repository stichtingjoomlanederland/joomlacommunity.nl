<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RSFormProBackup
{
	public static function create($formIds, $submissions, $filename)
	{
		$db 	 = JFactory::getDbo();
		$user 	 = JFactory::getUser();
		$app 	 = JFactory::getApplication();
		$config  = JFactory::getConfig();
		$version = new RSFormProVersion();
		
		$xml  = '<?xml version="1.0" encoding="utf-8"?>'."\n";
		$xml .= '<RSinstall type="rsformbackup">'."\n";
		$xml .= '<name>RSform backup</name>'."\n";
		$xml .= '<creationDate>'.date('Y-m-d').'</creationDate>'."\n";
		$xml .= '<author>'.$user->username.'</author>'."\n";
		$xml .= '<copyright>(C) '.date('Y').' '.JURI::root().'</copyright>'."\n";
		$xml .= '<authorEmail>'.$config->get('mailfrom').'</authorEmail>'."\n";
		$xml .= '<authorUrl>'.JURI::root().'</authorUrl>'."\n";
		$xml .= '<version>'.(string) $version.'</version>'."\n";
		$xml .= '<revision>'.$version->revision.'</revision>'."\n";
		$xml .= '<description>RSForm! Pro Backup</description>'."\n";
		$xml .= '<tasks>'."\n";
		
		// We need to see all available languages here, because the conditions are attached to languages
		$lang = JFactory::getLanguage();
		$languages = $lang->getKnownLanguages(JPATH_SITE);
		
		//LOAD FORMS
		$db->setQuery("SELECT * FROM #__rsform_forms WHERE FormId IN ('".implode("','",$formIds)."') ORDER BY FormId");
		$form_rows = $db->loadObjectList();
		foreach($form_rows as $form_row)
		{
			$xml .= RSFormProBackup::createXMLEntry('#__rsform_forms',$form_row,'FormId')."\n";
			$xml .= "\t".'<task type="eval"><![CDATA[$GLOBALS[\'q_FormId\'] = $db->insertid();]]></task>'."\n";
			
			$db->setQuery("SELECT * FROM #__rsform_translations WHERE `form_id`='".$form_row->FormId."' AND `reference`='forms'");
			$translations = $db->loadObjectList();
			foreach ($translations as $translation)
				$xml .= RSFormProBackup::createXMLEntry('#__rsform_translations',$translation,'id')."\n";
			
			$xml .= "\t".'<task type="eval"><![CDATA[$GLOBALS[\'ComponentIds\'] = array();]]></task>'."\n";
			
			 //LOAD COMPONENTS
			$db->setQuery("SELECT * FROM #__rsform_components WHERE FormId = '".$form_row->FormId."'");
			$component_rows = $db->loadObjectList();
			foreach ($component_rows as $component_row)
			{
				$xml .= RSFormProBackup::createXMLEntry('#__rsform_components',$component_row,'ComponentId','FormId')."\n";
				$xml .= "\t".'<task type="eval"><![CDATA[$GLOBALS[\'q_ComponentId\'] = $db->insertid();]]></task>'."\n";
				 
				//LOAD PROPERTIES
				$db->setQuery("SELECT * FROM #__rsform_properties WHERE ComponentId = '".$component_row->ComponentId."'");
				$property_rows = $db->loadObjectList();
				$ComponentName = '';
				foreach ($property_rows as $property_row)
				{
					if ($property_row->PropertyName == 'NAME')
						$ComponentName = $property_row->PropertyValue;
						
					$xml .= RSFormProBackup::createXMLEntry('#__rsform_properties',$property_row,'PropertyId','ComponentId')."\n";
				}
				
				if ($ComponentName)
					$xml .= "\t".'<task type="eval"><![CDATA[$GLOBALS[\'ComponentIds\'][\''.$ComponentName.'\'] = $GLOBALS[\'q_ComponentId\'];]]></task>'."\n";
				
				//LOAD TRANSLATIONS
				$db->setQuery("SELECT * FROM #__rsform_translations WHERE `form_id`='".$form_row->FormId."' AND `reference_id` LIKE '".$component_row->ComponentId.".%'");
				$translations = $db->loadObjectList();
				foreach ($translations as $translation)
					$xml .= RSFormProBackup::createXMLEntry('#__rsform_translations',$translation,'id')."\n";
			}
			
			if ($submissions)
			{
				//LOAD SUBMISSIONS
				$db->setQuery("SELECT * FROM #__rsform_submissions WHERE FormId = '".$form_row->FormId."'");
				$submission_rows = $db->loadObjectList();
				foreach ($submission_rows as $submission_row)
				{
					$xml .= RSFormProBackup::createXMLEntry('#__rsform_submissions',$submission_row,'SubmissionId','FormId')."\n";
					$xml .= "\t".'<task type="eval"><![CDATA[$GLOBALS[\'q_SubmissionId\'] = $db->insertid();]]></task>'."\n";
	 
					//LOAD SUBMISSION_VALUES
					$db->setQuery("SELECT * FROM #__rsform_submission_values WHERE SubmissionId = '".$submission_row->SubmissionId."'");
					$submission_value_rows = $db->loadObjectList();
					foreach($submission_value_rows as $submission_value_row)
						$xml .= RSFormProBackup::createXMLEntry('#__rsform_submission_values',$submission_value_row,'SubmissionValueId',array('SubmissionId', 'FormId'))."\n";
				}
			}
			
			//LOAD CONDITIONS
			foreach ($languages as $tag => $properties)
			{
				$conditions = RSFormProHelper::getConditions($form_row->FormId, $tag);
				if ($conditions)
					foreach ($conditions as $condition)
					{
						$xml .= RSFormProBackup::createXMLEntry('#__rsform_conditions', $condition, array('id'))."\n";
						$xml .= "\t".'<task type="eval"><![CDATA[$GLOBALS[\'q_ConditionId\'] = $db->insertid();]]></task>'."\n";
						
						if ($condition->details)
							foreach ($condition->details as $detail)
								$xml .= RSFormProBackup::createXMLEntry('#__rsform_condition_details', $detail, array('id'))."\n";
					}
			}
			
			//LOAD POSTS
			$db->setQuery("SELECT * FROM #__rsform_posts WHERE `form_id`='".$form_row->FormId."'");
			if ($post = $db->loadObject()) {
				$xml .= RSFormProBackup::createXMLEntry('#__rsform_posts',$post,null,null,true)."\n";
			}
			
			//LOAD CALCULATIONS
			if ($calculations = RSFormProHelper::getCalculations($form_row->FormId)) {
				foreach ($calculations as $calculation) {
					$xml .= RSFormProBackup::createXMLEntry('#__rsform_calculations',$calculation,null,null,true)."\n";
				}
			}
			
			//Trigger Event - onFormBackup
			$app->triggerEvent('rsfp_bk_onFormBackup', array(
				array(
					'formId' => $form_row->FormId,
					'xml' => &$xml
				)
			));
		}
		
		$xml .= '</tasks>'."\n";
		$xml .= '</RSinstall>';
		
		jimport('joomla.filesystem.file');
		return JFile::write($filename, $xml);
	}
	
	public static function createXMLEntry($tb_name, $row, $exclude = null, $dynamic = null, $ignore=false)
	{
		$fields = array();
		$values = array();
		
		if (!empty($exclude) && !is_array($exclude))
			$exclude = array($exclude);
		
		if (!empty($dynamic) && !is_array($dynamic))
			$dynamic = array($dynamic);

		$db = JFactory::getDBO();
		
		foreach ($row as $k => $v)
		{
			$fields[] = '`' . $k . '`';
			if ($exclude && in_array($k, $exclude))
				$v = '';
			
			if ($dynamic)
			{
				if (is_array($dynamic))
				{
					if (in_array($k, $dynamic))
						$v = "{".$dynamic[array_search($k, $dynamic)]."}";
				}
				else
					if($k == $dynamic) $v = "{".$dynamic."}";
			}
			
			// Conditions
			if ($tb_name == '#__rsform_conditions')
			{
				if ($k == 'ComponentName')
				{
					unset($fields[array_search('`ComponentName`', $fields)]);
					continue;
				}
				if ($k == 'details')
				{
					unset($fields[array_search('`details`', $fields)]);
					continue;
				}
				
				if ($k == 'component_id')
				{
					$v = '{ComponentIds['.$row->ComponentName.']}';
				}
				if ($k == 'form_id')
				{
					$v = '{FormId}';
				}
			}
			
			// Condition details
			if ($tb_name == '#__rsform_condition_details')
			{
				if ($k == 'ComponentName')
				{
					unset($fields[array_search('`ComponentName`', $fields)]);
					continue;
				}
				
				if ($k == 'condition_id')
				{
					$v = '{ConditionId}';
				}
				
				if ($k == 'component_id')
				{
					$v = '{ComponentIds['.$row->ComponentName.']}';
				}
			}
			
			// Translations
			if ($tb_name == '#__rsform_translations')
			{				
				if ($k == 'reference_id' && $row->reference == 'properties')
				{
					$v = end(explode('.', $v, 2));
					$v = "{ComponentId}.".$v;
				}
				if ($k == 'form_id')
				{
					$v = '{FormId}';
				}
			}
			
			// Post to another location
			if ($tb_name == '#__rsform_posts')
			{		
				if ($k == 'form_id')
				{
					$v = '{FormId}';
				}
			}
			
			// CALCULATIONS
			if ($tb_name == '#__rsform_calculations')
			{		
				if ($k == 'id')
				{
					$v = '';
				}
				
				if ($k == 'formId')
				{
					$v = '{FormId}';
				}
			}
			
			$values[] = "'" . $db->escape($v) . "'";
		}

		$xml = 'INSERT '.($ignore ? 'IGNORE' : '').' INTO `' . $tb_name . '` (' . implode(',',$fields) . ') VALUES (' . implode(',',$values) . ' )';
		$xml = str_replace(']]>', ']]]]><![CDATA[>', $xml);
		return "\t".'<task type="query"><![CDATA['.$xml.']]></task>';
	}
}