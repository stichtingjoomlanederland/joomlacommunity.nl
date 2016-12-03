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
<div class="ed-my-subscribe-select" data-ed-susbcribe-select>
    <div class="ed-my-subscribe-select__title"><?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_POST_COUNT'); ?></div>
    <select data-ed-subscription-settings data-method="updateSubscribeCount">
        <option value="5" <?php echo $subscribe->count == '5' ? 'selected="selected"' : ''; ?>>5</option>
        <option value="10" <?php echo $subscribe->count == '10' ? 'selected="selected"' : ''; ?>>10</option>
        <option value="15" <?php echo $subscribe->count == '15' ? 'selected="selected"' : ''; ?>>15</option>
        <option value="20" <?php echo $subscribe->count == '20' ? 'selected="selected"' : ''; ?>>20</option>
        <option value="25" <?php echo $subscribe->count == '25' ? 'selected="selected"' : ''; ?>>25</option> 
        <option value="30" <?php echo $subscribe->count == '30' ? 'selected="selected"' : ''; ?>>30</option>                                         
    </select>
    <div class="loading-bar loader" style="display:none;" data-ed-subscribe-select-loading>
        <div class="discuss-loader">
            <div class="test-object is-loading">
              <div class="o-loading">
                  <div class="o-loading__content">
                      <i class="fa fa-spinner fa-spin"></i>    
                  </div>
              </div>
            </div>
        </div>
    </div>                                                                                   
</div> 