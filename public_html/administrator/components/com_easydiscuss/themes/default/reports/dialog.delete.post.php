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
    <width>400</width>
    <height>120</height>
    <selectors type="json">
    {
        "{deleteButton}": "[data-delete-button]",
        "{cancelButton}": "[data-cancel-button]",
        "{form}": "[data-form-delete-post]"
    }
    </selectors>
    <bindings type="javascript">
    {
        "{deleteButton} click": function() {
            this.form().submit();
        }
    }
    </bindings>
    <title><?php echo JText::_('COM_EASYDISCUSS_DIALOG_REPORTS_DELETE_POST'); ?></title>
    <content>
        <form data-form-delete-post method="post" action="<?php echo JRoute::_('index.php');?>">
    	   <p><?php echo JText::_('COM_EASYDISCUSS_DIALOG_REPORTS_DELETE_POST_CONFIRMATION');?></p>
           <input type="hidden" name="post_id" value="<?php echo $id;?>" />
           <?php echo $this->html('form.hidden', 'reports', 'reports', 'deletePost'); ?>
        </form>
    </content>
    <buttons>
        <button data-cancel-button type="button" class="btn btn-default btn-sm"><?php echo JText::_('COM_EASYDISCUSS_CANCEL_BUTTON'); ?></button>
        <button data-delete-button type="button" class="btn btn-danger btn-sm"><?php echo JText::_('COM_EASYDISCUSS_DELETE_BUTTON'); ?></button>
    </buttons>
</dialog>
