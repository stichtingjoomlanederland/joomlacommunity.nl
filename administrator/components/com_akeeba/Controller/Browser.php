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
use FOF30\Controller\Controller;

class Browser extends Controller
{
	use CustomACL;

	protected function onBeforeDefault()
	{
		$folder        = $this->input->get('folder', '', 'string');
		$processfolder = $this->input->get('processfolder', 0, 'int');

		/** @var \Akeeba\Backup\Admin\Model\Browser $model */
		$model = $this->getModel();
		$model->setState('folder', $folder);
		$model->setState('processfolder', $processfolder);
		$model->makeListing();
	}
}