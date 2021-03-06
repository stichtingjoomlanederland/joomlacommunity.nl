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
	<width>900</width>
	<height>600</height>
	<selectors type="json">
	{
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
	<title><?php echo JText::_('COM_EASYDISCUSS_BROWSE_POSTS'); ?></title>
	<content type="text"><?php echo JURI::root();?>administrator/index.php?option=com_easydiscuss&view=posts&tmpl=component&browse=1&browseFunction=insertPost</content>
	<buttons>
		<button data-cancel-button type="button" class="ed-dialog-footer-content__btn"><?php echo JText::_('COM_EASYDISCUSS_CANCEL_BUTTON'); ?></button>
	</buttons>
</dialog>
