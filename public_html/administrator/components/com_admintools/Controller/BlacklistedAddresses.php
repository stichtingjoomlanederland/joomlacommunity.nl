<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2021 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\Controller;

defined('_JEXEC') || die;

use Akeeba\AdminTools\Admin\Controller\Mixin\CustomACL;
use FOF40\Controller\DataController;
use FOF40\View\Exception\AccessForbidden;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use RuntimeException;

class BlacklistedAddresses extends DataController
{
	use CustomACL;

	public function import()
	{
		$this->layout = 'import';

		$this->display();
	}

	public function export()
	{
		try
		{
			parent::display();
		}
		catch (AccessForbidden $e)
		{
			$msg = Text::_('COM_ADMINTOOLS_BLACKLISTEDADDRESSES_EXPORT_EMPTY');
			$this->setRedirect('index.php?option=com_admintools&view=BlacklistedAddresses', $msg);
		}
	}

	public function doimport()
	{
		$app = Factory::getApplication();
		/** @var \Akeeba\AdminTools\Admin\Model\BlacklistedAddresses $model */
		$model     = $this->getModel();
		$file      = $this->input->files->get('csvfile', null, 'raw');
		$delimiter = $this->input->getInt('csvdelimiters', 0);
		$field     = $this->input->getString('field_delimiter', '');
		$enclosure = $this->input->getString('field_enclosure', '');

		if ($file['error'])
		{
			$this->setRedirect('index.php?option=com_admintools&view=BlacklistedAddresses&task=import', Text::_('COM_ADMINTOOLS_ERR_IMPORTANDEXPORT_UPLOAD'), 'error');

			return;
		}

		if ($delimiter != -99)
		{
			[$field, $enclosure] = $model->decodeDelimiterOptions($delimiter);
		}

		// Import ok, but maybe I have warnings (ie skipped lines)
		try
		{
			$model->import($file['tmp_name'], $field, $enclosure);
		}
		catch (RuntimeException $e)
		{
			//Uh oh... import failed, let's inform the user why it happened
			$app->enqueueMessage(Text::sprintf('COM_ADMINTOOLS_ERR_IMPORTANDEXPORT_FAILURE', $e->getMessage()), 'error');
		}

		$this->setRedirect('index.php?option=com_admintools&view=BlacklistedAddresses');
	}
}
