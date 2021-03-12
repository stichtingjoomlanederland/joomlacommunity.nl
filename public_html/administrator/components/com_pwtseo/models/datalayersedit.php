<?php
/**
 * @package    Pwtseo
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2021 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

use Joomla\CMS\Dispatcher\Dispatcher;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Table\Table;
use Joomla\Filter\InputFilter;
use Symfony\Component\EventDispatcher\EventDispatcher;

defined('_JEXEC') or die;

/**
 * Datalayers model.
 *
 * @since  1.3.1
 */
class PWTSEOModelDataLayersEdit extends AdminModel
{
	/**
	 * @var    InputFilter
	 * @since  1.3.1
	 */
	protected $inputFilter;

	/**
	 * The type alias for this content type.
	 *
	 * @var    string
	 * @since  1.3.1
	 */
	public $typeAlias = 'com_pwtseo.datalayersedit';

	/**
	 * Abstract method for getting the form from the model.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  Form|boolean  A JForm object on success, false on failure
	 *
	 * @since   1.3.1
	 */
	public function getForm($data = array(), $loadData = true)
	{
		/** @var Form $form */
		$form = $this->loadForm(
			'com_pwtseo.datalayersedit',
			'datalayersedit',
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
	 * Method to validate the form data.
	 *
	 * @param   \JForm  $form   The form to validate against.
	 * @param   array   $data   The data to validate.
	 * @param   string  $group  The name of the field group to validate.
	 *
	 * @return  array|boolean  Array of filtered data if valid, false otherwise.
	 *
	 * @see     \JFormRule
	 * @see     \JFilterInput
	 * @since   1.3.1
	 */
	public function validate($form, $data, $group = null)
	{
		// Due to the way we create the form, we have to do manual filter.
		$this->inputFilter = new InputFilter;

		$this->filterFields($data);

		return $data;
	}

	/**
	 * Method to filter the fields of the form data
	 *
	 * @param   array  $fields  The fields to apply the filter to.
	 *
	 * @since   1.3.1
	 */
	protected function filterFields(&$fields)
	{
		foreach ($fields as $key => &$field)
		{
			if (is_array($field))
			{
				$this->filterFields($field);
			}
			else
			{
				$field = $this->inputFilter->clean($field, 'STRING');
			}
		}
	}


	/**
	 * Fill the form according to the layers in the database. We store the used languages and templates in the form
	 * because that object will always pass to the view.
	 *
	 * @param   JForm   $form
	 * @param   mixed   $data
	 * @param   string  $group
	 *
	 * @throws Exception
	 */
	protected function preprocessForm(\JForm $form, $data, $group = 'content')
	{
		$db = Factory::getDbo();

		$contextId = $this->getState('pwtseo.context_id');
		$context   = $this->getState('pwtseo.context');

		$query = $db->getQuery(true)
			->select(
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
			->select($db->quote($contextId) . ' AS context_id')
			->select($db->quote($context) . ' AS context')
			->from($db->quoteName('#__plg_pwtseo_datalayers', 'layers'))
			->where($db->quoteName('layers.published') . ' = 1');

		// We want to filter the layers so we show only the ones that apply to the language of the current item
		if (in_array($context, array('com_content.article', 'com_menus.item')))
		{
			$contextTable = ($context === 'com_content.article' ? '#__content' : '#__menu');

			// We use a subQuery because wouldn't fit in single query because language can be either * or blank, in which case we don't want to filter on language at all
			$language = $db->getQuery(true);
			$language
				->select($db->quoteName('language'))
				->from($contextTable)
				->where($db->quoteName('id') . ' = ' . $contextId);

			$language = $db->setQuery($language)->loadResult();

			if ($language && $language !== '*')
			{
				$query->where(
					$db->quoteName('layers.language') . ' = ' . $db->quote($language)
					. ' OR ' . $db->quoteName('layers.language') . ' = ' . $db->quote('*')
				);
			}
		}

		$layers = $db->setQuery($query)->loadObjectList();

		// Keep track of all additional settings, we need to query them later
		$templates = array();
		$languages = array();

		if ($layers)
		{
			foreach ($layers as $layer)
			{
				$xml    = '<form><fields name="pwtseo">';
				$xml    .= '<fieldset name="' . $layer->name . '" label="' . $layer->title . '" language="' . $layer->language . '" template="' . $layer->template . '">';
				$fields = json_decode($layer->fields);

				$templates[] = $layer->template;
				$languages[] = $layer->language;

				foreach ($fields as $field)
				{
					$data = json_decode($field);

					/**
					 * The brackets here are weird because the Form puts it's own around the name
					 */
					$name = 'datalayers][' . $layer->id . '][' . $data->name;

					switch ($data->type)
					{
						case 'select':
							$xml .= '<field name="' . $name . '" type="list" label="' . $data->label . '"' . ($data->required ? ' required="true"' : '') . ' >';

							foreach ($data->options as $option)
							{
								if (isset($data->value) && $data->value && $option->value == $data->value)
								{
									$xml .= '<option value="' . $option->value . '" selected>' . $option->label . '</option>';
								}
								else
								{
									$xml .= '<option value="' . $option->value . '">' . $option->label . '</option>';
								}
							}

							$xml .= '</field>';

							break;
						case 'radio':
							break;
						case 'text':
						default:
							$xml .= '<field name="' . $name . '" type="text" label="' . $data->label . '" ' . (isset($data->default) ? 'default="' . $data->default . '"' : '') . ($data->required ? ' required="true"' : '') . ' />';
							break;
					}
				}

				$xml .= '</fieldset></fields></form>';

				$form->load($xml);
			}
		}

		if ($templates)
		{
			$query
				->clear()
				->select(
					$db->quoteName(
						array(
							'id',
							'title'
						)
					)
				)
				->from($db->quoteName('#__template_styles', 'template'))
				->where($db->quoteName('id') . ' IN (' . implode(', ', $templates) . ')');

			$form->templates = $db->setQuery($query)->loadObjectList('id');
		}

		if ($languages)
		{
			$query
				->clear()
				->select(
					$db->quoteName(
						array(
							'lang_code',
							'title',
							'image'
						),
						array(
							'language',
							'language_title',
							'language_image'
						)
					)
				)
				->from($db->quoteName('#__languages', 'language'))
				->where($db->quoteName('lang_code') . ' IN (' . implode(',', $db->quote($languages)) . ')');

			$form->languages = $db->setQuery($query)->loadObjectList('language');
		}

		parent::preprocessForm($form, $data, $group);
	}


	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $type    The table name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  Table  A Table object
	 *
	 * @since   1.3.1
	 */
	public function getTable($type = 'DatalayerEdit', $prefix = 'PWTSEOTable', $config = array())
	{
		return Table::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @throws  Exception
	 * @since   1.3.1
	 */
	public function save($data)
	{
		$app = Factory::getApplication();

		try
		{
			$pwtseo = $data['pwtseo'];

			foreach ($pwtseo['datalayers'] as $index => $datalayer)
			{
				$obj = (object) array(
					'context'      => $pwtseo['context'],
					'context_id'   => $pwtseo['context_id'],
					'datalayer_id' => $index,
					'values'       => json_encode($datalayer)
				);

				try
				{
					Factory::getDbo()->insertObject('#__plg_pwtseo_datalayers_map', $obj, array('context', 'context_id', 'datalayer_id'));
				}
				catch (Exception $e)
				{
					Factory::getDbo()->updateObject('#__plg_pwtseo_datalayers_map', $obj, array('context', 'context_id', 'datalayer_id'));
				}
			}

			$app->enqueueMessage(Text::_('COM_PWTSEO_DATALAYERS_SAVE_SUCCESS'));
		}
		catch (Exception $e)
		{
			$app->enqueueMessage(Text::_('COM_PWTSEO_DATALAYERS_SAVE_ERROR'), 'error');
		}

		$app->redirect('index.php?option=com_pwtseo&view&view=datalayersedit&layout=modal&tmpl=component&context=' . $pwtseo['context'] . '&context_id=' . $pwtseo['context_id']);
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @throws  Exception
	 * @since   1.3.1
	 */
	protected function loadFormData()
	{
		$data = Factory::getApplication()->getUserState(
			'com_pwtseo.edit.datalayersedit.data',
			array()
		);

		if (empty($data))
		{
			$data = $this->getItem();

			// We need to transform the data due to the weird names used in form, if code could cry...
			$buildData = array();

			array_map(
				function ($el) use (&$buildData) {
					$dataLayerName = 'datalayers][' . $el->datalayer_id . ']';
					$values        = json_decode($el->values);

					foreach ($values as $key => $value)
					{
						$buildData[$dataLayerName . '[' . $key] = $value;
					}
				},
				$data
			);

			$data = array(
				'pwtseo' => $buildData
			);
		}

		return $data;
	}

	/**
	 * Method to get the layers which are associated with this item
	 *
	 * @param   integer  $id       The id of the primary key.
	 * @param   string   $context  The context along with the id.
	 *
	 * @return  array|boolean  Object on success, false on failure.
	 *
	 * @throws  Exception
	 * @since   1.3.1
	 */
	public function getItem($id = null, $context = null)
	{
		$id      = (!empty($id)) ? $id : (int) $this->getState('pwtseo.context_id');
		$context = (!empty($context)) ? $context : $this->getState('pwtseo.context');

		if ($id > 0 && $context)
		{
			$db    = Factory::getDbo();
			$query = $db->getQuery(true);

			$query
				->select(
					$db->quoteName(
						array(
							'map.datalayer_id',
							'map.values',
							'layer.name'
						)
					)
				)
				->from($db->quoteName('#__plg_pwtseo_datalayers_map', 'map'))
				->leftJoin($db->quoteName('#__plg_pwtseo_datalayers', 'layer') . ' ON ' .
					$db->quoteName('map.datalayer_id') . ' = ' . $db->quoteName('layer.id')
				)
				->where($db->quoteName('map.context_id') . ' = ' . $id)
				->where($db->quoteName('map.context') . ' = ' . $db->quote($context));

			try
			{
				return $db->setQuery($query)->loadObjectList();
			}
			catch (Exception $e)
			{
			}
		}

		return false;
	}


	/**
	 * Stock method to auto-populate the model state.
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @since   1.3.0
	 */
	protected function populateState()
	{
		parent::populateState();

		$app = Factory::getApplication();

		$pk = $app->input->getInt('context_id');
		$this->setState('pwtseo.context_id', $pk);

		$pk = $app->input->getCmd('context');
		$this->setState('pwtseo.context', $pk);
	}
}
