<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div id="account" class="tab-pane active in">
	<div class="row">
		<div class="col-md-6">
			<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_USER_ACCOUNT'); ?>
				<div class="panel-body">
					<div class="o-form-horizontal">
						<?php if (!$this->config->get('layout_text_avatar') && $this->config->get('layout_avatarIntegration') == 'default') { ?>
						<div class="o-form-group">
							<div class="col-md-5 o-form-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_AVATAR'); ?>
							</div>
							<div class="col-md-7">
								<div>
									<img id="avatar" style="border-style:solid; float:none;" src="<?php echo $profile->getAvatar(); ?>?<?php echo time();?>=1" width="120" height="120"/>
								</div>

								<?php if ($profile->avatar) { ?>
								<div style="margin-top:5px;">
									<a class="o-btn o-btn--default-o t-text--danger" href="javascript:void(0);" data-ed-remove-avatar><?php echo JText::_('COM_EASYDISCUSS_REMOVE_AVATAR'); ?></a>
								</div>
								<?php } ?>

								<div style="margin-top:5px;">
									<input id="file-upload" type="file" name="Filedata" size="65" class="o-form-control" />
								</div>
								<div class="t-mt--sm o-alert o-alert--info">
									<?php echo JText::sprintf('COM_EASYDISCUSS_AVATAR_UPLOAD_CONDITION', $maxSizeInMB, $this->config->get( 'layout_avatarwidth' ) ); ?>
								</div>
							</div>
						</div>
						<?php } ?>
						
						<div class="o-form-group">
							<div class="col-md-5 o-form-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_USERNAME'); ?>
							</div>
							<div class="col-md-7">
								<div class="o-form-control disabled">
									<?php echo $user->username; ?>
								</div>
							</div>
						</div>

						<?php echo $this->html('forms.textbox', 'alias', 'COM_EASYDISCUSS_USER_ALIAS', $profile->alias); ?>

						<?php echo $this->html('forms.textbox', 'points', 'COM_EASYDISCUSS_USER_POINTS', $profile->points, array('size' => '5', 'class' => 'text-center')); ?>

						<?php echo $this->html('forms.textbox', 'fullname', 'COM_EASYDISCUSS_FULL_NAME', $this->escape($user->name)); ?>

						<?php echo $this->html('forms.textbox', 'nickname', 'COM_EASYDISCUSS_NICK_NAME', $this->escape($profile->nickname)); ?>

						<div class="o-form-group">
							<div class="col-md-5 o-form-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_EMAIL'); ?>
							</div>
							<div class="col-md-7">
								<div class="o-form-control disabled">
									<?php echo $user->email; ?>
								</div>
							</div>
						</div>

						<?php echo $this->html('forms.textarea', 'description', 'COM_EASYDISCUSS_PROFILE_DESCRIPTION', $profile->getDescription(true), ['rows' => '15']); ?>

						<?php echo $this->html('forms.textarea', 'signature', 'COM_EASYDISCUSS_PROFILE_SIGNATURE', $profile->getSignature(true), ['rows' => '15']); ?>
						
					</div>
				</div>
			</div>
		</div>

		<div class="col-md-6">
			<div class="panel">
				<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_USER_SOCIAL_PROFILES'); ?>
				
				<div class="panel-body">
					<div class="o-form-horizontal">
						<div class="o-form-group">
							<div class="col-md-5 o-form-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_FACEBOOK'); ?>
							</div>
							<div class="col-md-7">
								<?php echo $this->html('form.textbox', 'facebook', $this->escape($userparams->get('facebook'))); ?>

								<div class="t-mt--sm">
									<input type="checkbox" class="checkbox" value="1" id="show_facebook" name="show_facebook" <?php echo $userparams->get('show_facebook') ? ' checked="1"' : ''; ?>>
									<label for="show_facebook" style="display: inline-block;"><?php echo JText::_('COM_EASYDISCUSS_SHOW_ON_PROFILE'); ?></label>
								</div>
							</div>							
						</div>

						<div class="o-form-group">
							<div class="col-md-5 o-form-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_TWITTER'); ?>
							</div>
							<div class="col-md-7">
								<?php echo $this->html('form.textbox', 'twitter', $this->escape($userparams->get('twitter'))); ?>

								<div class="t-mt--sm">
									<input type="checkbox" class="checkbox" value="1" id="show_twitter" name="show_twitter" <?php echo $userparams->get('show_twitter') ? ' checked="1"' : ''; ?>>
									<label for="show_twitter" style="display: inline-block;"><?php echo JText::_('COM_EASYDISCUSS_SHOW_ON_PROFILE'); ?></label>
								</div>
							</div>
						</div>

						<div class="o-form-group">
							<div class="col-md-5 o-form-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_LINKEDIN'); ?>
							</div>
							<div class="col-md-7">
								<?php echo $this->html('form.textbox', 'linkedin', $this->escape($userparams->get('linkedin'))); ?>

								<div class="t-mt--sm">
									<input type="checkbox" class="checkbox" value="1" id="show_linkedin" name="show_linkedin" <?php echo $userparams->get('show_linkedin') ? ' checked="1"' : ''; ?>>
									<label for="show_linkedin" style="display: inline-block;"><?php echo JText::_('COM_EASYDISCUSS_SHOW_ON_PROFILE'); ?></label>
								</div>
							</div>
						</div>

						<div class="o-form-group">
							<div class="col-md-5 o-form-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_SKYPE'); ?>
							</div>
							<div class="col-md-7">
								<?php echo $this->html('form.textbox', 'skype', $this->escape($userparams->get('skype'))); ?>

								<div class="t-mt--sm">
									<input type="checkbox" class="checkbox" value="1" id="show_skype" name="show_skype" <?php echo $userparams->get('show_skype') ? ' checked="1"' : ''; ?>>
									<label for="show_skype" style="display: inline-block;"><?php echo JText::_('COM_EASYDISCUSS_SHOW_ON_PROFILE'); ?></label>
								</div>
							</div>
						</div>

						<div class="o-form-group">
							<div class="col-md-5 o-form-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_WEBSITE'); ?>
							</div>
							<div class="col-md-7">
								<?php echo $this->html('form.textbox', 'website', $this->escape($userparams->get('website'))); ?>

								<div class="t-mt--sm">
									<input type="checkbox" class="checkbox" value="1" id="show_website" name="show_website" <?php echo $userparams->get('show_website') ? ' checked="1"' : ''; ?>>
									<label for="show_website" style="display: inline-block;"><?php echo JText::_('COM_EASYDISCUSS_SHOW_ON_PROFILE'); ?></label>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
