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
use Joomla\CMS\Form\Form;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\Registry\Registry;

/**
 * Customer view.
 *
 * @package  JDiDEAL
 * @since    5.0.0
 */
class JdidealgatewayViewCustomer extends HtmlView
{
	/**
	 * Form with settings
	 *
	 * @var    Form
	 * @since  5.0.0
	 */
	protected $form;

	/**
	 * The item object
	 *
	 * @var    object
	 * @since  5.0.0
	 */
	protected $item;

	/**
	 * List of mandates
	 *
	 * @var    array
	 * @since  5.0.0
	 */
	protected $mandates = [];

	/**
	 * List of subscriptions
	 *
	 * @var    array
	 * @since  5.0.0
	 */
	protected $subscriptions = [];

	/**
	 * Get the state
	 *
	 * @var    object
	 * @since  5.0.0
	 */
	protected $state;

	/**
	 * Access rights of a user
	 *
	 * @var    Registry
	 * @since  4.0.0
	 */
	protected $canDo;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 *
	 * @since   5.0.0
	 */
	public function display($tpl = null)
	{
		/** @var JdidealgatewayModelCustomer $model */
		$model               = $this->getModel();
		$this->form          = $model->getForm();
		$this->item          = $model->getItem();
		$this->mandates      = $this->item->email ? $model->getMandates($this->item->email, (int) $this->item->profileId) : [];
		$this->subscriptions = $this->item->email ? $model->getSubscriptions($this->item->email, (int) $this->item->profileId) : [];
		$this->state         = $model->getState();
		$this->canDo         = ContentHelper::getActions('com_jdidealgateway');

		$this->addToolbar();

		parent::display($tpl);
	}

	/**
	 * Displays a toolbar for a specific page.
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 *
	 * @since   5.0.0
	 */
	private function addToolbar(): void
	{
		Factory::getApplication()->input->set('hidemainmenu', true);

		ToolbarHelper::title(Text::_('COM_ROPAYMENTS_CUSTOMER'), 'user');

		if ($this->canDo->get('core.edit') || $this->canDo->get('core.create'))
		{
			ToolbarHelper::apply('customer.apply');
			ToolbarHelper::save('customer.save');
		}

		if ($this->canDo->get('core.create') && $this->canDo->get('core.manage'))
		{
			ToolbarHelper::save2new('customer.save2new');
		}

		if (0 === $this->item->id)
		{
			ToolbarHelper::cancel('customer.cancel');
		}
		else
		{
			ToolbarHelper::cancel('customer.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
