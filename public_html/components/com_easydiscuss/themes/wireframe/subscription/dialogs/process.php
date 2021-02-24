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
	<width>450</width>
	<height>200</height>
	<selectors type="json">
	{
		"{closeButton}": "[data-close-button]",
		"{manageButton}": "[data-manage-button]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{closeButton} click": function() {
			window.location.reload();

			this.parent.close();
		},

		"{manageButton} click": function() {
			window.location = "<?php echo $manageLink;?>";
		}
	}
	</bindings>
	<title><?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_TO_SITE'); ?></title>
	<content>
		<p>
			<?php echo $contents;?>
		</p>
	</content>
	<buttons>
		<button data-close-button type="button" class="ed-dialog-footer-content__btn"><?php echo JText::_('COM_EASYDISCUSS_BUTTON_CLOSE'); ?></button>
		<?php if ($manageLink) { ?>
		<button data-manage-button type="button" class="ed-dialog-footer-content__btn t-text--primary"><?php echo JText::_('COM_ED_MANAGE_SUBSCRIPTION'); ?></button>
		<?php } ?>
	</buttons>
</dialog>
