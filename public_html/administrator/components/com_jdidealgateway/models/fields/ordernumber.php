<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

JFormHelper::loadFieldClass('list');

/**
 * List of available order number fields.
 *
 * @package  JDiDEAL
 * @since    4.11.0
 */
class JdidealFormFieldOrdernumber extends JFormFieldList
{
	/**
	 * Type of field
	 *
	 * @var    string
	 * @since  4.11.0
	 */
	protected $type = 'Ordernumber';

	/**
	 * Build a list of available banks.
	 *
	 * @return  string  HTML select list with banks.
	 *
	 * @since   4.11.0
	 */
	public function getInput(): string
	{
		// Initialize some field attributes.
		$attr = $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';

		// Create the list of banks
		$options   = array();
		$options[] = HTMLHelper::_('select.option', 'order_id', Text::_('COM_ROPAYMENTS_FIELD_ORDER_ID'));
		$options[] = HTMLHelper::_('select.option', 'order_number', Text::_('COM_ROPAYMENTS_FIELD_ORDER_NUMBER'));

		return HTMLHelper::_(
			'select.genericlist',
			$options,
			$this->name,
			trim($attr),
			'value',
			'text',
			$this->value,
			$this->id
		);
	}
}
