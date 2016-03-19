<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class rseventsproControllerEvents extends JControllerAdmin
{
	protected $text_prefix = 'COM_RSEVENTSPRO_EVENTS';
	
	/**
	 * Constructor.
	 *
	 * @param	array	$config	An optional associative array of configuration settings.

	 * @return	rseventsproControllerGroups
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array()) {
		parent::__construct($config);
		
		$this->registerTask('unfeatured',	'featured');
	}
	
	/**
	 * Proxy for getModel.
	 *
	 * @param	string	$name	The name of the model.
	 * @param	string	$prefix	The prefix for the PHP class name.
	 *
	 * @return	JModel
	 * @since	1.6
	 */
	public function getModel($name = 'Event', $prefix = 'rseventsproModel', $config = array('ignore_request' => true)) {
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	
	/**
	 * Method to export events to iCal format.
	 *
	 * @return	.ics file
	 */
	public function exportical() {
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		// Get the selected items
		$pks = JFactory::getApplication()->input->get('cid', array(0), 'array');
		
		$model = $this->getModel();
		
		// Force array elements to be integers
		JArrayHelper::toInteger($pks);
		
		// Export events
		if ($model->exportical($pks)) {
			JFactory::getApplication()->close();
		} else {
			$this->setMessage($model->getError());
			$this->setRedirect('index.php?option=com_rseventspro&view=events');
		}
	}
	
	/**
	 * Method to export events to CSV format.
	 *
	 * @return	.csv file
	 */
	public function exportcsv() {
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		// Get the selected items
		$pks = JFactory::getApplication()->input->get('cid', array(0), 'array');
		
		$model = $this->getModel();
		
		// Force array elements to be integers
		JArrayHelper::toInteger($pks);
		
		// Export events
		if (!$model->exportcsv($pks)) {
			$this->setMessage($model->getError());
			$this->setRedirect('index.php?option=com_rseventspro&view=events');
		}
	}
	
	/**
	 * Method to clear event rating.
	 *
	 * @return	void
	 */
	public function rating() {
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		// Get the selected items
		$pks = JFactory::getApplication()->input->get('cid', array(0), 'array');
		
		$model = $this->getModel();
		
		// Force array elements to be integers
		JArrayHelper::toInteger($pks);
		
		// Export events
		if (!$model->rating($pks)) {
			$this->setMessage($model->getError());
		}
		
		$this->setRedirect('index.php?option=com_rseventspro&view=events');
	}
	
	/**
	 * Method to copy events.
	 *
	 * @return	void
	 */
	public function copy() {
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		// Get the selected items
		$pks = JFactory::getApplication()->input->get('cid', array(0), 'array');
		
		$model = $this->getModel();
		
		// Force array elements to be integers
		JArrayHelper::toInteger($pks);
		
		// Copy events
		if (!$model->copy($pks)) {
			$this->setMessage($model->getError());
		} else {
			$this->setMessage(JText::_('COM_RSEVENTSPRO_EVENTS_COPIED'));
		}
		
		$this->setRedirect('index.php?option=com_rseventspro&view=events');
	}
	
	/**
	 * Method to copy events.
	 *
	 * @return	void
	 */
	public function deletereports() {
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		// Get the selected items
		$pks = JFactory::getApplication()->input->get('cid', array(0), 'array');
		
		$model = $this->getModel();
		
		// Force array elements to be integers
		JArrayHelper::toInteger($pks);
		
		// Delete reports
		$model->deletereports($pks);
		
		$this->setRedirect('index.php?option=com_rseventspro&view=events&layout=report&id='.JFactory::getApplication()->input->getInt('ide', 0), JText::_('COM_RSEVENTSPRO_REPORTS_DELETED'));
	}
	
	/**
	 * Method to toggle the featured setting of a list of events.
	 *
	 * @return  void
	 * @since   1.6
	 */
	public function featured() {
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$ids    = JFactory::getApplication()->input->get('cid', array(), 'array');
		$values = array('featured' => 1, 'unfeatured' => 0);
		$task   = $this->getTask();
		$value  = JArrayHelper::getValue($values, $task, 0, 'int');

		if (empty($ids)) {
			JError::raiseWarning(500, JText::_('JERROR_NO_ITEMS_SELECTED'));
		} else {
			// Get the model.
			$model = $this->getModel();

			// Publish the items.
			if (!$model->featured($ids, $value)) {
				JError::raiseWarning(500, $model->getError());
			}
		}

		$this->setRedirect('index.php?option=com_rseventspro&view=events');
	}
	
	/**
	 * Method to run batch operations.
	 *
	 * @param   object  $model  The model.
	 * @return  boolean   True if successful, false otherwise and internal error is set.
	 * @since   1.6
	 */
	public function batch() {
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Set the model
		$model	= $this->getModel();
		$pks    = JFactory::getApplication()->input->get('cid', array(), 'array');
		
		if (!$model->batchProcess($pks)) {
			JError::raiseWarning(500, $model->getError());
		} else {
			JFactory::getApplication()->enqueueMessage(JText::_('COM_RSEVENTSPRO_BATCH_COMPLETED'));
		}
		
		// Preset the redirect
		$this->setRedirect('index.php?option=com_rseventspro&view=events');
	}
	
	/**
	 * Method to sync event dates.
	 */
	public function sync() {
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Set the model
		$model	= $this->getModel();
		
		if (!$model->sync()) {
			JError::raiseWarning(500, $model->getError());
		} else {
			JFactory::getApplication()->enqueueMessage(JText::_('COM_RSEVENTSPRO_SYNC_COMPLETED'));
		}
		
		// Preset the redirect
		$this->setRedirect('index.php?option=com_rseventspro&view=events');
	}
}