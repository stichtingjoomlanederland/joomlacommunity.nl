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
	<width>480</width>
	<height>400</height>
	<selectors type="json">
	{
		"{dialogCloseButton}": ".ed-dialog-close-button",
		"{closeButton}" : "[data-close-button]",
		"{form}" : "[data-form-response]",
		"{submitButton}" : "[data-submit-button]",
		"{selectInput}" : "[data-field-suggest]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{submitButton} click": function() {

			var selected = this.selectInput().val();
			if (selected == "") {
				return false;
			}

			this.form().submit();
		}
	}
	</bindings>
	<title><?php echo JText::_('COM_EASYDISCUSS_MERGE_POST_TITLE'); ?></title>
	<content>
		<p class="t-mb--lg">
			<?php echo JText::_('COM_EASYDISCUSS_MERGE_POST_DESC'); ?>
		</p>

		<form data-form-response method="post" action="<?php echo JRoute::_('index.php');?>">
			<div class="t-mt--lg t-mb--lg">
				<select name="id" class="o-form-select" data-field-suggest>
					<option value=""><?php echo JText::_('COM_ED_MERGE_SELECT_POST');?></option>
				</select>
			</div>
			
			<div class="t-mt--lg">
				<?php echo JText::_('COM_EASYDISCUSS_MERGE_NOTES');?>
			</div>
			
			<input type="hidden" name="current" value="<?php echo $current;?>" />
			<?php echo $this->html('form.action', 'posts', 'posts', 'merge');?>

		</form>
	</content>
	<buttons>
		<button data-close-button type="button" class="ed-dialog-footer-content__btn"><?php echo JText::_('COM_EASYDISCUSS_BUTTON_CLOSE'); ?></button>
		<button data-submit-button type="button" class="ed-dialog-footer-content__btn t-text--primary"><?php echo JText::_('COM_EASYDISCUSS_BUTTON_MERGE'); ?></button>
	</buttons>
</dialog>
