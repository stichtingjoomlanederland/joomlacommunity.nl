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
    <div class="ed-my-subscribe-select__title"><?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_INTERVAL'); ?></div>
    <select data-ed-subscription-settings data-method="updateSubscribeInterval">
        <option value="instant" <?php echo $subscribe->interval == 'instant' ? 'selected="selected"' : ''; ?> disabled><?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_INSTANT'); ?></option>

        <?php if (!$this->config->get('main_email_digest') && $subscribe->interval != 'instant') { ?>
          <option value="<?php echo $subscribe->interval; ?>" selected="selected">
            <?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_' . strtoupper($subscribe->interval)); ?>
          </option>
        <?php } ?>

        <?php if ($this->config->get('main_email_digest')) {  ?>
          <option value="daily" <?php echo $subscribe->interval == 'daily' ? 'selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_DAILY'); ?></option>
          <option value="weekly" <?php echo $subscribe->interval == 'weekly' ? 'selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_WEEKLY'); ?></option>
          <option value="monthly" <?php echo $subscribe->interval == 'monthly' ? 'selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_MONTHLY'); ?></option>                                        
        <?php } ?>
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