<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<style>
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
						        		<?php echo $composer->renderEditor('description', $profile->getDescription()); ?>
						        	</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
