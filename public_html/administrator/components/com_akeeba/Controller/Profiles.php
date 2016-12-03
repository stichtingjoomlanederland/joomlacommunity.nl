<?php
/**
 * @package   AkeebaBackup
 * @copyright Copyright (c)2006-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Backup\Admin\Controller;

// Protect from unauthorized access
defined('_JEXEC') or die();

use Akeeba\Backup\Admin\Controller\Mixin\CustomACL;
use FOF30\Container\Container;
use FOF30\Controller\DataController;
use JText;
use RuntimeException;

class Profiles extends DataController
{
	use CustomACL;

	/**
	 * Imports an exported profile .json file
	 */
	public function import()
	{
		$this->csrfProtection();

		$user = $this->container->platform->getUser();

		if (!$user->authorise('akeeba.configure', 'com_akeeba'))
		{
			throw new RuntimeException(JText::_('JERROR_ALERTNOAUTHOR'), 403);
		}

		// Get some data from the request
		$file = $this->input->files->get('importfile', array(), 'array');

		if (!isset($file['name']))
		{
			$this->setRedirect('index.php?option=com_akeeba&view=Profiles', JText::_('MSG_UPLOAD_INVALID_REQUEST'), 'error');

			return ;
		}

		// Load the file data
		$data = @file_get_contents($file['tmp_name']);
		@unlink($file['tmp_name']);

		// JSON decode
		$data = json_decode($data, true);

		// Check for data validity
		$isValid =
			is_array($data) &&
			!empty($data) &&
			array_key_exists('description', $data) &&
			array_key_exists('configuration', $data) &&
			array_key_exists('filters', $data);

		if (!$isValid)
		{
			$this->setRedirect('index.php?option=com_akeeba&view=Profiles', JText::_('COM_AKEEBA_PROFILES_ERR_IMPORT_INVALID'), 'error');

			return;
		}

		// Unset the id, if it exists
		if (array_key_exists('id', $data))
		{
			unset($data['id']);
		}

		$data['akeeba.flag.confwiz'] = 1;

		// Try saving the profile
		/** @var \Akeeba\Backup\Admin\Model\Profiles $model */
		$model  = $this->getModel();
		$result = $model->save($data);

		$this->setRedirect('index.php?option=com_akeeba&view=Profiles', JText::_('COM_AKEEBA_PROFILES_MSG_IMPORT_COMPLETE'));

		if (!$result)
		{
			$this->setRedirect('index.php?option=com_akeeba&view=Profiles', JText::_('COM_AKEEBA_PROFILES_ERR_IMPORT_FAILED'), 'error');
		}
	}
}