<?php
/**
 * @package    Pwtimage
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2019 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

use Joomla\CMS\Factory;

defined('_JEXEC') or die;

JLoader::import('components.com_fields.libraries.fieldsplugin', JPATH_ADMINISTRATOR);

/**
 * Fields PWT Image Plugin
 *
 * @since  1.0
 */
class PlgFieldsPwtimage extends FieldsPlugin
{
	/**
	 * Transforms the field into a DOM XML element and appends it as a child on the given parent.
	 *
	 * @param   stdClass    $field   The field.
	 * @param   DOMElement  $parent  The field node parent.
	 * @param   JForm       $form    The form.
	 *
	 * @return  DOMElement
	 *
	 * @since   1.0
	 */
	public function onCustomFieldsPrepareDom($field, DOMElement $parent, JForm $form)
	{
		$fieldNode = parent::onCustomFieldsPrepareDom($field, $parent, $form);

		if (!$fieldNode)
		{
			return $fieldNode;
		}

		// Set field path the PWT Image field
		$fieldNode->setAttribute('addfieldpath', '/components/com_pwtimage/models/fields');
		$origin = $field->fieldparams->get('origin');
		$fieldNode->setAttribute('origin', $origin . ':');
	}

	/**
	 * Before the data is validated save the ratioWidth + the ratioHeight in ratio.
	 *
	 * @param   JForm  $form  joomla form
	 * @param   array  $data  data to be validated
	 *
	 * @return  boolean always return true but if we are in pwtimage.image and ratio is set we update it  .
	 *
	 * @since 1.0
	 */
	public function onUserBeforeDataValidation($form, &$data)
	{
		if (array_key_exists('type', $data) && $data['type'] === 'pwtimage.image')
		{
			if (array_key_exists('ratioWidth', $data['fieldparams'])
				&& array_key_exists('ratioHeight', $data['fieldparams'])
			)
			{
				$data['fieldparams']['ratio'] =
					$data['fieldparams']['ratioWidth'] . '/' . $data['fieldparams']['ratioHeight'];
			}
		}

		/** @todo Multiple images */
		// Multiple is set to always be false because when loading the custom field.
		// The onContentPrepareData does not get triggered
		// So don't json encode
		return true;

		// Check if we are in com_fields
		if (array_key_exists('com_fields', $data))
		{
			$db = Factory::getDbo();

			// Check for seach name in com field if the name belongs to a pwtimage.image field
			foreach ($data['com_fields'] as $name => $value)
			{
				$query = $db->getQuery(true);
				$query->select($db->quoteName('fields.id'));
				$query->from($db->quoteName('#__fields'), 'fields');
				$query->where($db->quoteName('name') . ' = ' . $db->quote($name));
				$query->where($db->quoteName('type') . ' = ' . 'pwtimage.image');
				$db->setQuery($query);

				try
				{
					$id = (int) $db->loadResult();
				}
				catch (Exception $e)
				{
					$id = 0;
				}

				// Json encode if the field is a pwtimage.image field
				if ($id > 0)
				{
					$data['com_fields'][$name] = json_encode($value);
				}
			}
		}

	}

	/**
	 * Returns the custom fields types.
	 *
	 * @return  string[][]
	 *
	 * @throws  Exception If no Application can be loaded
	 *
	 * @since   1.0
	 */
	public function onCustomFieldsGetTypes()
	{
		$types      = parent::onCustomFieldsGetTypes();
		$typesCount = count($types);

		// If types is empty return it
		if (!(is_array($types) && $typesCount > 0))
		{
			return $types;
		}

		// We only need to edit is on the backend
		if (Factory::getApplication()->isClient('site'))
		{
			$types[]['type'] = 'pwtimage.image';

			return $types;
		}

		// Update pwtimage to pwtimage.image because Custom fields uses this for filenames as well
		for ($i = 0; $i < $typesCount; $i++)
		{
			if (isset($types[$i]['type']) && $types[$i]['type'] === 'pwtimage')
			{
				$types[$i]['type'] = 'pwtimage.image';
			}
		}

		return $types;

	}

	/**
	 * Prepares the field value.
	 *
	 * @param   string    $context  The context.
	 * @param   stdclass  $item     The item.
	 * @param   stdclass  $field    The field.
	 *
	 * @return  string
	 *
	 * @throws  Exception If no Application can be loaded
	 *
	 * @since   1.0
	 */
	public function onCustomFieldsPrepareField($context, $item, $field)
	{
		// On the client side we need to update pwtimage.image to pwtimage since joomla will look for the tmpl file
		// in the directory with the name of the type
		if ($field->type === 'pwtimage.image' && Factory::getApplication()->isClient('site'))
		{
			$field->type = 'pwtimage';
		}

		return parent::onCustomFieldsPrepareField($context, $item, $field);
	}
}
