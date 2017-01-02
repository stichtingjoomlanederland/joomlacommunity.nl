<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\View\HtaccessMaker;

defined('_JEXEC') or die;

use Akeeba\AdminTools\Admin\Helper\ServerTechnology;
use Akeeba\AdminTools\Admin\Model\HtaccessMaker;
use FOF30\View\DataView\Html as BaseView;

class Html extends BaseView
{
	/**
	 * .htaccess contents for preview
	 *
	 * @var  string
	 */
	public $htaccess;

	/**
	 * The .htaccess Maker configuration
	 *
	 * @var  array
	 */
	public $htconfig;

	/**
	 * Is this supported? 0 No, 1 Yes, 2 Maybe
	 *
	 * @var  int
	 */
	public $isSupported;

	protected function onBeforePreview()
	{
		/** @var HtaccessMaker $model */
		$model          = $this->getModel();
		$this->htaccess = $model->makeConfigFile();
		$this->setLayout('plain');
	}

	protected function onBeforeMain()
	{
		/** @var HtaccessMaker $model */
		$model             = $this->getModel();
		$this->htconfig    = $model->loadConfiguration();
		$this->isSupported = ServerTechnology::isHtaccessSupported();
	}
}