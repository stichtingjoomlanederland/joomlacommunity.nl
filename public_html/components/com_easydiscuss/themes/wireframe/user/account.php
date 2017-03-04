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
<div class="tab-item user-bio">
	<div class="ed-form-panel__hd">
	    <div class="ed-form-panel__title"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_ACCOUNT'); ?></div>
	    <div class="ed-form-panel__"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_ACCOUNT_DESC'); ?></div>
	</div>

	<div class="ed-form-panel__bd">
		
		<div class="o-row">
			<div class="o-col t-lg-pr--md t-xs-pr--no">
				<div class="form-group">
				    <label for="fullname"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_FULLNAME'); ?></label>
				    <input type="text" class="form-control" name="fullname" id="fullname" value="<?php echo $profile->user->name; ?>" placeholder="<?php echo JText::_('COM_EASYDISCUSS_PROFILE_FULLNAME_PLACEHOLDER'); ?>">
				</div>
			</div>

			<div class="o-col t-lg-pr--md t-xs-pr--no">
				<div class="form-group">
				    <label for="nickname"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_NICKNAME'); ?></label>
				    <input type="text" class="form-control" name="nickname" id="nickname" value="<?php echo $profile->getNickname(); ?>" placeholder="<?php echo JText::_('COM_EASYDISCUSS_PROFILE_NICKNAME_PLACEHOLDER'); ?>">
				</div>
			</div>
		</div>
		
		<div class="o-row">
			<div class="o-col t-lg-pr--md t-xs-pr--no">
				<div class="form-group">
				    <label for="username"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_USERNAME'); ?></label>
				    <input <?php echo $changeUsername; ?> type="text" class="form-control" name="username" id="username" value="<?php echo $profile->getUsername(); ?>" placeholder="<?php echo JText::_('COM_EASYDISCUSS_PROFILE_USERNAME_PLACEHOLDER'); ?>">
				</div>
			</div>

			<div class="o-col t-lg-pr--md t-xs-pr--no">
				<div class="form-group">
				    <label for="email"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_EMAIL'); ?></label>
				    <input type="text" class="form-control" name="email" id="email" value="<?php echo $profile->getEmail(); ?>" placeholder="<?php echo JText::_('COM_EASYDISCUSS_PROFILE_EMAIL_PLACEHOLDER'); ?>">
				</div>
			</div>
		</div>

		<div class="o-row">
			<div class="o-col t-lg-pr--md t-xs-pr--no">
				<div class="form-group">
				    <label for="password"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_PASSWORD'); ?></label>
				    <input type="password" class="form-control" name="password" id="password" value="" placeholder="<?php echo JText::_('COM_EASYDISCUSS_PROFILE_PASSWORD_PLACEHOLDER'); ?>">
				</div>
			</div>

			<div class="o-col t-lg-pr--md t-xs-pr--no">
				<div class="form-group">
				    <label for="password2"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_RETYPE_PASSWORD'); ?></label>
				    <input type="password" class="form-control" name="password2" id="password2" value="" placeholder="<?php echo JText::_('COM_EASYDISCUSS_PROFILE_RETYPE_PASSWORD_PLACEHOLDER'); ?>">
				</div>
			</div>
		</div>

		<?php if ($this->config->get('main_signature_visibility')) { ?>
		<div class="form-group">
		    <label for="signature"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_SIGNATURE'); ?> </label>
			<div class="ed-editor ed-editor--<?php echo $composer->getEditorClass();?>" <?php echo $composer->uid;?>>
				<div class="ed-editor-widget ed-editor-widget--no-pad">
	        		<?php echo $composer->renderEditor('signature', $profile->getSignature(true)); ?>
	        	</div>
			</div>	
		</div>
		<?php } ?>

		<?php if ($this->config->get('main_description_visibility')) { ?>
		<div class="form-group">
		    <label for="description"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_DESCRIPTION'); ?></label>
			<div class="ed-editor ed-editor--<?php echo $composer->getEditorClass();?>" <?php echo $composer->uid;?>>
				<div class="ed-editor-widget ed-editor-widget--no-pad">
	        		<?php echo $composer->renderEditor('description', $profile->getDescription(true), true); ?>
	        	</div>
			</div>
		</div>
		<?php } ?>

	</div>
</div>
