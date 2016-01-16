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
 * Slider Main Controller
 */
class SliderController extends JControllerLegacy
{
	protected $default_view = 'slides';

	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JControllerLegacy  This object to support chaining.
	 *
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$view   = $this->input->get('view', 'slides');
		$layout = $this->input->get('layout', 'default');
		$id     = $this->input->getInt('id');

		// Check for edit form.
		if ($view == 'slide' && $layout == 'edit' && !$this->checkEditId('com_slider.edit.slide', $id))
		{
			// Somehow the person just went to the form - we don't allow that.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(JRoute::_('index.php?option=com_slider&view=slides', false));

			return false;
		}

		return parent::display();
	}
}
