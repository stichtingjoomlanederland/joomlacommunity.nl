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
 * List of payment methods.
 *
 * @package  JDiDEAL
 * @since    4.0.0
 */
class JdidealFormFieldPsp extends JFormFieldList
{
	/**
	 * Type of field
	 *
	 * @var    string
	 * @since  4.0.0
	 */
	protected $type = 'Psp';

	/**
	 * Build a list of payment methods.
	 *
	 * @return  array  List of payment providers.
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
						'id',
						'name',
					],
					[
						'value',
						'text'
					]
				)
			)
			->from($db->quoteName('#__jdidealgateway_profiles'))
			->order($db->quoteName('ordering') . ' ASC');
		$db->setQuery($query);

		$options = $db->loadAssocList();

		return array_merge(parent::getOptions(), $options);
	}
}
