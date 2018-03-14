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
    <height>340</height>
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
    <title><?php echo JText::_('COM_EASYDISCUSS_MERGE_POST_TITLE'); ?></title>
    <content>
        <p class="mb-10">
            <?php echo JText::_('COM_EASYDISCUSS_MERGE_POST_DESC'); ?>
        </p>

        <form data-form-response method="post" action="<?php echo JRoute::_('index.php');?>">

            <div>
                <span class="label label-info small"><?php echo JText::_('COM_EASYDISCUSS_NOTE');?>:</span>
                <span class="small"><?php echo JText::_('COM_EASYDISCUSS_MERGE_NOTES');?></span>
            </div>
            
            <div class="mt-20">
                <?php if ($posts){ ?>
                    <select name="id" class="inputbox full-width" data-field-suggest>
                        <?php foreach ($posts as $post) { ?>
                            <?php if ($post->id != $current) { ?>
                                <option value="<?php echo $post->id;?>"><?php echo $post->id; ?> - <?php echo $this->escape($post->title); ?></option>
                            <?php } ?>
                        <?php } ?>
                    </select>
                <?php } else { ?>
                    <div class="o-alert o-alert--error"><?php echo JText::_('COM_EASYDISCUSS_MERGE_NO_POSTS');?></div>
                <?php } ?>
            </div>
            
            <input type="hidden" name="current" value="<?php echo $current;?>" />
            <?php echo $this->html('form.hidden', 'posts', 'posts', 'merge');?>

        </form>       
    </content>
    <buttons>
        <button data-close-button type="button" class="btn btn-default btn-sm"><?php echo JText::_('COM_EASYDISCUSS_BUTTON_CLOSE'); ?></button>
        <button data-submit-button type="button" class="btn btn-default btn-sm"><?php echo JText::_('COM_EASYDISCUSS_BUTTON_MERGE'); ?></button>
    </buttons>
</dialog>
