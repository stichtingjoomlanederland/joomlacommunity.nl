<?php
/**
 * @package    Pwtimage
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2019 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;

defined('_JEXEC') or die;

/**
 * Profile view.
 *
 * @package  Pwtimage
 * @since    1.1.0
 */
class PwtimageViewProfile extends HtmlView
{
	/**
	 * Form with settings
	 *
	 * @var    Joomla\CMS\Form\Form
	 * @since  1.1.0
	 */
	protected $form;

	/**
	 * The item object
	 *
	 * @var    object
	 * @since  1.1.0
	 */
	protected $item;

	/**
	 * Get the state
	 *
	 * @var    object
	 * @since  1.1.0
	 */
	protected $state;

	/**
	 * A list of image fields that can be configured
	 *
	 * @var    array
	 * @since  1.1.0
	 */
	protected $extensions = array();

	/**
	 * Access rights of a user
	 *
	 * @var    JObject
	 * @since  1.1.0
	 */
	protected $canDo;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 *
	 * @since   1.1.0
	 *
	 * @throws  Exception
	 */
	public function display($tpl = null)
	{
		/** @var PwtimageModelProfile $model */
		$model            = $this->getModel();
		$this->extensions = $model->getExtensions();
		$this->form       = $model->getForm();
		$this->item       = $model->getItem();
		$this->state      = $model->getState();
		$this->canDo      = JHelperContent::getActions('com_pwtimage');

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
	 * @since   1.1.0
	 *
	 * @throws  Exception
	 */
	private function addToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);

		JToolbarHelper::title(Text::_('COM_PWTIMAGE_PWTIMAGE_PROFILE'), 'pwtimage');

		if ($this->canDo->get('core.edit') || $this->canDo->get('core.create'))
		{
			JToolbarHelper::apply('profile.apply');
			JToolbarHelper::save('profile.save');
		}

		if ($this->canDo->get('core.create') && $this->canDo->get('core.manage'))
		{
			JToolbarHelper::save2new('profile.save2new');
		}

		if ($this->canDo->get('core.create'))
		{
			JToolbarHelper::save2copy('profile.save2copy');
		}

		if (0 === $this->item->id)
		{
			JToolbarHelper::cancel('profile.cancel');
		}
		else
		{
			JToolbarHelper::cancel('profile.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
