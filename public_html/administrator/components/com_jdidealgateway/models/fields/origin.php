<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

defined('_JEXEC') or die;

use Jdideal\Addons\Addon;
use Joomla\CMS\Factory;

JFormHelper::loadFieldClass('list');

/**
 * List of extensions.
 *
 * @package  JDiDEAL
 * @since    4.0.0
 */
class JdidealFormFieldOrigin extends JFormFieldList
{
	/**
	 * Type of field
	 *
	 * @var    string
	 * @since  4.0.0
	 */
	protected $type = 'Origin';

	/**
	 * Build a list of extensions.
	 *
	 * @return  array  List of extensions.
	 *
	 * @since   4.0.0
	 *
	 * @throws  RuntimeException
	 * @throws  Exception
	 */
	public function getOptions(): array
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select(
				$db->quoteName(
					[
						'origin',
						'origin',
					],
					[
						'value',
						'text'
					]
				)
			)
			->from($db->quoteName('#__jdidealgateway_logs'))
			->where($db->quoteName('origin') . ' <> ' . $db->quote(''))
			->group($db->quoteName('origin'));
		$db->setQuery($query);

		$options = $db->loadObjectList();

		$addons = new Addon;
		$found  = [];

		foreach ($options as $key => $option)
		{
			if (!array_key_exists($option->value, $found))
			{
				$found[$option->value] = $option->text;

				if ($addons->exists($option->value))
				{
					$addon = false;

					try
					{
						$addon = $addons->get($option->value);
					}
					catch (Exception $exception)
					{
						Factory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
					}

					$found[$option->value] = $addon ? $addon->getName() : $option->text;
				}
			}

			$option->text  = $found[$option->value];
			$options[$key] = $option;
		}

		return array_merge(parent::getOptions(), $options);
	}
}
