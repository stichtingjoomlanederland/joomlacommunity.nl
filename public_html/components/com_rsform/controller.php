<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RSFormController extends JControllerLegacy
{	
	public function captcha() {
		require_once JPATH_SITE.'/components/com_rsform/helpers/captcha.php';
		
		$componentId 	= JFactory::getApplication()->input->getInt('componentId');
		$captcha 		= new RSFormProCaptcha($componentId);

		JFactory::getSession()->set('com_rsform.captcha.captchaId'.$componentId, $captcha->getCaptcha());
		JFactory::getApplication()->close();
	}
	
	public function plugin() {
		JFactory::getApplication()->triggerEvent('rsfp_f_onSwitchTasks');
	}
	
	/* deprecated */
	public function showForm() {}
	
	public function submissionsViewFile() {
		$db 	= JFactory::getDbo();
		$secret = JFactory::getConfig()->get('secret');
		$hash 	= JFactory::getApplication()->input->getCmd('hash');
		
		// Load language file
		JFactory::getLanguage()->load('com_rsform', JPATH_ADMINISTRATOR);
		
		if (strlen($hash) != 32) {
			JError::raiseError(500, JText::_('RSFP_VIEW_FILE_NOT_FOUND'));
		}
		
		$db->setQuery("SELECT * FROM #__rsform_submission_values WHERE MD5(CONCAT(SubmissionId,'".$db->escape($secret)."',FieldName)) = '".$hash."'");
		if ($result = $db->loadObject()) {
			// Check if it's an upload field
			$db->setQuery("SELECT c.ComponentTypeId FROM #__rsform_properties p LEFT JOIN #__rsform_components c ON (p.ComponentId=c.ComponentId) WHERE p.PropertyName='NAME' AND p.PropertyValue='".$db->escape($result->FieldName)."' AND c.FormId='".(int) $result->FormId."'");
			$type = $db->loadResult();
			if ($type != 9) {
				JError::raiseError(500, JText::_('RSFP_VIEW_FILE_NOT_UPLOAD'));
			}
			
			if (file_exists($result->FieldValue)) {
				RSFormProHelper::readFile($result->FieldValue);
			}
		} else {
			JError::raiseError(500, JText::_('RSFP_VIEW_FILE_NOT_FOUND'));
		}
	}
	
	public function ajaxValidate()
	{
		$db = JFactory::getDbo();
		$form = JRequest::getVar('form');
		$formId = (int) @$form['formId'];
		
		$db->setQuery("SELECT ComponentId, ComponentTypeId FROM #__rsform_components WHERE `FormId`='".$formId."' AND `Published`='1' ORDER BY `Order`");
		$components = $db->loadObjectList();
		
		$page = JFactory::getApplication()->input->getInt('page');
		if ($page)
		{
			$current_page = 1;
			foreach ($components as $i => $component)
			{
				if ($current_page != $page)
					unset($components[$i]);
				if ($component->ComponentTypeId == RSFORM_FIELD_PAGEBREAK)
					$current_page++;
			}
		}
		
		$removeUploads   = array();
		$formComponents  = array();
		foreach ($components as $component)
		{
			$formComponents[] = $component->ComponentId;
			if ($component->ComponentTypeId == RSFORM_FIELD_FILEUPLOAD)
				$removeUploads[] = $component->ComponentId;
		}
		
		echo implode(',', $formComponents);
		
		echo "\n";
		
		$invalid = RSFormProHelper::validateForm($formId);
		
		//Trigger Event - onBeforeFormValidation
		$mainframe = JFactory::getApplication();
		$post = JRequest::getVar('form', array(), 'post', 'none', JREQUEST_ALLOWRAW);
		$mainframe->triggerEvent('rsfp_f_onBeforeFormValidation', array(array('invalid'=>&$invalid, 'formId' => $formId, 'post' => &$post)));
		
		if (count($invalid))
		{
			foreach ($invalid as $i => $componentId)
				if (in_array($componentId, $removeUploads))
					unset($invalid[$i]);
			
			$invalidComponents = array_intersect($formComponents, $invalid);
			
			echo implode(',', $invalidComponents);
		}
		
		if (isset($invalidComponents))
		{
			echo "\n";

			$pages = RSFormProHelper::componentExists($formId, RSFORM_FIELD_PAGEBREAK);
			$pages = count($pages);
			
			if ($pages && !$page)
			{
				$first = reset($invalidComponents);
				$current_page = 1;
				foreach ($components as $i => $component)
				{
					if ($component->ComponentId == $first)
						break;
					if ($component->ComponentTypeId == RSFORM_FIELD_PAGEBREAK)
						$current_page++;
				}
				echo $current_page;
				
				echo "\n";
				
				echo $pages;
			}
		}
		
		jexit();
	}
	
	public function confirm() {
		$db 	= JFactory::getDbo();
		$app	= JFactory::getApplication();
		$hash 	= $app->input->getCmd('hash');
		
		if (strlen($hash) == 32) {
			$db->setQuery("SELECT `SubmissionId` FROM `#__rsform_submissions` WHERE MD5(CONCAT(`SubmissionId`,`FormId`,`DateSubmitted`)) = '".$db->escape($hash)."' ");
			if ($SubmissionId = $db->loadResult()) {
				$db->setQuery("UPDATE `#__rsform_submissions` SET `confirmed` = 1 WHERE `SubmissionId` = '".(int) $SubmissionId."'");
				$db->execute();
				
				$app->triggerEvent('rsfp_f_onSubmissionConfirmation', array(array('SubmissionId' => $SubmissionId, 'hash' => $hash)));
				
				JError::raiseNotice(200, JText::_('RSFP_SUBMISSION_CONFIRMED'));
			}
		} else {
			JError::raiseWarning(500, JText::_('RSFP_SUBMISSION_CONFIRMED_ERROR'));
		}
	}
	
	public function display($cachable = false, $safeurlparams = false)
	{
		$app	= JFactory::getApplication();
		$vName	= $app->input->getCmd('view', '');
		
		jimport('joomla.filesystem.folder');
		
		$allowed = JFolder::folders(JPATH_COMPONENT.'/views');
		
		if (!in_array($vName, $allowed)) {
			$app->input->set('view', 'rsform');
		}

		parent::display($cachable, $safeurlparams);
	}
}