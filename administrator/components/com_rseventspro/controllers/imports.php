<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class rseventsproControllerImports extends JControllerLegacy
{
	public function __construct() {
		parent::__construct();
	}
	
	public function rsevents() {
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

		// Get the model
		$model = $this->getModel('imports');
		
		// Save
		if ($count = $model->rsevents())
			$this->setMessage(JText::plural('COM_RSEVENTSPRO_IMPORTED_EVENTS', $count));
		else
			$this->setMessage($model->getError(),'error');
		
		$this->setRedirect('index.php?option=com_rseventspro&view=imports');
	}
	
	public function jevents() {
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

		// Get the model
		$model = $this->getModel('imports');
		
		// Save
		if ($count = $model->jevents())
			$this->setMessage(JText::plural('COM_RSEVENTSPRO_IMPORTED_EVENTS', $count));
		else
			$this->setMessage($model->getError(),'error');
		
		$this->setRedirect('index.php?option=com_rseventspro&view=imports');
	}
	
	public function jcalpro() {
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

		// Get the model
		$model = $this->getModel('imports');
		
		// Save
		if ($count = $model->jcalpro())
			$this->setMessage(JText::plural('COM_RSEVENTSPRO_IMPORTED_EVENTS', $count));
		else
			$this->setMessage($model->getError(),'error');
		
		$this->setRedirect('index.php?option=com_rseventspro&view=imports');
	}
	
	public function eventlistbeta() {
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

		// Get the model
		$model = $this->getModel('imports');
		
		// Save
		if ($count = $model->eventlistbeta())
			$this->setMessage(JText::plural('COM_RSEVENTSPRO_IMPORTED_EVENTS', $count));
		else
			$this->setMessage($model->getError(),'error');
		
		$this->setRedirect('index.php?option=com_rseventspro&view=imports');
	}
	
	public function eventlist() {
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

		// Get the model
		$model = $this->getModel('imports');
		
		// Save
		if ($count = $model->eventlist())
			$this->setMessage(JText::plural('COM_RSEVENTSPRO_IMPORTED_EVENTS', $count));
		else
			$this->setMessage($model->getError(),'error');
		
		$this->setRedirect('index.php?option=com_rseventspro&view=imports');
	}
	
	public function ohanah() {
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

		// Get the model
		$model = $this->getModel('imports');
		
		// Save
		if ($count = $model->ohanah())
			$this->setMessage(JText::plural('COM_RSEVENTSPRO_IMPORTED_EVENTS', $count));
		else
			$this->setMessage($model->getError(),'error');
		
		$this->setRedirect('index.php?option=com_rseventspro&view=imports');
	}
	
	public function csv() {
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

		// Get the model
		$model = $this->getModel('imports');
		
		if ($count = $model->csv())
			$this->setMessage(JText::plural('COM_RSEVENTSPRO_IMPORTED_EVENTS', $count));
		else
			$this->setMessage($model->getError(),'error');
		
		$this->setRedirect('index.php?option=com_rseventspro&view=imports');
	}
}