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
<div id="attachments-<?php echo $editorId;?>" class="ed-editor-tab__content attachments-tab tab-pane" <?php echo $editorId;?> data-ed-attachments>

	<div class="attachment-itemgroup unstyled" data-ed-attachments-list>
		<?php if ($attachments) { ?>
			<?php foreach ($attachments as $attachment) { ?>
				<div id="attachment-<?php echo $attachment->id;?>" class="attachment-item attachment-type-<?php echo $attachment->getType();?>">
					<i class="attachment-icon" data-ed-attachment-item-icon></i>
					<span class="attachment-title" data-ed-attachment-item-title><?php echo $attachment->title;?></span>

					<?php if ($this->config->get('layout_editor') == 'bbcode' && $attachment->isImage()) { ?>
						<a href="javascript:void(0);" data-ed-attachment-item-insert> &bull; <?php echo JText::_('COM_EASYDISCUSS_INSERT'); ?></a>
					<?php } ?>

					<?php if ($attachment->canDelete()) { ?>
					<a href="javascript:void(0);" data-ed-attachment-item-remove data-id="<?php echo $attachment->id;?>"> &bull; <?php echo JText::_('COM_EASYDISCUSS_REMOVE'); ?></a>
					<?php } ?>
				</div>
			<?php } ?>
		<?php } ?>
	</div>

	<?php if (!$exceededLimit) { ?>
		<div class="ed-editor-tab__content-note t-lg-mt--md" data-ed-attachment-info>
			<?php echo JText::sprintf('COM_EASYDISCUSS_ATTACHMENTS_INFO', $allowedExtensions); ?>
		</div>

		<div class="ed-attachment-form attachment-item" data-ed-attachment-form>
			<i class="attachment-icon" data-ed-attachment-item-icon></i>
			<span class="attachment-title" data-ed-attachment-item-title></span>

			<?php if ($this->config->get('layout_editor') == 'bbcode') { ?>
				<a href="javascript:void(0);" data-ed-attachment-item-insert> &bull; <?php echo JText::_('COM_EASYDISCUSS_INSERT'); ?></a>
			<?php } ?>
			<a href="javascript:void(0);" data-ed-attachment-item-remove> &bull; <?php echo JText::_('COM_EASYDISCUSS_REMOVE'); ?></a>

			<span class="btn btn-default btn-file" data-attachment-item-input>
				<?php echo JText::sprintf('COM_EASYDISCUSS_UPLOAD_BUTTON_WITH_LIMIT', $uploadLimit);?> <input type="file" name="filedata[]" size="50"  />
			</span>
		</div>
	<?php } else { ?>
		<div class="ed-editor-tab__content-note t-lg-mt--md">
			<?php echo JText::_('COM_EASYDISCUSS_EXCEED_ATTACHMENT_LIMIT'); ?>
		</div>
	<?php } ?>
</div>