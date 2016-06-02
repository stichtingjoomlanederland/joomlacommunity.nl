<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2009-2016 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @since 1.3
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

use Akeeba\Engine\Platform;

/**
 * Akeeba Backup Configuration view class
 *
 */
class AkeebaViewSchedule extends F0FViewHtml
{
	public function onAdd($tpl = null)
	{
		// Get profile ID
		$profileid = Platform::getInstance()->get_active_profile();
		$this->profileid = $profileid;

		// Get profile name
		$profileName = F0FModel::getTmpInstance('Profiles','AkeebaModel')
			->setId($profileid)
			->getItem()
			->description;
		$this->profilename = $this->escape($profileName);

		// Get the CRON paths
		$this->croninfo  = $this->getModel()->getPaths();
        $this->checkinfo = $this->getModel()->getCheckPaths();
	}
}