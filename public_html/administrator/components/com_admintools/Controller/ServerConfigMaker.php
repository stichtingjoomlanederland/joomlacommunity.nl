<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2021 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\Controller;

defined('_JEXEC') || die;

use Akeeba\AdminTools\Admin\Controller\Mixin\CustomACL;
use Akeeba\AdminTools\Admin\Controller\Mixin\PredefinedTaskList;
use Akeeba\AdminTools\Admin\Controller\Mixin\SendTroubleshootingEmail;
use FOF40\Container\Container;
use FOF40\Controller\Controller;
use Joomla\CMS\Language\Text;

class ServerConfigMaker extends Controller
{
	use PredefinedTaskList, CustomACL, SendTroubleshootingEmail;

	/**
	 * The prefix for the language strings of the information and error messages
	 *
	 * @var string
	 */
	protected $langKeyPrefix = 'COM_ADMINTOOLS_LBL_HTACCESSMAKER_';

	public function __construct(Container $container, array $config)
	{
		parent::__construct($container, $config);

		$this->predefinedTaskList = ['browse', 'preview', 'save', 'apply', 'reset'];
	}

	public function preview()
	{
		parent::display(false);
	}

	public function save()
	{
		// CSRF prevention
		$this->csrfProtection();

		/** @var \Akeeba\AdminTools\Admin\Model\ServerConfigMaker $model */
		$model = $this->getModel();

		$data = $this->input->getData();

		// Wrong $live_site value, force no wwwredir
		if (!$model->enableRedirects())
		{
			$data['wwwredir'] = 0;
		}

		$model->saveConfiguration($data);

		$this->setRedirect('index.php?option=com_admintools&view=' . $this->view, Text::_($this->langKeyPrefix . 'SAVED'));
	}

	public function apply()
	{
		/** @var \Akeeba\AdminTools\Admin\Model\ServerConfigMaker $model */
		$model = $this->getModel();

		$data = $this->input->getData();

		// Wrong $live_site value, force no wwwredir
		if (!$model->enableRedirects())
		{
			$data['wwwredir'] = 0;
		}

		$this->sendTroubelshootingEmail($this->getName());
		$model->saveConfiguration($data);

		$status = $model->writeConfigFile();

		if (!$status)
		{
			$this->setRedirect('index.php?option=com_admintools&view=' . $this->view, Text::_($this->langKeyPrefix . 'NOTAPPLIED'), 'error');

			return;
		}

		$this->setRedirect('index.php?option=com_admintools&view=' . $this->view, Text::_($this->langKeyPrefix . 'APPLIED'));
	}

	public function reset()
	{
		$this->csrfProtection();

		/** @var \Akeeba\AdminTools\Admin\Model\ServerConfigMaker $model */
		$model = $this->getModel();

		// Fetch the default config from the model and save it again
		$default_options = $model->defaultConfig;
		$model->saveConfiguration($default_options, true);

		$this->setRedirect('index.php?option=com_admintools&view=' . $this->view, Text::_($this->langKeyPrefix . 'RESET_DONE'));
	}
}
