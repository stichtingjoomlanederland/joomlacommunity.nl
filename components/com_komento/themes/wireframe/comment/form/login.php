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

	<h3 class="kmt-title"><?php echo JText::_( 'COM_KOMENTO_LOGIN_TO_COMMENT' ); ?></h3>


	<form action="<?php echo JRoute::_( 'index.php' ); ?>" method="post" class="kmt-login-form">
		<div class="kmt-form-grid">
			<div class="row-table">
				<div class="col-cell">
					<label for="username">
						<span><?php echo JText::_( 'COM_KOMENTO_LOGIN_USERNAME' ); ?></span>
					</label>
					<div>
						<input type="text" id="username" name="username" class="form-control" />
					</div>
				</div>
				<div class="col-cell">
					<label for="password">
						<span><?php echo JText::_( 'COM_KOMENTO_LOGIN_PASSWORD' ); ?></span>
					</label>
					<div>
						<?php if( Komento::isJoomla15() ) { ?>
							<input type="password" id="passwd" name="passwd" class="input text"/>
						<?php } else { ?>
							<input type="password" id="passwd" name="password" class="form-control"/>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
		<div class="kmt-login-body clearfix">
			<div class="pull-right">
				<button type="submit" class="kmt-login-button btn btn-success"><?php echo JText::_( 'COM_KOMENTO_LOGIN_BUTTON' ); ?></button>
			</div>
			<div class="pull-left">
				<?php if( JPluginHelper::isEnabled( 'system', 'remember' ) ) { ?>
				<div class="checkbox">
					<input id="remember" type="checkbox" name="remember" value="yes" alt="<?php echo JText::_( 'COM_KOMENTO_LOGIN_REMEMBER_ME' ); ?>" />
					<label for="remember" style="margin-top: 0;">
						<?php echo JText::_( 'COM_KOMENTO_LOGIN_REMEMBER_ME' ); ?>
					</label>
				</div>
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
		&middot;
		<a href="<?php echo Komento::getHelper( 'login' )->getResetPasswordLink(); ?>" class="kmt-login-link link-forgot"><i></i><?php echo JText::_( 'COM_KOMENTO_LOGIN_FORGOT_PASSWORD' ); ?></a>
	</div>
</div>
