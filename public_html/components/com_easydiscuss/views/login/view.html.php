<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(DISCUSS_ROOT . '/views/views.php');

class EasyDiscussViewLogin extends EasyDiscussView
{
	/**
	 * Renders users listing
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function display($tmpl = null)
	{
		// If user is already logged in, just redirect them
		if (!$this->my->guest) {

			ED::setMessageQueue(JText::_('COM_EASYDISCUSS_YOU_ARE_ALREADY_LOGIN'), 'error');
			return $this->app->redirect(EDR::_('view=index'));
		}

		$return = EDR::getLoginRedirect();

        // Get any callback url and use it.
        $url = ED::getCallback('', false);

        if ($url) {
            $return = base64_encode($url);
        }

		$title = JText::_('COM_EASYDISCUSS_PLEASE_LOGIN_TITLE');
		$info = JText::_('COM_EASYDISCUSS_PLEASE_LOGIN_INFO');

        $usernameField = 'COM_EASYDISCUSS_USERNAME';
        
        if (ED::easysocial()->exists() && $this->config->get('main_login_provider') == 'easysocial') {
            $usernameField = ED::easysocial()->getUsernameField();
        }

        $this->set('title', $title);
        $this->set('info', $info);
        $this->set('usernameField', $usernameField);
        $this->set('return', $return);
		
		parent::display('login/form');
	}
}