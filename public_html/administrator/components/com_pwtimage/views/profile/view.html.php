<?php
/**
 * @package    Pwtimage
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2020 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;

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
	 * @throws  Exception
	 * @since   1.1.0
	 *
	 */
	public function display($tpl = null)
	{
		/** @var PwtimageModelProfile $model */
		$model            = $this->getModel();
		$this->extensions = $model->getExtensions();
		$this->form       = $model->getForm();
		$this->item       = $model->getItem();
		$this->state      = $model->getState();
		$this->canDo      = ContentHelper::getActions('com_pwtimage');

		if (!$this->canDo->get('core.admin'))
		{
			Factory::$application->redirect('index.php?option=com_pwtimage');
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
	 * @since   1.1.0
	 *
	 */
	private function addToolbar()
	{
		Factory::getApplication()->input->set('hidemainmenu', true);

		ToolbarHelper::title(Text::_('COM_PWTIMAGE_PWTIMAGE_PROFILE'), 'pwtimage');

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

		if (0 === $this->item->id)
		{
			ToolbarHelper::cancel('profile.cancel');
		}
		else
		{
			ToolbarHelper::cancel('profile.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
