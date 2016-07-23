<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussTnc extends EasyDiscuss
{
	public function hasAcceptedTnc($type = 'global', $userId = null)
	{
		static $acceptedTnc = array();

		if (!$this->config->get('main_tnc_remember')) {
			return false;
		}

        if (is_null($userId)) {
            $userId = $this->my->id;
        }

        $key = $userId . $type;

        if (!isset($acceptedTnc[$key])) {

			$model = ED::model('Tnc');

	        $sessionId = '';
	        $ipaddress = '';

	        if (empty($userId)) {
	            // mean this is a guest.
	            $sessionId = JFactory::getSession()->getId();
	            $ipaddress = @$_SERVER['REMOTE_ADDR'];
	        }

	        if ($this->config->get('main_tnc_remember_type') == 'global') {
	        	$type = 'global';
			}

			$acceptedTnc[$key] = false;

			if ($model->hasAcceptedTnc($userId, $type, $sessionId, $ipaddress)) {
				$acceptedTnc[$key] = true;
			}
		}

		return $acceptedTnc[$key];
	}

	public function storeTnc($type = null)
	{
		$settings = 'main_tnc_' . $type;

		if (!$this->config->get($settings) || !$this->config->get('main_tnc_remember')) {
			return;
		}

		// Do not proceed if user has accepted tnc before.
		if ($this->hasAcceptedTnc($type)) {
			return false;
		}

		// Get the user's session
		$session = JFactory::getSession();

		$table = ED::table('Tnc');

		$table->user_id = $this->my->id;
		$table->type = $type;
		$table->created = ED::date()->toSql();
		$table->state = 1;
		$table->ipaddress = @$_SERVER['REMOTE_ADDR'];
		$table->session_id = $session->getId();
		$table->store();
	}
}