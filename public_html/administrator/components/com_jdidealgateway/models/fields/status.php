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

defined('_JEXEC') or die;

JFormHelper::loadFieldClass('list');

/**
 * Order statuses.
 *
 * @package  JDiDEAL
 * @since    2.0.0
 */
class JdidealFormFieldStatus extends JFormFieldList
{
	/**
	 * Type of field
	 *
	 * @var    string
	 * @since  2.0.0
	 */
	protected $type = 'Status';

	/**
	 * Build a list of available order statuses.
	 *
	 * @return  string  HTML select list with order statuses.
	 *
	 * @since   2.0.0
	 */
	public function getInput(): string
	{
		// Initialize variables.
		$attr = '';

		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';

		// To avoid user's confusion, readonly="true" should imply disabled="true".
		if ((string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true')
		{
			$attr .= ' disabled="disabled"';
		}

		$attr .= $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';
		$attr .= $this->multiple ? ' multiple="multiple"' : '';

		// Initialize JavaScript field attributes.
		$attr .= $this->element['onchange'] ? ' onchange="' . (string) $this->element['onchange'] . '"' : '';

		// Create the list of statuses
		$options   = array();
		$options[] = HTMLHelper::_('select.option', 'C', 'COM_ROPAYMENTS_STATUS_SUCCESS');
		$options[] = HTMLHelper::_('select.option', 'P', 'COM_ROPAYMENTS_STATUS_PENDING');
		$options[] = HTMLHelper::_('select.option', 'X', 'COM_ROPAYMENTS_STATUS_CANCELLED');
		$options[] = HTMLHelper::_('select.option', 'F', 'COM_ROPAYMENTS_STATUS_FAILURE');
		$options[] = HTMLHelper::_('select.option', 'E', 'COM_ROPAYMENTS_STATUS_EXPIRED');
		$options[] = HTMLHelper::_('select.option', 'R', 'COM_ROPAYMENTS_STATUS_REFUNDED');
		$options[] = HTMLHelper::_('select.option', 'B', 'COM_ROPAYMENTS_STATUS_CHARGEBACK');
		$options[] = HTMLHelper::_('select.option', 'O', 'COM_ROPAYMENTS_STATUS_OPEN');
		$options[] = HTMLHelper::_('select.option', 'T', 'COM_ROPAYMENTS_STATUS_TRANSFER');

		return HTMLHelper::_(
			'select.genericlist',
			$options,
			$this->name,
			trim($attr),
			'value',
			'text',
			$this->value,
			$this->id, true
		);
	}
}
