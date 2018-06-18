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
<div class="ed-reply-login t-lg-mt--lg">
	<div class="ed-reply-login__title">
		<?php echo $title;?>
	</div>

	<div class="ed-reply-login__info t-lg-mb--md">
		<?php echo $info;?>
		<a href="<?php echo ED::getRegistrationLink();?>" class=""><?php echo JText::_('COM_EASYDISCUSS_REGISTER_HERE');?></a>
	</div>

	<div class="ed-reply-login__form-wrap t-lg-mb--md">
		<form method="post" action="<?php echo JRoute::_('index.php');?>">

			<div class="o-grid">
				<div class="o-grid__cell">
					<?php echo $this->html('form.floatingLabel', $usernameField, 'username'); ?>
				</div>

				<div class="o-grid__cell t-lg-ml--xl t-xs-ml--no">
					<?php echo $this->html('form.floatingLabel', 'COM_EASYDISCUSS_TOOLBAR_PASSWORD', 'password', 'password'); ?>
				</div>
			</div>

			<div class="o-checkbox o-checkbox--inline t-lg-mr--md">
				<input type="checkbox" tabindex="203" id="discuss-post-remember" name="remember" class="" value="yes" checked="" />
				<label for="discuss-post-remember">
				   <?php echo JText::_('COM_EASYDISCUSS_REMEMBER_ME');?>
				</label>
			</div>
			<input type="submit" tabindex="204" value="<?php echo JText::_('COM_EASYDISCUSS_LOGIN', true);?>" name="Submit" class="btn btn-primary pull-right" />

			<?php if ($this->config->get('integrations_jfbconnect') && ED::jfbconnect()->exists()) { ?>
				{JFBCLogin}
			<?php } ?>

			<input type="hidden" value="com_users"  name="option">
			<input type="hidden" value="user.login" name="task">
			<input type="hidden" name="return" value="<?php echo $return; ?>" />
			<?php echo JHTML::_('form.token'); ?>
		</form>
	</div>
	<a tabindex="206" class="pull-lef" href="<?php echo ED::getResetPasswordLink();?>"><?php echo JText::_('COM_EASYDISCUSS_FORGOT_PASSWORD');?></a>
</div>
