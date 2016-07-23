<?php
/**
 * wbAMP - Accelerated Mobile Pages for Joomla!
 *
 * @author      Yannick Gaultier
 * @copyright   (c) Yannick Gaultier - Weeblr llc - 2016
 * @package     wbAmp
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version     1.4.2.551
 * @date        2016-07-19
 */

defined('_JEXEC') or die();

/**
 * Replace email addresses in content
 * with an obfuscated version
 *
 */
class WbampModelProcessor_Obfuscator
{
	private $testMode = false;

	/**
	 * Process raw content (html), finding email addresses
	 * and replacing them by something that's harder to
	 * harvest for crawlers
	 *
	 * @return bool whether the content has been modified
	 */
	public function process(&$content, $testMode = false)
	{
		$this->testMode = $testMode;

		// in all cases, remove the emailcloak=off we added to stop joomla obfuscator
		$content = JString::str_ireplace(array('{emailcloak=off}', '<p>{emailcloak=off}</p>'), '', $content);

		/*
		 * Check for presence of {wbamp_disable_email_protection} which explicitely disables this
		 * bot for the item.
		 */
		if (JString::strpos($content, '{wbamp_disable_email_protection}') !== false)
		{
			$content = JString::str_ireplace(array('{wbamp_disable_email_protection}', '<p>{wbamp_disable_email_protection}</p>'), '', $content);

			return true;
		}

		// quick check
		if (JString::strpos($content, '@') === false)
		{
			return false;
		}

		// regexp to find emails
		$regex = '#(mailto:)?[A-Z0-9-%_.+]{1,64}@(?:[A-Z0-9](?:[A-Z0-9-]{0,62}[A-Z0-9])?\.){1,8}[A-Z]{2,63}#iu';
		$newContent = preg_replace_callback($regex, array($this, '_obfuscateAddress'), $content);
		$modified = $content != $newContent;
		if ($modified)
		{
			$content = $newContent;
		}

		return $modified;
	}

	/**
	 * Process a email address match
	 *
	 * @param $match
	 * @return string
	 */
	protected function _obfuscateAddress($match)
	{
		// detect type we can handle
		$originalMatch = $match[0];
		if (!empty($originalMatch))
		{
			// Test mode wrap captured text with brackets
			// allow test suite to be sure regexp works fine
			// on edge cases
			if ($this->testMode)
			{
				return '[' . $originalMatch . ']';
			}
			else
			{
				return $this->processAddress($originalMatch);
			}
		}

		return $originalMatch;
	}

	/**
	 * Apply encoding to email address
	 *
	 * @param $address
	 * @return mixed
	 */
	protected function processAddress($address)
	{
		// we separate the mailto: prefix as if left with the
		// full address, it may be left unencoded because
		// of poor randomness of encoding function
		if (JString::substr($address, 0, 7) == 'mailto:')
		{
			$prefix = WbampHelper_Email::eae_encode_str('mailto:');
			$address = JString::substr($address, 7);
		}
		else
		{
			$prefix = '';
		}

		$processed = $prefix . WbampHelper_Email::eae_encode_str($address);

		return $processed;
	}
}
