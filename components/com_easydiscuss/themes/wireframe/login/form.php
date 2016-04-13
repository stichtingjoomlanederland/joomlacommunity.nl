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
<form class="form-horizontal" method="post" action="<?php echo JRoute::_( 'index.php' );?>">
<fieldset class="well">
	<div class="control-group">
		<div class="control-label">
			<label class=" required" for="username" id="username-lbl"><?php echo JText::_( 'COM_EASYDISCUSS_USERNAME' );?><span class="star">&nbsp;*</span></label>
		</div>

		<div class="controls">
			<input type="text" size="25" class="validate-username required" value="" id="username" name="username">
		</div>
	</div>

	<div class="control-group">
		<div class="control-label">
			<label class=" required" for="password" id="password-lbl"><?php echo JText::_( 'COM_EASYDISCUSS_PASSWORD' );?><span class="star">&nbsp;*</span></label>
		</div>
		
		<div class="controls">
			<input type="password" size="25" class="validate-password required" value="" id="password" name="password">
		</div>
	</div>
			
	<div class="control-group">
		<div class="controls">
			<button class="btn btn-primary" type="submit"><?php echo JText::_( 'COM_EASYDISCUSS_BUTTON_LOGIN' );?></button>
		</div>
	</div>
	
	<?php if( DiscussHelper::getJoomlaVersion() >= '1.6' ){ ?>
	<input type="hidden" value="com_users"  name="option">
	<input type="hidden" value="user.login" name="task">
	<?php } else { ?>
	<input type="hidden" value="com_user"  name="option">
	<input type="hidden" value="login" name="task">
	<?php } ?>

	<input type="hidden" value="<?php echo base64_encode( $redirect ); ?>" name="return" />
	<input type="hidden" value="1" name="<?php echo DiscussHelper::getToken(); ?>" />
</fieldset>
</form>