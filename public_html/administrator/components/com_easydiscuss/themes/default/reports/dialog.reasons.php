<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<dialog>
    <width>960</width>
    <height>400</height>
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
    <title><?php echo JText::_('COM_EASYDISCUSS_DIALOG_REPORTS_DELETE_POST'); ?></title>
    <content>
        <iframe src="<?php echo $url; ?>" width="100%" height="100%" frameborder="0" scrolling="auto" />
    </content>
    <buttons>
        <button data-cancel-button type="button" class="btn btn-default btn-sm"><?php echo JText::_('COM_EASYDISCUSS_BUTTON_CLOSE'); ?></button>
    </buttons>
</dialog>
