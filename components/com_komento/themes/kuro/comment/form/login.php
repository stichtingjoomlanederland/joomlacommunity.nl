<?php
/**
 * @package		Komento
 * @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * Komento is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<div class="kmt-login">

	<div class="kmt-login-header">
		<h5 class="kmt-login-title"><?php echo JText::_( 'COM_KOMENTO_LOGIN_TO_COMMENT' ); ?></h5>
	</div>


	<form action="<?php echo JRoute::_( 'index.php' ); ?>" method="post" class="kmt-login-form">
		<ul class="reset-ul kmt-login-form-list">
			<li>
				<label for="username">
					<span><?php echo JText::_( 'COM_KOMENTO_LOGIN_USERNAME' ); ?></span>
					<input type="text" id="username" name="username" class="input text" />
				</label>
			</li><!--
			--><li>
				<label for="password">
					<span><?php echo JText::_( 'COM_KOMENTO_LOGIN_PASSWORD' ); ?></span>

					<?php if( Komento::isJoomla15() ) { ?>
						<input type="password" id="passwd" name="passwd" class="input text"/>
					<?php } else { ?>
						<input type="password" id="passwd" name="password" class="input text"/>
					<?php } ?>
				</label>
			</li>
		</ul>
		<div class="kmt-login-body">
			<div class="float-r">
				<button type="submit" class="input button kmt-login-button"><?php echo JText::_( 'COM_KOMENTO_LOGIN_BUTTON' ); ?></button>
			</div>
			<div class="float-r">
				<?php if( JPluginHelper::isEnabled( 'system', 'remember' ) ) { ?>
				<label for="remember">
					<input id="remember" type="checkbox" name="remember" value="yes" alt="<?php echo JText::_( 'COM_KOMENTO_LOGIN_REMEMBER_ME' ); ?>" />
					<span><?php echo JText::_( 'COM_KOMENTO_LOGIN_REMEMBER_ME' ); ?></span>
				</label>
				<?php } ?>
			</div>
		</div>
		<?php if( Komento::isJoomla15() ){ ?>
		<input type="hidden" value="com_user"  name="option">
		<input type="hidden" value="login" name="task">
		<input type="hidden" name="return" value="<?php echo base64_encode( JRequest::getURI() ); ?>" />
		<?php } else { ?>
		<input type="hidden" value="com_users"  name="option">
		<input type="hidden" value="user.login" name="task">
		<input type="hidden" name="return" value="<?php echo base64_encode( JRequest::getURI() ); ?>" />
		<?php } ?>
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>

	<div class="kmt-login-footer">
		<a href="<?php echo Komento::getHelper( 'login' )->getRegistrationLink(); ?>" class="kmt-login-link link-register"><i></i><?php echo JText::_( 'COM_KOMENTO_LOGIN_REGISTER' ); ?></a>
		<a href="<?php echo Komento::getHelper( 'login' )->getResetPasswordLink(); ?>" class="kmt-login-link link-forgot"><i></i><?php echo JText::_( 'COM_KOMENTO_LOGIN_FORGOT_PASSWORD' ); ?></a>
	</div>
</div>
