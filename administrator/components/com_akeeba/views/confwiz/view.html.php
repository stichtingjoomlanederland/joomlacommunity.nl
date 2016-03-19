<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2009-2016 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 *
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;

/**
 * Akeeba Backup Configuration Wizard view class
 *
 */
class AkeebaViewConfwiz extends F0FViewHtml
{
	public function onAdd($tpl = null)
	{
		$aeconfig = Factory::getConfiguration();

		// Load the Configuration Wizard Javascript file
		AkeebaStrapper::addJSfile('media://com_akeeba/js/backup.js');
		AkeebaStrapper::addJSfile('media://com_akeeba/js/confwiz.js');

		$this->setLayout('wizard');

		return true;
	}
}