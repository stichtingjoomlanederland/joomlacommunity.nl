<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\View\WebApplicationFirewall;

defined('_JEXEC') or die;

use Akeeba\AdminTools\Admin\View\Mixin\SystemPluginExists;
use FOF30\View\DataView\Html as BaseView;

class Html extends BaseView
{
	use SystemPluginExists;

	protected function onBeforeMain()
	{
		$this->populateSystemPluginExists();
	}

}