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

<div class="discuss-auth">
	<div class="discuss-auth-register">
		<p><b><?php echo JText::_( 'COM_EASYDISCUSS_PLEASE_LOGIN_TO_REPLY_TITLE' );?></b></p>
		<p><?php echo JText::_( 'COM_EASYDISCUSS_PLEASE_LOGIN_TO_REPLY_INFO' );?></p>
		<hr>
		<a href="<?php echo DiscussHelper::getRegistrationLink();?>" class="butt butt-success"><?php echo JText::_( 'COM_EASYDISCUSS_REGISTER_HERE' );?></a>
	</div>
	<div class="discuss-auth-login">
		<form method="post" action="<?php echo JRoute::_( 'index.php' );?>">
			<div class="form-group">
				<a tabindex="205" class="float-r" href="<?php echo DiscussHelper::getRegistrationLink();?>"><?php echo JText::_( 'COM_EASYDISCUSS_REGISTER' );?></a>
				<label for="discuss-post-username"><?php echo JText::_( 'COM_EASYDISCUSS_USERNAME' );?></label>
				<input type="text" tabindex="201" id="discuss-post-username" name="username" class="form-control" size="18" autocomplete="off" />
			</div>
			<div class="form-group">
				<a tabindex="206" class="float-r" href="<?php echo DiscussHelper::getResetPasswordLink();?>"><?php echo JText::_( 'COM_EASYDISCUSS_FORGOT_PASSWORD' );?></a>
				<label for="discuss-post-password"><?php echo JText::_( 'COM_EASYDISCUSS_PASSWORD' );?></label>
				<?php if( DiscussHelper::getJoomlaVersion() >= '1.6' ){ ?>
					<input type="password" tabindex="202" id="discuss-post-password" class="form-control" name="password" autocomplete="off" />
				<?php } else { ?>
					<input type="password" tabindex="202" id="discuss-post-password" class="form-control" name="passwd" autocomplete="off" />
				<?php } ?>
			</div>
			<div class="form-group">
				<div class="checkbox float-l">
					<label for="discuss-post-remember">
						<input type="checkbox" tabindex="203" id="discuss-post-remember" name="remember" class="" value="yes" />
						<?php echo JText::_( 'COM_EASYDISCUSS_REMEMBER_ME' );?>
					</label>
				</div>
				<input type="submit" tabindex="204" value="<?php echo JText::_( 'COM_EASYDISCUSS_LOGIN' , true);?>" name="Submit" class="butt butt-primary float-r" />
			</div>
			<?php if( DiscussHelper::getJoomlaVersion() >= '1.6' ){ ?>
			<input type="hidden" value="com_users"  name="option">
			<input type="hidden" value="user.login" name="task">
			<input type="hidden" name="return" value="<?php echo base64_encode( DiscussRouter::getPostRoute( $post->id , false ) ); ?>" />
			<?php } else { ?>
			<input type="hidden" value="com_user"  name="option">
			<input type="hidden" value="login" name="task">
			<input type="hidden" name="return" value="<?php echo base64_encode( DiscussRouter::getPostRoute( $post->id , false ) ); ?>" />
			<?php } ?>
			<?php echo JHTML::_( 'form.token' ); ?>		
		</form>
	</div>
</div>




