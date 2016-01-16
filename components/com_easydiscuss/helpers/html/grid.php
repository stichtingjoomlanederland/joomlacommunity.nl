<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

class DiscussHtmlGrid
{
	public static function makeDefault($value, $cid, $controller, $task='makeDefault', $class='')
	{
		$alt	= $value ? JText::_('JPUBLISHED') : JText::_('JUNPUBLISHED');
		$action	= $value ? '' : JText::_('Make Default');
		$icon	= $value ? '<i class="icon-star"></i>' : '<i class="icon-star-empty"></i>';
		$click	= $value ? 'javascript:void(0);' : JRoute::_( 'index.php?option=com_easydiscuss&controller='.$controller.'&task='.$task.'&cid=' . $cid );

		$href = '<a href="'.$click.'" class="' . $class . '" title="' . $action . '">'
			. $icon . '</a>';

		return $href;
	}

	public static function published($value, $i, $class='')
	{
		if (is_object($value)) {
			$value = $value->published;
		}

		$task	= $value ? 'unpublish' : 'publish';
		$alt	= $value ? JText::_('JPUBLISHED') : JText::_('JUNPUBLISHED');
		$action	= $value ? JText::_('JLIB_HTML_UNPUBLISH_ITEM') : JText::_('JLIB_HTML_PUBLISH_ITEM');
		$icon	= $value ? '<i class="icon-ok"></i>' : '<i class="icon-remove"></i>';

		$href = '<a href="#toggle" class="' . $class . '" onclick="return listItemTask(\'cb' . $i . '\',\'' . $task . '\')" title="' . $action . '">'
			. $icon . '</a>';

		return $href;
	}

	public static function order($i, $orderkey, $condition_up, $condition_down)
	{
		$orderkey = $orderkey + 1;

		$output = '<div class="pull-left arrow-wrapper">';
		if( $condition_up ) {
			$output .= '	<span class="pull-left"><a title="' . JText::_('JLIB_HTML_MOVE_UP') .'" onclick="return listItemTask(\'cb'. $i . '\', \'orderup\')" href="javascript:void(0);" class="btn btn-micro "><i class="icon-chevron-up"></i></a></span>';
		} else {
			$output .= '&#160;';
		}
		if( $condition_down ) {
			$output .= '	<span class="pull-right"><a title="' . JText::_('JLIB_HTML_MOVE_DOWN') .'" onclick="return listItemTask(\'cb'. $i . '\', \'orderdown\')" href="javascript:void(0);" class="btn btn-micro "><i class="icon-chevron-down"></i></a></span>';
		} else {
			$output .= '&#160;';
		}
		$output .= '</div>';
		$output .= '<input type="text" style="text-align: center" class="span1 pull-right" disabled="disabled" value="' . $orderkey . '" size="3" name="order[]">';

		return $output;
	}
}
