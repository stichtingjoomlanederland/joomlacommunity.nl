<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2009-2012 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');

class rseventsproControllerSubscription extends JControllerForm
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
		echo 'RS_DELIMITER0';
		echo JFactory::getUser(JFactory::getApplication()->input->getInt('id'))->get('email');
		echo 'RS_DELIMITER1';
		JFactory::getApplication()->close();
	}
}