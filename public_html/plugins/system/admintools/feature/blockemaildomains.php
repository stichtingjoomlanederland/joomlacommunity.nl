<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

class AtsystemFeatureBlockemaildomains extends AtsystemFeatureAbstract
{
	protected $loadOrder = 930;

	/**
	 * Is this feature enabled?
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		if (!$this->container->platform->isFrontend())
		{
			return false;
		}

		$domains = $this->cparams->getValue('blockedemaildomains', '');

		if (empty($domains))
		{
			return false;
		}

		return true;
	}

	public function onUserBeforeSave($olduser, $isnew, $user)
	{
		$allowed = false;
		$block   = ($this->cparams->getValue('filteremailregistration', 'block') == 'block');
		$domains = $this->cparams->getValue('blockedemaildomains', '');

		$domains = str_replace("\r", "\n", $domains);
		$domains = str_replace("\n\n", "\n", $domains);
		$domains = explode("\n", $domains);

		foreach ($domains as $domain)
		{
			// Block specific domains and we have a match
			if ($block && (stripos($user['email'], trim($domain)) !== false))
			{
				// Load the component's administrator translation files
				$jlang = JFactory::getLanguage();
				$jlang->load('com_admintools', JPATH_ADMINISTRATOR, 'en-GB', true);
				$jlang->load('com_admintools', JPATH_ADMINISTRATOR, $jlang->getDefault(), true);
				$jlang->load('com_admintools', JPATH_ADMINISTRATOR, null, true);

				throw new Exception(JText::sprintf('COM_ADMINTOOLS_ERR_BLOCKEDEMAILDOMAINS', $domain));
			}

			// Allow only specific domains and the user is using a domain that is NOT in the list
			if (!$block && (stripos($user['email'], trim($domain)) !== false))
			{
				// Let's raise the flag to mark that we got a match
				$allowed = true;
			}
		}

		// If I have to allow only specific email domains and we didn't have a match, let's block the registration
		if (!$block && !$allowed)
		{
			$jlang = JFactory::getLanguage();
			$jlang->load('com_admintools', JPATH_ADMINISTRATOR, 'en-GB', true);
			$jlang->load('com_admintools', JPATH_ADMINISTRATOR, $jlang->getDefault(), true);
			$jlang->load('com_admintools', JPATH_ADMINISTRATOR, null, true);

			throw new Exception(JText::sprintf('COM_ADMINTOOLS_ERR_BLOCKEDEMAILDOMAINS', $user['email']));
		}

		return true;
	}
}
