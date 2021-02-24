<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<script type="text/javascript">
<?php if ($my->id > 0) { ?>
ed.require(['edq', 'site/src/toolbar'], function($, App) {
	var toolbarSelector = '[data-mod-notification]';

	// Implement the abstract
	App.execute(toolbarSelector, {
		"notifications": {
			"interval": <?php echo $config->get('main_notifications_interval') * 1000; ?>,
			"enabled": <?php echo $my->id && $config->get('main_notifications') ? 'true' : 'false';?>
		}
	});
});
<?php } ?>
</script>
<div id="ed" class="ed-mod ed-mod--notification <?php echo $lib->getModuleWrapperClass();?>">
	<div class="ed-mod-card">
		<div class="ed-mod-card__body">
			<div class="ed-toolbar__item--action">
				<div class="o-nav ed-toolbar__o-nav">
					<div class="o-nav__item"
						data-ed-notifications-wrapper
						data-ed-popbox="ajax://site/views/notifications/popbox"
						data-ed-popbox-position="<?php echo $params->get('popbox_position', 'bottom-right'); ?>"
						data-ed-popbox-toggle="click"
						data-ed-popbox-offset="<?php echo $params->get('popbox_offset', 32); ?>"
						data-ed-popbox-type="navbar-notifications"
						data-ed-popbox-component="popbox--navbar"
						data-ed-popbox-cache="0"
						data-ed-popbox-collision="<?php echo $params->get('popbox_collision', 'flip'); ?>"

						data-ed-provide="tooltip"
						data-placement="<?php echo $params->get('tooltip_position', 'top');?>"
						data-original-title="<?php echo JText::_('MOD_NOTIFICATIONS_NOTIFICATIONS');?>"
					>
						<a href="javascript:void(0);" class="o-nav__link ed-toolbar__link no-active-state <?php echo $notificationsCount ? 'has-new' : '';?>">
							<i class="fa fa-bell"></i>
							<span class="ed-toolbar__link-bubble" data-ed-notifications-counter><?php echo $notificationsCount;?></span>
						</a>

					</div>

					<?php if (!$my->id) { ?>
					<div class="o-nav__item"
						data-original-title="<?php echo JText::_('MOD_NOTIFICATIONS_LOGIN');?>"
						data-placement="<?php echo $params->get('tooltip_position', 'top');?>"
						data-ed-provide="tooltip"
						data-ed-popbox
						data-ed-popbox-position="<?php echo $params->get('popbox_position', 'bottom-right'); ?>"
						data-ed-popbox-offset="<?php echo $params->get('popbox_offset', 32); ?>"
						data-ed-popbox-type="navbar-signin"
						data-ed-popbox-component="popbox--navbar"
						data-ed-popbox-target="[data-ed-toolbar-signin-dropdown]"
						data-ed-popbox-collision="<?php echo $params->get('popbox_collision', 'flip'); ?>"
						>
						<a href="javascript:void(0);" class="o-nav__link ed-toolbar__link"><i class="fa fa-lock"></i></a>

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
										<?php echo $lib->html('form.floatingLabel', $usernameField, 'username'); ?>
										<?php echo $lib->html('form.floatingLabel', 'COM_EASYDISCUSS_TOOLBAR_PASSWORD', 'password', 'password'); ?>

										<?php if (ED::isTwoFactorEnabled()) { ?>
											<?php echo $lib->html('form.floatingLabel', 'COM_ED_TOOLBAR_SECRET_KEY', 'secretkey'); ?>
										<?php } ?>

										<div class="t-d--flex t-align-items--c">
											<div class="t-flex-grow--1">
												<div class="o-form-check">
													<input type="checkbox" id="ed-remember" name="remember" class="o-form-check-input" />
													<label for="ed-remember" class="o-form-check-label"><?php echo JText::_('MOD_NOTIFICATIONS_REMEMBER_ME');?></label>
												</div>
											</div>
											<div class="">
												<button class="o-btn o-btn--primary o-btn--s"><?php echo JText::_('MOD_NOTIFICATIONS_LOGIN');?></button>
											</div>
										</div>
										<?php if ($lib->config->get('integrations_jfbconnect') && ED::jfbconnect()->exists()) { ?>
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
									<a href="<?php echo ED::getRemindUsernameLink();?>" class="popbox-dropdown__note"><?php echo JText::_('COM_EASYDISCUSS_FORGOT_USERNAME');?></a>
								</div>
								<div class="popbox-dropdown__ft">
									<a href="<?php echo ED::getResetPasswordLink();?>" class="popbox-dropdown__note"><?php echo JText::_('COM_EASYDISCUSS_FORGOT_PASSWORD');?></a>
								</div>
							</div>
						</div>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
		
	</div>
</div>