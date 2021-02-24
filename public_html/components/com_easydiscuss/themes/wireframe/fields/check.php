<?php
/**
* @package    EasyDiscuss
* @copyright  Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license    GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php if ($options) { ?>
	<?php foreach ($options as $option) { ?>
	<?php $uid = uniqid(); ?>
	<div class="o-form-check" <?php echo $field->required ? 'data-ed-custom-fields-required' : '' ?>>
		<input type="checkbox" class="o-form-check-input"
			value="<?php echo $this->html('string.escape', $option);?>" 
			name="fields[<?php echo $field->id;?>][]" 
			id="checkbox-<?php echo $uid;?>" 
			<?php echo $value && is_array($value) && in_array($option, $value) ? ' checked="checked"' : '';?>
			<?php echo $field->required ? 'data-ed-checkbox-fields' : '' ?>
		/>
		<label for="checkbox-<?php echo $uid;?>" class="o-form-check-label">
				<?php echo $option;?>
		</label>
	</div>
	<?php } ?>
<?php } ?>