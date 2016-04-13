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
    
<div class="ed-pass-note">
    <div class="ed-pass-note__title">
        <?php echo JText::_('COM_EASYDISCUSS_PASSWORD_FORM_TITLE'); ?>        
    </div>
    <div class="ed-pass-note__desp">
        <?php echo JText::_('COM_EASYDISCUSS_PASSWORD_FORM_TIPS'); ?>
    </div>
    <form action="<?php echo JRoute::_('index.php');?>" method="post">
    <div class="o-grid o-grid--1of2">
        <div class="o-grid__cell">
            <div class="input-group t-lg-mb--lg">
                <span id="sizing-addon1" class="input-group-addon"><i class="fa fa-lock"></i></span>
                <input type="password" name="discusspassword" id="password-post" autocomplete="off" placeholder="<?php echo JText::_('COM_EASYDISCUSS_INSERT_PASSWORD'); ?>" class="form-control">
                <span class="input-group-btn">
                    <input type="submit" class="btn btn-default" value="<?php echo JText::_('COM_EASYDISCUSS_VIEW_POST_BUTTON'); ?>" />
                </span>
            </div>            
        </div>
    </div>
    <?php echo $this->html('form.hidden', 'posts','index', 'setPassword'); ?>
    <input type="hidden" name="id" value="<?php echo $post->id;?>" />
    <input type="hidden" name="type" value="<?php echo $type;?>" />
    <input type="hidden" name="return" value="<?php echo base64_encode('index.php?option=com_easydiscuss&view=post&id=' . $post->id); ?>" />
    </form>
</div> 