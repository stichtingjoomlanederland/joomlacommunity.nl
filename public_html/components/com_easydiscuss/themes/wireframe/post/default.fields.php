<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

// Ensure that custom fields is enabled
if (!$this->config->get('main_customfields')) {
	return;
}

$fields = $post->getCustomFields();

$hasField = false;

foreach ($fields as $field) {
	// Check this post is it got custom field value 
	if (!empty($field->value)) {
		$hasField = true;
	}
}

if (!$hasField) {
	return;
}

?>

<div class="ed-post-widget">
    
    <div class="ed-post-widget__hd">
    	<?php echo JText::_('COM_EASYDISCUSS_CUSTOM_FIELDS'); ?>
    </div>

    <div class="ed-polls__bd">
		<div class="ed-fields">
		<?php foreach ($fields as $field) { ?>
			<?php if ($field->value) { ?>
				<div class="row">
					<div class="col-md-3 discuss-field-title">
						<label><?php echo JText::_($field->title); ?>:</label>
					</div>

					<div class="col-md-9">
						<?php echo ED::field($field)->format($field->value);?>
					</div>
				</div>
			<?php } ?>
		<?php } ?>
		</div>
    </div>
</div>
