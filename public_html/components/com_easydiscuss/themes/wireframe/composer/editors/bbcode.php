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
<ul class="o-tabs o-tabs--ed o-tabs--ed-editor">
	<li class="o-tabs__item active">
		<a class="o-tabs__link" href="#preview-editor-<?php echo $editorId;?>" data-ed-toggle="tab"><?php echo JText::_('COM_ED_WRITE');?></a>
	</li>
	<li class="o-tabs__item">
		<a class="o-tabs__link" href="#display-preview-<?php echo $editorId;?>" data-ed-preview data-ed-toggle="tab"><?php echo JText::_('COM_ED_PREVIEW');?></a>
	</li>
</ul>

<div class="tab-content" data-ed-editor-tabs-content>
	<div id="preview-editor-<?php echo $editorId;?>" class="ed-editor-tab__content tab-pane active">
		<div class="ed-editor-composer" style="width: 100%;" data-ed-composer>
			<textarea class="o-form-control" name="<?php echo $name; ?>" placeholder="<?php echo JText::_('COM_EASYDISCUSS_COMPOSER_PLACEHOLDER_' . strtoupper($operation));?>" data-ed-editor><?php echo $this->html('string.escape', $content);?></textarea>

			<?php if ($giphy->isEnabled()) { ?>
				<?php echo $this->output('site/composer/forms/giphy'); ?>
			<?php } ?>
		</div>
	</div>
	<div id="display-preview-<?php echo $editorId;?>" class="ed-editor-tab__content tab-pane">
		<div class="o-alert o-alert--info hide" data-editor-preview-notice></div>
		<div class="ed-editor-preview" data-editor-preview-wrapper>
			<?php echo $this->html('loading.block');?>
			<div class="is-editor-markup" data-editor-preview-content></div>
		</div>
	</div>
</div>


<?php echo $this->output('site/composer/buttons'); ?>