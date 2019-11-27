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
 * @since  1.0.0
 */
class PwtimageFormFieldProfiles extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 * @since  1.0.0
	 */
	protected $type = 'Pwtprofiles';

	/**
	 * Get a list of possible profiles.
	 *
	 * @return  array  List of available profiles.
	 *
	 * @since   1.0.0
	 */
	protected function getOptions()
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);

		$query
			->select($db->quoteName('profiles.name', 'text'))
			->select($db->quoteName('profiles.id', 'value'))
			->from($db->quoteName('#__pwtimage_profiles', 'profiles'))
			->where($db->quoteName('profiles.published') . ' = 1');
		$db->setQuery($query);

		return array_merge(parent::getOptions(), $db->loadObjectList());
	}
}
