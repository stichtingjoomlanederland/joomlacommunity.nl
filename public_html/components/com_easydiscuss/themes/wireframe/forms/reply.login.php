<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');
?>

<div class="ed-reply-login t-lg-mt--lg">
	<div class="ed-reply-login__title">
		<?php echo JText::_( 'COM_EASYDISCUSS_PLEASE_LOGIN_TO_REPLY_TITLE' );?>
	</div>
	<div class="ed-reply-login__info t-lg-mb--md">
		<?php echo JText::_( 'COM_EASYDISCUSS_PLEASE_LOGIN_TO_REPLY_INFO' );?>
		<a href="<?php echo ED::getRegistrationLink();?>" class=""><?php echo JText::_( 'COM_EASYDISCUSS_REGISTER_HERE' );?></a>
	</div>
	<div class="ed-reply-login__form-wrap t-lg-mb--md">
		<form method="post" action="<?php echo JRoute::_( 'index.php' );?>">
			<div class="form-group">
				<label for="discuss-post-username"><?php echo JText::_( 'COM_EASYDISCUSS_USERNAME' );?></label>
				<input type="text" tabindex="201" id="discuss-post-username" name="username" class="form-control" autocomplete="off" />
			</div>
			<div class="form-group">
				<label for="discuss-post-password"><?php echo JText::_( 'COM_EASYDISCUSS_PASSWORD' );?></label>
				<input type="password" tabindex="202" id="discuss-post-password" class="form-control" name="password" autocomplete="off" />
			</div>
			<div class="">
				<div class="o-checkbox o-checkbox--inline t-lg-mr--md">
				    <input type="checkbox" tabindex="203" id="discuss-post-remember" name="remember" class="" value="yes" checked="" />
				    <label for="discuss-post-remember">
				       <?php echo JText::_( 'COM_EASYDISCUSS_REMEMBER_ME' );?>
				    </label>
				</div>
				<input type="submit" tabindex="204" value="<?php echo JText::_( 'COM_EASYDISCUSS_LOGIN' , true);?>" name="Submit" class="btn btn-primary pull-right" />
			</div>

			<?php if ($this->config->get('integrations_jfbconnect') && ED::jfbconnect()->exists()) { ?>
				{JFBCLogin}
			<?php } ?>

			<input type="hidden" value="com_users"  name="option">
			<input type="hidden" value="user.login" name="task">
			<input type="hidden" name="return" value="<?php echo base64_encode('index.php?option=com_easydiscuss&view=post&id=' . $post->id); ?>" />
			<?php echo JHTML::_( 'form.token' ); ?>
		</form>
	</div>
	<a tabindex="206" class="pull-lef" href="<?php echo ED::getResetPasswordLink();?>"><?php echo JText::_( 'COM_EASYDISCUSS_FORGOT_PASSWORD' );?></a>
</div>
