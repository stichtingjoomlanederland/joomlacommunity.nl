<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

use Joomla\Utilities\ArrayHelper;

class RseventsproControllerSubscriptions extends JControllerAdmin
{
	protected $text_prefix = 'COM_RSEVENTSPRO_SUBSCRIPTIONS';
	
	/**
	 * Constructor.
	 *
	 * @param	array	$config	An optional associative array of configuration settings.

	 * @return	rseventsproControllerSubscriptions
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array()) {
		parent::__construct($config);
		$this->registerTask('complete', 'status');
		$this->registerTask('incomplete', 'status');
		$this->registerTask('denied', 'status');
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
	public function getModel($name = 'Subscription', $prefix = 'RseventsproModel', $config = array('ignore_request' => true)) {
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	
	/**
	 * Method to toggle the subscription status.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public function status() {
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		$ids	= JFactory::getApplication()->input->get('cid', array(), 'array');
		$values	= array('complete' => 1, 'incomplete' => 0, 'denied' => 2);
		$task	= $this->getTask();
		$value	= ArrayHelper::getValue($values, $task, 0, 'int');
		
		if (empty($ids)) {
			$this->setMessage(JText::_('JERROR_NO_ITEMS_SELECTED'), 'error');
		} else {
			// Get the model.
			$model = $this->getModel();

			// Change status.
			if (!$model->status($ids, $value)) {
				$this->setMessage($model->getError(), 'error');
			}
		}

		$this->setRedirect('index.php?option=com_rseventspro&view=subscriptions');
	}
	
	/**
	 * Method to export subscriptions.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public function export() {
		$model = parent::getModel('Subscriptions', 'rseventsproModel');
		$model->export();
	}
}