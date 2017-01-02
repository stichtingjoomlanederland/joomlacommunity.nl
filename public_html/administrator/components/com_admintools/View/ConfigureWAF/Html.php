<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\View\ConfigureWAF;

defined('_JEXEC') or die;

use Akeeba\AdminTools\Admin\Model\ConfigureWAF;
use Akeeba\AdminTools\Admin\Model\ControlPanel;
use FOF30\View\DataView\Html as BaseView;
use JText;

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
		JText::script('JNO', true);
		JText::script('JYES', true);

		// Push data to Javascript
		$script = <<< JS

; // Working around broken 3PD plugins

akeeba.jQuery(document).ready(function($){
	admintools.ConfigureWAF.myIP = '$this->myIP';
});

JS;
		$this->addJavascriptFile('admin://components/com_admintools/media/js/ConfigureWAF.min.js');
		$this->addJavascriptInline($script);
	}
}