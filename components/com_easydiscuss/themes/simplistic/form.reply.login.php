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

<div class="discuss-post-login">
	<div class="row-fluid">
		<div class="span7">
			<div class="media">
				<div class="media-object pull-left">
					<img src="<?php echo JURI::root();?>components/com_easydiscuss/themes/simplistic/images/icon-locked.png" alt="<?php echo JText::_( 'COM_EASYDISCUSS_LOGIN_IMAGE' , true );?>" />
				</div>
				<div class="media-body small">
					<h4><?php echo JText::_( 'COM_EASYDISCUSS_PLEASE_LOGIN_TO_REPLY_TITLE' );?></h4>
					<p><?php echo JText::_( 'COM_EASYDISCUSS_PLEASE_LOGIN_TO_REPLY_INFO' );?></p>
					<a href="<?php echo DiscussHelper::getRegistrationLink();?>" class="btn btn-success mt-10"><?php echo JText::_( 'COM_EASYDISCUSS_REGISTER_HERE' );?></a>
				</div>
			</div>

		</div>
		<div class="span5">
			<form method="post" action="<?php echo JRoute::_( 'index.php' );?>">
				<ul class="discuss-login-menu unstyled small">
					<li>
						<a tabindex="205" class="pull-right" href="<?php echo DiscussHelper::getRegistrationLink();?>"><?php echo JText::_( 'COM_EASYDISCUSS_REGISTER' );?></a>
						<label for="discuss-post-username"><?php echo JText::_( 'COM_EASYDISCUSS_USERNAME' );?></label>
						<input type="text" tabindex="201" id="discuss-post-username" name="username" class="input full-width" size="18" autocomplete="off" />
					</li>
					<li>
						<a tabindex="206" class="pull-right" href="<?php echo DiscussHelper::getResetPasswordLink();?>"><?php echo JText::_( 'COM_EASYDISCUSS_FORGOT_PASSWORD' );?></a>
						<label for="discuss-post-password"><?php echo JText::_( 'COM_EASYDISCUSS_PASSWORD' );?></label>

						<?php if( DiscussHelper::getJoomlaVersion() >= '1.6' ){ ?>
							<input type="password" tabindex="202" id="discuss-post-password" class="input full-width" name="password" autocomplete="off" />
						<?php } else { ?>
							<input type="password" tabindex="202" id="discuss-post-password" class="input full-width" name="passwd" autocomplete="off" />
						<?php } ?>
					</li>
					<li>
						<span class="pull-left">
							<label for="discuss-post-remember" class="checkbox">
								<input type="checkbox" tabindex="203" id="discuss-post-remember" name="remember" class="" value="yes" />
								<?php echo JText::_( 'COM_EASYDISCUSS_REMEMBER_ME' );?>
							</label>
						</span>
						<input type="submit" tabindex="204" value="<?php echo JText::_( 'COM_EASYDISCUSS_LOGIN' , true);?>" name="Submit" class="btn btn-primary pull-right" />
					</li>
				</ul>
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
</div>
