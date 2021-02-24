<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<select name="<?php echo $name;?>" class="o-form-select" <?php echo $attributes;?> autocomplete="off" data-ed-select>
	<option value="" <?php echo !$selected ? 'selected="selected"' : '';?>><?php echo JText::_('COM_ED_SELECT_MENU_ITEM'); ?></option>
	<?php foreach ($menus as $menutype => $items) { ?>
		<?php if ($menutype) { ?>
		<optgroup label="<?php echo $menutype;?>">
		<?php } ?>

		<?php foreach ($items as $item) { ?>
			<option value="<?php echo $item->value;?>"<?php echo $selected == $item->value ? ' selected="selected"' : '';?>><?php echo $item->text;?></option>
		<?php } ?>

		<?php if ($menutype) { ?>
		</optgroup>
		<?php } ?>
	<?php } ?>
</select>