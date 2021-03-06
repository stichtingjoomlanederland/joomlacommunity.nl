<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2021 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\View\WebApplicationFirewall;

defined('_JEXEC') || die;

use Akeeba\AdminTools\Admin\View\Mixin\SystemPluginExists;
use FOF40\View\DataView\Html as BaseView;

class Html extends BaseView
{
	use SystemPluginExists;

	protected function onBeforeMain()
	{
		$this->populateSystemPluginExists();
	}

}
