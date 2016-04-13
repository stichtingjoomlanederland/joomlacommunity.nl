<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
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
    <height>145</height>
    <selectors type="json">
    {
        "{closeButton}" : "[data-close-button]",
        "{submitButton}" : "[data-submit-button]"
    }
    </selectors>
    <bindings type="javascript">
    {
        "{closeButton} click": function() {
            this.parent.close();
        },
        "{submitButton} click": function() {

            EasyDiscuss.ajax('site/views/subscription/unsubscribe', {
                "type": '<?php echo $type; ?>',
                "cid": '<?php echo $cid; ?>',
                "sid": '<?php echo $sid; ?>'
            })
            .done(function(msg) {
                EasyDiscuss.dialog({
                    content: msg
                });

                // refresh the page in next 1.5 seconds
                setTimeout(function(){
                    window.location.reload(1);
                }, 1500);
            })
            .fail(function(msg) {
                EasyDiscuss.dialog({
                    content: msg
                });
            });
        }
    }
    </bindings>
    <title><?php echo JText::_('COM_EASYDISCUSS_UNSUBSCRIBE_TO_' . strtoupper($type)); ?></title>
    <content>
        <p class="mb-10">
            <?php echo JText::_('COM_EASYDISCUSS_UNSUBSCRIBE_TO_' . strtoupper($type) . '_DESC'); ?>
        </p>
    </content>
    <buttons>
        <button data-close-button type="button" class="btn btn-default btn-sm"><?php echo JText::_('COM_EASYDISCUSS_BUTTON_CANCEL'); ?></button>
        <button data-submit-button type="button" class="btn btn-primary btn-sm"><?php echo JText::_('COM_EASYDISCUSS_BUTTON_UNSUBSCRIBE'); ?></button>
    </buttons>
</dialog>
