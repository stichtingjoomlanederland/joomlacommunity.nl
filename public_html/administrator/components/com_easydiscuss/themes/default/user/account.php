<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<style type="text/css">
body .key{width:300px !important;}
#discuss-wrapper .markItUp{ width: 715px;}
</style>

<div id="account" class="tab-pane active in">
	<div class="row">
		<div class="col-md-6">
			<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_USER_ACCOUNT'); ?>
				<div class="panel-body">
					<div class="form-horizontal">
						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_AVATAR'); ?>
							</div>
							<div class="col-md-7">
							<?php if ($this->config->get('layout_avatar')) { ?>
								<div>
									<img id="avatar" style="border-style:solid; float:none;" src="<?php echo $profile->getAvatar(); ?>" width="120" height="120"/>
								</div>
								<?php if ($profile->avatar) { ?>
								<div style="margin-top:5px;">
									<a class="btn btn-warning" href="javascript:void(0);" data-ed-remove-avatar><?php echo JText::_('COM_EASYDISCUSS_REMOVE_AVATAR'); ?></a>
								</div>
								<?php } ?>

								<div style="margin-top:5px;">
									<input id="file-upload" type="file" name="Filedata" size="65" class=""/>
								</div>
								<div class="alert mt-20">
									<?php echo JText::sprintf('COM_EASYDISCUSS_AVATAR_UPLOAD_CONDITION', $maxSizeInMB, $this->config->get( 'layout_avatarwidth' ) ); ?>
								</div>
							<?php } else { ?>
								<div class="alert mt-20">
									<?php echo JText::_('COM_EASYDISCUSS_AVATAR_DISABLE_BY_ADMINISTRATOR'); ?>
								</div>
							<?php } ?>
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_USERNAME'); ?>
							</div>
							<div class="col-md-7">
								<?php echo $user->username; ?>
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_USER_ALIAS'); ?>
							</div>
							<div class="col-md-7">
								<?php echo $this->html('form.textbox', 'alias', $profile->alias); ?>
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_USER_POINTS'); ?>
							</div>
							<div class="col-md-7">
								<input type="text" class="form-control" id="points" name="points" size="20" maxlength="255" value="<?php echo $profile->points; ?>" />

							</div>
						</div>

						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_RESET_RANK'); ?>
							</div>
							<div class="col-md-7">
								<a href="javascript:void(0);" class="btn btn-info resetButton" data-ed-reset-rank ><?php echo JText::_( 'COM_EASYDISCUSS_RESET_BUTTON' ); ?></a>
							</div>

						</div>

						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_FULL_NAME'); ?>
							</div>
							<div class="col-md-7">
								<input type="text" class="form-control" id="fullname" name="fullname" size="55" maxlength="255" value="<?php echo $this->escape($user->name); ?>" />
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_NICK_NAME'); ?>
							</div>
							<div class="col-md-7">
								<input type="text" class="form-control" id="nickname" name="nickname" size="55" maxlength="255" value="<?php echo $this->escape($profile->nickname); ?>" />
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_EMAIL'); ?>
							</div>
							<div class="col-md-7">
								<?php echo $user->email; ?>
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_PROFILE_SIGNATURE'); ?>
							</div>
							<div class="col-md-7">
								<div class="ed-editor ed-editor--<?php echo $composer->getEditorClass();?>" <?php echo $composer->uid;?>>
									<div class="ed-editor-widget ed-editor-widget--no-pad">
										<?php echo $composer->renderEditor('signature', $profile->getSignature(true)); ?>
									</div>
								</div>
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_PROFILE_DESCRIPTION'); ?>
							</div>
							<div class="col-md-7">
								<div class="ed-editor ed-editor--<?php echo $composer->getEditorClass();?>" <?php echo $composer->uid;?>>
									<div class="ed-editor-widget ed-editor-widget--no-pad">
										<?php echo $composer->renderEditor('description', $profile->getDescription(true), true); ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-md-6">
			<div class="panel">
				<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_TAB_LOCATION'); ?>
				<div class="panel-body">
					<div class="form-horizontal">
						<div class="form-group">
							<div class="col-md-4 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_USER_CURRENT_LOCATION'); ?>
							</div>

							<div class="col-md-8">
								<?php $locations = array('latitude' => $profile->latitude, 'longitude' => $profile->longitude, 'address' => $profile->location, 'hasLocation' => $profile->hasLocation()) ?>
								<?php echo $this->output('site/forms/location.form', $locations); ?>
							</div>

							<input type="hidden" name="latitude" value="<?php echo $profile->latitude;?>" data-ed-location-latitude />
							<input type="hidden" name="longitude" value="<?php echo $profile->longitude;?>" data-ed-location-longitude />

						</div>
					</div>
				</div>
			</div>
			
			<div class="panel">
				<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_USER_SOCIAL_PROFILES'); ?>
				
				<div class="panel-body">
					<div class="form-horizontal">
						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_FACEBOOK'); ?>
							</div>
							<div class="col-md-7">
								<input type="text" class="form-control" id="facebook" name="facebook" size="55" maxlength="255" value="<?php echo $this->escape($userparams->get('facebook')); ?>" />
							</div>							
						</div>

						<div class="form-group">
							<div class="col-md-5 control-label">
							</div>
							<div class="col-md-7">
								<div class="o-checkbox">
									<input type="checkbox" value="1" id="show_facebook" name="show_facebook" <?php echo $userparams->get('show_facebook') ? ' checked="1"' : ''; ?>>
									<label for="show_facebook">
										<?php echo JText::_('COM_EASYDISCUSS_SHOW_ON_PROFILE'); ?>
									</label>
								</div>
							</div>							
						</div>						

						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_TWITTER'); ?>
							</div>
							<div class="col-md-7">
								<input type="text" class="form-control" id="twitter" name="twitter" size="55" maxlength="255" value="<?php echo $this->escape($userparams->get('twitter')); ?>" />
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-5 control-label">
							</div>
							<div class="col-md-7">
								<div class="o-checkbox">
									<input type="checkbox" value="1" id="show_twitter" name="show_twitter" <?php echo $userparams->get('show_twitter') ? ' checked="1"' : ''; ?>>
									<label for="show_twitter">
										<?php echo JText::_('COM_EASYDISCUSS_SHOW_ON_PROFILE'); ?>
									</label>
								</div>
							</div>							
						</div>						

						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_LINKEDIN'); ?>
							</div>
							<div class="col-md-7">
								<input type="text" class="form-control" id="linkedin" name="linkedin" size="55" maxlength="255" value="<?php echo $this->escape($userparams->get('linkedin')); ?>" />
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-5 control-label">
							</div>
							<div class="col-md-7">
								<div class="o-checkbox">
									<input type="checkbox" value="1" id="show_linkedin" name="show_linkedin" <?php echo $userparams->get('show_linkedin') ? ' checked="1"' : ''; ?>>
									<label for="show_linkedin">
										<?php echo JText::_('COM_EASYDISCUSS_SHOW_ON_PROFILE'); ?>
									</label>
								</div>
							</div>							
						</div>						

						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_SKYPE'); ?>
							</div>
							<div class="col-md-7">
								<input type="text" class="form-control" id="skype" name="skype" size="55" maxlength="255" value="<?php echo $this->escape($userparams->get('skype')); ?>" />
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-5 control-label">
							</div>
							<div class="col-md-7">
								<div class="o-checkbox">
									<input type="checkbox" value="1" id="show_skype" name="show_skype" <?php echo $userparams->get('show_skype') ? ' checked="1"' : ''; ?>>
									<label for="show_skype">
										<?php echo JText::_('COM_EASYDISCUSS_SHOW_ON_PROFILE'); ?>
									</label>
								</div>
							</div>							
						</div>						

						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_WEBSITE'); ?>
							</div>
							<div class="col-md-7">
								<input type="text" class="form-control" id="website" name="website" size="55" maxlength="255" value="<?php echo $this->escape($userparams->get('website')); ?>" />
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-5 control-label">
							</div>
							<div class="col-md-7">
								<div class="o-checkbox">
									<input type="checkbox" value="1" id="show_website" name="show_website" <?php echo $userparams->get('show_website') ? ' checked="1"' : ''; ?>>
									<label for="show_website">
										<?php echo JText::_('COM_EASYDISCUSS_SHOW_ON_PROFILE'); ?>
									</label>
								</div>
							</div>							
						</div>						

					</div>
				</div>
			</div>
		</div>
	</div>
</div>
