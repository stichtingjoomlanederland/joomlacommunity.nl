<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2021 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use Akeeba\AdminTools\Admin\Helper\Storage;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

defined('_JEXEC') || die;

/**
 * Detect if the Quick Start Wizard has ran (or Admin Tools has been manually configured). Otherwise display a message
 * reminding the user to run the wizard.
 */
class AtsystemFeatureQuickstart extends AtsystemFeatureAbstract
{
	protected $loadOrder = 999;

	public function onBeforeRender()
	{
		if (!$this->container->platform->isBackend())
		{
			return;
		}

		if ($this->container->platform->getUser()->guest)
		{
			return;
		}

		/** @var Storage $storage */
		$storage      = Storage::getInstance();
		$wizardHasRan = $storage->getValue('quickstart', 0);

		if ($wizardHasRan)
		{
			return;
		}

		if (!$this->container->platform->getUser()->authorise('core.manage', 'admintools.security'))
		{
			return;
		}

		if (!$this->container->platform->getUser()->authorise('core.manage', 'admintools.maintenance'))
		{
			return;
		}

		$jlang = Factory::getLanguage();
		$jlang->load('com_admintools', JPATH_ADMINISTRATOR, 'en-GB');
		$jlang->load('com_admintools', JPATH_ADMINISTRATOR, null, true);

		$msg = Text::sprintf('COM_ADMINTOOLS_QUICKSTART_MSG_PLEASERUNWIZARD', 'index.php?option=com_admintools&view=QuickStart');
		Factory::getApplication()->enqueueMessage($msg, 'error');
	}
}
