<?php
/**
 * @package   AdminTools
 * @copyright Copyright (c)2010-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

class AtsystemFeatureMuashield extends AtsystemFeatureAbstract
{
	protected $loadOrder = 330;

	/**
	 * Is this feature enabled?
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		if (!$this->helper->isFrontend())
		{
			return false;
		}

		if ($this->skipFiltering)
		{
			return false;
		}

		return ($this->cparams->getValue('muashield', 0) == 1);
	}

	/**
	 * Protects against a malicious User Agent string
	 */
	public function onAfterInitialise()
	{
		// Some PHP binaries don't set the $_SERVER array under all platforms
		if (!isset($_SERVER))
		{
			return;
		}

		if (!is_array($_SERVER))
		{
			return;
		}

		$this->blockUserAgent();

		$this->blockForwardHeader();
	}

	private function blockUserAgent()
	{
		// Some user agents don't set a UA string at all
		if (!array_key_exists('HTTP_USER_AGENT', $_SERVER))
		{
			return;
		}

		$mua = $_SERVER['HTTP_USER_AGENT'];
		$mua = trim($mua);

		if (strstr($mua, '<?'))
		{
			$this->exceptionsHandler->blockRequest('muashield');
		}

		// Serialised data in the MUA string?
		$patterns = array(
			'@"feed_url@', // feed_url isn't your typical UA but it sure as hell is part of an exploit
			'@}__(.*)|O:@', // Typical start of serialised data
			'@J?Simple(p|P)ie(Factory)?@', // If SimplePie or JSimplepieFactory is referenced
		);

		foreach ($patterns as $pattern)
		{
			if (preg_match($pattern, $mua) == 1)
			{
				// Neuter the attack
				$neuterMUA                  = 'HACKING ATTEMPT DETECTED';
				// 1. Reset the User Agent string reported by the server
				$_SERVER['HTTP_USER_AGENT'] = $neuterMUA;
				// 2. Replace the saved User Agent in the session storage to something non-malicious
				JFactory::getSession()->set('session.client.browser', $neuterMUA);
				// 3. KILL THE SESSION (may not work, depends on the session handler)
				JFactory::getSession()->destroy();

				// Immediately block the scumbag
				$this->exceptionsHandler->blockRequest('muashield');
			}
		}
	}

	private function blockForwardHeader()
	{
		// Do I have a HTTP_X_FORWARDED_FOR header?
		if(!isset($_SERVER['HTTP_X_FORWARDED_FOR']))
		{
			return;
		}

		// The same attack could be performed using the HTTP_X_FORWARDED_FOR header
		$header = $_SERVER['HTTP_X_FORWARDED_FOR'];
		$header = trim($header);

		$patterns = array(
			'@"feed_url@', // feed_url isn't your typical UA but it sure as hell is part of an exploit
			'@}__(.*)|O:@', // Typical start of serialised data
			'@"J?Simple(p|P)ie(Factory)?"@', // If SimplePie or JSimplepieFactory is referenced
		);

		foreach ($patterns as $pattern)
		{
			if (preg_match($pattern, $header))
			{
				// Neuter the attack
				$neuterMUA                  = 'HACKING ATTEMPT DETECTED';
				// 1. Reset the Forwarded header reported by the server
				$_SERVER['HTTP_X_FORWARDED_FOR'] = $neuterMUA;
				// 2. Replace the saved Forwarded header in the session storage to something non-malicious
				JFactory::getSession()->set('session.client.forwarded', $neuterMUA);
				// 3. KILL THE SESSION (may not work, depends on the session handler)
				JFactory::getSession()->destroy();

				// Immediately block the scumbag
				$this->exceptionsHandler->blockRequest('muashield');
			}
		}
	}
}