<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class RsformControllerRichtext extends RsformController
{
	function __construct()
	{
		parent::__construct();
		
		$this->registerTask('apply', 'save');
		
		$this->_db = JFactory::getDbo();
	}
	
	function show()
	{
		JFactory::getApplication()->input->set('view', 	'forms');
		JFactory::getApplication()->input->set('layout', 	'richtext');
		
		parent::display();
	}
	
	function save()
	{
		$db 	= JFactory::getDbo();
		$formId = JFactory::getApplication()->input->getInt('formId');
		$opener = JFactory::getApplication()->input->getCmd('opener');
		$value  = JRequest::getVar($opener, '', 'post', 'none', JREQUEST_ALLOWRAW);
		$model  = $this->getModel('forms');

		$model->getForm();
		$lang = $model->getLang();
		if ($model->_form->Lang != $lang)
		{
			$model->saveFormRichtextTranslation($formId, $opener, $value, $lang);
		}
		else
		{
			$db->setQuery("UPDATE #__rsform_forms SET `".$opener."`='".$db->escape($value)."' WHERE FormId='".$formId."'");
			$db->execute();
		}

		/**
		 * Add feedback in the modal window
		 */
		JFactory::getApplication()->enqueueMessage(JText::_('RSFP_CHANGES_SAVED'));

		if ($this->getTask() == 'apply')
			return $this->setRedirect('index.php?option=com_rsform&task=richtext.show&opener='.$opener.'&formId='.$formId.'&tmpl=component');
		
		$document = JFactory::getDocument();
		$document->addScriptDeclaration("window.close();");
	}
	
	function preview()
	{
		$formId = JFactory::getApplication()->input->getInt('formId');
		$opener = JFactory::getApplication()->input->getCmd('opener');
		
		$db = JFactory::getDbo();
		$db->setQuery("SELECT `".$opener."` FROM #__rsform_forms WHERE FormId='".$formId."'");
		$value = $db->loadResult();
		
		$model = $this->getModel('forms');
		$model->getForm();
		$lang = $model->getLang();
		$translations = RSFormProHelper::getTranslations('forms', $formId, $lang);
		if ($translations && isset($translations[$opener]))
			$value = $translations[$opener];
		
		echo $value;
	}
}