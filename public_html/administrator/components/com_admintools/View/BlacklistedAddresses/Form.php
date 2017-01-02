<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\View\BlacklistedAddresses;

defined('_JEXEC') or die;

use Akeeba\AdminTools\Admin\Helper\Storage;
use Akeeba\AdminTools\Admin\View\WhitelistedAddresses\Form as BaseView;
use JLoader;

class Form extends BaseView
{
	/**
	 * The component's parameters
	 *
	 * @var  Storage
	 */
	public $componentParams;

	protected function onBeforeBrowse()
	{
		parent::onBeforeBrowse();

		// Load the components parameters
		JLoader::import('joomla.application.component.model');
		require_once JPATH_ADMINISTRATOR . '/components/com_admintools/Helper/Storage.php';
		$this->componentParams = Storage::getInstance();
	}

}