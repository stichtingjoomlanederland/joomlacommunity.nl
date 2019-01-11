<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

use Joomla\Utilities\ArrayHelper;

class RseventsproControllerRsvp extends JControllerAdmin
{
	protected $text_prefix = 'COM_RSEVENTSPRO_RSVP';
	
	/**
	 * Constructor.
	 *
	 * @param	array	$config	An optional associative array of configuration settings.

	 * @return	RseventsproControllerRsvp
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array()) {
		parent::__construct($config);
		$this->registerTask('going', 'status');
		$this->registerTask('interested', 'status');
		$this->registerTask('notgoing', 'status');
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
	public function getModel($name = 'Rsvp', $prefix = 'RseventsproModel', $config = array('ignore_request' => true)) {
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	
	/**
	 * Method to toggle the RSVP status.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public function status() {
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		$ids	= JFactory::getApplication()->input->get('cid', array(), 'array');
		$ide	= JFactory::getApplication()->input->getInt('ide', 0);
		$task	= $this->getTask();
		
		if (empty($ids)) {
			$this->setMessage(JText::_('JERROR_NO_ITEMS_SELECTED'), 'error');
		} else {
			// Get the model.
			$model = $this->getModel();

			// Change status.
			if (!$model->status($ids, $task)) {
				$this->setMessage($model->getError(), 'error');
			}
		}

		$this->setRedirect('index.php?option=com_rseventspro&view=rsvp&id='.$ide);
	}
	
	public function delete() {
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		$pks	= JFactory::getApplication()->input->get('cid', array(), 'array');
		$ide	= JFactory::getApplication()->input->getInt('ide', 0);
		
		if (empty($pks)) {
			$this->setMessage(JText::_('JERROR_NO_ITEMS_SELECTED'), 'error');
		} else {
			// Get the model.
			$model = $this->getModel();

			// Change status.
			if (!$model->delete($pks)) {
				$this->setMessage($model->getError(), 'error');
			} else {
				$this->setMessage(JText::_('COM_RSEVENTSPRO_RSVP_USERS_REMOVED'));
			}
		}

		$this->setRedirect('index.php?option=com_rseventspro&view=rsvp&id='.$ide);
	}
	
	public function export() {
		// Get the model.
		$model = $this->getModel();
		
		$model->export();
	}
}