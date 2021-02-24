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
<div id="ed" class="ed-mod ed-mod--navigation-button <?php echo $lib->getModuleWrapperClass();?>">
	<div class="ed-mod-card">
		<div class="ed-mod-card__body">
			<div class="mod-ed-menu-bar">
				<div class="mod-ed-menu-bar__nav">
					<?php if ($acl->allowed('add_question') && !$post->isUserBanned()) { ?>
					<div class="o-nav__item">
						<a data-ed-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_NEW_DISCUSSION');?>" href="<?php echo EDR::_('view=ask');?>" class="mod-ed-menu-bar__icon-link has-composer">
							<i class="fas fa-pen"></i>
						</a>
					</div>
					<?php } ?>
					
					<?php if (!$guest) { ?>
						<?php if ($config->get('main_conversations') && $showConversation && $acl->allowed('allow_privatemessage')) { ?>
							<?php if ($useExternalConversations) { ?>
								<div class="o-nav__item">
									<a href="<?php echo ED::getConversationsRoute();?>" class="mod-ed-menu-bar__icon-link <?php echo $conversationsCount ? 'has-new' : '';?>" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_CONVERSATIONS');?>">
										<i class="fa fa-envelope"></i>
									</a>
								</div>
							<?php } else { ?>
								<div class="o-nav__item">
									<a href="javascript:void(0);" class="mod-ed-menu-bar__icon-link <?php echo $conversationsCount ? 'has-new' : '';?>" data-ed-conversations-wrapper data-ed-popbox="ajax://site/views/conversation/popbox" data-ed-popbox-position="<?php echo JFactory::getDocument()->getDirection() == 'rtl' ? 'bottom-left' : 'bottom-right';?>" data-ed-popbox-toggle="click" data-ed-popbox-offset="4" data-ed-popbox-type="navbar-conversations" data-ed-popbox-component="popbox--navbar" data-ed-popbox-cache="0" data-ed-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_CONVERSATIONS');?>">
										<i class="fa fa-envelope"></i>
										<span class="mod-ed-menu-bar__link-bubble" data-ed-conversations-counter><?php echo $conversationsCount;?></span>
									</a>
								</div>
							<?php } ?>
						<?php } ?>

						<?php if ($showNotification) { ?>
						<div class="o-nav__item">
							<a href="javascript:void(0);" class="mod-ed-menu-bar__icon-link <?php echo $notificationsCount ? 'has-new' : '';?>" data-ed-notifications-wrapper data-ed-popbox="ajax://site/views/notifications/popbox" data-ed-popbox-position="<?php echo JFactory::getDocument()->getDirection() == 'rtl' ? 'bottom-left' : 'bottom-right';?>" data-ed-popbox-toggle="click" data-ed-popbox-offset="4" data-ed-popbox-type="navbar-notifications" data-ed-popbox-component="popbox--navbar" data-ed-popbox-cache="0" data-ed-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_NOTIFICATIONS');?>">
								<i class="fa fa-bell"></i>
								<span class="mod-ed-menu-bar__link-bubble" data-ed-notifications-counter><?php //echo $notificationsCount;?></span>
							</a>
						</div>
						<?php } ?>

						<!-- Show more settings -->
						<?php if ($showSettings) { ?>
						<div class="o-nav__item">
							<a href="javascript:void(0);" class="mod-ed-menu-bar__icon-link" data-ed-popbox data-ed-popbox-position="<?php echo JFactory::getDocument()->getDirection() == 'rtl' ? 'bottom-left' : 'bottom-right';?>" data-ed-popbox-offset="4" data-ed-popbox-type="navbar-profile" data-ed-popbox-component="popbox--navbar" data-ed-popbox-target="[data-ed-toolbar-profile-dropdown]" data-ed-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_MORE_SETTINGS');?>">
								<i class="fa fa-cog"></i> 
							</a>

							<div class="t-hidden" data-ed-toolbar-profile-dropdown>
								<div class="popbox-dropdown">
									<div class="ed-toolbar-profile">
										<div class="ed-toolbar-profile__hd ">
											<div class="ed-toolbar-profile-info">
												<div class="o-media o-media--rev t-mb--sm">
													<div class="o-media__body o-media__body--text-overflow">

														<?php echo $lib->html('user.username', $my, array()); ?>

														<div class="ed-user-rank o-label t-ml--sm" style="background-color: <?php echo $my->getRoleLabelColour();?> !important;">
															<?php echo $my->getRole(); ?>
														</div>

														<div class="ed-toolbar-profile-meta">
															<div class="ed-toolbar-profile-meta__item">
																<span>
																	<?php echo ED::ranks()->getRank($my->getId()); ?>
																</span>
															</div>
														</div>
													</div>
													<div class="o-media__image">
														<?php echo $lib->html('user.avatar', $my, array('rank' => true, 'popbox' => false)); ?>
													</div>
												</div>

												<?php echo ED::badges()->getToolbarHtml();?>
											</div>
										</div>
										<div class="ed-toolbar-profile__bd">
											<?php if (ED::easyblog()->hasToolbar()) { ?>
												<?php echo ED::easyblog()->getToolbarDropdown();?>
											<?php } ?>

											<?php if (ED::easysocial()->hasToolbar($my->id)) { ?>
												<?php echo ED::easysocial()->getToolbarDropdown();?>
											<?php } ?>

											<div class="ed-toolbar-dropdown-nav">
												<div class="ed-toolbar-dropdown-nav__item ">
													<a href="<?php echo $my->getEditProfileLink();?>"  class="ed-toolbar-dropdown-nav__link">
														<div class="ed-toolbar-dropdown-nav__name">
															<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_EDIT_PROFILE'); ?>
														</div>
													</a>
												</div>

												<div class="ed-toolbar-dropdown-nav__item ">
													<a href="<?php echo $my->getPermalink();?>"  class="ed-toolbar-dropdown-nav__link">
														<div class="ed-toolbar-dropdown-nav__name">
															<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_MY_PROFILE'); ?>
														</div>
													</a>
												</div>

												<div class="ed-toolbar-dropdown-nav__item ">
													<a href="<?php echo EDR::_('view=mypost');?>"  class="ed-toolbar-dropdown-nav__link">
														<div class="ed-toolbar-dropdown-nav__name">
															<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_MY_POSTS'); ?>
														</div>

														<span class="ed-toolbar-dropdown-nav__badge">
															<?php echo $my->getTotalQuestions(); ?>
														</span>
													</a>
												</div>

												<?php if ($lib->config->get('main_postassignment') && ED::isModerator()) { ?>
												<div class="ed-toolbar-dropdown-nav__item ">
													<a href="<?php echo EDR::_('view=assigned');?>"  class="ed-toolbar-dropdown-nav__link">
														<div class="ed-toolbar-dropdown-nav__name">
															<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_MY_ASSIGNED_POSTS'); ?>
														</div>

														<span class="ed-toolbar-dropdown-nav__badge">
															<?php echo $my->getTotalAssigned(); ?>
														</span>
													</a>
												</div>
												<?php } ?>

												<?php if ($lib->config->get('main_favorite')) { ?>
												<div class="ed-toolbar-dropdown-nav__item ">
													<a href="<?php echo EDR::_('view=favourites');?>"  class="ed-toolbar-dropdown-nav__link">
														<div class="ed-toolbar-dropdown-nav__name">
															<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_MY_FAVOURITES'); ?>
														</div>

														<span class="ed-toolbar-dropdown-nav__badge">
															<?php echo $my->getTotalFavourites(); ?>
														</span>
													</a>
												</div>
												<?php } ?>

												<?php if ($showManageSubscription) { ?>
												<div class="ed-toolbar-dropdown-nav__item ">
													<a href="<?php echo EDR::_('view=subscription');?>"  class="ed-toolbar-dropdown-nav__link">
														<div class="ed-toolbar-dropdown-nav__name">
															<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_MY_SUBSCRIPTION'); ?>
														</div>

														<span class="ed-toolbar-dropdown-nav__badge">
															<?php echo $my->getTotalSubscriptions(); ?>
														</span>
													</a>
												</div>
												<?php } ?>

												<?php if ($lib->acl->allowed('manage_pending') || ED::isSiteAdmin()) { ?>
												<div class="ed-toolbar-dropdown-nav__item ">
													<a href="<?php echo EDR::_('view=dashboard');?>"  class="ed-toolbar-dropdown-nav__link">
														<div class="ed-toolbar-dropdown-nav__name">
															<?php echo JText::_('COM_ED_MANAGE_SITE'); ?>
														</div>
													</a>
												</div>
												<?php } ?>
											</div>
										</div>
										<div class="ed-toolbar-profile__ft">
											<a href="javascript:void(0);" class="ed-toolbar-dropdown-nav__link" data-ed-button-logout>
												<div class="ed-toolbar-dropdown-nav__name">
													<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_LOGOUT');?> </div>
												<i class="fas fa-sign-out-alt"></i>
											</a>
											<form method="post" action="<?php echo JRoute::_('index.php');?>" data-ed-button-logout-form>
												<input type="hidden" value="com_users"  name="option">
												<input type="hidden" value="user.logout" name="task">
												<input type="hidden" name="<?php echo ED::getToken();?>" value="1" />
												<input type="hidden" value="<?php echo EDR::getLogoutRedirect(); ?>" name="return" />
											</form>
										</div>
									</div>

								</div>
							</div>
						</div>
						<?php } ?>
					<?php } ?>

					<?php if ($guest && $config->get('layout_toolbarlogin', '')) { ?>
					<div class="o-nav__item">
						<a href="javascript:void(0);" class="mod-ed-menu-bar__icon-link" data-ed-popbox 
						data-ed-popbox-position="<?php echo $popboxPosition; ?>" 
						data-ed-popbox-offset="4" 
						data-ed-popbox-type="navbar-signin" 
						data-ed-popbox-component="popbox--navbar" 
						data-ed-popbox-target="[data-ed-toolbar-signin-dropdown-module]" 
						data-ed-provide="tooltip" 
						data-original-title="<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_SIGN_IN');?>">
							<i class="fa fa-lock"></i>
						</a>
						<div class="t-hidden" data-ed-toolbar-signin-dropdown-module>
							<div class="popbox-dropdown">

								<div class="popbox-dropdown__hd">
									<div class="o-flag o-flag--rev">
										<div class="o-flag__body">
											<div class="popbox-dropdown__title"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_SIGN_IN_HEADING');?></div>
											<div class="popbox-dropdown__meta"><?php echo JText::sprintf('COM_EASYDISCUSS_TOOLBAR_NEW_USERS_REGISTRATION', ED::getRegistrationLink());?></div>
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
													<label for="ed-remember" class="o-form-check-label"><?php echo JText::_('COM_EASYDISCUSS_REMEMBER_ME');?></label>
												</div>
											</div>
											<div>
												<button class="o-btn o-btn--primary o-btn--s"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_SIGN_IN');?></button>
											</div>
										</div>
										<?php if ($lib->config->get('integrations_jfbconnect') && ED::jfbconnect()->exists()) { ?>
											<div class="o-row">
												{JFBCLogin}
											</div>
										<?php } ?>
										<input type="hidden" value="com_users"  name="option" />
										<input type="hidden" value="user.login" name="task" />
										<input type="hidden" name="return" value="<?php echo $return; ?>" />
										<input type="hidden" name="<?php echo ED::getToken();?>" value="1" />
									</form>
								</div>

								<div class="popbox-dropdown__ft">
									<a href="<?php echo ED::getRemindUsernameLink();?>" class="popbox-dropdown__note si-link"><?php echo JText::_('COM_EASYDISCUSS_FORGOT_USERNAME');?></a>
								</div>
								<div class="popbox-dropdown__ft">
									<a href="<?php echo ED::getResetPasswordLink();?>" class="popbox-dropdown__note si-link"><?php echo JText::_('COM_EASYDISCUSS_FORGOT_PASSWORD');?></a>
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



<script>
ed.require(['edq', 'site/src/toolbar', 'site/src/floatlabels'], function($, App) {

var toolbarSelector = '[data-ed-toolbar]';

// Implement the abstract
App.execute(toolbarSelector, {
	"notifications": {
		"interval": <?php echo $lib->config->get('main_notifications_interval') * 1000; ?>,
		"enabled": <?php echo $lib->my->id && $lib->config->get('main_notifications') ? 'true' : 'false';?>
	},
	"conversations": {
		"interval": <?php echo $lib->config->get('main_conversations_notification_interval') * 1000 ?>,
		"enabled": <?php echo $lib->my->id && $lib->config->get('main_conversations') && $lib->config->get('main_conversations_notification') ? 'true' : 'false';?>
	}
});

$(document)
.off('click.logout')
.on('click.logout', '[data-ed-button-logout]', function() {
	$('[data-ed-button-logout-form]').submit();
});


});
</script>