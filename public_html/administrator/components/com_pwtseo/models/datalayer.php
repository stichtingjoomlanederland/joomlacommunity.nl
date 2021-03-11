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
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;

defined('_JEXEC') or die;

/**
 * Datalayer model.
 *
 * @since  1.3.0
 */
class PWTSEOModelDatalayer extends AdminModel
{
	/**
	 * The type alias for this content type.
	 *
	 * @var    string
	 * @since  1.3.0
	 */
	public $typeAlias = 'com_pwtseo.datalayer';

	/**
	 * Abstract method for getting the form from the model.
	 *
	 * @param   array   $data     Data for the form.
	 * @param   boolean $loadData True if the form is to load its own data (default case), false if not.
	 *
	 * @return  Form|boolean  A JForm object on success, false on failure
	 *
	 * @since   1.3.0
	 */
	public function getForm($data = array(), $loadData = true)
	{
		$form = $this->loadForm(
			'com_pwtseo.datalayer',
			'datalayer',
			array(
				'control'   => 'jform',
				'load_data' => $loadData
			)
		);

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string $type   The table name. Optional.
	 * @param   string $prefix The class prefix. Optional.
	 * @param   array  $config Configuration array for model. Optional.
	 *
	 * @return  Table  A Table object
	 *
	 * @since   1.3.0
	 */
	public function getTable($type = 'Datalayer', $prefix = 'PWTSEOTable', $config = array())
	{
		return Table::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array $data The form data.
	 *
	 * @return  boolean  True on success, False on error.
	 *
	 * @since   1.3.0
	 */
	public function save($data)
	{
		// We need to encode all of it separately
		array_walk(
			$data['fields'],
			static function (&$ele) {
				$ele = json_encode($ele);
			}
		);

		$data['fields'] = json_encode($data['fields']);

		return parent::save($data);
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   1.3.0
	 * @throws  Exception
	 */
	protected function loadFormData()
	{
		$data = Factory::getApplication()->getUserState(
			'com_pwtseo.edit.datalayer.data',
			array()
		);

		if (empty($data))
		{
			$data = $this->getItem();
		}

		// Decode everything again...
		$data->fields = json_decode($data->fields);

		if ($data->fields)
		{
			array_walk(
				$data->fields,
				static function (&$ele) {
					$ele = json_decode($ele);
				}
			);
		}

		return $data;
	}
}
