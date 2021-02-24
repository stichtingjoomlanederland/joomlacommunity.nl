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
use Joomla\CMS\MVC\Model\AdminModel;

defined('_JEXEC') or die;

/**
 * Message model.
 *
 * @package  JDiDEAL
 * @since    4.0.0
 */
class JdidealgatewayModelMessage extends AdminModel
{
	/**
	 * Get the form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success | False on failure.
	 *
	 * @throws  Exception
	 *
	 * @since   4.0.0
	 */
	public function getForm($data = array(), $loadData = true): Form
	{
		// Get the form.
		$form = $this->loadForm(
			'com_jdidealgateway.message',
			'message',
			array('control' => 'jform', 'load_data' => $loadData)
		);

		if (!is_object($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  array  The data for the form.
	 *
	 * @since   4.0.0
	 *
	 * @throws  Exception
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState('com_jdidealgateway.edit.message.data', array());

		if (0 === count($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Save the configuration.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success or false on failure.
	 *
	 * @throws  Exception
	 *
	 * @since   4.5.0
	 */
	public function save($data): bool
	{
		$input = Factory::getApplication()->input;

		// Alter the title for save as copy
		if ($input->get('task') == 'save2copy')
		{
			// Unset the ID so a new item is created
			unset($data['id']);
		}

		// Save the profile
		return parent::save($data);
	}
}
