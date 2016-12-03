<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
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
    <height>120</height>
    <selectors type="json">
    {
        "{approveButton}" : "[data-approve-button]",
        "{form}" : "[data-form-response]",
        "{rejectButton}" : "[data-reject-button]"
    }
    </selectors>
    <title><?php echo JText::_('COM_EASYDISCUSS_DIALOG_MODERATE_TITLE'); ?></title>
    <content>
        <p class="mb-10">
            <?php echo JText::_('COM_EASYDISCUSS_DIALOG_MODERATE_CONTENT'); ?>
        </p>
    </content>
    <buttons>
        <button data-approve-button type="button" class="btn btn-default btn-sm"><?php echo JText::_('COM_EASYDISCUSS_APPROVE_BUTTON'); ?></button>
        <button data-reject-button type="button" class="btn btn-danger btn-sm"><?php echo JText::_('COM_EASYDISCUSS_REJECT_BUTTON'); ?></button>
    </buttons>
</dialog>
