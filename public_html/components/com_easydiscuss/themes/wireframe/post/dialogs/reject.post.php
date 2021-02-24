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
?>
<dialog>
	<width>500</width>
	<height>250</height>
	<selectors type="json">
	{
		"{closeButton}" : "[data-close-button]",
		"{form}" : "[data-form-response]",
		"{submitButton}" : "[data-submit-button]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{closeButton} click": function() {
			this.parent.close();
		},
		"{submitButton} click": function() {
			this.form().submit();
		}
	}
	</bindings>
	<title><?php echo JText::_('COM_EASYDISCUSS_REPLY_MODERATION_REJECT_TITLE'); ?></title>
	<content>
		<p class="mb-10">
			<?php echo JText::_('COM_EASYDISCUSS_REPLY_MODERATION_REJECT_DESC'); ?>
		</p>

		<form data-form-response method="post" action="<?php echo JRoute::_('index.php');?>">
			<div class="o-form-group">
				<?php echo $this->html('form.textarea', 'message', '', 5, 'data-reason placeholder="' . JText::_('COM_ED_REJECT_REPLY_PLACEHOLDER') . '"'); ?>
			</div>
		
			<input type="hidden" id="postId" name="postId" value="<?php echo $id; ?>">
			<?php echo $this->html('form.action', 'posts', 'post', 'rejectPendingPost');?>
		</form>
	</content>
	<buttons>
		<button data-close-button type="button" class="ed-dialog-footer-content__btn"><?php echo JText::_('COM_EASYDISCUSS_BUTTON_CANCEL'); ?></button>
		<button data-submit-button type="button" class="ed-dialog-footer-content__btn t-text--danger"><?php echo JText::_('COM_EASYDISCUSS_BUTTON_REJECT_REPLY'); ?></button>
	</buttons>
</dialog>