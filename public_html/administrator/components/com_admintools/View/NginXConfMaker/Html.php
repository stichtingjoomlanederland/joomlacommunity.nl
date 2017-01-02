<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\View\NginXConfMaker;

defined('_JEXEC') or die;

use Akeeba\AdminTools\Admin\Helper\ServerTechnology;
use Akeeba\AdminTools\Admin\Model\NginXConfMaker;
use FOF30\View\DataView\Html as BaseView;

class Html extends BaseView
{
	/**
	 * nginx.conf contents for preview
	 *
	 * @var  string
	 */
	public $nginxconf;

	/**
	 * The nginx.conf Maker configuration
	 *
	 * @var  array
	 */
	public $nginxconfig;

	/**
	 * Is this supported? 0 No, 1 Yes, 2 Maybe
	 *
	 * @var  int
	 */
	public $isSupported;

	protected function onBeforePreview()
	{
		/** @var NginXConfMaker $model */
		$model           = $this->getModel();
		$this->nginxconf = $model->makeConfigFile();
		$this->setLayout('plain');
	}

	protected function onBeforeMain()
	{
		/** @var NginXConfMaker $model */
		$model             = $this->getModel();
		$this->nginxconfig = $model->loadConfiguration();
		$this->isSupported = ServerTechnology::isNginxSupported();
	}
}