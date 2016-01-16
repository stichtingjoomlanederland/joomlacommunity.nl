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
<h3><?php echo JText::_('COM_EASYDISCUSS_PLEASE_SELECT_A_USER_TYPE'); ?></h3>
<div id="usertype_status"><div class="msg_in"></div></div>

<div id="usertype_pane_container">

	<ul id="usertype_pane_left" class="reset-ul">
		<?php if($system->my->id == 0 && $acl->allowed('add_reply')) { ?>
		<li id="usertype_guest">
			<a href="javascript:void(0);" onclick="discuss.login.showpane('guest');"><?php echo JText::_('COM_EASYDISCUSS_GUEST'); ?></a>
		</li>
		<?php } ?>

		<li id="usertype_member">
			<a href="javascript:void(0);" onclick="discuss.login.showpane('member');"><?php echo JText::_('COM_EASYDISCUSS_MEMBER'); ?></a>
		</li>

		<?php if($system->my->id == 0 && $acl->allowed('add_reply')) { ?>
		<li id="discuss_register">
			<a href="javascript:void(0);" onclick="discuss.login.showpane('register');"><?php echo JText::_('COM_EASYDISCUSS_REGISTER'); ?></a>
		</li>
		<?php } ?>

		<?php if($system->config->get('integration_twitter_enable') && ($system->config->get('integration_twitter_consumer_key') && $system->config->get('integration_twitter_consumer_secret_key'))) { ?>
		<li id="usertype_twitter">
			<a href="javascript:void(0);" onclick="discuss.login.showpane('twitter');"><?php echo JText::_('COM_EASYDISCUSS_TWITTER'); ?></a>
		</li>
		<?php } ?>
	</ul>


	<div id="usertype_pane_right">
	<?php if($system->my->id == 0 && $acl->allowed('add_reply')) { ?>
		<div id="usertype_guest_pane_wrapper" style="display:none;">
			<form action="<?php echo DiscussRouter::_( 'index.php', true); ?>" method="" name="discuss-guest-login" id="discuss-guest-login">
				<h1><?php echo JText::_('COM_EASYDISCUSS_GUEST_SIGN_IN');?></h1>
				<div id="usertype_guest_pane">
					<p class="small"><?php echo JText::_( 'COM_EASYDISCUSS_GUEST_SIGN_IN_DESC' );?></p>
					<p class="halfcut">
						<label for="discuss_usertype_guest_email"><?php echo JText::_( 'COM_EASYDISCUSS_GUEST_EMAIL' );?></label>
						<span class="si_input">
							<input type="text" name="discuss_usertype_guest_email" id="discuss_usertype_guest_email" value="<?php echo empty($guest->email)? '':$guest->email; ?>" onkeyup="discuss.login.getGuestDefaultName();">
						</span>
					</p>

					<p class="halfcut">
						<label for="discuss_usertype_guest_name"><?php echo JText::_( 'COM_EASYDISCUSS_GUEST_NAME' );?></label>
						<span class="si_input">
							<input type="text" name="discuss_usertype_guest_name" id="discuss_usertype_guest_name" value="<?php echo empty($guest->name)? '':$guest->name; ?>">
						</span>
					</p>
				</div>
				<div id="usertype_guest_pane_button">
					<input type="button" value="Reply" class="si_btn" id="edialog-guest-reply" name="edialog-reply" onclick="discuss.login.submit.reply('guest');"/>
					<input type="button" value="Cancel" class="si_btn" id="edialog-cancel" name="edialog-cancel" />
				</div>
			</form>
		</div>
	<?php } ?>


		<div id="usertype_member_pane_wrapper" style="display:none;">
			<form action="<?php echo DiscussRouter::_( 'index.php', true); ?>" method="post" name="member-form" id="member-form-login" >
				<h1><?php echo JText::_('COM_EASYDISCUSS_MEMBER_SIGN_IN');?></h1>
				<div id="usertype_member_pane">
					<p class="small"><?php echo JText::_( 'COM_EASYDISCUSS_MEMBER_SIGN_IN_DESC' );?></p>
					<p class="halfcut">
						<label for="discuss_member_username"><?php echo JText::_('COM_EASYDISCUSS_MEMBER_USERNAME') ?></label>
						<span class="si_input">
							<input id="discuss_usertype_member_username" type="text" name="discuss_usertype_member_username" class="inputbox" alt="username" size="18" />
						</span>
					</p>
					<p class="halfcut">
						<label for="discuss_member_passwd"><?php echo JText::_('COM_EASYDISCUSS_MEMBER_PASSWORD') ?></label>
						<span class="si_input">
							<input id="discuss_usertype_member_password" type="password" name="passwd" class="inputbox" size="18" alt="password" />
						</span>
					</p>
				</div>
				<div id="usertype_member_pane_button">
					<input type="button" value="Reply" class="si_btn" id="edialog-member-reply" name="edialog-reply" onclick="discuss.login.submit.reply('member');"/>
					<input type="button" value="Cancel" class="si_btn" id="edialog-cancel" name="edialog-cancel" />
					<input type="hidden" name="option" value="<?php echo DiscussHelper::getUserComponent(); ?>" />
					<input type="hidden" name="task" value="login" />
					<input type="hidden" name="return" value="<?php echo $return; ?>" />
				</div>
			</form>
		</div>

	<?php if($system->my->id == 0 && $acl->allowed('add_reply')) { ?>
		<div id="discuss_register_pane_wrapper" style="display:none;">
			<form action="<?php echo DiscussRouter::_( 'index.php', true); ?>" method="" name="discuss-register-login" id="discuss-register-login">
				<h1><?php echo JText::_('COM_EASYDISCUSS_REGISTER');?></h1>
				<div id="discuss_register_pane">
					<input>
				</div>
				<div id="discuss_register_pane_button">
					<input type="button" value="Reply" class="si_btn" id="edialog-register-reply" name="edialog-reply" onclick="discuss.login.submit.reply('register');"/>
					<input type="button" value="Cancel" class="si_btn" id="edialog-cancel" name="edialog-cancel" />
				</div>
			</form>
		</div>
	<?php } ?>

	<?php if($system->config->get('integration_twitter_enable') && ($system->config->get('integration_twitter_consumer_key') && $system->config->get('integration_twitter_consumer_secret_key'))) { ?>
		<div id="usertype_twitter_pane_wrapper" style="display:none;">
			<h1><?php echo JText::_('COM_EASYDISCUSS_TWITTER_SIGN_IN');?></h1>
			<div id="usertype_twitter_pane">
				<?php echo $twitter; ?>
			</div>
			<div id="usertype_twitter_pane_button">
				<input type="button" value="Reply" class="si_btn" id="edialog-twitter-reply" name="edialog-reply" onclick="discuss.login.submit.reply('twitter');"/>
				<input type="button" value="Cancel" class="si_btn" id="edialog-cancel" name="edialog-cancel" />
			</div>
		</div>
	<?php } ?>
		<div id="usertype_loading_pane" style="align:center;display:none;">
			<img src="<?php echo DISCUSS_JURIROOT.'/components/com_easydiscuss/assets/images/loading.gif'; ?>" alt="Loading">
		</div>
	</div>
</div>
