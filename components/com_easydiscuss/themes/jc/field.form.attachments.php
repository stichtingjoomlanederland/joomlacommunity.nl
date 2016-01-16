<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');

if( ( !$system->config->get( 'attachment_questions' ) || !$acl->allowed('add_attachment', '0' ) ) && !DiscussHelper::isSiteAdmin() ) {
	return;
}

$app    = JFactory::getApplication();
$config = DiscussHelper::getConfig();

$attachments             = ($post) ? $post->getAttachments() : null;
$hasAttachments          = !empty($attachments);
$hasAttachmentLimit      = $config->get('enable_attachment_limit');
$attachmentLimit         = $config->get('attachment_limit');
$attachmentLimitExceeded = $hasAttachmentLimit && (count($attachments) >= $attachmentLimit);
?>

<script type="text/javascript">
EasyDiscuss.require()
	.script('attachments')
	.done(function($){

		EasyDiscuss.module("<?php echo $composer->id; ?>")
			.done(function(){
				$('#attachmentsTab-<?php echo $composer->id; ?>').addController(
					"EasyDiscuss.Controller.Attachments",
					{
						hasAttachmentLimit: <?php echo ($hasAttachmentLimit) ? 'true' : 'false'; ?>,
						attachmentLimit: <?php echo $attachmentLimit; ?>
					});
			});
	});
</script>

<div id="attachmentsTab-<?php echo $composer->id; ?>"
	 class="tab-pane discuss-attachments editable <?php echo ($attachmentLimitExceeded) ? 'limit-exceeded' : ''; ?>">

	<div class="attachment-limit-exceed-hint alert alert-warn"><?php echo JText::_('COM_EASYDISCUSS_EXCEED_ATTACHMENT_LIMIT'); ?></div>

	<ul class="attachment-itemgroup reset-ul" data-attachment-itemgroup>
	<?php if ($hasAttachments) { ?>
	<?php foreach ($attachments as $attachment) { ?>
		<li data-attachment-item
			id="attachment-<?php echo $attachment->id; ?>"
		    class="attachment-item attachment-type-<?php echo $attachment->getType(); ?>">
			<span data-attachment-title><?php echo $attachment->title; ?></span>
			<?php if ($attachment->deleteable() && !$app->isAdmin()) { ?>
			 <a data-attachment-remove-button href="javascript:void(0);" data-id="<?php echo $attachment->id; ?>"> &bull; <?php echo JText::_('COM_EASYDISCUSS_REMOVE'); ?></a>
			<?php } ?>
		</li>
	<?php } ?>
	<?php } ?>

	<?php if (!$attachmentLimitExceeded) { ?>
		<li data-attachment-item class="attachment-item new">
			<span data-attachment-title></span>
			<a data-attachment-remove-button href="javascript:void(0);"> &bull; <?php echo JText::_('COM_EASYDISCUSS_REMOVE'); ?></a>
			<input type="file" name="filedata[]" size="50" data-attachment-file disabled />
		</li>
	<?php } ?>
	</ul>
</div>
