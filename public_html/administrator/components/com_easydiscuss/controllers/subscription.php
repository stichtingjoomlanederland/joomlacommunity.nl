<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

defined('_JEXEC') or die('Restricted access');

class EasyDiscussControllerSubscription extends EasyDiscussController
{
	public function __construct()
	{
		parent::__construct();

		$this->checkAccess('discuss.manage.subscriptions');
	}

	public function remove()
	{
		$subs = $this->input->get('cid', '', 'POST');
		$message = '';
		$type = 'success';

		if (count($subs) <= 0) {
			$message = JText::_('COM_EASYDISCUSS_INVALID_POST_ID');
			$type = 'error';
		} else {
			$table = ED::table('Subscribe');
			
			foreach($subs as $sub) {
				$table->load($sub);

				if (!$table->delete()) {
					ED::setMessage(JText::_('COM_EASYDISCUSS_REMOVING_SUBSCRIPTION_PLEASE_TRY_AGAIN_LATER'), 'error');
					return $this->app->redirect('index.php?option=com_easydiscuss&view=subscription');
				}
			}

			$message = JText::_('COM_EASYDISCUSS_SUBSCRIPTION_DELETED');
		}

		ED::setMessage($message, $type);

		return $this->app->redirect('index.php?option=com_easydiscuss&view=subscription');
	}

}
