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
    <width>600</width>
    <height>240</height>
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
            this.parent.close();
        }
    }
    </bindings>
    <title><?php echo JText::_('COM_EASYDISCUSS_TERMS_AND_CONDITIONS'); ?></title>
    <content>
        <p class="mb-10">
            <?php echo nl2br($this->config->get('main_tnctext')); ?>
        </p>

        <form data-form-response method="post" action="<?php echo JRoute::_('index.php');?>">
            <?php echo JHTML::_('form.token');?>
        </form>        
    </content>
    <buttons>
        <button data-submit-button type="button" class="btn btn-danger btn-sm"><?php echo JText::_('COM_EASYDISCUSS_BUTTON_CLOSE'); ?></button>
    </buttons>
</dialog>
