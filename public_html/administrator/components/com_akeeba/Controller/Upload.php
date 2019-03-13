<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Backup\Admin\Controller;

// Protect from unauthorized access
defined('_JEXEC') or die();

use Akeeba\Backup\Admin\Controller\Mixin\CustomACL;
use Akeeba\Backup\Admin\View\Upload\Html as UploadView;
use Akeeba\Engine\Platform;
use FOF30\Controller\Controller;
use JText;
use RuntimeException;

class Upload extends Controller
{
	use CustomACL;

	/**
	 *
	 * @return  void
	 */
	public function upload()
	{
		// Get the parameters from the URL
		$id = $this->getAndCheckId();
		$part = $this->input->get('part', 0, 'int');
		$frag = $this->input->get('frag', 0, 'int');

		// Check the backup stat ID
		if ($id === false)
		{
			$url = 'index.php?option=com_akeeba&view=Upload&tmpl=component&task=cancelled&id=' . $id;
			$this->setRedirect($url, JText::_('COM_AKEEBA_TRANSFER_ERR_INVALIDID'), 'error');

			return;
		}

		// Set the model state
		/** @var \Akeeba\Backup\Admin\Model\Upload $model */
		$model = $this->getModel();
		$model->setState('id', $id);
		$model->setState('part', $part);
		$model->setState('frag', $frag);

		// Try uploading
		$error = '';

		try
		{
			$result = $model->upload();
		}
		catch (RuntimeException $e)
		{
			$result = false;
			$error = $e->getMessage();
		}

		// Get the modified model state
		$id = $model->getState('id');
		$part = $model->getState('part');
		$frag = $model->getState('frag');
		$stat = $model->getState('stat');
		$remote_filename = $model->getState('remotename');

		// Push the state to the view. We assume we have to continue uploading. We only change that if we detect an
		// upload completion or error condition in the if-blocks further below.
		/** @var UploadView $view */
		$view = $this->getView();

		$view->setLayout('uploading');
		$view->parts = $stat['multipart'];
		$view->part = $part;
		$view->frag = $frag;
		$view->id = $id;
		$view->done = 0;
		$view->error = 0;

		if (($part >= 0) && ($result === true))
		{
			// If we are told the upload finished successfully we can display the "done" page
			$view->setLayout('done');
			$view->done = 1;
			$view->error = 0;

			// Also reset the saved post-processing engine
			$this->container->platform->setSessionVar('postproc_engine', null, 'akeeba');
		}
		elseif ($result === false)
		{
			// If we have an error we have to display it and stop the upload
			$view->done = 0;
			$view->error = 1;
			$view->errorMessage = $error;
			$view->setLayout('error');

			// Also reset the saved post-processing engine
			$this->container->platform->setSessionVar('postproc_engine', null, 'akeeba');
		}

		$this->display(false, false);
	}

	/**
	 * This task is called when we have to cancel the upload
	 *
	 * @param bool $cachable
	 * @param bool $urlparams
	 */
	public function cancelled($cachable = false, $urlparams = false)
	{
		/** @var UploadView $view */
		$view = $this->getView();
		$view->setLayout('error');

		$this->display(false, false);
	}

	/**
	 * Start uploading
	 *
	 * @return  void
	 */
	public function start($cachable = false, $urlparams = false)
	{
		$id = $this->getAndCheckId();

		// Check the backup stat ID
		if ($id === false)
		{
			$url = 'index.php?option=com_akeeba&view=Upload&tmpl=component&task=cancelled&id=' . $id;
			$this->setRedirect($url, JText::_('COM_AKEEBA_TRANSFER_ERR_INVALIDID'), 'error');

			return;
		}

		// Start by resetting the saved post-processing engine
		$this->container->platform->setSessionVar('postproc_engine', null, 'akeeba');

		// Initialise the view
		/** @var UploadView $view */
		$view = $this->getView();

		$view->done = 0;
		$view->error = 0;

		$view->id = $id;
		$view->setLayout('default');

		$this->display(false, false);
	}

	/**
	 * Gets the stats record ID from the request and checks that it does exist
	 *
	 * @return bool|int False if an invalid ID is found, the numeric ID if it's valid
	 */
	private function getAndCheckId()
	{
		$id = $this->input->get('id', 0, 'int');

		if ($id <= 0)
		{
			return false;
		}

		$statObject = Platform::getInstance()->get_statistics($id);

		if (empty($statObject) || !is_array($statObject))
		{
			return false;
		}

		return $id;
	}
}
