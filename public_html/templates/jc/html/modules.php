<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  Templates.protostar
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * This is a file to add template specific chrome to module rendering.  To use it you would
 * set the style attribute for the given module(s) include in your template to use the style
 * for each given modChrome function.
 *
 * eg.  To render a module mod_test in the submenu style, you would use the following include:
 * <jdoc:include type="module" name="test" style="submenu" />
 *
 * This gives template designers ultimate control over how modules are rendered.
 *
 * NOTICE: All chrome wrapping methods should be named: modChrome_{STYLE} and take the same
 * two arguments.
 */

/*
 * Module chrome for rendering the module in a submenu
 */
function modChrome_no($module, &$params, &$attribs)
{
	if ($module->content)
	{
		echo $module->content;
	}
}

function modChrome_well($module, &$params, &$attribs)
{
	$headerTag   = htmlspecialchars($params->get('header_tag', 'h3'));

	if ($module->content)
	{
		if ($params->get('bootstrap_size'))
		{
			echo '<div class="col-lg-' . htmlspecialchars($params->get('bootstrap_size')) . '">';
		}
		echo '<div class="well ' . htmlspecialchars($params->get('moduleclass_sfx')) . '">';
		if ($module->showtitle)
		{
			echo '<div class="page-header ' . $headerTag . '"><strong>' . $module->title . '</strong></div>';
		}
		echo $module->content;
		echo '</div>';
		if ($params->get('bootstrap_size'))
		{
			echo '</div>';
		}
	}
}

function modChrome_panel($module, &$params, &$attribs)
{
	if ($module->content)
	{
		if ($params->get('bootstrap_size'))
		{
			echo '<div class="col-lg-' . htmlspecialchars($params->get('bootstrap_size')) . '">';
		}
		echo '<div class="panel ' . htmlspecialchars($params->get('moduleclass_sfx')) . '">';
		if ($module->showtitle)
		{
			echo '<div class="panel-heading">' . $module->title . '</div>';
		}
		echo $module->content;
		echo '</div>';
		if ($params->get('bootstrap_size'))
		{
			echo '</div>';
		}
	}
}

?>
