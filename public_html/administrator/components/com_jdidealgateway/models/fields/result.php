<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2020 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;

JFormHelper::loadFieldClass('list');

/**
 * List of payment providers.
 *
 * @package  JDiDEAL
 * @since    4.0.0
 */
class JdidealFormFieldResult extends JFormFieldList
{
	/**
	 * Type of field
	 *
	 * @var    string
	 * @since  4.11.0
	 */
	protected $type = 'Result';

	/**
	 * Build a list of payment results.
	 *
	 * @return  array  List of payment results.
	 *
	 * @since   4.0.0
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
						'result',
						'result',
					],
					[
						'value',
						'text'
					]
				)
			)
			->from($db->quoteName('#__jdidealgateway_logs'))
			->group($db->quoteName('result'));
		$db->setQuery($query);

		$options = $db->loadAssocList();

		return array_merge(parent::getOptions(), $options);
	}
}
