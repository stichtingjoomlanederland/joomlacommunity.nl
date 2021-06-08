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
 * Order statuses.
 *
 * @package  JDiDEAL
 * @since    4.0.0
 */
class JdidealFormFieldMessageresult extends JFormFieldList
{
	/**
	 * Type of field
	 *
	 * @var    string
	 * @since  4.0.0
	 */
	protected $type = 'Messageresult';

	/**
	 * Build a list of available order statuses.
	 *
	 * @return  array  List with order statuses.
	 *
	 * @since   4.0.0
	 */
	public function getOptions(): array
	{
		// Create the list of statuses
		$options = array();
		$options[] = HTMLHelper::_('select.option', 'SUCCESS', Text::_('COM_ROPAYMENTS_STATUS_SUCCESS'));
		$options[] = HTMLHelper::_('select.option', 'OPEN', Text::_('COM_ROPAYMENTS_STATUS_OPEN'));
		$options[] = HTMLHelper::_('select.option', 'CANCELLED', Text::_('COM_ROPAYMENTS_STATUS_CANCELLED'));
		$options[] = HTMLHelper::_('select.option', 'FAILURE', Text::_('COM_ROPAYMENTS_STATUS_FAILURE'));
		$options[] = HTMLHelper::_('select.option', 'TRANSFER', Text::_('COM_ROPAYMENTS_STATUS_TRANSFER'));
		$options[] = HTMLHelper::_('select.option', 'UNKNOWN', Text::_('COM_ROPAYMENTS_STATUS_UNKNOWN'));

		return array_merge(parent::getOptions(), $options);
	}
}
