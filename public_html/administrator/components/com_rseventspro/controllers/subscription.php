<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

class RseventsproControllerSubscription extends JControllerForm
{
	/**
	 * Class constructor.
	 *
	 * @param   array  $config  A named array of configuration variables.
	 *
	 * @since	1.6
	 */
	public function __construct() {
		parent::__construct();
	}
	
	/*
	 *	Method to send the activation email.
	 */
	public function activation() {
		$id = JFactory::getApplication()->input->getInt('id');
		
		// Send activation email
		rseventsproHelper::confirm($id);
		
		// Redirect
		$this->setRedirect('index.php?option=com_rseventspro&task=subscription.edit&id='.$id, JText::_('COM_RSEVENTSPRO_ACTIVATION_EMAIL_SENT'));
	}
	
	/*
	 *	Method to get user email address.
	 */
	public function email() {
		$id		= JFactory::getApplication()->input->getInt('id');
		$user	= JFactory::getUser($id);
		
		echo json_encode(array('id' => $user->get('id'), 'name' => $user->get('name'), 'username' => $user->get('username'), 'email' => $user->get('email')));
		JFactory::getApplication()->close();
	}
	
	/*
	 *	Method to confirm a ticket
	 */
	public function confirm() {
		// Get the model
		$model = $this->getModel();
		// Get the subscription id
		$id = JFactory::getApplication()->input->getInt('id',0);
		// Get the ticket code
		$code = JFactory::getApplication()->input->getString('code');
		
		echo $model->confirm($id, $code);
		
		JFactory::getApplication()->close();
	}
}