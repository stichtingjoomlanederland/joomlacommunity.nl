<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\View\BlacklistedAddresses;

defined('_JEXEC') or die;

use Akeeba\AdminTools\Admin\View\WhitelistedAddresses\Html as BaseView;

class Html extends BaseView
{
	protected function onBeforeBrowse()
	{
		$this->hash_view = 'admintoolsblacklistedaddresses';

		parent::onBeforeBrowse();
	}

	protected function onBeforeImport()
	{
		$this->addJavascriptFile('admin://components/com_admintools/media/js/BlacklistedAddresses.min.js');
	}
}
