<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="tab-item user-alias pb-15" >
    <div class="ed-form-panel__hd">
        <div class="ed-form-panel__title"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_ALIAS'); ?></div>
        <div class="ed-form-panel__subtitle"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_ALIAS_DESC'); ?></div>
    </div>
    <div class="ed-form-panel__bd">
		<div class="form-group">

		    <label for="alias"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_ALIAS'); ?></label>
		    <input type="text" class="form-control" value="<?php echo $profile->alias; ?>" id="alias" name="alias" data-ed-alias-input>

		    <div class="t-mb--no t-hidden" data-ed-alias-status></div>

			<div class="ed-profile-alias" data-ed-alias-loading>
			  <div class="o-loading">
			      <div class="o-loading__content">
			          <i class="fa fa-spinner fa-spin"></i>
			      </div>
			  </div>
			</div>

            <a href="javascript:void(0)" class="btn btn-success btn-sm t-lg-mt--md" data-ed-check-alias><?php echo JText::_('COM_EASYDISCUSS_CHECK_AVAILABILITY');?></a>

		</div>
	</div>
</div>
