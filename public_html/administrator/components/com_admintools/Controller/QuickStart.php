<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\Controller;

defined('_JEXEC') or die;

use Akeeba\AdminTools\Admin\Controller\Mixin\CustomACL;
use Akeeba\AdminTools\Admin\Controller\Mixin\PredefinedTaskList;
use Akeeba\AdminTools\Admin\Model\ConfigureWAF;
use FOF30\Container\Container;
use FOF30\Controller\Controller;

class QuickStart extends Controller
{
	use PredefinedTaskList, CustomACL;

	public function __construct(Container $container, array $config)
	{
		parent::__construct($container, $config);

		$this->predefinedTaskList = ['browse', 'commit', 'cancel'];
	}

	public function commit()
	{
		// CSRF prevention
		$this->csrfProtection();

		/** @var \Akeeba\AdminTools\Admin\Model\QuickStart $model */
		$model = $this->getModel();

		$stateVariables = array(
			'adminpw', 'admin_username', 'admin_password', 'emailonadminlogin', 'ipwl', 'detectedip', 'nonewadmins',
			'nofesalogin', 'enablewaf', 'ipworkarounds', 'autoban', 'autoblacklist', 'emailbreaches', 'bbhttpblkey',
			'htmaker'
		);

		foreach ($stateVariables as $k)
		{
			$v = $this->input->get($k, null, 'raw', 2);
			$model->setState($k, $v);
		}

		$model->applyPreferences();

		$message = \JText::_('COM_ADMINTOOLS_QUICKSTART_MSG_DONE');
		$this->setRedirect('index.php?option=com_admintools&view=ControlPanel', $message);
	}

	public function onBeforeBrowse()
	{
		/** @var ConfigureWAF $wafConfigModel */
		$wafConfigModel = $this->getModel('ConfigureWAF');
		$this->getView()->setModel('ConfigureWAF', $wafConfigModel);
	}
}