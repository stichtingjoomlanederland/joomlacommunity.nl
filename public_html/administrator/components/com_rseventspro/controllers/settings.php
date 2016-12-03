<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class rseventsproControllerSettings extends JControllerLegacy
{	
	/**
	 * Constructor.
	 *
	 * @param	array	$config	An optional associative array of configuration settings.

	 * @return	rseventsproControllerSettings
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array()) {
		parent::__construct($config);
		
		$this->registerTask('apply', 'save');
	}
	
	/**
	 * Method to cancel.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public function cancel() {
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		$this->setRedirect(JRoute::_('index.php?option=com_rseventspro', false));
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
	public function getModel($name = 'Settings', $prefix = 'rseventsproModel', $config = array('ignore_request' => true)) {
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	
	/**
	 * Method to save configuration.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public function save() {
		$jinput	= JFactory::getApplication()->input;
		$data	= $jinput->get('jform', array(), 'array');
		$model	= $this->getModel();
		
		if (!$model->save($data)) {
			$this->setMessage($model->getError(), 'error');
		} else {
			$this->setMessage(JText::_('COM_RSEVENTSPRO_SETTINGS_SAVED'), 'message');
		}
		
		$task = $this->getTask();
		if ($task == 'save') {
			$this->setRedirect(JRoute::_('index.php?option=com_rseventspro', false));
		} elseif ($task == 'apply') {
			$this->setRedirect(JRoute::_('index.php?option=com_rseventspro&view=settings', false));
		}
	}
	
	
	/**
	 * Method to save Facebook token.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public function savetoken() {
		$model	= $this->getModel();
		
		if (!$model->savetoken()) {
			$this->setMessage($model->getError(), 'error');
			$this->setRedirect('index.php?option=com_rseventspro&view=settings');
		} else {
			echo '<script type="text/javascript">';
			echo 'window.opener.location = \''.addslashes(JRoute::_('index.php?option=com_rseventspro&view=settings&fb=1',false)).'\';';
			echo 'window.close();';
			echo '</script>';
			JFactory::getApplication()->close();
		}
	}
	
	/**
	 * Method to import Facebook events.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public function facebook() {
		$model	= $this->getModel();
		
		if (!$model->facebook()) {
			$this->setMessage($model->getError(), 'error');
			$this->setRedirect('index.php?option=com_rseventspro&view=settings');
		} else {
			$this->setRedirect('index.php?option=com_rseventspro&view=events');
		}
	}
	
	/**
	 * Method to import Google events.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public function google() {
		$model	= $this->getModel();
		
		if (!$model->google()) {
			$this->setMessage($model->getError(), 'error');
			$this->setRedirect('index.php?option=com_rseventspro&view=settings');
		} else {
			$events = $model->getState('settings.gcevents');
			$this->setMessage(JText::sprintf('COM_RSEVENTSPRO_IMPORTED_FROM_GOOGLE',$events), 'message');
			$this->setRedirect('index.php?option=com_rseventspro&view=events');
		}
	}
}