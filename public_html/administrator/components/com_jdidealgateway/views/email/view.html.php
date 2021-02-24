<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Toolbar\ToolbarHelper;

defined('_JEXEC') or die;

/**
 * Email view.
 *
 * @package  JDiDEAL
 * @since    2.0.0
 */
class JdidealgatewayViewEmail extends HtmlView
{
	/**
	 * Form with settings
	 *
	 * @var    Form
	 * @since  4.0.0
	 */
	protected $form;

	/**
	 * The item object
	 *
	 * @var    object
	 * @since  4.0.0
	 */
	protected $item;

	/**
	 * Get the state
	 *
	 * @var    object
	 * @since  4.0.0
	 */
	protected $state;

	/**
	 * Access rights of a user
	 *
	 * @var    CMSObject
	 * @since  4.0.0
	 */
	protected $canDo;

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
		/** @var JdidealgatewayModelEmail $model */
		$model       = $this->getModel();
		$this->form  = $model->getForm();
		$this->item  = $model->getItem();
		$this->state = $model->getState();
		$this->canDo = ContentHelper::getActions('com_jdidealgateway');

		// Add the toolbar
		$this->addToolbar();

		// Display it all
		return parent::display($tpl);
	}

	/**
	 * Displays a toolbar for a specific page.
	 *
	 * @return  void.
	 *
	 * @throws  Exception
	 *
	 * @since   2.0.0
	 */
	private function addToolbar()
	{
		Factory::getApplication()->input->set('hidemainmenu', true);

		ToolbarHelper::title(Text::_('COM_ROPAYMENTS_JDIDEAL_EMAIL'), 'mail');

		if ($this->canDo->get('core.edit') || $this->canDo->get('core.create'))
		{
			ToolbarHelper::apply('email.apply');
			ToolbarHelper::save('email.save');
		}

		if ($this->canDo->get('core.create') && $this->canDo->get('core.manage'))
		{
			ToolbarHelper::save2new('email.save2new');
		}

		if (!$this->item->id)
		{
			ToolbarHelper::cancel('email.cancel');
		}
		else
		{
			ToolbarHelper::cancel('email.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
