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
<div class="tab-item user-social">
	<div class="ed-form-panel__hd">
	    <div class="ed-form-panel__title"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_SOCIAL'); ?></div>
	    <div class="ed-form-panel__"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_SOCIAL_DESC'); ?></div>
	</div>
	<div class="ed-form-panel__bd">
	    <div class="form-group">
	        <label for="facebook"><?php echo JText::_('COM_EASYDISCUSS_FACEBOOK'); ?></label>
	        <input type="text" class="form-control" id="facebook" name="facebook" value="<?php echo $this->escape($userparams->get('facebook'));?>" placeholder="<?php echo JText::_('COM_EASYDISCUSS_FACEBOOK_PLACEHOLDER'); ?>">
	        <div class="o-checkbox">
	            <input type="checkbox" value="1" id="show_facebook" name="show_facebook" <?php echo $userparams->get('show_facebook') ? ' checked="1"' : ''; ?>>
	            <label for="show_facebook">
	                <?php echo JText::_('COM_EASYDISCUSS_SHOW_ON_PROFILE'); ?>
	            </label>
	        </div>
	    </div>
	    <div class="form-group">
	        <label for="twitter"><?php echo JText::_('COM_EASYDISCUSS_TWITTER'); ?></label>
	        <input type="text" class="form-control" id="twitter" name="twitter" value="<?php echo $this->escape($userparams->get('twitter'));?>" placeholder="<?php echo JText::_('COM_EASYDISCUSS_TWITTER_PLACEHOLDER'); ?>">
	        <div class="o-checkbox">
	            <input type="checkbox" value="1" id="show_twitter" name="show_twitter" <?php echo $userparams->get('show_twitter') ? ' checked="1"' : ''; ?>>
	            <label for="show_twitter">
	                <?php echo JText::_('COM_EASYDISCUSS_SHOW_ON_PROFILE'); ?>
	            </label>
	        </div>
	    </div>
	    <div class="form-group">
	        <label for="linkedin"><?php echo JText::_('COM_EASYDISCUSS_LINKEDIN'); ?></label>
	        <input type="text" class="form-control" id="linkedin" name="linkedin" value="<?php echo $this->escape($userparams->get('linkedin'));?>" placeholder="<?php echo JText::_('COM_EASYDISCUSS_LINKEDIN_PLACEHOLDER'); ?>">
	        <div class="o-checkbox">
	            <input type="checkbox" value="1" id="show_linkedin" name="show_linkedin" <?php echo $userparams->get('show_linkedin') ? ' checked="1"' : ''; ?>>
	            <label for="show_linkedin">
	                <?php echo JText::_('COM_EASYDISCUSS_SHOW_ON_PROFILE'); ?>
	            </label>
	        </div>
	    </div>
	    <div class="form-group">
	        <label for="skype"><?php echo JText::_('COM_EASYDISCUSS_SKYPE_USERNAME'); ?></label>
	        <input type="text" class="form-control" id="skype" name="skype" value="<?php echo $this->escape($userparams->get('skype'));?>" placeholder="<?php echo JText::_('COM_EASYDISCUSS_SKYPE_USERNAME_PLACEHOLDER'); ?>">
	        <div class="o-checkbox">
	            <input type="checkbox" value="1" id="show_skype" name="show_skype" <?php echo $userparams->get('show_skype') ? ' checked="1"' : ''; ?>>
	            <label for="show_skype">
	                <?php echo JText::_('COM_EASYDISCUSS_SHOW_ON_PROFILE'); ?>
	            </label>
	        </div>
	    </div>
	    <div class="form-group">
	        <label for="website"><?php echo JText::_('COM_EASYDISCUSS_WEBSITE'); ?></label>
	        <input type="text" class="form-control" id="website" name="website" value="<?php echo $this->escape($userparams->get('website'));?>" placeholder="<?php echo JText::_('COM_EASYDISCUSS_WEBSITE_PLACEHOLDER'); ?>">
	        <div class="o-checkbox">
	            <input type="checkbox" value="1" id="show_website" name="show_website" <?php echo $userparams->get('show_website') ? ' checked="1"' : ''; ?>>
	            <label for="show_website">
	                <?php echo JText::_('COM_EASYDISCUSS_SHOW_ON_PROFILE'); ?>
	            </label>
	        </div>
	    </div>	    	    	    
	</div>
</div>
