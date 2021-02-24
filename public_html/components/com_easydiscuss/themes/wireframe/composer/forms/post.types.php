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
<div class="o-form-group" data-post-types-wrapper>
	<label for="post-type"><?php echo JText::_('COM_EASYDISCUSS_SELECT_A_POST_TYPE');?></label>

	<select id="post-type" class="o-form-select" name="post_type">
		<option value=""><?php echo JText::_('COM_EASYDISCUSS_SELECT_POST_TYPES'); ?></option>
		<?php foreach ($postTypes as $type) { ?>
			<option <?php echo $type->alias == $selected ? 'selected="selected"' : '' ?> value="<?php echo $type->alias; ?>"><?php echo JText::_($type->title); ?></option>
		<?php } ?>
	</select>
</div>