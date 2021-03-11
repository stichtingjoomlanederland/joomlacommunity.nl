<?php
/**
 * @package    Pwtseo
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2021 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;

defined('_JEXEC') or die;

/**
 * View to perform a SEO check on a custom URL
 *
 * @since  1.1.0
 */
class PWTSEOViewCustom extends HtmlView
{
	/**
	 * The Form object
	 *
	 * @var    Form
	 * @since  1.1.0
	 */
	protected $form;

	/**
	 * The active item.
	 *
	 * @var    object
	 * @since  1.1.0
	 */
	protected $item;

	/**
	 * The model state
	 *
	 * @var    object
	 * @since  1.1.0
	 */
	protected $state;

	/**
	 * Display the view
	 *
	 * @param   string $tpl The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise an Error object.
	 *
	 * @since   1.0
	 */
	public function display($tpl = null)
	{
		// Initialise variables
		$this->form  = $this->get('Form');
		$this->item  = $this->get('Item');
		$this->state = $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));

			return false;
		}

		$this->addToolbar();

		return parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.1.0
	 */
	protected function addToolbar()
	{
		Factory::getApplication()->input->set('hidemainmenu', true);

		$isNew = ($this->item->id == 0);
		$canDo = ContentHelper::getActions('com_pwtseo');

		JToolbarHelper::title(Text::_('COM_PWTSEO_CUSTOM_ANALYSE'), 'pwtseo');

		// If not checked out, can save the item.
		if ($canDo->get('core.edit'))
		{
			JToolbarHelper::apply('custom.apply');
			JToolbarHelper::save('custom.save');

			if ($canDo->get('core.create'))
			{
				JToolbarHelper::save2new('custom.save2new');
			}
		}

		if ($isNew)
		{
			JToolbarHelper::cancel('custom.cancel');
		}
		else
		{
			JToolbarHelper::cancel('custom.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
