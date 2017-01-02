<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\View\WhitelistedAddresses;

defined('_JEXEC') or die;

use Akeeba\AdminTools\Admin\Helper\Storage;
use Akeeba\AdminTools\Admin\Model\ControlPanel;
use Akeeba\AdminTools\Admin\View\Mixin\SystemPluginExists;
use FOF30\View\DataView\Form as BaseView;
use JLoader;


class Form extends BaseView
{
	use SystemPluginExists;

	/**
	 * The detected IP address of the visitor
	 *
	 * @var  string
	 */
	protected $myIP = '';

	/**
	 * The component's parameters
	 *
	 * @var  Storage
	 */
	public $componentParams;

	protected function onBeforeBrowse()
	{
		$this->populateSystemPluginExists();

		parent::onBeforeBrowse();
		
		// Load the components parameters
		JLoader::import('joomla.application.component.model');
		require_once JPATH_ADMINISTRATOR . '/components/com_admintools/Helper/Storage.php';
		$this->componentParams = Storage::getInstance();

	}

	protected function onBeforeEdit()
	{
		$this->populateMyIP();

		parent::onBeforeEdit();
	}

	protected function onBeforeAdd()
	{
		$this->populateMyIP();

		parent::onBeforeAdd();
	}

	private function populateMyIP()
	{
		/** @var ControlPanel $cpanelModel */
		$cpanelModel = $this->container->factory->model('ControlPanel');
		$this->myIP = $cpanelModel->getVisitorIP();
	}
}