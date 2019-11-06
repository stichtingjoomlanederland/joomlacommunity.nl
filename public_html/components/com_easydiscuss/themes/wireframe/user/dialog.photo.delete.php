<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
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
    <width>400</width>
    <height><?php echo (!$this->my->guest) ? '145' : '200'; ?></height>
    <selectors type="json">
    {
        "{closeButton}" : "[data-close-button]",
        "{form}" : "[data-form-response]",
        "{deleteButton}" : "[data-delete-button]"
    }
    </selectors>
    <bindings type="javascript">
    {
        "{closeButton} click": function() {
            this.parent.close();
        },
        "{deleteButton} click": function() {
            this.deleteButton().addClass("disabled");
            this.closeButton().addClass("disabled");
        }
    }
    </bindings>
    <title><?php echo JText::_('COM_EASYDISCUSS_REMOVE_AVATAR'); ?></title>
    <content>
        <p class="mb-10">
            <?php echo JText::_('COM_EASYDISCUSS_REMOVE_AVATAR_DESCRIPTION');?>
        </p>
    </content>
    <buttons>
        <button data-close-button type="button" class="btn btn-default btn-sm"><?php echo JText::_('COM_EASYDISCUSS_BUTTON_CANCEL'); ?></button>
        <a href="<?php echo EDR::_( 'index.php?option=com_easydiscuss&controller=profile&task=removePicture');?>" data-delete-button type="button" class="btn btn-danger btn-sm"><?php echo JText::_('COM_EASYDISCUSS_AVATAR_BUTTON_DELETE'); ?></button>
    </buttons>
</dialog>
