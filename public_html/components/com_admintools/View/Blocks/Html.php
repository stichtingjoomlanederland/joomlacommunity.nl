<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2021 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Site\View\Blocks;

defined('_JEXEC') || die;

use Akeeba\AdminTools\Admin\Helper\Storage;
use FOF40\View\DataView\Html as BaseView;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

class Html extends BaseView
{
	protected $message;

	public function display($tpl = null)
	{
		// Get the message
		$cparams = Storage::getInstance();

		$message = $this->container->platform->getSessionVar('message', null, 'com_admintools');

		if (empty($message))
		{
			$customMessage = $cparams->getValue('custom403msg', '');

			if (!empty($customMessage))
			{
				$message = $customMessage;
			}
			else
			{
				$message = 'ADMINTOOLS_BLOCKED_MESSAGE';
			}
		}

		// Merge the default translation with the current translation
		$jlang = Factory::getLanguage();

		// Front-end translation
		$jlang->load('plg_system_admintools', JPATH_ADMINISTRATOR, 'en-GB', true);
		$jlang->load('plg_system_admintools', JPATH_ADMINISTRATOR, $jlang->getDefault(), true);
		$jlang->load('plg_system_admintools', JPATH_ADMINISTRATOR, null, true);

		if ((Text::_('ADMINTOOLS_BLOCKED_MESSAGE') == 'ADMINTOOLS_BLOCKED_MESSAGE') && ($message == 'ADMINTOOLS_BLOCKED_MESSAGE'))
		{
			$message = "Access Denied";
		}
		else
		{
			$message = Text::_($message);
		}

		$this->message = $message;

		parent::display($tpl);

		$this->container->platform->closeApplication();
	}
}
