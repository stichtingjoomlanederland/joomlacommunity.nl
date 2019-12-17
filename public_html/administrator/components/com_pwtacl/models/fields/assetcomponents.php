<?php
/**
 * @package    PwtAcl
 *
 * @author     Sander Potjer - Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2011 - 2019 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com/pwt-acl
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Utilities\ArrayHelper;

// No direct access.
defined('_JEXEC') or die;

JFormHelper::loadFieldClass('list');

/**
 * Components Field class.
 *
 * @since   3.0
 */
class JFormFieldAssetComponents extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var     string
	 * @since   3.0
	 */
	protected $type = 'AssetComponents';

	/**
	 * Method to get the field options.
	 *
	 * @return  array The field option objects.
	 * @since   3.0
	 * @throws  Exception
	 */
	public function getOptions()
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select('name AS value, name AS text')
			->from('#__assets')
			->where($db->quoteName('level') . ' = 1')
			->where($db->quoteName('name') . ' NOT LIKE ' . $db->quote('#__ucm_content.%'))
			->where($db->quoteName('name') . ' NOT LIKE ' . $db->quote('com_admin'))
			->where($db->quoteName('name') . ' NOT LIKE ' . $db->quote('com_config'))
			->where($db->quoteName('name') . ' NOT LIKE ' . $db->quote('com_cpanel'))
			->where($db->quoteName('name') . ' NOT LIKE ' . $db->quote('com_login'))
			->where($db->quoteName('name') . ' NOT LIKE ' . $db->quote('com_mailto'))
			->where($db->quoteName('name') . ' NOT LIKE ' . $db->quote('com_wrapper'))
			->where($db->quoteName('name') . ' NOT LIKE ' . $db->quote('com_contenthistory'))
			->where($db->quoteName('name') . ' NOT LIKE ' . $db->quote('com_ajax'))
			->where($db->quoteName('name') . ' NOT LIKE ' . $db->quote('com_fields'))
			->where($db->quoteName('name') . ' NOT LIKE ' . $db->quote('com_actionlogs'))
			->where($db->quoteName('name') . ' NOT LIKE ' . $db->quote('com_privacy'))
			->where($db->quoteName('name') . ' NOT LIKE ' . $db->quote('com_joomlaupdate'));

		// Get the options.
		$db->setQuery($query);

		try
		{
			$options = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			throw new Exception($e->getMessage(), 500, $e);
		}

		foreach ($options as $key => $option)
		{
			// Translate component name
			$option->text = strip_tags(Text::_($option->text));
		}

		// Sort by component name
		$options = ArrayHelper::sortObjects($options, 'text', 1, false, true);

		return array_merge(parent::getOptions(), $options);
	}
}
