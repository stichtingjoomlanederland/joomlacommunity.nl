<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<script type="text/javascript">
<?php if ($my->id > 0) { ?>
ed.require(['edq', 'site/src/toolbar', 'site/src/discuss'], function($, App, discuss) {
	var toolbarSelector = '[data-mod-notification]';

	// Implement the abstract
	App.execute(toolbarSelector, {
		"notifications": {
			"interval": <?php echo $config->get('main_notifications_interval') * 1000; ?>,
			"enabled": <?php echo $my->id && $config->get('main_notifications') ? 'true' : 'false';?>
		},
		"conversations": {
			"interval": <?php echo $config->get('main_conversations_notification_interval') * 1000 ?>,
			"enabled": <?php echo $my->id && $config->get('main_conversations') && $config->get('main_conversations_notification') ? 'true' : 'false';?>
		}
	});
});
<?php } ?>
</script>
<div id="ed" class="ed-mod m-notification">
	<div class="" data-mod-notification>
		<div class="m-notification__wrapper">
			<ul class="o-nav m-notification__o-nav">
			<?php if (!empty($my->id)) { ?>
                <?php if ($config->get('main_conversations') && $acl->allowed('allow_privatemessage')) { ?>
				<li>
					<a href="javascript:void(0);" class="m-notification__icon-link <?php echo $conversationsCount ? 'has-new' : '';?>"
						data-ed-conversations-wrapper
						data-ed-popbox="ajax://site/views/conversation/popbox"
						data-ed-popbox-toggle="click"
						data-ed-popbox-position="<?php echo $params->get('popbox_position', 'bottom-right'); ?>"
						data-ed-popbox-collision="<?php echo $params->get('popbox_collision', 'flip'); ?>"
						data-ed-popbox-offset="<?php echo $params->get('popbox_offset', 4); ?>"
						data-ed-popbox-type="navbar-conversations"
						data-ed-popbox-component="popbox--navbar"
						data-ed-popbox-cache="0"

						data-ed-provide="tooltip"
						data-original-title="<?php echo JText::_('MOD_NOTIFICATIONS_CONVERSATIONS');?>"
						data-placement="<?php echo $params->get('tooltip_position', 'top');?>"
					>
						<i class="fa fa-envelope"></i>
						<span class="m-notification__link-text"><?php echo JText::_('MOD_NOTIFICATIONS_CONVERSATIONS');?></span>
						<span class="m-notification__link-bubble" data-ed-conversations-counter><?php echo $conversationsCount;?></span>
					</a>
				</li>
				<?php } ?>

				<li>
					<a href="javascript:void(0);" class="m-notification__icon-link <?php echo $notificationsCount ? 'has-new' : '';?>"
						data-ed-notifications-wrapper
						data-ed-popbox="ajax://site/views/notifications/popbox"
						data-ed-popbox-toggle="click"
						data-ed-popbox-position="<?php echo $params->get('popbox_position', 'bottom-right'); ?>"
						data-ed-popbox-collision="<?php echo $params->get('popbox_collision', 'flip'); ?>"
						data-ed-popbox-offset="<?php echo $params->get('popbox_offset', 4); ?>"
						data-ed-popbox-type="navbar-notifications"
						data-ed-popbox-component="popbox--navbar"
						data-ed-popbox-cache="0"

						data-ed-provide="tooltip"
						data-original-title="<?php echo JText::_('MOD_NOTIFICATIONS_NOTIFICATIONS');?>"
						data-placement="<?php echo $params->get('tooltip_position', 'top');?>"
					>
						<i class="fa fa-bell"></i> <span class="m-notification__link-text"><?php echo JText::_('MOD_NOTIFICATIONS_NOTIFICATIONS');?></span>
						<span class="m-notification__link-bubble" data-ed-notifications-counter><?php echo $notificationsCount;?></span>
					</a>
				</li>


			<?php }else{ ?>

				<li>
					<a href="javascript:void(0);" class="m-notification__icon-link"
						data-ed-popbox
						data-ed-popbox-position="<?php echo $params->get('popbox_position', 'bottom-right'); ?>"
						data-ed-popbox-collision="<?php echo $params->get('popbox_collision', 'flip'); ?>"
						data-ed-popbox-offset="<?php echo $params->get('popbox_offset', 4); ?>"
						data-ed-popbox-type="navbar-signin"
						data-ed-popbox-component="popbox--navbar"
						data-ed-popbox-target="[data-ed-toolbar-signin-dropdown]"

						data-ed-provide="tooltip"
						data-original-title="<?php echo JText::_('MOD_NOTIFICATIONS_LOGIN');?>"
						data-placement="<?php echo $params->get('tooltip_position', 'top');?>"
					>
						<i class="fa fa-lock"></i> <span class="m-notification__link-text"><?php echo JText::_('MOD_NOTIFICATIONS_LOGIN');?></span>
					</a>

					<div class="t-hidden" data-ed-toolbar-signin-dropdown>
						<div class="popbox-dropdown">
							<div class="popbox-dropdown__hd">
								<div class="o-flag o-flag--rev">
									<div class="o-flag__body">
										<div class="popbox-dropdown__title"><?php echo JText::_('MOD_NOTIFICATIONS_SIGN_IN_HEADING');?></div>
										<div class="popbox-dropdown__meta"><?php echo JText::sprintf('MOD_NOTIFICATIONS_NEW_USERS_REGISTRATION', ED::getRegistrationLink());?></div>
									</div>
								</div>
							</div>
							<div class="popbox-dropdown__bd">
								<form action="<?php echo JRoute::_('index.php');?>" class="popbox-dropdown-signin" method="post" data-ed-toolbar-login-form>
									<div class="form-group">
										<label for="ed-username"><?php echo JText::_($usernameField);?>:</label>
										<input name="username" type="text" class="form-control" id="ed-username" placeholder="<?php echo JText::_($usernameField);?>" />
									</div>
									<div class="form-group">
										<label for="ed-password"><?php echo JText::_('MOD_NOTIFICATIONS_PASSWORD');?>:</label>
										<input name="password" type="password" class="form-control" id="ed-password" placeholder="<?php echo JText::_('MOD_NOTIFICATIONS_PASSWORD');?>" />
									</div>
									<div class="o-row">
										<div class="o-col o-col--8">
											<div class="o-checkbox o-checkbox--sm">
												<input type="checkbox" id="ed-remember" name="remember" />
												<label for="ed-remember"><?php echo JText::_('MOD_NOTIFICATIONS_REMEMBER_ME');?></label>
											</div>
										</div>
										<div class="o-col">
											<button class="btn btn-primary btn-sm pull-right"><?php echo JText::_('MOD_NOTIFICATIONS_LOGIN');?></button>
										</div>
									</div>
									<?php if ($config->get('integrations_jfbconnect') && ED::jfbconnect()->exists()) { ?>
										<div class="o-row">
											{JFBCLogin}
										</div>
									<?php } ?>
									<input type="hidden" value="com_users"  name="option" />
									<input type="hidden" value="user.login" name="task" />
									<input type="hidden" name="return" value="<?php echo EDR::getLogoutRedirect(); ?>" />
									<input type="hidden" name="<?php echo ED::getToken();?>" value="1" />
								</form>
							</div>
							<div class="popbox-dropdown__ft">
								<a href="<?php echo ED::getResetPasswordLink();?>" class="popbox-dropdown__note pull-left"><?php echo JText::_('MOD_NOTIFICATIONS_FORGOT_PASSWORD');?></a>
							</div>
						</div>
					</div>
				</li>
			<?php } ?>
		   </ul>
		</div>
	</div>
</div>