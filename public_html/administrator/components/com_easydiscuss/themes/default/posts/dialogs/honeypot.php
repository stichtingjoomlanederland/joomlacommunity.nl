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
	<width>860</width>
	<height>460</height>
	<selectors type="json">
	{
		"{close}": "[data-close-button]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{close} click": function() {
			this.parent.close();
		}
	}
	</bindings>
	<title><?php echo JText::_('View Data');?></title>
	<content>
		<textarea class="o-form-control" style="height: 400px;" disabled><?php echo json_encode(json_decode($honeypot->data), JSON_PRETTY_PRINT);?></textarea>
	</content>
	<buttons>
		<button data-close-button type="button" class="ed-dialog-footer-content__btn"><?php echo JText::_('COM_ED_CLOSE_BUTTON'); ?></button>
	</buttons>
</dialog>
