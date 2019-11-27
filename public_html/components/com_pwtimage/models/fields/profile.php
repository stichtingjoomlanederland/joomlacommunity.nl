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
use Joomla\CMS\Form\FormHelper;

defined('_JEXEC') or die;

FormHelper::loadFieldClass('list');

/**
 * A list of available profiles.
 *
 * @since  1.0
 */
class PwtimageFormFieldProfile extends \JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 * @since  1.1
	 */
	protected $type = 'Pwtprofile';

	/**
	 * Get a list of possible profiles.
	 *
	 * @return  array  List of available profiles.
	 *
	 * @since   1.0
	 */
	protected function getOptions()
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$value = $query->concatenate(
			$db->quoteName(
				array(
					'extensions.profile_id',
					'extensions.path'
				)
			),
			':'
		);

		$idValue = $query->concatenate(
			array($db->quoteName('profiles.id'), $db->quote('')),
			':'
		);

		$query->select($db->quoteName('profiles.name', 'text'))
			->select('IF(' . $value . ' = ' . $db->quote('') . ',' . $idValue . ', ' . $value . ') AS ' . $db->quoteName('value'))
			->from($db->quoteName('#__pwtimage_profiles', 'profiles'))
			->leftJoin(
				$db->quoteName('#__pwtimage_extensions', 'extensions')
				. ' ON ' . $db->quoteName('extensions.profile_id') . ' = ' . $db->quoteName('profiles.id')
			)
			->where($db->quoteName('profiles.published') . ' = 1')
			->order($db->quoteName('profiles.ordering'))
			->group($db->quoteName('text'));
		$db->setQuery($query);

		return array_merge(parent::getOptions(), $db->loadObjectList());
	}
}
