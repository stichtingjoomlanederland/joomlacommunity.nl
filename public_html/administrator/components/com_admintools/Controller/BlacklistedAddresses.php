<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\Controller;

defined('_JEXEC') or die;

use Akeeba\AdminTools\Admin\Controller\Mixin\CustomACL;
use FOF30\Controller\DataController;
use JText;

class BlacklistedAddresses extends DataController
{
	use CustomACL;

	public function import()
	{
		$this->layout = 'import';

		$this->display();
	}

	public function doimport()
	{
		$app       = \JFactory::getApplication();
		/** @var \Akeeba\AdminTools\Admin\Model\BlacklistedAddresses $model */
		$model     = $this->getModel();
		$file      = $this->input->files->get('csvfile', null, 'raw');
		$delimiter = $this->input->getInt('csvdelimiters', 0);
		$field     = $this->input->getString('field_delimiter', '');
		$enclosure = $this->input->getString('field_enclosure', '');

		if ($file['error'])
		{
			$this->setRedirect('index.php?option=com_admintools&view=BlacklistedAddresses&task=import', JText::_('COM_ADMINTOOLS_ERR_IMPORTANDEXPORT_UPLOAD'), 'error');

			return;
		}

		if ($delimiter != - 99)
		{
			list($field, $enclosure) = $model->decodeDelimiterOptions($delimiter);
		}

		// Import ok, but maybe I have warnings (ie skipped lines)
		try
		{
			$model->import($file['tmp_name'], $field, $enclosure);
		}
		catch (\RuntimeException $e)
		{
			//Uh oh... import failed, let's inform the user why it happened
			$app->enqueueMessage(JText::sprintf('COM_ADMINTOOLS_ERR_IMPORTANDEXPORT_FAILURE', $e->getMessage()), 'error');
		}

		$this->setRedirect('index.php?option=com_admintools&view=BlacklistedAddresses');
	}
}