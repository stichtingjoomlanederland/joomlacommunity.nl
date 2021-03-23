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
if (!$categoryId) {
	$model = ED::model('Category');
	$defaultCategory = $model->getDefaultCategory();

	$categoryId = $defaultCategory->id;
}

$model = ED::model('CustomFields');
$fields = $model->getFields(DISCUSS_CUSTOMFIELDS_ACL_INPUT, $operation, $post->id, $categoryId);

// if empty fields then we do not show this tab.
if (!$fields) {
	return;
}
?>
<div data-ed-custom-fields id="fields-<?php echo $editorId; ?>" class="ed-editor-tab__content fields-tab tab-pane">
	<div role="alert" class="o-alert o-alert--danger-o" data-fields-notice></div>

	<div class="ed-editor-tab__content-note t-lg-mb--xl">
		<?php echo JText::_('COM_EASYDISCUSS_FIELDS_INFO'); ?>
	</div>

	<div class="l-stack">
		<?php foreach ($fields as $field) { ?>
		<div class="t-d--flex sm:t-flex-direction--c"
			 <?php echo $field->required ? 'data-fields-required' : '' ?>
			 data-field-type=<?php echo $field->type?>
			 >

			<div class="lg:t-w--25 sm:t-mb--sm">
				<label class="o-form-label" for="field-<?php echo $field->id;?>">
					<b><?php echo JText::_($field->title);?></b>

					<?php if ($field->required) { ?>
						<span class="t-text--danger">*</span>
					<?php } ?>
				</label>
			</div>

			<div class="lg:t-w--75">
				<?php echo $field->getForm($field->getValue($post)); ?>

				<?php if ($field->hasTooltips()) { ?>
				<div class="o-form-text">
					<?php echo JText::_($this->html('string.escape', $field->tooltips));?>
				</div>
				<?php } ?>
			</div>
		</div>
		<?php } ?>
	</div>
</div>
