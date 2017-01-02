<?php
/**
 * @package   AdminTools
 * @copyright Copyright (c)2010-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

class AtsystemFeatureTemplateswitch extends AtsystemFeatureAbstract
{
	protected $loadOrder = 400;

	private static $siteTemplates = null;

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

		if ($this->cparams->getValue('template', 0) != 1)
		{
			return false;
		}

		JLoader::import('joomla.filesystem.folder');
		self::$siteTemplates = JFolder::folders(JPATH_SITE . '/templates');

		return true;
	}

	/**
	 * Disable template switching in the URL
	 */
	public function onAfterInitialise()
	{
		$template = JFactory::getApplication()->input->getCmd('template', null);
		$block = true;

		if (!empty($template))
		{
			// Exception: existing site templates are allowed
			if ($this->input->getCmd('option', '') == 'com_mailto')
			{
				// com_email URLs in Joomla! 1.7 and later have template= defined; force $allowsitetemplate in this case
				$allowsitetemplate = true;
			}
			else
			{
				// Otherwise, allow only of the switch is set
				$allowsitetemplate = $this->cparams->getValue('allowsitetemplate', 0);
			}

			if ($allowsitetemplate)
			{
				$block = !in_array($template, self::$siteTemplates);
			}

			if ($block)
			{
				$this->exceptionsHandler->blockRequest('template');
			}
		}
	}
} 