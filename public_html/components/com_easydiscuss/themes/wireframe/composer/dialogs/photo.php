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
	<width>600</width>
	<height>200</height>
	<selectors type="json">
	{
		"{closeButton}" : "[data-close-button]",
		"{submitButton}" : "[data-submit-button]",

		"{form}" : "[data-ed-photo-form]",
		"{photoUrl}": "[data-ed-photo-url]",
		"{message}": "[data-ed-message]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{closeButton} click": function() {
			this.parent.close();
		},

		"{submitButton} click": function() {
			var url = this.photoUrl().val();
			var contents = this.message().val();

			// Insert the photo
			window.insertPhotoCode(url, "<?php echo $caretPosition;?>", "<?php echo $element;?>", contents, "<?php echo $dialogRecipient; ?>");

			// Close the dialog
			this.parent.close();
		}
	}
	</bindings>
	<title><?php echo JText::_('COM_EASYDISCUSS_BBCODE_INSERT_PHOTO'); ?></title>
	<content>
		<form class="l-stack" data-ed-photo-form>
			<div>
				<label class="o-form-label" for="photo">
					<strong><?php echo JText::_('COM_EASYDISCUSS_PHOTO_URL');?>:</strong>
				</label>
				<input type="text" id="photo" value="" class="o-form-control" data-ed-photo-url />
				<input type="hidden" id="message" value="<?php echo ED::string()->escape($contents); ?>" data-ed-message />

				<div class="o-form-text"><?php echo JText::_('COM_ED_PHOTO_TITLE_HELP');?></div>
			</div>
		</form>
	</content>
	<buttons>
		<button data-close-button type="button" class="ed-dialog-footer-content__btn"><?php echo JText::_('COM_EASYDISCUSS_BUTTON_CANCEL'); ?></button>
		<button data-submit-button type="button" class="ed-dialog-footer-content__btn t-text--primary"><?php echo JText::_('COM_EASYDISCUSS_BUTTON_INSERT'); ?></button>
	</buttons>
</dialog>
