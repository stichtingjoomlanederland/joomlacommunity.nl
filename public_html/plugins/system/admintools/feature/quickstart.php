<?php
/**
 * @package   AdminTools
 * @copyright Copyright (c)2010-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

use Akeeba\AdminTools\Admin\Helper\Storage;

defined('_JEXEC') or die;

/**
 * Detect if the Quick Start Wizard has ran (or Admin Tools has been manually configured). Otherwise display a message
 * reminding the user to run the wizard.
 */
class AtsystemFeatureQuickstart extends AtsystemFeatureAbstract
{
	protected $loadOrder = 999;

	public function onBeforeRender()
	{
		if (!$this->helper->isBackend())
		{
			return;
		}

		if (JFactory::getUser()->guest)
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

		if (!JFactory::getUser()->authorise('core.manage', 'admintools.security'))
		{
			return;
		}

		if (!JFactory::getUser()->authorise('core.manage', 'admintools.maintenance'))
		{
			return;
		}

		$jlang = JFactory::getLanguage();
		$jlang->load('com_admintools', JPATH_ADMINISTRATOR, 'en-GB');
		$jlang->load('com_admintools', JPATH_ADMINISTRATOR, null, true);

		$msg = JText::sprintf('COM_ADMINTOOLS_QUICKSTART_MSG_PLEASERUNWIZARD', 'index.php?option=com_admintools&view=QuickStart');
		JFactory::getApplication()->enqueueMessage($msg, 'error');
	}
} 