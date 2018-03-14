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
<div class="tab-item user-photo">
	<div class="ed-form-panel__hd">
	    <div class="ed-form-panel__title"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_AVATAR'); ?></div>
	    <div class="ed-form-panel__"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_AVATAR_EDIT'); ?></div>
	</div>
	<div class="ed-form-panel__bd">
		<?php if($this->config->get('layout_avatarIntegration') == 'gravatar') { ?>
		<div class="o-flag">
			<div class="o-flag__image">
				<div class="o-avatar o-avatar--xl t-lg-mr--lg" >
				    <img src="<?php echo $profile->getAvatar(false); ?>" data-ed-avatar/>
				    <div class="ed-avatar-crop-preview" data-ed-avatar-preview></div>
				</div>		
			</div>
		</div>		
		<p>
			<?php echo JText::sprintf('COM_EASYDISCUSS_AVATARS_INTEGRATED_WITH', 'http://gravatar.com');?><br />
			<?php echo JText::sprintf('COM_EASYDISCUSS_GRAVATAR_EMAIL', $profile->getEmail());?>
		</p>
		<?php } else { ?>
		<div class="form-group">
		    <label for="exampleInputEmail1"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_AVATAR_DESC'); ?></label>
		    <input type="file" size="25" name="Filedata" id="file-upload">
		</div>
		<div class="o-alert o-alert--warning" role="alert">
		    <?php echo JText::sprintf('COM_EASYDISCUSS_PROFILE_AVATAR_CONDITION', $configMaxSize); ?>
		</div>
		<div class="o-flag">
			<div class="o-flag__image">
				<div class=""><?php echo JText::_('COM_EASYDISCUSS_YOUR_PICTURE');?></div>
				<div class="o-avatar o-avatar--xl t-lg-mr--lg" >
				    <img src="<?php echo $profile->getAvatar(false); ?>" data-ed-avatar/>
				    <div class="ed-avatar-crop-preview" data-ed-avatar-preview></div>
				</div>		
			</div>
			
			<?php if ($avatar) { ?>
				<div class="o-flag__body">
					<div class="ed-avatar-crop-action">
						<a href="javascript:void(0);" class="btn btn-default t-xs-mb--lg" data-ed-avatar-crop-button>
							<i class=""></i><?php echo JText::_('COM_EASYDISCUSS_CROP_IMAGE'); ?>
						</a>
						<a href="javascript:void(0);" class="btn btn-danger t-xs-mb--lg" data-ed-avatar-remove>
							<i class="fa fa-times t-lg-mr--md"></i><?php echo JText::_('COM_EASYDISCUSS_REMOVE_PICTURE'); ?>
						</a>
						<a href="javascript:void(0);" class="btn btn-success t-xs-mb--lg" style="display:none;" data-ed-avatar-crop-save>
							<i class="fa fa-spinner fa-spin" style="display:none;" data-ed-avatar-loading></i>
							<i class=""></i><?php echo JText::_('COM_EASYDISCUSS_AVATAR_SAVE_CROPPING'); ?>
						</a>
						<a href="javascript:void(0);" class="btn btn-danger t-xs-mb--lg" style="display:none;" data-ed-avatar-crop-cancel>
							<i class=""></i><?php echo JText::_('COM_EASYDISCUSS_AVATAR_CANCEL_CROPPING'); ?>
						</a>
					</div>
				</div>
			<?php } ?>
		</div>
		
		<div role="alert" class="o-alert o-alert--success o-alert--icon t-lg-mt--lg t-hidden" data-ed-avatar-crop-alert>
		      <?php echo JText::_('COM_EASYDISCUSS_AVATAR_CROP_HAS_BEEN_UPDATED'); ?>
		  </div>
		<div class="ed-original-avatar t-hidden" data-ed-original-avatar-container>
			<div class="t-lg-mt--lg"><?php echo JText::_('COM_EASYDISCUSS_AVATAR_CROP_SELECT_AREA');?></div>
			<img src="<?php echo $profile->getOriginalAvatar(); ?>" data-ed-original-avatar/>
		</div>
		<?php } ?>
	</div>
</div>
