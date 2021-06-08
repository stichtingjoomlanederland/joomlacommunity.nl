<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\User\User;
use Joomla\Registry\Registry;

/**
 * Statuses view.
 *
 * @package  JDiDEAL
 * @since    4.9.0
 */
class JdidealgatewayViewStatuses extends HtmlView
{
	/**
	 * Array with profiles
	 *
	 * @var    array
	 * @since  4.9.0
	 */
	protected $items;

	/**
	 * Pagination class
	 *
	 * @var    Pagination
	 * @since  4.9.0
	 */
	protected $pagination;

	/**
	 * The user state
	 *
	 * @var    Registry
	 * @since  4.9.0
	 */
	protected $state;

	/**
	 * Access rights of a user
	 *
	 * @var    CMSObject
	 * @since  4.9.0
	 */
	protected $canDo;

	/**
	 * The sidebar to show
	 *
	 * @var    string
	 * @since  4.9.0
	 */
	protected $sidebar = '';

	/**
	 * The logged in use
	 *
	 * @var    User
	 * @since  5.0.0
	 */
	protected $loggedInUser;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a Error object.
	 *
	 * @throws  Exception
	 *
	 * @since   4.9.0
	 */
	public function display($tpl = null)
	{
		/** @var JdidealgatewayModelStatuses $model */
		$model = $this->getModel();
		$this->items	     = $model->getItems();
		$this->pagination    = $model->getPagination();
		$this->state         = $model->getState();
		$this->canDo         = ContentHelper::getActions('com_jdidealgateway');
		$this->loggedInUser  = Factory::getUser();

		$this->toolbar();

		if (JVERSION < 4)
		{
			$jdidealgatewayHelper = new JdidealGatewayHelper;
			$jdidealgatewayHelper->addSubmenu('statuses');
			$this->sidebar = JHtmlSidebar::render();
		}

		return parent::display($tpl);
	}

	/**
	 * Displays a toolbar for a specific page.
	 *
	 * @return  void
	 *
	 * @since   4.9.0
	 */
	private function toolbar()
	{
		ToolbarHelper::title(Text::_('COM_ROPAYMENTS_STATUSES'), 'checkin');

		if ($this->canDo->get('core.create'))
		{
			ToolbarHelper::addNew('status.add');
		}

		if ($this->canDo->get('core.edit') || $this->canDo->get('core.edit.own'))
		{
			ToolbarHelper::editList('status.edit');
		}

		if ($this->canDo->get('core.delete'))
		{
			ToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'statuses.delete', 'JTOOLBAR_DELETE');
		}
	}
}
