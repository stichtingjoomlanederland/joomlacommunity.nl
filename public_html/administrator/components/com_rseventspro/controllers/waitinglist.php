<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

class RseventsproControllerWaitinglist extends JControllerAdmin
{
	public function __construct($config = array()) {
		parent::__construct($config);
		
		$this->registerTask('apply', 'save');
	}
	
	public function getModel($name = 'Waitinglist', $prefix = 'RseventsproModel', $config = array('ignore_request' => true)) {
		return parent::getModel($name, $prefix, $config);
	}
	
	public function save() {
		$model	= $this->getModel();
		$task	= $this->getTask();
		$app	= JFactory::getApplication();
		$jform	= $app->input->get('jform', array(), 'array');
		
		if ($model->save($jform)) {
			$this->setMessage(JText::_('JLIB_APPLICATION_SAVE_SUCCESS'));
		} else {
			$errors = $model->getErrors();

			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++) {
				if ($errors[$i] instanceof \Exception) {
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				} else {
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}
		}
		
		if ($task == 'apply') {
			return $this->setRedirect('index.php?option=com_rseventspro&view=waitinglist&layout=edit&id='.$jform['id']);
		} else {
			return $this->setRedirect('index.php?option=com_rseventspro&view=waitinglist&id='.$jform['ide']);
		}
	}
	
	public function cancel() {
		$jform = JFactory::getApplication()->input->get('jform', array(), 'array');
		
		return $this->setRedirect('index.php?option=com_rseventspro&view=waitinglist&id='.$jform['ide']);
	}
	
	public function delete() {
		$model	= $this->getModel();
		$app	= JFactory::getApplication();
		$pks	= $app->input->get('cid', array(), 'array');
		$id		= $app->input->getInt('id', 0);
		
		$this->setMessage(JText::plural('COM_RSEVENTSPRO_N_ITEMS_DELETED', count($pks)));
		
		$model->delete($pks);
		
		return $this->setRedirect('index.php?option=com_rseventspro&view=waitinglist&id='.$id);
	}
	
	public function approve() {
		$model	= $this->getModel();
		$app	= JFactory::getApplication();
		$pks	= $app->input->get('cid', array(), 'array');
		$id		= $app->input->getInt('id', 0);
		
		$model->approve($pks);
		
		$this->setMessage(JText::_('COM_RSEVENTSPRO_WAITINGLIST_EMAILS_SENT'));
		
		return $this->setRedirect('index.php?option=com_rseventspro&view=waitinglist&id='.$id);
	}
}