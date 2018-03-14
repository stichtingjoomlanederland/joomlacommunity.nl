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
<form name="adminForm" id="adminForm" method="post">
    <div class="row">
        <div class="col-md-6">
            <div class="panel">
                <div class="panel-head">
                    <b>
                        <span><?php echo JText::_('COM_EASYDISCUSS_API_KEY_INVALID');?></span>
                    </b>
                    <div class="panel-info">
                        <?php echo JText::_('COM_EASYDISCUSS_API_KEY_REQUIRED_DESC');?>
                    </div>
                </div>

                <div class="panel-body">
                    <div class="form-group">
                        <div class="col-lg-12">
                            <div class="input-group">
                                <input type="text" class="form-control" name="apikey" value="<?php echo $this->config->get('main_apikey');?>" />
                                <div class="input-group-btn">
                                    <button class="btn btn-primary"> <?php echo JText::_('COM_EASYDISCUSS_SAVE_APIKEY_BUTTON'); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-12">
                            <div class="obtain-key">
                                <i class="fa fa-support"></i>&nbsp; <a href="https://stackideas.com/dashboard" target="_blank"><?php echo JText::_('COM_EASYDISCUSS_OBTAIN_API_KEY'); ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php echo $this->html('form.hidden', 'settings', 'settings', 'saveApi'); ?>
    <input type="hidden" name="return" value="<?php echo $return;?>" />
</form>