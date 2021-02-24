<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die;

JFormHelper::loadFieldClass('list');

/**
 * List of available banks.
 *
 * @package  JDiDEAL
 * @since    2.0.0
 */
class JdidealFormFieldBanks extends JFormFieldList
{
	/**
	 * Type of field
	 *
	 * @var    string
	 * @since  2.0.0
	 */
	protected $type = 'Banks';

	/**
	 * Build a list of available banks.
	 *
	 * @return  string  HTML select list with banks.
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

		// Create the list of banks
		$options = array();
		$options[] = HTMLHelper::_('select.option', 'INGSEPA', 'ING');
		$options[] = HTMLHelper::_('select.option', 'RABOBANKSEPA', 'Rabobank');
		$options[] = HTMLHelper::_('select.option', 'INGSEPATEST', 'ING | TEST Server');
		$options[] = HTMLHelper::_('select.option', 'RABOBANKSEPATEST', 'Rabobank | TEST Server');

		return HTMLHelper::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);
	}
}
