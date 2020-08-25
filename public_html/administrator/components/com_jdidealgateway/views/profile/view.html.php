<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2020 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\Registry\Registry;

/**
 * Profile view.
 *
 * @package  JDiDEAL
 * @since    4.0.0
 */
class JdidealgatewayViewProfile extends HtmlView
{
	/**
	 * RO Payments helper
	 *
	 * @var    JdIdealgatewayHelper
	 * @since  4.0.0
	 */
	protected $jdidealgatewayHelper;

	/**
	 * Form with settings
	 *
	 * @var    Form
	 * @since  4.0.0
	 */
	protected $form;

	/**
	 * Payment provider form with settings
	 *
	 * @var    Form
	 * @since  4.0
	 */
	protected $pspForm;

	/**
	 * The item object
	 *
	 * @var    object
	 * @since  4.0
	 */
	protected $item;

	/**
	 * Get the state
	 *
	 * @var    Registry
	 * @since  4.0
	 */
	protected $state;

	/**
	 * Holds the active payment provider
	 *
	 * @var    string
	 * @since  4.0.0
	 */
	protected $activeProvider = '';

	/**
	 * Check if the certificates exist
	 *
	 * @var    boolean
	 * @since  3.0.0
	 */
	protected $filesExist = false;

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
	 * @since   4.0.0
	 */
	public function display($tpl = null)
	{
		/** @var JdidealgatewayModelProfile $model */
		$model       = $this->getModel();
		$this->form  = $model->getForm();
		$this->item  = $model->getItem();
		$this->state = $model->getState();
		$this->canDo = ContentHelper::getActions('com_jdidealgateway');

		// Load the PSP form
		$this->pspForm = $model->getPspForm($this->item->psp);

		if ($this->pspForm)
		{
			$this->pspForm->bind($this->item->paymentInfo);
		}

		// Set the active provider
		$this->activeProvider = $this->item->psp;

		// Clear the user state
		Factory::getApplication()->setUserState('profile.psp', false);

		// Check if the security files exist for advanced
		if ($this->item->psp === 'advanced')
		{
			$this->filesExist = $model->getFilesExist();
		}

		// Add the toolbar
		$this->addToolbar();

		// Display it all
		return parent::display($tpl);
	}

	/**
	 * Displays a toolbar for a specific page.
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 *
	 * @since   4.0.0
	 */
	private function addToolbar()
	{
		Factory::getApplication()->input->set('hidemainmenu', true);

		ToolbarHelper::title(Text::_('COM_ROPAYMENTS_JDIDEAL_PROFILE'), 'user');

		if ($this->canDo->get('core.edit') || $this->canDo->get('core.create'))
		{
			ToolbarHelper::apply('profile.apply');
			ToolbarHelper::save('profile.save');
		}

		if ($this->canDo->get('core.create') && $this->canDo->get('core.manage'))
		{
			ToolbarHelper::save2new('profile.save2new');
		}

		if ($this->canDo->get('core.create'))
		{
			ToolbarHelper::save2copy('profile.save2copy');
		}

		if (0 === (int) $this->item->id)
		{
			ToolbarHelper::cancel('profile.cancel');
		}
		else
		{
			ToolbarHelper::cancel('profile.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
