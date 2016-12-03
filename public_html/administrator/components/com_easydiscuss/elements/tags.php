<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once JPATH_ROOT . '/components/com_easydiscuss/constants.php';
require_once JPATH_ROOT . '/components/com_easydiscuss/helpers/helper.php';

class JElementTags extends JElement
{
	var	$_name = 'Tags';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$mainframe	= JFactory::getApplication();
		$db			= DiscussHelper::getDBO();
		$doc 		= JFactory::getDocument();

		$query  = 'SELECT `id`, `title` FROM `#__discuss_tags`';
		$query  .= ' WHERE `published` = ' . $db->Quote('1');

		$db->setQuery($query);
		$data = $db->loadObjectList();



		ob_start();
		?>
		<select name="<?php echo $control_name;?>[<?php echo $name;?>]">
			<option value="0"<?php echo $value == 0 ? ' selected="selected"' :'';?>><?php echo JText::_('Select a tag');?></option>
		<?php
		if(count($data) > 0)
		{
			foreach($data as $blog)
			{
				$selected	= $blog->id == $value ? ' selected="selected"' : '';
		?>
			<option value="<?php echo $blog->id;?>"<?php echo $selected;?>><?php echo '(' . $blog->id . ') ' . $blog->title;?></option>
		<?php
			}
		}
		?>
		</select>
		<?php
		$html	= ob_get_contents();
		ob_end_clean();
		return $html;
	}
}
