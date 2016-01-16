<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');

require_once DISCUSS_ADMIN_ROOT . '/views.php';

class EasyDiscussViewSettings extends EasyDiscussAdminView
{
	public function testParser( $server , $port , $service , $ssl , $user , $pass , $validate )
	{
		$ajax	= new Disjax();

		// Variable check
		if($server=='' || $port=='' || $user=='' || $pass=='')
		{
			$result	= 'Credentials incomplete.';

			$ajax->assign( 'test-result' , JText::_( 'Please complete the information') );
			return $ajax->send();
		}

		require_once DISCUSS_CLASSES . '/mailbox.php';
		$result		= DiscussMailer::testConnect( $server , $port , $service , $ssl , 'INBOX' , $user , $pass  );
		$ajax->assign( 'test-result' , $result );

		return $ajax->send();
	}
}
