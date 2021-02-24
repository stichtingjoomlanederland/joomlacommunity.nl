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
<div class="o-card o-card--ed-reply-login">
	<div class="o-card__body l-stack">
		<div class="o-title-01">
			<?php echo $title;?>
		</div>
		<div class="o-body">
			<?php echo $info;?>
			<a href="<?php echo ED::getRegistrationLink();?>" class="si-link"><?php echo JText::_('COM_EASYDISCUSS_REGISTER_HERE');?></a>
		</div>

		<div class="ed-reply-login-form">
			<form method="post" action="<?php echo JRoute::_('index.php');?>">

				<div class="lg:o-grid lg:o-grid--gutters">
					<div class="lg:o-grid__cell">
						<?php echo $this->html('form.floatingLabel', $usernameField, 'username'); ?>
					</div>

					<div class="lg:o-grid__cell t-lg-ml--xl t-xs-ml--no">
						<?php echo $this->html('form.floatingLabel', 'COM_EASYDISCUSS_TOOLBAR_PASSWORD', 'password', 'password'); ?>
					</div>

					<?php if (ED::isTwoFactorEnabled()) { ?>
						<div class="lg:o-grid__cell t-lg-ml--xl t-xs-ml--no">
							<?php echo $this->html('form.floatingLabel', 'COM_ED_TOOLBAR_SECRET_KEY', 'secretkey'); ?>
						</div>
					<?php } ?>
				</div>

				<div class="t-d--flex t-align-items--c sm:t-flex-directions--c">
					<div class="t-flex-grow--1">
						<div class="o-form-check">
							<input type="checkbox" tabindex="203" id="discuss-post-remember" name="remember" class="o-form-check-input" value="yes" checked="" />
							<label for="discuss-post-remember" class="o-form-check-label">
								<?php echo JText::_('COM_EASYDISCUSS_REMEMBER_ME');?>
							</label>
						</div>
					</div>
					<div class="">
						<input type="submit" tabindex="204" value="<?php echo JText::_('COM_EASYDISCUSS_LOGIN', true);?>" name="Submit" class="o-btn o-btn--primary" />
					</div>
				</div>

				<?php if ($this->config->get('integrations_jfbconnect') && ED::jfbconnect()->exists()) { ?>
					{JFBCLogin}
				<?php } ?>

				<input type="hidden" value="com_users"  name="option">
				<input type="hidden" value="user.login" name="task">
				<input type="hidden" name="return" value="<?php echo $return; ?>" />
				<?php echo JHTML::_('form.token'); ?>
			</form>
		</div>

	</div>
	<div class="o-card__footer l-stack">
		<a tabindex="206" class="si-link o-meta" href="<?php echo ED::getResetPasswordLink();?>"><?php echo JText::_('COM_EASYDISCUSS_FORGOT_PASSWORD');?></a>
	</div>

	

	
	
</div>
