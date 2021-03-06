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
    <width>400</width>
    <height><?php echo (! $this->my->guest) ? '175' : '230'; ?></height>
    <selectors type="json">
    {
        "{closeButton}" : "[data-close-button]",
        "{form}" : "[data-form-response]",
        "{submitButton}" : "[data-submit-button]"
    }
    </selectors>
    <bindings type="javascript">
    {
        "{closeButton} click": function() {
            this.parent.close();
        },
        "{submitButton} click": function() {

            if (EasyDiscuss.$('#subscribe_email').val() == "" || EasyDiscuss.$('#subscribe_name').val() == "") {
                //show message here.
                EasyDiscuss.$('[data-subscribe-message]').removeClass('hide');
                return false;
            }

            $oriErrorMsg = EasyDiscuss.$('[data-subscribe-message]').text;

            // hide message bar if it is visiable to user.
            EasyDiscuss.$('[data-subscribe-message]').addClass('hide');

            EasyDiscuss.ajax('site/views/subscription/process', {
                "type": '<?php echo $type; ?>',
                "cid": '<?php echo $cid; ?>',
                "subscribe_email" : EasyDiscuss.$('#subscribe_email').val(),
                "subscribe_name" : EasyDiscuss.$('#subscribe_name').val()
            })
            .fail(function(msg){
                EasyDiscuss.$('[data-subscribe-message]')
                    .removeClass('hide')
                    .text(msg);
            })
            .done(function(msg) {
                EasyDiscuss.dialog({
                    content: msg,
                    buttons: '<button class="btn btn-default btn-sm" type="button" onclick="EasyDiscuss.dialog.close();window.location.reload(1);"><?php echo JText::_('COM_EASYDISCUSS_BUTTON_CLOSE'); ?></button>'
                });
            });

        }
    }
    </bindings>
    <title><?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_TO_SITE'); ?></title>
    <content>
        <p class="mb-10">
            <?php if ($subscription) { ?>
                <?php echo JText::_('COM_EASYDISCUSS_ALREADY_SUBSCRIBED'); ?>
            <?php } else { ?>
                <?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_SITE_DESCRIPTION');?>
            <?php } ?>
        </p>

        <div class="o-alert o-alert--warning t-hidden" role="alert" data-subscribe-message><?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_PLEASE_ENTER_NAME_EMAIL'); ?></div>

        <form data-form-response method="post" action="<?php echo JRoute::_('index.php');?>">

            <?php if ($this->my->id) { ?>
            <div class="form-horizontal">
                <label><?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_YOUR_EMAIL');?> : </label>
                <span class="dc_ico email"><b><?php echo $this->my->email; ?></b></span>
                <input type="hidden" id="subscribe_email" name="subscribe_email" value="<?php echo $this->my->email; ?>">
                <input type="hidden" id="subscribe_name" name="subscribe_name" value="<?php echo $this->my->name; ?>">
            </div>
            <?php } else {  ?>
            <div class="form-horizontal">
                <div class="form-group">
                    <label class="col-sm-4 control-label" for="subscribe_email"><?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_YOUR_EMAIL');?> : </label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control input-sm" id="subscribe_email" name="subscribe_email" value="" />
                    </div>
                </div>
            </div>

            <div class="form-horizontal">
                <div class="form-group">
                    <label class="col-sm-4 control-label" for="subscribe_name"><?php echo JText::_('COM_EASYDISCUSS_NAME');?> : </label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control input-sm" id="subscribe_name" name="subscribe_name" value="" />
                    </div>
                </div>
            </div>
            <?php } ?>

            <!-- input type="hidden" name="cid" value="<?php echo $cid; ?>" />
            <input type="hidden" name="type" value="<?php echo $type; ?>" / -->

            <?php //echo $this->html('form.hidden', 'subscription', 'index', 'subscribe');?>
        </form>
    </content>
    <buttons>
        <button data-close-button type="button" class="btn btn-default btn-sm"><?php echo JText::_('COM_EASYDISCUSS_BUTTON_CANCEL'); ?></button>
        <button data-submit-button type="button" class="btn btn-primary btn-sm"><?php echo JText::_('COM_EASYDISCUSS_BUTTON_SUBSCRIBE'); ?></button>
    </buttons>
</dialog>
