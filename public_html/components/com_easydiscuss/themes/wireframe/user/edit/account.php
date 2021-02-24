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
<div class="l-stack">

	<?php if (!$this->config->get('layout_text_avatar') && $this->config->get('layout_avatarIntegration') == 'default' || $this->config->get('layout_avatarIntegration') == 'gravatar' || $allowJFBCAvatarEdit) { ?>
	<div class="o-card o-card--ed-edit-profile-item">
		<div class="o-card__body l-stack l-spaces--sm">
			<div class="o-title-01"><?php echo JText::_('COM_ED_APPEARENCE');?></div>
			<div class="o-body">
				<?php if ($this->config->get('layout_avatarIntegration') == 'default') { ?>
					<?php echo JText::sprintf('COM_ED_APPEARENCE_INFO_UPLOAD', $configMaxSize);?>
				<?php } ?>
			</div>

			<div class="lg:t-d--flex lg:t-align-items--c sm:t-align-items--fs">
				<div class="sm:t-text--center lg:t-pr--md">
					<a href="javascript:void(0);" class="o-avatar o-avatar--lg o-avatar--rounded">
						<img src="<?php echo $profile->getAvatar(false); ?>" data-ed-avatar />
					</a>
				</div>

				<div class="">
					<div class="t-d--flex sm:t-flex-direction--c">
						<?php if ($this->config->get('layout_avatarIntegration') != 'gravatar') { ?>
							<div class="lg:t-mr--md sm:t-mt--md">
								<input id="edFileid" name="Filedata" type="file" hidden="" data-ed-avatar-input />
								<span class="t-hidden" data-ed-avatar-filename></span>
								<input type="button" value="<?php echo JText::_('COM_ED_UPLOAD_PICTURE');?>" class="o-btn o-btn--primary sm:t-d--block sm:t-w--100" data-ed-upload-button>
							</div>

							<?php if ($avatar) { ?>
							<div class="lg:t-mr--md sm:t-mt--md">
								<div class="">
									<a href="javascript:void(0);" class="o-btn o-btn--default-o sm:t-d--block" data-ed-avatar-remove>
										<?php echo JText::_('COM_EASYDISCUSS_REMOVE_PICTURE'); ?>
									</a>
								</div>
							</div>
							<?php } ?>
						<?php } else { ?>
						<div>
							<?php echo JText::sprintf('COM_EASYDISCUSS_AVATARS_INTEGRATED_WITH', 'http://gravatar.com');?><br />
							<?php echo JText::sprintf('COM_EASYDISCUSS_GRAVATAR_EMAIL', $profile->getEmail());?>
						</div>
						<?php } ?>
					</div>
				</div>
				
			</div>
		</div>
	</div>
	<?php } ?>


	<div class="o-card o-card--ed-edit-profile-item">
		<div class="o-card__body l-stack l-spaces--sm">
			<div class="o-title-01"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_ACCOUNT'); ?></div>
			<div class="o-body"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_ACCOUNT_DESC'); ?></div>
		</div>

		<div class="o-card__body t-border-top--1 l-stack">
			
			<div class="t-d--flex sm:t-flex-direction--c">
				<div class="lg:t-w--33 sm:t-mb--sm">
					<label class="o-form-label" for="fullname">
						<?php echo JText::_('COM_EASYDISCUSS_PROFILE_FULLNAME'); ?>
					</label>
				</div>
				<div class="lg:t-w--100 l-stack l-spaces--xs">
					<?php echo $this->html('form.textbox', 'fullname', $this->html('string.escape', $user->name), 'COM_EASYDISCUSS_PROFILE_FULLNAME_PLACEHOLDER'); ?>
				</div>
			</div>

			<div class="t-d--flex sm:t-flex-direction--c">
				<div class="lg:t-w--33 sm:t-mb--sm">
					<label class="o-form-label" for="nickname">
						<?php echo JText::_('COM_EASYDISCUSS_PROFILE_NICKNAME'); ?>
					</label>
				</div>
				<div class="lg:t-w--100 l-stack l-spaces--xs">
					<?php echo $this->html('form.textbox', 'nickname', $this->html('string.escape', $profile->getNickname()), 'COM_EASYDISCUSS_PROFILE_NICKNAME_PLACEHOLDER'); ?>
				</div>
			</div>

			<div class="t-d--flex sm:t-flex-direction--c">
				<div class="lg:t-w--33 sm:t-mb--sm">
					<label class="o-form-label" for="username">
						<?php echo JText::_('COM_EASYDISCUSS_PROFILE_USERNAME'); ?>
					</label>
				</div>

				<div class="lg:t-w--100 l-stack l-spaces--xs">
					<?php echo $this->html('form.textbox', 'username', $this->html('string.escape', $profile->getUsername()), '', '', ['attr' => !$canChangeUsername ? 'disabled' : '']); ?>
				</div>
			</div>

			<div class="t-d--flex sm:t-flex-direction--c">
				<div class="lg:t-w--33 sm:t-mb--sm">
					<label class="o-form-label" for="email">
						<?php echo JText::_('COM_EASYDISCUSS_PROFILE_EMAIL'); ?>
					</label>
				</div>

				<div class="lg:t-w--100 l-stack l-spaces--xs">
					<input type="email" class="o-form-control" name="email" id="email" value="<?php echo $this->html('string.escape', $profile->getEmail()); ?>" placeholder="<?php echo JText::_('COM_EASYDISCUSS_PROFILE_EMAIL_PLACEHOLDER'); ?>">
				</div>
			</div>

			<div class="t-d--flex sm:t-flex-direction--c">
				<div class="lg:t-w--33 sm:t-mb--sm">
					<label class="o-form-label" for="password">
						<?php echo JText::_('COM_EASYDISCUSS_PROFILE_PASSWORD'); ?>
					</label>
				</div>

				<div class="lg:t-w--100 l-stack l-spaces--xs">
					<input type="password" class="o-form-control" name="password" id="password" value="" placeholder="<?php echo JText::_('COM_EASYDISCUSS_PROFILE_PASSWORD_PLACEHOLDER'); ?>" autocomplete="new-password" />
				</div>
			</div>

			<div class="t-d--flex sm:t-flex-direction--c">
				<div class="lg:t-w--33 sm:t-mb--sm">
					<label class="o-form-label" for="password2">
						<?php echo JText::_('COM_EASYDISCUSS_PROFILE_RETYPE_PASSWORD'); ?>
					</label>
				</div>

				<div class="lg:t-w--100 l-stack l-spaces--xs">
					<input type="password" class="o-form-control" name="password2" id="password2" value="" placeholder="<?php echo JText::_('COM_EASYDISCUSS_PROFILE_RETYPE_PASSWORD_PLACEHOLDER'); ?>" autocomplete="new-password" />
				</div>
			</div>

			<div class="t-d--flex sm:t-flex-direction--c">
				<div class="lg:t-w--33 sm:t-mb--sm">
					<label class="o-form-label" for="alias">
						<?php echo JText::_('COM_EASYDISCUSS_PROFILE_ALIAS'); ?>
					</label>
				</div>

				<div class="lg:t-w--100 l-stack l-spaces--xs">
					<div class="o-input-group">
						<input class="o-form-control" value="<?php echo $this->html('string.escape', $profile->alias); ?>" type="text" data-ed-alias-input>
						
						<button class="o-btn o-btn--default-o" type="button" data-ed-check-alias><?php echo JText::_('COM_EASYDISCUSS_CHECK_AVAILABILITY');?></button>
					</div>

					<div class="t-mb--no t-d--none t-font-size--02" data-ed-alias-status></div>
				</div>
			</div>
		</div>
	</div>

	<?php if ($this->config->get('layout_profile_showsocial')) { ?>
	<div class="o-card o-card--ed-edit-profile-item">
		<div class="o-card__body l-stack l-spaces--sm">
			<div class="o-title-01"><?php echo JText::_('COM_ED_PROFILE_SOCIAL_PROFILES'); ?></div>
			<div class="o-body"><?php echo JText::_('COM_ED_PROFILE_SOCIAL_PROFILES_INFO'); ?></div>
		</div>

		<div class="o-card__body t-border-top--1 l-stack">			
			<div class="t-d--flex sm:t-flex-direction--c">
				<div class="lg:t-w--33 sm:t-mb--sm">
					<label class="o-form-label" for="website">
						<?php echo JText::_('COM_EASYDISCUSS_WEBSITE'); ?>
					</label>
				</div>
				<div class="lg:t-w--100 l-stack l-spaces--xs">
					<?php echo $this->html('form.textbox', 'website', $this->escape($userparams->get('website')), 'COM_EASYDISCUSS_WEBSITE_PLACEHOLDER'); ?>

					<div class="o-form-check">
						<input class="o-form-check-input" type="checkbox" value="1" name="show_website" id="show_website" <?php echo $userparams->get('show_website') ? ' checked="1"' : ''; ?> />
						<label class="o-form-check-label" for="show_website"><?php echo JText::_('COM_EASYDISCUSS_SHOW_ON_PROFILE'); ?></label>
					</div>
				</div>
			</div>

			<div class="t-d--flex sm:t-flex-direction--c">
				<div class="lg:t-w--33 sm:t-mb--sm">
					<label class="o-form-label" for="facebook">
						<?php echo JText::_('COM_EASYDISCUSS_FACEBOOK'); ?>
					</label>
				</div>
				<div class="lg:t-w--100 l-stack l-spaces--xs">
					<?php echo $this->html('form.textbox', 'facebook', $this->escape($userparams->get('facebook')), 'COM_EASYDISCUSS_FACEBOOK_PLACEHOLDER'); ?>

					<div class="o-form-check">
						<input class="o-form-check-input" type="checkbox" value="1" name="show_facebook" id="show_facebook" <?php echo $userparams->get('show_facebook') ? ' checked="1"' : ''; ?> />
						<label class="o-form-check-label" for="show_facebook"><?php echo JText::_('COM_EASYDISCUSS_SHOW_ON_PROFILE'); ?></label>
					</div>
				</div>
			</div>

			<div class="t-d--flex sm:t-flex-direction--c">
				<div class="lg:t-w--33 sm:t-mb--sm">
					<label class="o-form-label" for="twitter">
						<?php echo JText::_('COM_EASYDISCUSS_TWITTER'); ?>
					</label>
				</div>
				<div class="lg:t-w--100 l-stack l-spaces--xs">
					<?php echo $this->html('form.textbox', 'twitter', $this->escape($userparams->get('twitter')), 'COM_EASYDISCUSS_TWITTER_PLACEHOLDER'); ?>

					<div class="o-form-check">
						<input class="o-form-check-input" type="checkbox" value="1" name="show_twitter" id="show_twitter" <?php echo $userparams->get('show_twitter') ? ' checked="1"' : ''; ?> />
						<label class="o-form-check-label" for="show_twitter"><?php echo JText::_('COM_EASYDISCUSS_SHOW_ON_PROFILE'); ?></label>
					</div>
				</div>
			</div>

			<div class="t-d--flex sm:t-flex-direction--c">
				<div class="lg:t-w--33 sm:t-mb--sm">
					<label class="o-form-label" for="linkedin">
						<?php echo JText::_('COM_EASYDISCUSS_LINKEDIN'); ?>
					</label>
				</div>
				<div class="lg:t-w--100 l-stack l-spaces--xs">
					<?php echo $this->html('form.textbox', 'linkedin', $this->escape($userparams->get('linkedin')), 'COM_EASYDISCUSS_LINKEDIN_PLACEHOLDER'); ?>

					<div class="o-form-check">
						<input class="o-form-check-input" type="checkbox" value="1" name="show_linkedin" id="show_linkedin" <?php echo $userparams->get('show_linkedin') ? ' checked="1"' : ''; ?> />
						<label class="o-form-check-label" for="show_linkedin"><?php echo JText::_('COM_EASYDISCUSS_SHOW_ON_PROFILE'); ?></label>
					</div>
				</div>
			</div>

			<div class="t-d--flex sm:t-flex-direction--c">
				<div class="lg:t-w--33 sm:t-mb--sm">
					<label class="o-form-label" for="skype">
						<?php echo JText::_('COM_EASYDISCUSS_SKYPE_USERNAME'); ?>
					</label>
				</div>
				<div class="lg:t-w--100 l-stack l-spaces--xs">
					<?php echo $this->html('form.textbox', 'skype', $this->escape($userparams->get('skype')), 'COM_EASYDISCUSS_SKYPE_USERNAME_PLACEHOLDER'); ?>

					<div class="o-form-check">
						<input class="o-form-check-input" type="checkbox" value="1" name="show_skype" id="show_skype" <?php echo $userparams->get('show_skype') ? ' checked="1"' : ''; ?> />
						<label class="o-form-check-label" for="show_skype"><?php echo JText::_('COM_EASYDISCUSS_SHOW_ON_PROFILE'); ?></label>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php } ?>

	<div class="o-card o-card--ed-edit-profile-item">
		<div class="o-card__body l-stack l-spaces--sm">
			<div class="o-title-01"><?php echo JText::_('COM_ED_ABOUT_ME'); ?></div>
			<div class="o-body"><?php echo JText::_('COM_ED_ABOUT_ME_INFO'); ?></div>
		</div>

		<div class="o-card__body t-border-top--1 l-stack">			
			<div class="t-d--flex sm:t-flex-direction--c">
				<div class="lg:t-w--100 l-stack l-spaces--xs">
					<div class="ed-editor ed-editor--<?php echo $composer->getEditorClass();?>" <?php echo $composer->uid;?> data-ed-editor-wrapper>
						<div class="ed-editor-widget ed-editor-widget--no-pad">
							<?php echo $composer->renderEditor('description', $profile->getDescription(true), true); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php if ($this->config->get('main_signature_visibility')) { ?>
	<div class="o-card o-card--ed-edit-profile-item">
		<div class="o-card__body l-stack l-spaces--sm">
			<div class="o-title-01"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_SIGNATURE'); ?></div>
			<div class="o-body"><?php echo JText::_('COM_ED_PROFILE_SIGNATURE_INFO'); ?></div>
		</div>

		<div class="o-card__body t-border-top--1 l-stack">			
			<div class="t-d--flex sm:t-flex-direction--c">
				<div class="lg:t-w--100 l-stack l-spaces--xs">
					<div class="ed-editor ed-editor--<?php echo $composerSignature->getEditorClass();?>" <?php echo $composerSignature->uid;?> data-ed-editor-wrapper>
						<div class="ed-editor-widget ed-editor-widget--no-pad">
							<?php echo $composerSignature->renderEditor('signature', $profile->getSignature(true)); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php } ?>

	<?php if (ED::isTwoFactorEnabled()) { ?>
	<div class="o-card o-card--ed-edit-profile-item">
		<div class="o-card__body l-stack l-spaces--sm">
			<div class="o-title-01"><?php echo JText::_('COM_ED_PROFILE_TWOFACTOR'); ?></div>
			<div class="o-body"><?php echo JText::_('COM_ED_PROFILE_TWOFACTOR_INFO'); ?></div>
		</div>

		<div class="o-card__body t-border-top--1 l-stack">			
			<div class="t-d--flex sm:t-flex-direction--c">
				<div class="lg:t-w--33 sm:t-mb--sm">
					<label class="o-form-label" for="website">
						<?php echo JText::_('COM_ED_PROFILE_TWOFACTOR_METHOD'); ?>
					</label>
				</div>
				<div class="lg:t-w--100 l-stack l-spaces--xs">
					<?php echo JHtml::_('select.genericlist', $twoFactorMethods, 'jform[twofactor][method]', array('onchange' => 'twoFactorMethodChange()', 'class' => 'o-form-select'), 'value', 'text', $otpConfig->method, 'jform_twofactor_method', false); ?>

				</div>

			</div>

			<div class="t-d--flex sm:t-flex-direction--c">

				<div class="lg:t-w--100 l-stack l-spaces--xs">
					<div id="com_users_twofactor_forms_container" class="ed-com-users-twofactor">
						<?php foreach ($twoFactorForms as $form) { ?>
							<?php $style = $form['method'] == $otpConfig->method ? 'display: block' : 'display: none'; ?>
							<div id="com_users_twofactor_<?php echo $form['method']; ?>" style="<?php echo $style; ?>">
								<?php echo $form['form']; ?>
							</div>
						<?php } ?>

						<div class="l-stack">
							<div class="o-title-01">
								<?php echo JText::_('COM_USERS_PROFILE_OTEPS'); ?>
							</div>
							<div class="o-alert o-alert--info-o">
								<?php echo JText::_('COM_USERS_PROFILE_OTEPS_DESC'); ?>
							</div>
							<?php if (empty($otpConfig->otep)) { ?>
								<div class="o-alert o-alert--warning-o">
									<?php echo JText::_('COM_USERS_PROFILE_OTEPS_WAIT_DESC'); ?>
								</div>
							<?php } else { ?>
								<?php foreach ($otpConfig->otep as $otep) { ?>
									<span class="">
										<?php echo substr($otep, 0, 4); ?>-<?php echo substr($otep, 4, 4); ?>-<?php echo substr($otep, 8, 4); ?>-<?php echo substr($otep, 12, 4); ?>
									</span>
								<?php } ?>
								
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php } ?>

	<div class="o-card o-card--ed-edit-profile-item">
		<div class="o-card__body l-stack l-spaces--sm">
			<div class="o-title-01"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_OTHERS'); ?></div>
			<div class="o-body"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_OTHERS_DESC'); ?></div>
		</div>

		<div class="o-card__body t-border-top--1 l-stack">
			<div class="lg:t-d--flex t-align-items--c">
				<div class="t-flex-grow--1 t-pr--md">
					<div class="t-text--success" data-ed-allread-status></div>

					<div class="o-body"><?php echo JText::_('COM_EASYDISCUSS_PROFILE_MARK_ALL_READ_DESC'); ?></div>
				</div>
				<div class="t-flex-shrink--0 sm:t-mt--md">
					<a href="javascript:void(0)" class="o-btn o-btn--default-o" data-ed-mark-allread><?php echo JText::_('COM_EASYDISCUSS_PROFILE_MARK_ALL_READ');?></a>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
twoFactorMethodChange = function(e) {
	console.log('sss');
	var selectedPane = 'com_users_twofactor_' + jQuery('#jform_twofactor_method').val();

	jQuery.each(jQuery('#com_users_twofactor_forms_container>div'), function(i, el) {
		if (el.id != selectedPane) {
			jQuery('#' + el.id).hide(0);
		} else {
			jQuery('#' + el.id).show(0);
		}
	});
}
</script>