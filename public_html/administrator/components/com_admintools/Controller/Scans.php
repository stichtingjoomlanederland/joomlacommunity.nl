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

class Scans extends DataController
{
	use CustomACL;

	/**
	 * Apply hard-coded filters before rendering the Browse page
	 *
	 * @return bool
	 */
	protected function onBeforeBrowse()
	{
		/** @var \Akeeba\AdminTools\Admin\Model\Scans $model */
		$model = $this->getModel();

		$limitstart = $this->input->getInt('limitstart', null);

		if (is_null($limitstart))
		{
			$total = $model->count();
			$limitstart = $model->getState('limitstart', 0);

			if ($limitstart > $total)
			{
				$model->limitstart(0);
			}
		}

		$model->status('complete')->profile_id(1);
	}

	protected function onAfterBrowse()
	{
		/** @var \Akeeba\AdminTools\Admin\Model\Scans $model */
		$model = $this->getModel();

		$model->removeIncompleteScans();

		return true;
	}

	public function add()
	{
		throw new \Exception(JText::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN'), 403);
	}

	public function startscan()
	{
		$this->input->set('layout', 'scan');

		/** @var \Akeeba\AdminTools\Admin\View\ScanAlerts\Html $view */
		$view = $this->getView();
		/** @var \Akeeba\AdminTools\Admin\Model\Scans $model */
		$model = $this->getModel();

		$view->retarray = $model->startScan();
		$view->setLayout('scan');

		$this->layout = 'scan';

		parent::display(false);
	}

	public function stepscan()
	{
		$this->input->set('layout', 'scan');

		/** @var \Akeeba\AdminTools\Admin\View\ScanAlerts\Html $view */
		$view = $this->getView();
		/** @var \Akeeba\AdminTools\Admin\Model\Scans $model */
		$model = $this->getModel();

		$view->retarray = $model->stepScan();
		$view->setLayout('scan');

		$this->layout = 'scan';

		parent::display(false);
	}

	public function purge()
	{
		/** @var \Akeeba\AdminTools\Admin\Model\Scans $model */
		$model = $this->getModel();

		$type = null;

		if($model->purgeFilesCache())
		{
			$msg = JText::_('COM_ADMINTOOLS_MSG_SCAN_PURGE_COMPLETED');
		}
		else
		{
			$msg = JText::_('COM_ADMINTOOLS_MSG_SCAN_PURGE_ERROR');
			$type = 'error';
		}

		$this->setRedirect('index.php?option=com_admintools&view=Scans', $msg, $type);
	}
}