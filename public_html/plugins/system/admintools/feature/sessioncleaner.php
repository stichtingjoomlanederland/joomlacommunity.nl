<?php
/**
 * @package   AdminTools
 * @copyright Copyright (c)2010-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

class AtsystemFeatureSessioncleaner extends AtsystemFeatureAbstract
{
	protected $loadOrder = 610;

	/**
	 * Is this feature enabled?
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		return ($this->params->get('sescleaner', 0) == 1);
	}

	/**
	 * Run the session cleaner (garbage collector) on a schedule
	 */
	public function onAfterInitialise()
	{
		$minutes = (int)$this->params->get('ses_freq', 0);

		if ($minutes <= 0)
		{
			return;
		}

		$lastJob = $this->getTimestamp('session_clean');
		$nextJob = $lastJob + $minutes * 60;

		JLoader::import('joomla.utilities.date');
		$now = new JDate();

		if ($now->toUnix() >= $nextJob)
		{
			$this->setTimestamp('session_clean');
			$this->purgeSession();
		}
	}

	/**
	 * Purges expired sessions
	 */
	private function purgeSession()
	{
		JLoader::import('joomla.session.session');

		$options = array();

		$conf = JFactory::getConfig();

		$handler = $conf->get('session_handler', 'none');

		// config time is in minutes
		$options['expire'] = ($conf->get('lifetime')) ? $conf->get('lifetime') * 60 : 900;

		$storage = JSessionStorage::getInstance($handler, $options);
		$storage->gc($options['expire']);
	}
}