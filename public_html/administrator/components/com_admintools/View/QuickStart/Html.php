<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\View\QuickStart;

defined('_JEXEC') or die;

use Akeeba\AdminTools\Admin\Model\ControlPanel;
use Akeeba\AdminTools\Admin\Model\QuickStart;
use Akeeba\AdminTools\Admin\Model\ConfigureWAF;
use FOF30\Utils\Ip;
use FOF30\View\DataView\Html as BaseView;
use JFactory;

class Html extends BaseView
{
	/**
	 * The detected IP of the current visitor
	 *
	 * @var  string
	 */
	public $myIp = '';

	/**
	 * The configuration of WAF
	 *
	 * @var  array
	 */
	public $wafconfig = null;

	/**
	 * Is this the first run of the Quick Setup wizard, i.e. no existing configuration was detected?
	 *
	 * @var  bool
	 */
	public $isFirstRun = true;

	/**
	 * Username for the Password Protect Administrator Directory feature
	 *
	 * @var  string
	 */
	public $admin_username;

	/**
	 * Password for the Password Protect Administrator Directory feature
	 *
	 * @var  string
	 */
	public $admin_password;

	protected function onBeforeMain()
	{
		// Get the reported IP
		/** @var ControlPanel $cpanelModel */
		$cpanelModel = $this->container->factory->model('ControlPanel')->tmpInstance();
		$this->myIp = $cpanelModel->getVisitorIP();

		// Get the WAF configuration
		/** @var ConfigureWAF $wafConfigModel */
		$wafConfigModel = $this->getModel('ConfigureWAF');
		$this->wafconfig = $wafConfigModel->getConfig();

		// Create an admin password if necessary
		if (empty($this->wafconfig['adminpw']))
		{
			$this->wafconfig['adminpw'] = \JUserHelper::genRandomPassword(8);
		}

		// Populate email addresses if necessary
		if (empty($this->wafconfig['emailonadminlogin']))
		{
			$this->wafconfig['emailonadminlogin'] = JFactory::getUser()->email;
		}

		if (empty($this->wafconfig['emailbreaches']))
		{
			$this->wafconfig['emailbreaches'] = JFactory::getUser()->email;
		}

		// Get the administrator username/password
		$this->admin_username = '';
		$this->admin_password = '';

		/** @var QuickStart $model */
		$model = $this->getModel();
		$this->isFirstRun = $model->isFirstRun();

		$this->addJavascriptFile('admin://components/com_admintools/media/js/QuickStart.min.js');

		\JText::script('JNO', true);
		\JText::script('JYES', true);

		$js = <<< JS

;;

akeeba.jQuery(document).ready(function ($){
	admintools.QuickStart.myIP = '{$this->myIp}';
});
JS;

		$this->addJavascriptInline($js);
	}
}