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
<div class="o-form-group">
	<label for="ed-post-label-form"><?php echo JText::_('COM_ED_COMPOSER_POST_LABEL_FIELD');?></label>

	<select name="post-label" class="o-form-select" id="post-label">
		<option value=""><?php echo JText::_('COM_ED_SELECT_POST_LABEL'); ?></option>
		<?php foreach ($labels as $label) { ?>
			<option <?php echo $label->title == $selected->title ? 'selected="selected"' : '' ?> value="<?php echo $label->title; ?>"><?php echo JText::_($label->title); ?></option>
		<?php } ?>
	</select>
</div>