<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

$model = ED::model('CustomFields');
$fields = $model->getFields(DISCUSS_CUSTOMFIELDS_ACL_INPUT, $operation, $post->id);

// if empty fields then we do not show this tab.
if (!$fields) {
	return;
}
?>
<div data-ed-custom-fields id="fields-<?php echo $editorId; ?>" class="ed-editor-tab__content fields-tab tab-pane">
	<div class="ed-editor-tab__content-note t-lg-mb--xl" data-ed-custom-fields-tab>
		<?php echo JText::_('COM_EASYDISCUSS_FIELDS_INFO'); ?>
	</div>
	<div class="form-horizontal">
	<?php foreach ($fields as $field) { ?>
		<div class="form-group"
			 <?php echo $field->required ? 'data-ed-custom-fields-required-group' : '' ?>
			 data-field-type=<?php echo $field->type?>
			 >

			<div class="control-label col-md-2">
				<?php if ($field->required) { ?>
					<span class="required">*</span>
				<?php } ?>

				<label for="field-<?php echo $field->id;?>"><?php echo JText::_($field->title);?></label>

				<?php if ($field->hasTooltips()) { ?>
				<i data-placement="bottom" data-content="<?php echo JText::_($this->html('string.escape', $field->tooltips));?>"
					data-title="<?php echo JText::_($field->title);?>"
					data-ed-provide="popover"
					class="fa fa-question-circle label__help-icon pull-right"></i>
				<?php } ?>
			</div>

			<div class="col-md-10">
				<?php echo $field->getForm($field->getValue($post)); ?>
			</div>
		</div>
	<?php } ?>
	</div>
</div>