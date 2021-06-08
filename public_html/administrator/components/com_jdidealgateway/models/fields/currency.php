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

use Joomla\CMS\Factory;

JFormHelper::loadFieldClass('list');

/**
 * List of used currencies.
 *
 * @package  JDiDEAL
 * @since    4.11.0
 */
class JdidealFormFieldCurrency extends JFormFieldList
{
	/**
	 * Type of field
	 *
	 * @var    string
	 * @since  4.11.0
	 */
	protected $type = 'Currency';

	/**
	 * Build a list of used currencies.
	 *
	 * @return  array  List of currencies.
	 *
	 * @since   4.11.0
	 *
	 * @throws  RuntimeException
	 */
	public function getOptions(): array
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select(
				$db->quoteName(
					[
						'currency',
						'currency',
					],
					[
						'value',
						'text'
					]
				)
			)
			->from($db->quoteName('#__jdidealgateway_logs'))
			->where($db->quoteName('currency') . ' <> ' . $db->quote(''))
			->group($db->quoteName('currency'));
		$db->setQuery($query);

		$options = $db->loadAssocList();

		return array_merge(parent::getOptions(), $options);
	}
}
