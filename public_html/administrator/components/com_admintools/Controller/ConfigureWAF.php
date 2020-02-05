<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\Controller;

defined('_JEXEC') or die;

use Akeeba\AdminTools\Admin\Controller\Mixin\CustomACL;
use Akeeba\AdminTools\Admin\Controller\Mixin\PredefinedTaskList;
use Akeeba\AdminTools\Admin\Controller\Mixin\SendTroubleshootingEmail;
use FOF30\Container\Container;
use FOF30\Controller\Controller;
use JText;

class ConfigureWAF extends Controller
{
	use PredefinedTaskList, CustomACL, SendTroubleshootingEmail;

	public function __construct(Container $container, array $config)
	{
		parent::__construct($container, $config);

		$this->predefinedTaskList = ['default', 'save', 'apply'];
	}

	public function apply()
	{
		/** @var \Akeeba\AdminTools\Admin\Model\ConfigureWAF $model */
		$model = $this->getModel();

		if (is_array($this->input))
		{
			$data = $this->input;
		}
		else
		{
			$data = $this->input->getData();
		}

		$this->sendTroubelshootingEmail($this->getName());

		$model->saveConfig($data);

		$this->setRedirect('index.php?option=com_admintools&view=ConfigureWAF', JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_CONFIGSAVED'));
	}

	public function save()
	{
		/** @var \Akeeba\AdminTools\Admin\Model\ConfigureWAF $model */
		$model = $this->getModel();

		if (is_array($this->input))
		{
			$data = $this->input;
		}
		else
		{
			$data = $this->input->getData();
		}

		// Chosen is pretty bad; it removes the entire field from the request when it's empty :(
		if (!array_key_exists('disableobsoleteadmins_protected', $data))
		{
			$data['disableobsoleteadmins_protected'] = [];
		}

		$this->sendTroubelshootingEmail($this->getName());

		$model->saveConfig($data);

		$this->setRedirect('index.php?option=com_admintools&view=WebApplicationFirewall', JText::_('COM_ADMINTOOLS_LBL_CONFIGUREWAF_CONFIGSAVED'));
	}
}
