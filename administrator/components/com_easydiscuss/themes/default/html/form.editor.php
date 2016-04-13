<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<select name="<?php echo $name;?>" class="form-control">
    <option value="bbcode" <?php echo $selected == "bbcode" ? 'selected="selected"' : '';?>><?php echo JText::_('COM_EASYDISCUSS_BUILT_IN_BBCODE');?>
<?php foreach ($editors as $editor) { ?>
    <option value="<?php echo $editor->value;?>" <?php echo $editor->value == $selected ? ' selected="selected"' : '';?>><?php echo $editor->text;?></option>
<?php } ?>
</select>