<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2020 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Toolbar\ToolbarHelper;

defined('_JEXEC') or die;

/**
 * Emails list.
 *
 * @package  JDiDEAL
 * @since    2.0.0
 */
class JdidealgatewayViewEmails extends HtmlView
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
	protected $items = array();

	/**
	 * The pagination object
	 *
	 * @var    Pagination
	 * @since  1.0.0
	 */
	protected $pagination;

	/**
	 * Access rights of a user
	 *
	 * @var    CMSObject
	 * @since  4.0.0
	 */
	protected $canDo;

	/**
	 * The sidebar to show
	 *
	 * @var    string
	 * @since  4.0.0
	 */
	protected $sidebar = '';

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 *
	 * @throws  Exception
	 *
	 * @since   2.0.0
	 */
	public function display($tpl = null)
	{
		/** @var JdidealgatewayModelEmails $model */
		$model            = $this->getModel();
		$this->items      = $model->getItems();
		$this->pagination = $model->getPagination();
		$this->canDo      = ContentHelper::getActions('com_jdidealgateway');

		// Show the toolbar
		$this->toolbar();

		// Render the sidebar
		$this->jdidealgatewayHelper = new JdidealGatewayHelper;
		$this->jdidealgatewayHelper->addSubmenu('emails');
		$this->sidebar = JHtmlSidebar::render();

		// Display it all
		return parent::display($tpl);
	}

	/**
	 * Displays a toolbar for a specific page.
	 *
	 * @return  void
	 *
	 * @since   2.0.0
	 */
	private function toolbar()
	{
		ToolbarHelper::title(Text::_('COM_ROPAYMENTS_JDIDEAL_EMAILS'), 'mail');

		if ($this->canDo->get('core.create'))
		{
			ToolbarHelper::addNew('email.add');
		}

		if ($this->canDo->get('core.edit') || $this->canDo->get('core.edit.own'))
		{
			ToolbarHelper::editList('email.edit');
		}

		if ($this->canDo->get('core.delete'))
		{
			ToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'emails.delete', 'JTOOLBAR_DELETE');
		}

		if ($this->canDo->get('core.create'))
		{
			ToolbarHelper::custom('emails.testemail', 'mail', 'mail', Text::_('COM_ROPAYMENTS_SEND_TESTMAIL'));
		}
	}
}
