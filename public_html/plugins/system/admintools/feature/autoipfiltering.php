<?php
/**
 * @package   AdminTools
 * @copyright Copyright (c)2010-2017 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

use FOF30\Date\Date;

defined('_JEXEC') or die;

class AtsystemFeatureAutoipfiltering extends AtsystemFeatureAbstract
{
	protected $loadOrder = 10;

	/**
	 * Blocks visitors coming from an automatically banned IP.
	 */
	public function onAfterInitialise()
	{
		$ip = AtsystemUtilFilter::getIp();

		if (!$this->isIPBlocked($ip))
		{
			return;
		}

		// Rescue URL check
		AtsystemUtilRescueurl::processRescueURL($this->exceptionsHandler);

		@ob_end_clean();
		header("HTTP/1.0 403 Forbidden");

		$spammerMessage = $this->cparams->getValue('spammermessage', '');

		if ($spammerMessage == 'You are a spammer, hacker or an otherwise bad person.')
		{
			$spammerMessage = 'You are a spammer, hacker or an otherwise bad person. [RESCUEINFO]';
		}

		$spammerMessage = str_replace('[IP]', $ip, $spammerMessage);
		$spammerMessage = AtsystemUtilRescueurl::processBlockMessage($spammerMessage);

		echo $spammerMessage;

		$this->app->close();
	}

	/**
	 * Is the IP blocked by an auto-blocking rule?
	 *
	 * @param   string  $ip  The IP address to check. Skip or pass empty string / null to use the current visitor's IP.
	 *
	 * @return  bool
	 */
	public function isIPBlocked($ip = null)
	{
		if (empty($ip))
		{
			// Get the visitor's IP address
			$ip = AtsystemUtilFilter::getIp();
		}

		// Let's get a list of blocked IP ranges
		$db  = $this->db;
		$sql = $db->getQuery(true)
		          ->select('*')
		          ->from($db->qn('#__admintools_ipautoban'))
		          ->where($db->qn('ip') . ' = ' . $db->q($ip));
		$db->setQuery($sql);

		try
		{
			$record = $db->loadObject();
		}
		catch (Exception $e)
		{
			$record = null;
		}

		if (empty($record))
		{
			return false;
		}

		// Is this record expired?
		JLoader::import('joomla.utilities.date');

		$jNow   = new Date();
		$jUntil = new Date($record->until);
		$now    = $jNow->toUnix();
		$until  = $jUntil->toUnix();

		if ($now > $until)
		{
			// Ban expired. Move the entry and allow the request to proceed.
			$history     = clone $record;
			$history->id = null;

			try
			{
				$db->insertObject('#__admintools_ipautobanhistory', $history, 'id');
			}
			catch (Exception $e)
			{
				// Oops...
			}

			$sql = $db->getQuery(true)
			          ->delete($db->qn('#__admintools_ipautoban'))
			          ->where($db->qn('ip') . ' = ' . $db->q($ip));
			$db->setQuery($sql);

			try
			{
				$db->execute();
			}
			catch (Exception $e)
			{
				// Oops...
			}

			return false;
		}

		// Move old entries - The fastest way is to create a INSERT with a SELECT statement
		$sql = 'INSERT INTO ' . $db->qn('#__admintools_ipautobanhistory') . ' (' . $db->qn('id') . ', ' . $db->qn('ip') . ', ' . $db->qn('reason') . ', ' . $db->qn('until') . ')' .
			' SELECT NULL, ' . $db->qn('ip') . ', ' . $db->qn('reason') . ', ' . $db->qn('until') .
			' FROM ' . $db->qn('#__admintools_ipautoban') .
			' WHERE ' . $db->qn('until') . ' < ' . $db->q($jNow->toSql());

		try
		{
			$r = $db->setQuery($sql)->execute();
		}
		catch (Exception $e)
		{
			// Oops...
		}

		$sql = $db->getQuery(true)
		          ->delete($db->qn('#__admintools_ipautoban'))
		          ->where($db->qn('until') . ' < ' . $db->q($jNow->toSql()));
		$db->setQuery($sql);

		try
		{
			$db->execute();
		}
		catch (Exception $e)
		{
			// Oops...
		}

		return true;
	}
} 