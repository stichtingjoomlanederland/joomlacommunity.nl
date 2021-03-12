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
use Joomla\Uri\Uri;

defined('_JEXEC') or die;

/**
 * URL model.
 *
 * @since  1.1.0
 */
class PWTSEOModelCustom extends AdminModel
{
	/**
	 * The type alias for this content type.
	 *
	 * @var    string
	 * @since  1.1.0
	 */
	public $typeAlias = 'com_pwtseo.custom';

	/**
	 * Abstract method for getting the form from the model.
	 *
	 * @param   array   $data     Data for the form.
	 * @param   boolean $loadData True if the form is to load its own data (default case), false if not.
	 *
	 * @return  Form|boolean  A JForm object on success, false on failure
	 *
	 * @since   1.1.0
	 */
	public function getForm($data = array(), $loadData = true)
	{
		$form = $this->loadForm(
			'com_pwtseo.custom',
			'custom',
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
	 * @since   1.1.0
	 */
	public function getTable($type = 'Custom', $prefix = 'PWTSEOTable', $config = array())
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
	 * @since   1.1.0
	 * @throws  Exception
	 */
	public function save($data)
	{
		if (isset($data['pwtseo']))
		{
			$data = array_merge($data, $data['pwtseo']);

			// Due to form constraints, we have the wrong name on the field
			$data['pwtseo_score'] = $data['pwtseo_pwtseo_score'];
		}

		if (isset($data['url']))
		{
			$data['url'] = $this->getPath($data['url']);
		}

		// Check for datalayers
		$aDataLayers = Factory::getApplication()->input->post->get('pwtseo', array(), 'array');

		if (isset($aDataLayers['datalayers']))
		{
			$db = Factory::getDbo();

			foreach ($aDataLayers['datalayers'] as $id => $values)
			{
				$item = (object) array(
					'context_id'   => $data['id'],
					'context'      => 'com_pwtseo.custom',
					'datalayer_id' => $id,
					'values'       => json_encode($values)
				);

				try
				{
					$db->insertObject('#__plg_pwtseo_datalayers_map', $item);
				}
				catch (Exception $e)
				{
					$db->updateObject('#__plg_pwtseo_datalayers_map', $item, array('context_id', 'context', 'datalayer_id'));
				}
			}
		}

		return parent::save($data);
	}

	/**
	 * Returns the path for a given url
	 *
	 * @param   string $url The url to transform to path
	 *
	 * @return  string The path of the url
	 *
	 * @since   1.1.0
	 */
	private function getPath($url)
	{
		if (stripos($url, 'http') !== 0 && stripos($url, '/') !== 0)
		{
			$url = '/' . $url;
		}

		$uri = new Uri($url);

		return $uri->getPath();
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   1.1.0
	 * @throws  Exception
	 */
	protected function loadFormData()
	{
		$data = JFactory::getApplication()->getUserState(
			'com_pwtseo.edit.custom.data',
			array()
		);

		if (empty($data))
		{
			$data = $this->getItem();
		}

		$data->pwtseo = clone $data;

		$db = Factory::getDbo();

		$query = $db->getQuery(true)
			->select(
				array_merge(
					array(
						$db->quote($data->id) . ' AS context_id',
						$db->quote('com_pwtseo.custom') . ' AS context'
					),
					$db->quoteName(
						array(
							'layers.id',
							'layers.title',
							'layers.name',
							'layers.fields',
							'layers.language',
							'layers.template',

						),
						array(
							'id',
							'title',
							'name',
							'fields',
							'language',
							'template'
						)
					)
				)
			)
			->from($db->quoteName('#__plg_pwtseo_datalayers', 'layers'))
			->where($db->quoteName('layers.published') . ' = 1');

		$data->pwtseo->datalayers = json_encode($db->setQuery($query)->loadObjectList());

		// $this->preprocessData('com_pwtseo.custom', $data);

		return $data;
	}
}
