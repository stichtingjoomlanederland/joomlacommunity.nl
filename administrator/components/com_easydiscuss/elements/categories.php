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

class JElementCategories extends JElement
{
	var	$_name = 'Categories';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$mainframe	= JFactory::getApplication();
		$db			= DiscussHelper::getDBO();
		$doc		= JFactory::getDocument();

		require_once DISCUSS_ADMIN_ROOT . '/models/categories.php';
		$model		= new EasyDiscussModelCategories();
		$categories	= $model->getAllCategories();

		ob_start();
		?>
		<select name="<?php echo $control_name;?>[<?php echo $name;?>]">
			<option value="0"<?php echo $value == 0 ? ' selected="selected"' :'';?>><?php echo JText::_('Select a category');?></option>
		<?php
		foreach($categories as $category)
		{
			$selected	= $category->id == $value ? ' selected="selected"' : '';
		?>
			<option value="<?php echo $category->id;?>"<?php echo $selected;?>><?php echo JText::_( $category->title );?></option>
		<?php
		}
		?>
		</select>
		<?php
		$html	= ob_get_contents();
		ob_end_clean();
		return $html;
	}
}
