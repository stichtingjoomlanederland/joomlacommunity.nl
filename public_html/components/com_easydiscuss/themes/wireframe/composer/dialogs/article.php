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

		"{form}" : "[data-ed-form]",
		"{articleId}": "[data-ed-article-id]",
		"{contentType}": "[data-ed-article-type]",
		"{message}": "[data-ed-message]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{closeButton} click": function() {
			this.parent.close();
		},

		"{submitButton} click": function() {
			var id = this.articleId().val();
			var contents = this.message().val();
			var type = this.contentType().val();
			
			// Insert the link
			window.insertArticleCode(id, type, "<?php echo $caretPosition;?>", "<?php echo $element;?>", contents, "<?php echo $dialogRecipient; ?>");

			// Close the dialog
			this.parent.close();
		}
	}
	</bindings>
	<title><?php echo JText::_('COM_ED_BBCODE_INSERT_ARTICLE'); ?></title>
	<content>
		<form data-ed-form>
			<div class="form-group">
				<label for="articleId">
					<strong><?php echo JText::_('COM_ED_BBCODE_ARTICLE_ID');?>:</strong>
				</label>
				<input type="text" id="articleId" value="" class="form-control" data-ed-article-id />
			</div>

			<div class="form-group">
				<label for="contentType">
					<strong><?php echo JText::_('COM_ED_BBCODE_ARTICLE_CONTENT');?>:</strong>
				</label>
				<select class="form-control" data-ed-article-type>
					<option value="intro"><?php echo JText::_('COM_ED_BBCODE_ARTICLE_INTRO'); ?></option>
					<option value="full"><?php echo JText::_('COM_ED_BBCODE_ARTICLE_FULL'); ?></option>
				</select>
			</div>

			<input type="hidden" id="message" value="<?php echo ED::string()->escape($contents); ?>" data-ed-message />
		</form>
	</content>
	<buttons>
		<button data-close-button type="button" class="ed-dialog-footer-content__btn"><?php echo JText::_('COM_EASYDISCUSS_BUTTON_CANCEL'); ?></button>
		<button data-submit-button type="button" class="ed-dialog-footer-content__btn t-text--primary"><?php echo JText::_('COM_EASYDISCUSS_BUTTON_INSERT'); ?></button>
	</buttons>
</dialog>
