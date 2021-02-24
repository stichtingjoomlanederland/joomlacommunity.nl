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
<dialog>
	<width>400</width>
	<height><?php echo (!$this->my->guest) ? '145' : '200'; ?></height>
	<selectors type="json">
	{
		"{closeButton}" : "[data-close-button]",
		"{form}" : "[data-delete-form]",
		"{deleteButton}" : "[data-delete-button]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{closeButton} click": function() {
			this.parent.close();
		},

		"{deleteButton} click": function() {
			this.form().submit();
		}
	}
	</bindings>
	<title><?php echo JText::_('COM_EASYDISCUSS_REMOVE_AVATAR'); ?></title>
	<content>
		<p class="mb-10">
			<?php echo JText::_('COM_EASYDISCUSS_REMOVE_AVATAR_DESCRIPTION');?>
		</p>
		<form method="post" action="<?php echo JRoute::_('index.php');?>" data-delete-form>
			<?php echo $this->html("form.action", 'profile', '', 'removePicture'); ?>
		</form>
	</content>
	<buttons>
		<button data-close-button type="button" class="ed-dialog-footer-content__btn"><?php echo JText::_('COM_EASYDISCUSS_BUTTON_CANCEL'); ?></button>
		<button data-delete-button type="button" class="ed-dialog-footer-content__btn t-text--danger"><?php echo JText::_('COM_EASYDISCUSS_AVATAR_BUTTON_DELETE'); ?></button>
	</buttons>
</dialog>
