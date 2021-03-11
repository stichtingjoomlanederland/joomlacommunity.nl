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
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;

defined('_JEXEC') or die;

/**
 * Structured Data model.
 *
 * @since  1.3.0
 */
class PWTSEOModelStructuredData extends AdminModel
{
	/**
	 * The type alias for this content type.
	 *
	 * @var    string
	 * @since  1.3.0
	 */
	public $typeAlias = 'com_pwtseo.structureddata';

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
		/** @var Form $form */
		$form = $this->loadForm(
			'com_pwtseo.structureddata',
			'structureddata',
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
	public function getTable($type = 'StructuredData', $prefix = 'PWTSEOTable', $config = array())
	{
		return Table::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array $data The form data.
	 *
	 * @since   1.3.0
	 * @throws  Exception
	 */
	public function save($data)
	{
		$app = Factory::getApplication();

		try
		{
			$pwtseo = $data['pwtseo'];

			$obj = (object) array(
				'context'        => $pwtseo['context'],
				'context_id'     => $pwtseo['context_id'],
				'structureddata' => json_encode($pwtseo['structureddata'])
			);

			if ($pwtseo['context'] === 'com_pwtseo.custom')
			{
				$obj->id         = $pwtseo['context_id'];
				$obj->context_id = 0;

				Factory::getDbo()->updateObject('#__plg_pwtseo', $obj, array('context', 'id'));
			}
			else
			{
				Factory::getDbo()->updateObject('#__plg_pwtseo', $obj, array('context', 'context_id'));
			}

			$app->enqueueMessage(Text::_('COM_PWTSEO_STRUCTUREDDATA_SAVE_SUCCESS'));
		}
		catch (Exception $e)
		{
			$app->enqueueMessage(Text::_('COM_PWTSEO_STRUCTUREDDATA_SAVE_ERROR'), 'error');
		}

		$app->redirect('index.php?option=com_pwtseo&view&view=structureddata&layout=modal&tmpl=component&context=' . $pwtseo['context'] . '&context_id=' . $pwtseo['context_id']);
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
			'com_pwtseo.edit.structureddata.data',
			array()
		);

		if (empty($data))
		{
			$data = $this->getItem();

			$data = array(
				'pwtseo' => array(
					'context_id'     => $this->getState('pwtseo.context_id'),
					'context'        => $this->getState('pwtseo.context'),
					'structureddata' => $data
				)
			);
		}

		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer $id      The id of the primary key.
	 * @param   string  $context The context along with the id.
	 *
	 * @return  \JObject|boolean  Object on success, false on failure.
	 *
	 * @since   1.3.0
	 * @throws  Exception
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
							'structureddata'
						)
					)
				)
				->from($db->quoteName('#__plg_pwtseo', 'seo'))
				->where($db->quoteName($context === 'com_pwtseo.custom' ? 'seo.id' : 'seo.context_id') . ' = ' . $id)
				->where($db->quoteName('seo.context') . ' = ' . $db->quote($context));

			try
			{
				return json_decode($db->setQuery($query)->loadResult());
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
	 * @since   1.3.0
	 * @throws  Exception
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
