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
	<width>1280</width>
	<height>800</height>
	<selectors type="json">
	{
		"{closeButton}" : "[data-close-button]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{closeButton} click": function() {
			this.parent.close();
		}
	}
	</bindings>
	<title><?php echo JText::_('COM_EASYDISCUSS_BROWSE_USERS'); ?></title>
	<content type="text"><?php echo $url;?></content>
	<buttons>
		<button data-close-button type="button" class="ed-dialog-footer-content__btn"><?php echo JText::_('COM_EASYDISCUSS_BROWSE_USER_DIALOG_CLOSE'); ?></button>
	</buttons>
</dialog>
