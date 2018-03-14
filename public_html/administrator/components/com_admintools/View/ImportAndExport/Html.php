<?php
/**
 * @package   AdminTools
 * @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\View\ImportAndExport;

defined('_JEXEC') or die;

use FOF30\View\DataView\Html as BaseView;

class Html extends BaseView
{
	protected function onBeforeExport()
	{
		$this->addJavascriptFile('admin://components/com_admintools/media/js/ImportExport.min.js');
	}

}
