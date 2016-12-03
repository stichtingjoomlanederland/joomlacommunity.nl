<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class RSFormControllerDirectory extends RSFormController
{
	public function __construct() {
		parent::__construct();
		
		$this->registerTask('apply', 'save');
	}

	public function manage() {
		JFactory::getApplication()->input->set('view', 'directory');
		JFactory::getApplication()->input->set('layout', 'default');
		
		parent::display();
	}
	
	public function edit() {
		JFactory::getApplication()->input->set('view', 	'directory');
		JFactory::getApplication()->input->set('layout', 	'edit');
		
		parent::display();
	}
	
	public function saveOrdering() {
		$db		= JFactory::getDBO();
		$cids	= JFactory::getApplication()->input->get('cid',array(),'array');
		$formId	= JFactory::getApplication()->input->getInt('formId',0);
		
		foreach ($cids as $key => $order) {
			$db->setQuery("SELECT componentId FROM #__rsform_directory_fields WHERE ComponentId='".$key."' AND `formId` = '".$formId."'");
			if (!$db->loadResult()) {
				$db->setQuery("INSERT INTO #__rsform_directory_fields SET ComponentId='".$key."', `formId` = '".$formId."', `ordering` = '".$order."' ");
				$db->execute();
			}
			
			$db->setQuery("UPDATE #__rsform_directory_fields SET `ordering`='".$order."' WHERE ComponentId='".$key."' AND `formId` = '".$formId."' ");
			$db->execute();
		}
		
		echo 'Ok';
		exit();
	}
	
	public function saveDetails() {
		$db		= JFactory::getDBO();
		$cids	= JFactory::getApplication()->input->get('cid',array(),'array');
		$formId	= JFactory::getApplication()->input->getInt('formId',0);
		
		foreach ($cids as $key => $val) {
			$db->setQuery("SELECT componentId FROM #__rsform_directory_fields WHERE ComponentId='".$key."' AND `formId` = '".$formId."'");
			if ($db->loadResult()) {
				$db->setQuery("UPDATE #__rsform_directory_fields SET `indetails`='".$val."' WHERE ComponentId='".$key."' AND `formId` = '".$formId."' ");
				$db->execute();
			} else {
				$db->setQuery("SELECT MAX(ordering) FROM #__rsform_directory_fields WHERE `formId` = '".$formId."'");
				$ordering = (int) $db->loadResult() + 1;
				
				$db->setQuery("INSERT INTO #__rsform_directory_fields SET `indetails`='".$val."', ComponentId='".$key."', `formId` = '".$formId."', `ordering` = '".$ordering."' ");
				$db->execute();
			}
		}
		
		echo 'Ok';
		exit();
	}
	
	public function save() {
		$data = JFactory::getApplication()->input->get('jform',array(),'array');
		
		$model = $this->getModel('directory');
		
		if (!$model->save($data)) {
			$this->setMessage($model->getError(),'error');
		} else {
			$this->setMessage(JText::_('RSFP_SUBM_DIR_SAVED'));
		}
		
		$task = $this->getTask();
		switch ($task) {
			case 'save':
				$link = 'index.php?option=com_rsform&view=directory';
			break;
			
			case 'apply':
				$tab	= JFactory::getApplication()->input->getInt('tab', 0);
				$link	= 'index.php?option=com_rsform&view=directory&layout=edit&formId='.$data['formId'].'&tab='.$tab;
			break;
		}
		
		$this->setRedirect($link);
	}
	
	public function cancel() {
		$this->setRedirect('index.php?option=com_rsform&view=directory');
	}
	
	public function cancelform() {
		$app 	= JFactory::getApplication();
		$jform	= $app->input->get('jform',array(),'array');
		$formId = $jform['formId'];
		$app->redirect('index.php?option=com_rsform&view=forms&layout=edit&formId='.$formId);
	}
	
	public function changeAutoGenerateLayout() {
		$formId 		= JFactory::getApplication()->input->getInt('formId');
		$ViewLayoutName = JRequest::getVar('ViewLayoutName');
		$db 			= JFactory::getDBO();
		
		$db->setQuery('SELECT COUNT('.$db->qn('formId').') FROM '.$db->qn('#__rsform_directory').' WHERE '.$db->qn('formId').' = '.(int) $formId.' ');
		if (!$db->loadResult()) {
			$db->setQuery('INSERT INTO '.$db->qn('#__rsform_directory').' SET '.$db->qn('formId').' = '.(int) $formId.' ');
			$db->execute();
		}
		
		$db->setQuery("UPDATE #__rsform_directory SET `ViewLayoutAutogenerate` = ABS(ViewLayoutAutogenerate-1), `ViewLayoutName`='".$db->escape($ViewLayoutName)."' WHERE `formId` = '".$formId."'");
		$db->execute();
		
		jexit();
	}
	
	public function saveName() {
		$formId = JFactory::getApplication()->input->getInt('formId');
		$name = JRequest::getVar('ViewLayoutName');
		$db = JFactory::getDBO();
		
		$db->setQuery('SELECT COUNT('.$db->qn('formId').') FROM '.$db->qn('#__rsform_directory').' WHERE '.$db->qn('formId').' = '.(int) $formId.' ');
		if (!$db->loadResult()) {
			$db->setQuery('INSERT INTO '.$db->qn('#__rsform_directory').' SET '.$db->qn('formId').' = '.(int) $formId.' ');
			$db->execute();
		}
		
		$db->setQuery("UPDATE #__rsform_directory SET ViewLayoutName='".$db->escape($name)."' WHERE formId='".$formId."'");
		$db->execute();
		
		jexit();
	}
	
	public function generate() {
		$db 	= JFactory::getDBO();
		$formId = JFactory::getApplication()->input->getInt('formId');
		$layout = JRequest::getVar('layoutName');
		
		$db->setQuery('SELECT COUNT('.$db->qn('formId').') FROM '.$db->qn('#__rsform_directory').' WHERE '.$db->qn('formId').' = '.(int) $formId.' ');
		if (!$db->loadResult()) {
			$db->setQuery('INSERT INTO '.$db->qn('#__rsform_directory').' SET '.$db->qn('formId').' = '.(int) $formId.' ');
			$db->execute();
		}
		
		$model = $this->getModel('directory');
		$model->getDirectory();
		$model->_directory->ViewLayoutName = JFactory::getApplication()->input->getCmd('layoutName');
		$model->autoGenerateLayout();
		
		echo $model->_directory->ViewLayout;
		jexit();
	}
	
	public function remove() {
		$model	= $this->getModel('directory');
		$cids	= JFactory::getApplication()->input->get('cid',array(),'array');
		
		$model->remove($cids);
		
		$this->setRedirect('index.php?option=com_rsform&view=directory');
	}
}