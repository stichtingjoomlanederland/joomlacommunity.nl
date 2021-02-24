<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

use Jdideal\Addons\Addon;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

defined('_JEXEC') or die;

/**
 * Dashboard view.
 *
 * @package  JDiDEAL
 * @since    2.0.0
 */
class JdidealgatewayViewJdidealgateway extends HtmlView
{
	/**
	 * RO Payments helper
	 *
	 * @var    JdidealGatewayHelper
	 * @since  4.0.0
	 */
	protected $jdidealgatewayHelper;

	/**
	 * List of properties
	 *
	 * @var    array
	 * @since  1.0.0
	 */
	protected $items = [];

	/**
	 * List of addons that have been loaded
	 *
	 * @var    Addon
	 * @since  4.0.0
	 */
	protected $addons;

	/**
	 * The sidebar to show
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $sidebar = '';

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 *
	 * @throws  Exception
	 *
	 * @since   1.0.0
	 */
	public function display($tpl = null)
	{
		/** @var JdidealgatewayModelJdidealgateway $model */
		$model = $this->getModel();
		$model->checkSystemRequirements();

		$this->jdidealgatewayHelper = new JdidealGatewayHelper;

		// Get the model
		/** @var JdidealgatewayModelLogs $logsModel */
		$logsModel = BaseDatabaseModel::getInstance('Logs', 'JdidealgatewayModel');

		$this->items = $logsModel->getItems();

		$this->addons = new Addon;

		$this->toolbar();

		if (JVERSION < 4)
		{
			$this->jdidealgatewayHelper->addSubmenu('jdidealgateway');
			$this->sidebar = JHtmlSidebar::render();
		}

		return parent::display($tpl);
	}

	/**
	 * Displays a toolbar for a specific page.
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 *
	 * @throws  Exception
	 */
	private function toolbar(): void
	{
		// Use our own layout because Joomla forces the use of the icon- prefix
		$layout = new FileLayout('joomla.toolbar.jdtitle');
		$html   = $layout->render(['title' => Text::_('COM_ROPAYMENTS_JDIDEAL'), 'icon' => 'jdideal']);

		$app = Factory::getApplication();
		$app->JComponentTitle = $html;
		Factory::getDocument()->setTitle(
			$app->get('sitename') . ' - ' . Text::_('JADMINISTRATION') . ' - ' . strip_tags(Text::_('COM_ROPAYMENTS_JDIDEAL'))
		);

		if (Factory::getUser()->authorise('core.admin', 'com_jdidealgateway'))
		{
			ToolbarHelper::preferences('com_jdidealgateway');
		}
	}
}
