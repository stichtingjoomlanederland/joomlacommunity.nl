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
    <height>300</height>
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
            this.form().submit();
        }
    }
    </bindings>
    <title><?php echo JText::sprintf('COM_EASYDISCUSS_FORM_BAN_USER_TITLE', $post->getOwner()->getName())?></title>
    <content>
        <form data-form-response method="post" action="<?php echo JRoute::_('index.php');?>">

            <div class="o-flag">
                <div class="o-flag__body">
                    <div class="ed-user-rank t-lg-mb--sm">
                        <p><?php echo JText::_('COM_EASYDISCUSS_SELECT_BAN_DURATION'); ?></p>
                        <select name="duration" class="inputbox full-width">
                            <option value="1"><?php echo JText::_('COM_EASYDISCUSS_BAN_DURATION_ONE_HOUR')?></option>
                            <option value="3"><?php echo JText::_('COM_EASYDISCUSS_BAN_DURATION_THREE_HOUR')?></option>
                            <option value="6"><?php echo JText::_('COM_EASYDISCUSS_BAN_DURATION_SIX_HOUR')?></option>
                            <option value="12"><?php echo JText::_('COM_EASYDISCUSS_BAN_DURATION_HALF_DAY')?></option>
                            <option value="24"><?php echo JText::_('COM_EASYDISCUSS_BAN_DURATION_ONE_DAY')?></option>
                            <option value="48"><?php echo JText::_('COM_EASYDISCUSS_BAN_DURATION_TWO_DAYS')?></option>
                            <option value="168"><?php echo JText::_('COM_EASYDISCUSS_BAN_DURATION_A_WEEK')?></option>
                            <option value="720"><?php echo JText::_('COM_EASYDISCUSS_BAN_DURATION_A_MONTH')?></option>
                            <option value="2160"><?php echo JText::_('COM_EASYDISCUSS_BAN_DURATION_NINETY_DAYS')?></option>
                            <option value="4320"><?php echo JText::_('COM_EASYDISCUSS_BAN_DURATION_HUNDRED_EIGHTY_DAYS')?></option>
                        </select>
                    </div>
                </div>
            </div>

            <textarea id="reporttext" name="reporttext" class="textarea full-width" rows="6" style="width:100%;"></textarea>

            <div>
                <span class="label label-info small"><?php echo JText::_('COM_EASYDISCUSS_NOTE');?>:</span>
                <span class="small"><?php echo JText::_('COM_EASYDISCUSS_FORM_BAN_USER_NOTE');?></span>
            </div>

            <input type="hidden" id="postid" name="postid" value="<?php echo $post->id; ?>">
            <input type="hidden" id="postName" name="postName" value="<?php echo $post->getOwner()->getName(); ?>">
            <?php echo $this->html('form.hidden', 'posts', 'posts', 'banUser');?>
        </form>        
    </content>
    <buttons>
        <button data-close-button type="button" class="btn btn-default btn-sm"><?php echo JText::_('COM_EASYDISCUSS_BUTTON_CLOSE'); ?></button>
        <button data-submit-button type="button" class="btn btn-danger btn-sm"><?php echo JText::_('COM_EASYDISCUSS_BUTTON_CONFIRM_BAN'); ?></button>
    </buttons>
</dialog>
