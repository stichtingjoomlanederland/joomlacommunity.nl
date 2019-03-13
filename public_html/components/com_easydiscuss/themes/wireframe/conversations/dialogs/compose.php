<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<dialog>
	<width>700</width>
	<height>450</height>
	<selectors type="json">
	{
		"{closeButton}" : "[data-close-button]",
		"{form}" : "[data-form-response]",
		"{submitButton}" : "[data-submit-button]",
		"{recipient}": "[data-ed-recipient]",
		"{message}": "[data-ed-conversation-api-message]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{closeButton} click": function() {
			this.parent.close();
		},

		"{submitButton} click": function() {

			var recipientId = this.recipient().val();

			EasyDiscuss.ajax('site/controllers/conversation/save', {
				"recipient": recipientId,
				"message": this.message().val()
			}).done(function() {
				EasyDiscuss.dialog({
					content: EasyDiscuss.ajax('site/views/conversation/sent', {"id": recipientId})
				})
			}).fail(function(msg) {
				EasyDiscuss.dialog({
					content: msg
				})
			});
		}
	}
	</bindings>
	<title><?php echo JText::sprintf('COM_EASYDISCUSS_CONVERSATION_COMPOSE_DIALOG_TITLE', $recipient->getName()); ?></title>
	<content>
		<script type="text/javascript">
		ed.require(['edq', 'easydiscuss', 'markitup', 'jquery.expanding'], function($, EasyDiscuss) {
			var textarea = $('[data-ed-conversation-api-message]');

			// Apply markitup
			textarea.markItUp({
				markupSet: EasyDiscuss.bbcode
			});

			// Apply expanding
			textarea.expandingTextarea();
		});
		</script>
		<form action="<?php echo JRoute::_('index.php');?>" method="post" data-form-response>
			<div class="t-lg-mt--xl t-lg-mb--xl">
				<?php echo JText::_('COM_EASYDISCUSS_WRITING_TO');?>
				<a href="<?php echo $recipient->getLink();?>"><?php echo $recipient->getName();?></a>
			</div>

			<div class="ed-convo-dialog">
				<textarea name="message" class="form-control" data-ed-conversation-api-message data-dialog-recipient="<?php echo $recipient->id;?>"><?php echo $previousContents; ?></textarea>
			</div>

			<input type="hidden" name="recipient" value="<?php echo $recipient->id;?>" data-ed-recipient />
			<?php echo $this->html('form.hidden', 'conversation', 'conversation', 'save'); ?>
		</form>
	</content>
	<buttons>
		<button data-close-button type="button" class="btn btn-default btn-sm"><?php echo JText::_('COM_EASYDISCUSS_BUTTON_CLOSE'); ?></button>
		<button data-submit-button type="button" class="btn btn-primary btn-sm"><?php echo JText::_('COM_EASYDISCUSS_BUTTON_SEND_MESSAGE'); ?></button>
	</buttons>
</dialog>
