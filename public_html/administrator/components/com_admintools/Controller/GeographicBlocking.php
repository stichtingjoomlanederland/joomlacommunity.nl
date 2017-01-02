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
use FOF30\Container\Container;
use FOF30\Controller\Controller;

class GeographicBlocking extends Controller
{
	use PredefinedTaskList, CustomACL;

	public function __construct(Container $container, array $config)
	{
		parent::__construct($container, $config);

		$this->predefinedTaskList = ['browse', 'save', 'cancel'];
	}

	public function save()
	{
		// CSRF prevention
		$this->csrfProtection();

		$continents = $this->input->get('continent', array(), 'array', 2);

		if (empty($continents))
		{
			$continents = '';
		}
		else
		{
			$continents = array_keys($continents);
			$continents = implode(',', $continents);
		}

		$countries = $this->input->get('country', array(), 'array', 2);
		if (empty($countries))
		{
			$countries = '';
		}
		else
		{
			$countries = array_keys($countries);
			$countries = implode(',', $countries);
		}

		/** @var \Akeeba\AdminTools\Admin\Model\GeographicBlocking $model */
		$model = $this->getModel();
		$config = array('countries' => $countries, 'continents' => $continents);
		$model->saveConfig($config);

		$textkey = 'COM_ADMINTOOLS_LBL_GEOGRAPHICBLOCKING_SAVED';

		$url = 'index.php?option=com_admintools&view=WebApplicationFirewall';
		$this->setRedirect($url, \JText::_($textkey));
	}
}