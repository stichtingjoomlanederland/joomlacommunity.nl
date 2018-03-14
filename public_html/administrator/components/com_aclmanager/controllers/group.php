<?php
/**
 * @package		ACL Manager for Joomla
 * @copyright 	Copyright (c) 2011-2016 Sander Potjer
 * @license 	GNU General Public License version 3 or later
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * ACL Manager Diagnostic Controller
 */
class AclmanagerControllerGroup extends JControllerLegacy
{
	/**
	 * Class Constructor
	 */
	function __construct($config = array())
	{
		parent::__construct($config);

		// Map the apply task to the save method.
		$this->registerTask('apply', 'save');
		$this->registerTask('clear', 'reset');
	}

	/**
	 * Save assets
	 */
	public function save()
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$app	= JFactory::getApplication();
		$model	= $this->getModel('group');
		$data	= JFactory::getApplication()->input->get('jform',array(), 'post', 'array');
		$id		= JFactory::getApplication()->input->get('id',0);

		// Attempt to save the configuration.
		$return = $model->save($data);

		// Check the return value.
		if ($return === false)
		{
			// Save failed, go back to the screen and display a notice.
			$message = JText::sprintf('JERROR_SAVE_FAILED', $model->getError());
			$this->setRedirect('index.php?option=com_aclmanager&view=group', $message, 'error');
			return false;
		}

		// Set the success message.
		$message = JText::_('COM_ACLMANAGER_PERMISSIONS_SAVED');

		// Set the redirect based on the task.
		switch ($this->getTask())
		{
			case 'apply':
				$this->setRedirect('index.php?option=com_aclmanager&view=group&id='.$id, $message);
				break;

			case 'save':
			default:
				$this->setRedirect('index.php?option=com_aclmanager', $message);
				break;
		}

		return true;
	}

	/**
	 * Reset permissions for group
	 */
	public function reset()
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$app		= JFactory::getApplication();
		$session 	= JFactory::getSession();
		$model		= $this->getModel('group');
		$data		= JFactory::getApplication()->input->get('jform',array(), 'post', 'array');
		$id			= JFactory::getApplication()->input->get('id',0);

		// Attempt to save the configuration.
		$return = $model->reset($data,$this->getTask());

		// Check the return value.
		if ($return === false)
		{
			// Save failed, go back to the screen and display a notice.
			$message = JText::sprintf('JERROR_SAVE_FAILED', $model->getError());
			$this->setRedirect('index.php?option=com_aclmanager&view=group', $message, 'error');
			return false;
		}

		// Set reset session
		switch ($this->getTask())
		{
			case 'reset':
				$session->set('aclmanager_reset', 'revert');
				break;

			case 'clear':
			default:
				$session->set('aclmanager_reset', 'clear');
				break;
		}

		// Set the redirect based on the task.
		if (version_compare(JVERSION, '3.0', 'ge')) {
			$this->setRedirect('index.php?option=com_aclmanager&view=group&id='.$id);
		} else {
			$this->setRedirect('index.php?option=com_aclmanager&view=group&id='.$id.'&layout=reset_close');
		}

		return true;
	}

}