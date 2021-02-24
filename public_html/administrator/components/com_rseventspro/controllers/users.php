<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted access');

class RseventsproControllerUsers extends JControllerAdmin
{
	public function getModel($name = 'User', $prefix = 'RseventsproModel', $config = array('ignore_request' => true)) {
		return parent::getModel($name, $prefix, $config);
	}
	
	public function deleteimage() {
		// Get the model
		$model = $this->getModel();
		
		// Delete image
		echo (int) $model->deleteimage();
		JFactory::getApplication()->close();
	}
	
	public function reset() {
		// Get the model
		$model = $this->getModel();
		
		$pks = JFactory::getApplication()->input->get('cid', array(), 'array');
		$pks = array_map('intval', $pks);
		
		$model->reset($pks);
		
		JFactory::getApplication()->enqueueMessage(JText::_('COM_RSEVENTSPRO_EVENTS_CREATED_RESET_OK'));
		
		$this->setRedirect(JRoute::_('index.php?option=com_rseventspro&view=users',false));
	}
}