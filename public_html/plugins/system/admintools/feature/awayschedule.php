<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use FOF30\Date\Date;

defined('_JEXEC') or die;

class AtsystemFeatureAwayschedule extends AtsystemFeatureAbstract
{
	protected $loadOrder = 70;

	/**
	 * Is this feature enabled?
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		if (!$this->container->platform->isBackend())
		{
			return false;
		}

		if (!$this->cparams->getValue('awayschedule_from') || !$this->cparams->getValue('awayschedule_to'))
		{
			return false;
		}

		return true;
	}

	/**
	 * Checks if the secret word is set in the URL query, or redirects the user
	 * back to the home page.
	 */
	public function onAfterInitialise()
	{
		$timezone = $this->container->platform->getConfig()->get('offset', 'UTC');
		
		$now  = new Date('now', $timezone);
		$from = new Date($this->cparams->getValue('awayschedule_from'), $timezone);
		$to   = new Date($this->cparams->getValue('awayschedule_to'), $timezone);

		// Wait, FROM is later than TO? This means that the user set an interval like this: 17:30 - 11:00
		// Let's move the FROM constrain one day back
		if($from > $to)
		{
			$from = $from->modify('-1 day');
		}

		// Login attempt, while we set the away schedule, let's ban the user
		if ($now > $from && $now < $to)
		{
			$this->redirectAdminToHome();
		}
	}
}
