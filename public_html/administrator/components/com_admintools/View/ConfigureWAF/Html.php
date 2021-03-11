<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2021 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\View\ConfigureWAF;

defined('_JEXEC') || die;

use Akeeba\AdminTools\Admin\Model\ConfigureWAF;
use Akeeba\AdminTools\Admin\Model\ControlPanel;
use FOF40\View\DataView\Html as BaseView;
use Joomla\CMS\Language\Text;

class Html extends BaseView
{
	/**
	 * WAF configuration
	 *
	 * @var  array
	 */
	public $wafconfig;

	/**
	 * Should I render the configuration as a long list (instead of tabs)?
	 *
	 * @var  bool
	 */
	public $longConfig;

	/**
	 * The detected visitor's IP address
	 *
	 * @var  string
	 */
	public $myIP = '';

	protected function onBeforeMain()
	{
		// Set the toolbar title
		/** @var ConfigureWAF $model */
		$model  = $this->getModel();
		$config = $model->getConfig();

		/** @var ControlPanel $cpanelModel */
		$cpanelModel = $this->container->factory->model('ControlPanel')->tmpInstance();
		$this->myIP  = $cpanelModel->getVisitorIP();

		// I'm converting these two fields only here,
		// since in the whole component they are handled as a comma-separated list
		$config['reasons_nolog']   = explode(',', $config['reasons_nolog']);
		$config['reasons_noemail'] = explode(',', $config['reasons_noemail']);

		$this->wafconfig  = $config;
		$this->longConfig = $this->container->params->get('longconfigpage', 0);

		// Push translations
		Text::script('JNO', true);
		Text::script('JYES', true);

		$this->container->platform->addScriptOptions('admintools.ConfigureWAF.longConfig', (bool) $this->longConfig);

		\AkeebaFEFHelper::loadScript('Tooltip');

		if (!$this->longConfig)
		{
			\AkeebaFEFHelper::loadScript('Tabs');
		}

		$this->addJavascriptFile('admin://components/com_admintools/media/js/ConfigureWAF.min.js', $this->container->mediaVersion, 'text/javascript', true);

	}
}
