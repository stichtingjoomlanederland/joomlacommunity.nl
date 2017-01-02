<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\View\WAFBlacklistedRequests;

defined('_JEXEC') or die;

use Akeeba\AdminTools\Admin\View\Mixin\SystemPluginExists;
use FOF30\View\DataView\Form as BaseView;

class Form extends BaseView
{
	use SystemPluginExists;

	protected function onBeforeBrowse()
	{
		$this->populateSystemPluginExists();

		parent::onBeforeBrowse();
	}
}