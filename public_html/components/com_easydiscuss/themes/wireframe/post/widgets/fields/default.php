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

// Ensure that custom fields is enabled
if (!$this->config->get('main_customfields')) {
	return;
}

$fields = $post->getCustomFields();

$hasField = false;

foreach ($fields as $field) {
	if (!empty($field->value)) {
		$hasField = true;
	}
}

if (!$hasField) {
	return;
}

?>
<div class="o-card o-card--ed-post-widget" data-ed-post-custom-fields>
	<div class="o-card__body l-stack">
		<div class="o-title-01">
			<?php echo JText::_('COM_EASYDISCUSS_CUSTOM_FIELDS'); ?>
		</div>

		<div class="ed-custom-fields">
		<?php foreach ($fields as $field) { ?>
			<?php if ($field->value) { ?>
				<div class="t-d--flex sm:t-flex-direction--c">
					<div class="lg:t-w--25 sm:t-mb--sm t-flex-shrink--0">
						<label class="ed-custom-fields-label"><?php echo JText::_($field->title); ?>:</label>
					</div>
					<div class="ed-custom-fields-ouput lg:t-w--100 l-stack l-spaces--xs t-text--truncate">
						<div class="t-text--wrap l-stack l-spaces--xs">

							<?php $values = ED::field($field)->format($field->value); ?>

							<?php if (is_array($values)) { ?>
								<?php foreach ($values as $val) { ?>
									<div class="ed-custom-fields-ouput__item">
										<?php echo $val; ?>
									</div>
								<?php } ?>
							<?php } ?>

							<?php if (!is_array($values)) { ?>
								<div class="ed-custom-fields-ouput__item">
									<?php echo $values; ?>
								</div>
							<?php } ?>
							
						</div>
					</div>
				</div>
			<?php } ?>
		<?php } ?>
		</div>
	</div>
	
</div>
