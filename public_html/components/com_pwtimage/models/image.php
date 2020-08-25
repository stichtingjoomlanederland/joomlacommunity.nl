<?php
/**
 * @package    Pwtimage
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2020 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\MVC\Model\FormModel;

defined('_JEXEC') or die;

/**
 * Images model.
 *
 * @since       1.0
 */
class PwtimageModelImage extends FormModel
{
	/**
	 * A notice to be shown to the user.
	 *
	 * @var    string
	 * @since  1.0
	 */
	private $message = '';

	/**
	 * Get the form.
	 *
	 * @param   array   $data     Data for the form.
	 * @param   boolean $loadData True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success | False on failure.
	 *
	 * @since   1.0
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		FormHelper::addFormPath(JPATH_SITE . '/components/com_pwtimage/models/forms');

		return $this->loadForm('com_pwtimage.image', 'image', array('control' => 'jform', 'load_data' => $loadData));
	}

	/**
	 * Get the message.
	 *
	 * @return  string  The message string.
	 *
	 * @since   1.1.0
	 */
	public function getMessage()
	{
		return $this->message;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  array  The data for the form..
	 *
	 * @since   4.0
	 *
	 * @throws  Exception
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState('com_pwtimage.edit.image.data', array());

		if (0 === count($data))
		{
			$data = new stdClass;
		}

		return $data;
	}
}
