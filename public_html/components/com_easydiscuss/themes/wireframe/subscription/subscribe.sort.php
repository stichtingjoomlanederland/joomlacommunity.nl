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
    <div class="ed-my-subscribe-select__title"><?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_SORT_BY'); ?></div>
    <select data-ed-subscription-settings data-method="updateSubscribeSort">
        <option value="recent" <?php echo $subscribe->sort == 'recent' ? 'selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_SORT_BY_RECENT'); ?></option>
        <option value="popular" <?php echo $subscribe->sort == 'popular' ? 'selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_SORT_BY_POPULAR'); ?></option>                                      
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