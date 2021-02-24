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
	<width>400</width>
	<height>120</height>
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
	<title><?php echo JText::_('COM_EASYDISCUSS_POST_UNRESOLVED_TITLE'); ?></title>
	<content>
		<p class="mb-10">
			<?php echo JText::_('COM_EASYDISCUSS_POST_UNRESOLVED_DESC'); ?>
		</p>

		<form data-form-response method="post" action="<?php echo JRoute::_('index.php');?>">
			<input type="hidden" name="id" value="<?php echo $id;?>" />
			<?php echo JHTML::_('form.token');?>
			<?php echo $this->html('form.action', 'posts', 'posts', 'unresolve');?>
		</form>        
	</content>
	<buttons>
		<button data-close-button type="button" class="ed-dialog-footer-content__btn"><?php echo JText::_('COM_EASYDISCUSS_BUTTON_CLOSE'); ?></button>
		<button data-submit-button type="button" class="ed-dialog-footer-content__btn t-text--danger"><?php echo JText::_('COM_EASYDISCUSS_BUTTON_YES_MARK_POST_UNRESOLVED'); ?></button>
	</buttons>
</dialog>
