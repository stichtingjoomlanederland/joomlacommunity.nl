<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

JFormHelper::loadFieldClass('list');

/**
 * List of payment providers.
 *
 * @package  JDiDEAL
 * @since    4.0.0
 */
class JdidealFormFieldPaystatus extends JFormFieldList
{
	/**
	 * Type of field
	 *
	 * @var    string
	 * @since  4.0.0
	 */
	protected $type = 'Paystatus';

	/**
	 * Build a list of payment results.
	 *
	 * @return  array  List of payment results.
	 *
	 * @since   4.0.0
	 *
	 * @throws  RuntimeException
	 */
	public function getOptions()
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select(
				$db->quoteName(
					array(
						'status',
						'status',
					),
					array(
						'value',
						'text'
					)
				)
			)
			->from($db->quoteName('#__jdidealgateway_pays'))
			->group($db->quoteName('status'));
		$db->setQuery($query);

		$options = $db->loadAssocList();

		array_walk($options,
			function (&$option) {
				$option['text'] = Text::_('COM_ROPAYMENTS_RESULT_' . $option['value']);
			}
		);

		return array_merge(parent::getOptions(), $options);
	}
}
