<?php
/**
 * @package   AkeebaBackup
 * @copyright Copyright (c)2006-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Backup\Admin\View\ViewTraits;

// Protect from unauthorized access
use Akeeba\Backup\Admin\Model\Profiles;
use Akeeba\Engine\Platform;

defined('_JEXEC') or die();

trait ProfileIdAndName
{
	/**
	 * Active profile ID
	 *
	 * @var  int
	 */
	public $profileid = 0;

	/**
	 * Active profile's description
	 *
	 * @var  string
	 */
	public $profilename = '';

	/**
	 * Find the currently active profile ID and name and put them in properties accessible by the view template
	 */
	protected function getProfileIdAndName()
	{
		/** @var Profiles $profilesModel */
		$profilesModel = $this->container->factory->model('Profiles')->tmpInstance();
		$profileId     = Platform::getInstance()->get_active_profile();

		try
		{
			$this->profilename = $profilesModel->findOrFail($profileId)->description;
			$this->profileid = $profileId;
		}
		catch (\Exception $e)
		{
			$this->container->session->set('profile', 1, 'akeeba');

			$this->profileid   = 1;
			$this->profilename = $profilesModel->findOrFail(1)->description;
		}
	}
}