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
use JText;
use JUri;

class Scanner extends Controller
{
	use PredefinedTaskList, CustomACL;

	public function __construct(Container $container, array $config)
	{
		parent::__construct($container, $config);

		$this->predefinedTaskList = ['browse', 'save', 'apply'];
	}

	/**
	 * Handle the apply task which saves settings and shows the editor again
	 *
	 */
	public function apply()
	{
		$this->csrfProtection();

		/** @var \Akeeba\AdminTools\Admin\Model\Scanner $model */
		$model = $this->getModel();
		$model->setState('rawinput', $this->input);
		$model->saveConfiguration();

		$this->setRedirect(JUri::base() . 'index.php?option=com_admintools&view=Scanner', JText::_('COM_ADMINTOOLS_LBL_SCANNER_CONFIGURATIONSAVED'));
	}

	/**
	 * Handle the save task which saves settings and returns to the cpanel
	 *
	 */
	public function save()
	{
		$this->apply();
		$this->setRedirect(JUri::base() . 'index.php?option=com_admintools&view=Scans', JText::_('COM_ADMINTOOLS_LBL_SCANNER_CONFIGURATIONSAVED'));
	}
}