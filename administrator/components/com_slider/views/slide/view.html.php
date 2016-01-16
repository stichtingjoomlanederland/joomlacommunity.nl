<?php
/**
 * @package     Slider
 * @subpackage  com_slider
 *
 * @copyright   Copyright (C) 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Slider view
 */
class SliderViewSlide extends JViewLegacy
{
	/**
	 * View form
	 *
	 * @var    form
	 */
	protected $form = null;

	/**
	 * Display the slider
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		// Get the Data
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));

			return false;
		}


		// Set the toolbar
		$this->addToolBar();

		// Display the template
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolBar()
	{
		$input = JFactory::getApplication()->input;

		// Hide Joomla Administrator Main menu
		$input->set('hidemainmenu', true);

		$isNew = ($this->item->id == 0);

		if ($isNew)
		{
			$title = JText::_('COM_SLIDER_MANAGER_SLIDE_NEW');
		}
		else
		{
			$title = JText::_('COM_SLIDER_MANAGER_SLIDE_EDIT');
		}

		JToolBarHelper::title($title, 'pencil-2');
		JToolBarHelper::apply('slide.apply');
		JToolBarHelper::save('slide.save');
		JToolBarHelper::save2new('slide.save2new');
		JToolBarHelper::cancel(
			'slide. cancel',
			$isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE'
		);
	}
}
