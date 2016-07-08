<?php
/**
 * @package    Bootstrap3_Compatibility
 *
 * @copyright  Copyright (C) 2016 Niels van der Veer, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * JHtml class for Compatibility with Bootstrap 3
 *
 * @since  1.0.0
 */
class JHtmlBootstrap3Compatibility extends JHtmlBootstrap
{
	/**
	 * Adaptor for JHtmlBootstrap::startTabSet();
	 *
	 * @param   string  $selector  see JHtmlBootstrap::("bootstrap.startTabset");
	 * @param   array   $params    See JHtmlBootstrap::("bootstrap.startTabset");
	 *
	 * @return  JHtmlBootstrap
	 */
	public static function startTabSet($selector = 'myAccordian', $params = array())
	{
		JFactory::getDocument()->addScriptDeclaration("
			(function($)
			{
			    $(document).ready(function(){
			        var bootstrapLoaded = (typeof $().carousel == 'function');
			        var mootoolsLoaded = (typeof MooTools != 'undefined');
			        if (bootstrapLoaded && mootoolsLoaded) {
			            Element.implement({
			                hide: function () {
			                    return this;
			                },
			                show: function (v) {
			                    return this;
			                },
			                slide: function (v) {
			                    return this;
			                }
			            });
			        }
			    });
			})(jQuery);
		");

		return JHtmlBootstrap::startTabSet($selector, $params);
	}

	/**
	 * Override JHtmlBootstrap::addSlide();
	 *
	 * @param   string  $selector  Identifier of the accordion group.
	 * @param   string  $text      Text to display.
	 * @param   string  $id        Identifier of the slide.
	 * @param   string  $class     Class of the accordion group.
	 *
	 * @return  string  HTML to add the slide
	 */
	public static function addSlide($selector, $text, $id, $class = '')
	{
		$in = (static::$loaded['JHtmlBootstrap::startAccordion'][$selector]['active'] == $id) ? ' in' : '';
		$parent = static::$loaded['JHtmlBootstrap::startAccordion'][$selector]['parent'] ?
			' data-parent="' . static::$loaded[__CLASS__ . '::startAccordion'][$selector]['parent'] . '"' : '';
		$class = (!empty($class)) ? ' ' . $class : '';

		$html = '<div class="panel panel-default' . $class . '">'
			. '<div class="panel-heading">'
			. ' <h4 class="panel-title"><a href="#' . $id . '" role="button" data-toggle="collapse"' . $parent . '>'
			. $text
			. '</a></h4>'
			. '</div>'
			. '<div class="panel-collapse collapse' . $in . '" id="' . $id . '">'
			. '<div class="panel-body">';

		return $html;
	}

	/**
	 * Displays a calendar control field
	 *
	 * @param   string  $value    The date value
	 * @param   string  $name     The name of the text field
	 * @param   string  $id       The id of the text field
	 * @param   string  $format   The date format
	 * @param   mixed   $attribs  Additional HTML attributes
	 *
	 * @return  string  HTML markup for a calendar field
	 *
	 * @since   1.5
	 */
	public static function calendar($value, $name, $id, $format = '%Y-%m-%d', $attribs = null)
	{
		static $done;

		if ($done === null)
		{
			$done = array();
		}

		$readonly = isset($attribs['readonly']) && $attribs['readonly'] == 'readonly';
		$disabled = isset($attribs['disabled']) && $attribs['disabled'] == 'disabled';

		if (is_array($attribs))
		{
			$attribs['class'] = isset($attribs['class']) ? $attribs['class'] : 'input-medium';
			$attribs['class'] = trim($attribs['class'] . ' hasTooltip form-control');

			$attribs = JArrayHelper::toString($attribs);
		}

		JHtml::_('bootstrap.tooltip');

		// Format value when not nulldate ('0000-00-00 00:00:00'), otherwise blank it as it would result in 1970-01-01.
		if ($value && $value != JFactory::getDbo()->getNullDate() && strtotime($value) !== false)
		{
			$tz = date_default_timezone_get();
			date_default_timezone_set('UTC');
			$inputvalue = strftime($format, strtotime($value));
			date_default_timezone_set($tz);
		}
		else
		{
			$inputvalue = '';
		}

		// Load the calendar behavior
		JHtml::_('behavior.calendar');

		// Only display the triggers once for each control.
		if (!in_array($id, $done))
		{
			$document = JFactory::getDocument();
			$document
				->addScriptDeclaration(
					'jQuery(document).ready(function($) {Calendar.setup({
			// Id of the input field
			inputField: "' . $id . '",
			// Format of the input field
			ifFormat: "' . $format . '",
			// Trigger for the calendar (button ID)
			button: "' . $id . '_img",
			// Alignment (defaults to "Bl")
			align: "Tl",
			singleClick: true,
			firstDay: ' . JFactory::getLanguage()->getFirstDay() . '
			});});'
				);
			$done[] = $id;
		}

		// Hide button using inline styles for readonly/disabled fields
		$btn_style = ($readonly || $disabled) ? ' style="display:none;"' : '';
		$div_class = (!$readonly && !$disabled) ? ' class="input-group"' : '';

		return '<div' . $div_class . '>'
		. '<input type="text" title="' . ($inputvalue ? JHtml::_('date', $value, null, null) : '')
		. '" name="' . $name . '" id="' . $id . '" value="' . htmlspecialchars($inputvalue, ENT_COMPAT, 'UTF-8') . '" ' . $attribs . ' />'
		. '<div class="input-group-btn">'
		. '<button type="button" class="btn btn-default" id="' . $id . '_img"' . $btn_style . '><span class="icon-calendar"></span></button>'
		. '</div>'
		. '</div>';
	}
}