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
require_once( DISCUSS_HELPERS . DS . 'router.php' );
?>
<form action="<?php echo JRoute::_( 'index.php?option='.DiscussHelper::getUserComponent().'&task='.DiscussHelper::getUserComponentLoginTask(), true ); ?>" method="post" name="member-form" id="member-form-login" >
<h3><?php echo JText::_('COM_EASYDISCUSS_SIGNIN_PLEASE_LOGIN'); ?></h3>
	<div id="usertype_status"><div class="msg_in"></div></div>
	<div id="usertype_pane_container">
		<div id="usertype_member_pane">
			<p class="small"><?php echo JText::_( 'COM_EASYDISCUSS_SIGNIN_DESC' );?></p>
			<p class="halfcut">
				<label for="discuss_member_username"><?php echo JText::_('COM_EASYDISCUSS_MEMBER_USERNAME') ?></label>
				<span class="si_input"><input id="discuss_member_username" type="text" name="username" class="inputbox" alt="username" size="18" /></span>
			</p>
			<p class="halfcut">
				<label for="discuss_member_passwd"><?php echo JText::_('COM_EASYDISCUSS_MEMBER_PASSWORD') ?></label>
				<span class="si_input">
					<?php if( DiscussHelper::getJoomlaVersion() >= '1.6' ) : ?>
					<input id="discuss_member_passwd" type="password" name="password" class="inputbox" size="18" alt="password" />
					<?php else: ?>
					<input id="discuss_member_passwd" type="password" name="passwd" class="inputbox" size="18" alt="password" />
					<?php endif; ?>
				</span>
			</p>
			<p class="halfcut">
				<?php $registerView = ( DiscussHelper::getJoomlaVersion() >= '1.6') ? 'registration' : 'register'; ?>
				<?php echo JText::sprintf( 'COM_EASYDISCUSS_SIGNIN_CREATE_NEW_ACCOUNT' , JRoute::_( 'index.php?option='.DiscussHelper::getUserComponent().'&view=' . $registerView ) );?>
			</p>
		</div>
		<div class="dialog-buttons">
			<input type="submit" value="<?php echo JText::_( 'COM_EASYDISCUSS_BUTTON_LOGIN' );?>" class="si_btn" />
			<input type="button" value="<?php echo JText::_( 'COM_EASYDISCUSS_BUTTON_CANCEL' );?>"  class="si_btn" id="edialog-cancel" name="edialog-cancel" />
			<span id="dialog_loading" class="float-r"></span>
			<input type="hidden" name="return" value="<?php echo $return; ?>" />
			<?php echo JHTML::_( 'form.token' ); ?>
		</div>
	</div>
</form>
