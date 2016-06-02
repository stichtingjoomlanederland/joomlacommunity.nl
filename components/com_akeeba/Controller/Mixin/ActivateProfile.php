<?php
/**
 * @package   AkeebaBackup
 * @copyright Copyright (c)2006-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Backup\Site\Controller\Mixin;

// Protect from unauthorized access
use Akeeba\Engine\Platform;

defined('_JEXEC') or die();

/**
 * Provides the method to set the current backup profile from the request variables
 */
trait ActivateProfile
{
	/**
	 * Set the active profile from the input parameters
	 */
	protected function setProfile()
	{
		$profile = $this->input->get('profile', 1, 'int');
		$profile = max(1, $profile);

		$this->container->session->set('profile', $profile, 'akeeba');

		Platform::getInstance()->load_configuration($profile);
	}

}