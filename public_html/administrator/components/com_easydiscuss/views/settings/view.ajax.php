<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once DISCUSS_ADMIN_ROOT . '/views/views.php';

class EasyDiscussViewSettings extends EasyDiscussAdminView
{
	public function testParser()
	{
		$server = $this->input->get('server', '', 'default');
		$port = $this->input->get('port', '', 'default');
		$service = $this->input->get('service', '', 'default');
		$ssl = $this->input->get('ssl', true, 'bool');
		$user = $this->input->get('user', '', 'default');
		$pass = $this->input->get('pass', '', 'default');
		$validate = $this->input->get('validate', '');

		// Variable check
		if (!$server || !$port || !$user || !$pass) {
			return $this->ajax->reject(JText::_( 'COM_EASYDISCUSS_EMAIL_PARSER_PLEASE_COMPLETE_INFO'));
		}

		$result	= ED::Mailbox()->testConnect( $server , $port , $service , $ssl , 'INBOX' , $user , $pass  );
		return $this->ajax->resolve($result);
	}
}
