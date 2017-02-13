<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class RsformControllerEmails extends RsformController
{
	function __construct()
	{
		parent::__construct();
		
		$this->registerTask('apply', 'save');
		
		$this->_db = JFactory::getDbo();
	}
	
	function save()
	{
		$model	= $this->getModel('forms');
		$row	= $model->saveemail();
		$type	= JFactory::getApplication()->input->getCmd('type','additional');
		
		if ($this->getTask() == 'apply')
			return $this->setRedirect('index.php?option=com_rsform&task=forms.emails&type='.$type.'&cid='.$row->id.'&formId='.$row->formId.'&tmpl=component&update=1');
		
		$document = JFactory::getDocument();
		$document->addScriptDeclaration('window.opener.updateemails('.$row->formId.',\''.JFactory::getApplication()->input->getCmd('type','additional').'\');window.close();');
	}
	
	function remove()
	{
		$db		= JFactory::getDbo();
		$cid	= JFactory::getApplication()->input->getInt('cid');
		$formId = JFactory::getApplication()->input->getInt('formId');
		$type	= JFactory::getApplication()->input->getCmd('type','additional');
		$view	= $type == 'additional' ? 'forms' : 'directory';
		
		if ($cid)
		{
			$db->setQuery("DELETE FROM #__rsform_emails WHERE id = ".$cid." ");
			$db->execute();
			$db->setQuery("DELETE FROM #__rsform_translations WHERE reference_id IN ('".$cid.".fromname','".$cid.".subject','".$cid.".message') ");
			$db->execute();
		}
		
		JFactory::getApplication()->input->set('view', $view);
		JFactory::getApplication()->input->set('layout', 'edit_emails');
		JFactory::getApplication()->input->set('tmpl', 'component');
		JFactory::getApplication()->input->set('formId', $formId);
		JFactory::getApplication()->input->set('type', $type);
		
		parent::display();
		jexit();
	}
	
	function update()
	{
		$formId = JFactory::getApplication()->input->getInt('formId');
		$view	= JFactory::getApplication()->input->getCmd('type','additional') == 'additional' ? 'forms' : 'directory';
		
		JFactory::getApplication()->input->set('view', $view);
		JFactory::getApplication()->input->set('layout', 'edit_emails');
		JFactory::getApplication()->input->set('tmpl', 'component');
		JFactory::getApplication()->input->set('formId', $formId);
		
		parent::display();
		jexit();
	}
}