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
	<height>100</height>
	<selectors type="json">
	{
		"{submitButton}": "[data-submit-button]",
		"{cancelButton}": "[data-cancel-button]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{cancelButton} click": function() {
			this.parent.close();
		}
	}
	</bindings>
	<title><?php echo JText::_('Uninstall Selected Modules');?></title>
	<content>
		<p>
			<?php echo JText::_('Are you sure you want to uninstall the selected modules? Once the module is uninstalled, your settings on the module would also be erased.');?>
		</p>
	</content>
	<buttons>
		<button data-cancel-button type="button" class="ed-dialog-footer-content__btn"><?php echo JText::_('COM_EASYDISCUSS_CANCEL_BUTTON'); ?></button>
		<button data-submit-button type="button" class="ed-dialog-footer-content__btn t-text--danger"><?php echo JText::_('Uninstall'); ?></button>
	</buttons>
</dialog>
