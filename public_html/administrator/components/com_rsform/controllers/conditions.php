<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class RsformControllerConditions extends RsformController
{
	function __construct()
	{
		parent::__construct();
		
		$this->registerTask('apply', 'save');
	}
	
	function save()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit('Invalid Token');

		$model 	= $this->getModel('conditions');
		$task 	= $this->getTask();
		$formId = $model->getFormId();
		
		// Save
		$cid = $model->save();
		
		$link = $cid ? 'index.php?option=com_rsform&view=conditions&layout=edit&cid='.$cid.'&formId='.$formId.'&tmpl=component' : 'index.php?option=com_rsform&view=conditions&layout=edit&formId='.$formId.'&tmpl=component';
		$msg  = $cid ? JText::_('RSFP_CONDITION_SAVED') : JText::_('RSFP_CONDITION_ERROR');
		
		if ($task == 'save')
			$link .= '&close=1';
		
		$this->setRedirect($link, $msg);
	}
	
	function remove()
	{
		$model  = $this->getModel('conditions');
		$formId = $model->getFormId();
		
		$model->remove();
		
		JFactory::getApplication()->input->set('view', 'forms');
		JFactory::getApplication()->input->set('layout', 'edit_conditions');
		JFactory::getApplication()->input->set('tmpl', 'component');
		JFactory::getApplication()->input->set('formId', $formId);
		
		parent::display();
		jexit();
	}
	
	function showConditions()
	{
		$model  = $this->getModel('conditions');
		$formId = $model->getFormId();
		
		JFactory::getApplication()->input->set('view', 'forms');
		JFactory::getApplication()->input->set('layout', 'edit_conditions');
		JFactory::getApplication()->input->set('tmpl', 'component');
		JFactory::getApplication()->input->set('formId', $formId);
		
		parent::display();
		exit();
	}
}