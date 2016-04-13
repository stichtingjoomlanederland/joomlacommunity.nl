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
<div class="row">
    <div class="col-md-6">
        <div class="input-group">
            <span class="input-group-addon">
                <img src="<?php echo $source;?>" alt="<?php echo JText::_('Captcha Text', true);?>" data-ed-captcha-image />
            </span>

            <input name="captcha-response" type="text" class="form-control" placeholder="<?php echo JText::_('Enter security code', true);?>" />

            <span class="input-group-btn">
                <a href="javascript:void(0);" class="btn btn-default" data-ed-captcha-reload>
                    <i class="fa fa-refresh"></i>
                </a>
            </span>
        </div>
    </div>

    <input type="hidden" name="captcha-id" value="<?php echo $table->id;?>" data-ed-captcha-id />
</div>