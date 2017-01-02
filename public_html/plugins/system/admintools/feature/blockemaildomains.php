<?php
/**
 * @package   AdminTools
 * @copyright Copyright (c)2010-2016 Nicholas K. Dionysopoulos
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
		if (!$this->helper->isFrontend())
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
		$domains = $this->cparams->getValue('blockedemaildomains', '');

		$domains = str_replace("\r", "\n", $domains);
		$domains = str_replace("\n\n", "\n", $domains);
		$domains = explode("\n", $domains);

		foreach ($domains as $domain)
		{
			// The user used a blocked domain, let's prevent
			if (strpos($user['email'], trim($domain)) !== false)
			{
				// Load the component's administrator translation files
				$jlang = JFactory::getLanguage();
				$jlang->load('com_admintools', JPATH_ADMINISTRATOR, 'en-GB', true);
				$jlang->load('com_admintools', JPATH_ADMINISTRATOR, $jlang->getDefault(), true);
				$jlang->load('com_admintools', JPATH_ADMINISTRATOR, null, true);

				throw new Exception(JText::sprintf('COM_ADMINTOOLS_ERR_BLOCKEDEMAILDOMAINS', $domain));
			}
		}

		return true;
	}
}