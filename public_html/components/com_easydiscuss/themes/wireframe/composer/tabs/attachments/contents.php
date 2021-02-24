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

// Get post's attachments
$attachments = $post->getAttachments();
$hasLimits = $this->config->get('enable_attachment_limit');
$limit = $this->config->get('attachment_limit');

// Determines if user exceeded their limit
$exceededLimit = $hasLimits && $limit != 0 && (count($attachments) >= $limit);

// Determines the upload limit filesize
$uploadLimit = $this->config->get('attachment_maxsize') ? $this->config->get('attachment_maxsize') : ini_get('upload_max_filesize');
$uploadLimit = str_ireplace(array('M', 'B'), '', $uploadLimit);

// Determines the allowed extensions
$allowedExtensions = $this->config->get('main_attachment_extension');
?>
<div id="attachments-<?php echo $editorId;?>" class="ed-editor-tab__content attachments-tab tab-pane" data-ed-attachments>
	<?php if (!$exceededLimit) { ?>
	<div class="ed-editor-tab__content-note t-lg-mt--md t-font-size--02" data-ed-attachment-info>
		<?php echo JText::sprintf('COM_EASYDISCUSS_ATTACHMENTS_INFO', $allowedExtensions); ?>
	</div>
	<?php } ?>

	<div class="ed-attachment-itemgroup" data-ed-attachments-list>
		<?php if ($attachments) { ?>
			<?php foreach ($attachments as $attachment) { ?>
				<div id="attachment-<?php echo $attachment->id;?>" class="ed-attachment-form-item attachment-type-<?php echo $attachment->getType();?>" data-attachment-wrapper>
					<i class="ed-attachment-icon" data-ed-attachment-item-icon></i>
					<span class="ed-attachment-title" data-item-title><?php echo $attachment->title;?></span>

					<?php if ($this->config->get('layout_editor') == 'bbcode' && $attachment->isImage()) { ?>
						&middot; <a class="si-link" href="javascript:void(0);" data-insert><?php echo JText::_('COM_EASYDISCUSS_INSERT'); ?></a>
					<?php } ?>

					<?php if ($attachment->canDelete()) { ?>
						&middot; <a class="si-link" href="javascript:void(0);" data-ed-attachment-item-remove data-id="<?php echo $attachment->id;?>"><?php echo JText::_('COM_EASYDISCUSS_REMOVE'); ?></a>
					<?php } ?>
				</div>
			<?php } ?>
		<?php } ?>
	</div>

	<?php if (!$exceededLimit) { ?>
		<div class="ed-attachment-form ed-attachment-form-item" data-ed-attachment-form data-attachment-wrapper>
			<span class="attachment-title" data-item-title></span>

			<span class="t-d--none" data-item-actions>
				<?php if ($this->config->get('layout_editor') == 'bbcode') { ?>
					<a class="si-link" href="javascript:void(0);" data-insert>&middot; <?php echo JText::_('COM_EASYDISCUSS_INSERT'); ?></a>
				<?php } ?>

				<a class="si-link" href="javascript:void(0);" data-ed-attachment-item-remove>&middot; <?php echo JText::_('COM_EASYDISCUSS_REMOVE'); ?></a>
			</span>


			<div class="ed-attachment-item-actions t-mt--md">
				<span class="ed-attachment-item-input o-btn o-btn--default-o" data-attachment-item-input>
					<i class="fa fa-upload"></i>&nbsp; <?php echo JText::_('COM_ED_UPLOAD_FILES');?>
					<input type="file" name="filedata[]" size="50"  />

					<?php echo JText::sprintf('COM_ED_UPLOAD_MAX', $uploadLimit);?>
				</span>
			</div>
		</div>

	<?php } else { ?>
		<div class="ed-editor-tab__content-note t-lg-mt--md">
			<?php echo JText::_('COM_EASYDISCUSS_EXCEED_ATTACHMENT_LIMIT'); ?>
		</div>
	<?php } ?>
</div>