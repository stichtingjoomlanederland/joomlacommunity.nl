<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussCronHookHoneypot extends EasyDiscuss
{
	/**
	 * Optimize images in attachments
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function execute()
	{
		// Regenerate the honeypot key @daily
		$last = $this->config->get('antispam_honeypot_lastupdate');
		$update = false;
		$now = time();

		// Seconds in a day
		$day = 86400;

		if ($last == '') {
			$update = true;
		}

		if ($last) {
			$diff = $now - $last;
			$update = $diff > $day;
		}

		// Update the honeypot key used
		if ($update) {
			$honeypot = ED::honeypot();
			$honeypot->updateKey();
		}
	}
}
