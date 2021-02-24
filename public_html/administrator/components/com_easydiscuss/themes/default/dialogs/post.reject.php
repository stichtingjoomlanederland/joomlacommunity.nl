<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
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
	<width>600</width>
	<height>300</height>
	<selectors type="json">
	{
		"{submitButton}": "[data-submit-button]",
		"{cancelButton}": "[data-cancel-button]",
		"{form}": "[data-reject-form]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{cancelButton} click": function() {
			this.parent.close();
		},

		"{submitButton} click": function() {
			this.form().submit();
		}
	}
	</bindings>
	<title><?php echo JText::_('COM_ED_REJECT_POST_DIALOG_TITLE');?></title>
	<content>
		<form action="<?php echo JRoute::_('index.php');?>" method="post" data-reject-form>
			<p><?php echo JText::_('COM_ED_REJECT_POST_DIALOG_CONTENT'); ?></p>

			<div class="t-mt--lg t-mb--lg">
				<div class="o-form-group">
					<?php echo $this->html('form.textarea', 'message', '', 5, 'data-reason placeholder="' . JText::_('COM_ED_REJECT_POST_PLACEHOLDER') . '"'); ?>
				</div>
			</div>

			<?php foreach ($ids as $id) { ?>
			<input type="hidden" name="ids[]" value="<?php echo $id;?>" />
			<?php } ?>

			<input type="hidden" name="option" value="com_easydiscuss" />
			<input type="hidden" name="from" value="pending" />
			<input type="hidden" name="controller" value="posts" />
			<input type="hidden" id="task" name="task" value="reject" />
			<?php echo JHTML::_('form.token'); ?>
		</form>
	</content>
	<buttons>
		<button data-cancel-button type="button" class="ed-dialog-footer-content__btn"><?php echo JText::_('COM_EASYDISCUSS_CANCEL_BUTTON'); ?></button>
		<button data-submit-button type="button" class="ed-dialog-footer-content__btn t-text--danger"><?php echo JText::_('COM_EASYDISCUSS_REJECT_BUTTON'); ?></button>
	</buttons>
</dialog>

