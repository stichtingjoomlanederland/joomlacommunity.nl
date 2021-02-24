<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2021 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Folder;

defined('_JEXEC') || die;

class AtsystemFeatureTemplateswitch extends AtsystemFeatureAbstract
{
	private static $siteTemplates = null;

	protected $loadOrder = 400;

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

		if ($this->skipFiltering)
		{
			return false;
		}

		if ($this->cparams->getValue('template', 0) != 1)
		{
			return false;
		}

		self::$siteTemplates = Folder::folders(JPATH_SITE . '/templates');

		return true;
	}

	/**
	 * Disable template switching in the URL
	 */
	public function onAfterInitialise()
	{
		$template          = Factory::getApplication()->input->getCmd('template', null);
		$option            = $this->input->getCmd('option', '');
		$block             = true;
		$allowSiteTemplate = $this->cparams->getValue('allowsitetemplate', 0);

		// No template in the URL? Nothing to do.
		if (empty($template))
		{
			return;
		}

		/**
		 * Existing site templates are always allowed for com_mailto and com_ajax.
		 *
		 * com_mailto includes the default template in the URL since Joomla 1.7.
		 *
		 * com_ajax supports arbitrary AJAX tasks in templates using the template=something switch.
		 */
		if (in_array($option, ['com_mailto', 'com_ajax']))
		{
			$allowSiteTemplate = true;
		}

		if ($allowSiteTemplate)
		{
			$block = !in_array($template, self::$siteTemplates);
		}

		if (!$block)
		{
			return;
		}

		$this->exceptionsHandler->blockRequest('template');
	}
}
