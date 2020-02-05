<?php
/**
* @package		EasyDiscuss
* @copyright 	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<style type="text/css">
	body #ed .mod-ed-menu-bar__icon-link {
		/*color: #000;*/
		/*font-size: 18px;*/
	}
</style>
<div id="ed" class="mod-ed mod-ed-navigation-button">
	<div class="mod-ed-menu-bar">
		
		<div class="o-nav">
			<?php if ($acl->allowed('add_question') && !$post->isUserBanned()) { ?>
			<div class="o-nav__item">
				<a data-ed-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_NEW_DISCUSSION');?>" href="<?php echo EDR::_('view=ask');?>" class="mod-ed-menu-bar__icon-link">
					<i class="fa fa-pencil"></i>
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
						<span class="mod-ed-menu-bar__link-bubble" data-ed-notifications-counter><?php echo $notificationsCount;?></span>
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

							<div class="popbox-dropdown__hd">
								<div class="o-flag o-flag--rev">
									<div class="o-flag__body">
										<a href="<?php echo $my->getPermalink();?>" class="ed-user-name"><?php echo $my->getName();?></a>
										<div class="ed-user-rank "><?php echo ED::ranks()->getRank($my->getId()); ?></div>
									</div>

									<div class="o-flag__image">
										<?php echo ED::themes()->html('user.avatar', $my, array('rank' => true, 'popbox' => false)); ?>
									</div>
								</div>
							</div>

							<div class="popbox-dropdown__bd">
								<div class="popbox-dropdown-nav">

									<div class="popbox-dropdown-nav__item">
										<a href="<?php echo $my->getEditProfileLink();?>" class="popbox-dropdown-nav__link">
											<div class="o-flag">
												<div class="o-flag__image o-flag--top">
													<i class="popbox-dropdown-nav__icon fa fa-cog"></i>
												</div>
												<div class="o-flag__body">
													<div class="popbox-dropdown-nav__name"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_EDIT_PROFILE'); ?></div>
													<ol class="g-list-inline g-list-inline--delimited popbox-dropdown-nav__meta-lists">
														<li><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_PROFILE_ACCOUNT_SETTINGS'); ?></li>
													</ol>
												</div>
											</div>
										</a>
									</div>

									<div class="popbox-dropdown-nav__item">
										<a href="<?php echo EDR::_('view=mypost');?>" class="popbox-dropdown-nav__link">
											<div class="o-flag">
												<div class="o-flag__image o-flag--top">
													<i class="popbox-dropdown-nav__icon fa fa-file-text-o"></i>
												</div>
												<div class="o-flag__body">
													<div class="popbox-dropdown-nav__name"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_MY_POSTS');?></div>
													<ol class="g-list-inline g-list-inline--delimited popbox-dropdown-nav__meta-lists">
														<li><?php echo JText::sprintf('COM_EASYDISCUSS_TOTAL_QUESTION_CREATED', $my->getTotalQuestions()); ?></li>
													</ol>
												</div>
											</div>
										</a>
									</div>

									<?php if (ED::isModerator()) { ?>
									<div class="popbox-dropdown-nav__item">
										<a href="<?php echo EDR::_('view=assigned');?>" class="popbox-dropdown-nav__link">
											<div class="o-flag">
												<div class="o-flag__image o-flag--top">
													<i class="popbox-dropdown-nav__icon fa fa-file-text-o"></i>
												</div>
												<div class="o-flag__body">
													<div class="popbox-dropdown-nav__name"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_MY_ASSIGNED_POSTS');?></div>
													<ol class="g-list-inline g-list-inline--delimited popbox-dropdown-nav__meta-lists">
														<li><?php echo JText::sprintf('COM_EASYDISCUSS_TOOLBAR_ASSIGNED', $my->getTotalAssigned()); ?></li>
														
														<?php if ($config->get('main_qna')) { ?>
															<li><?php echo JText::sprintf('COM_EASYDISCUSS_TOOLBAR_RESOLVED', $my->getTotalResolved()); ?></li>
														<?php } ?>
													</ol>
												</div>
											</div>
										</a>
									</div>
									<?php } ?>

									<?php if ($config->get('main_favorite')) { ?>
									<div class="popbox-dropdown-nav__item">
										<a href="<?php echo EDR::_('view=favourites');?>" class="popbox-dropdown-nav__link">
											<div class="o-flag">
												<div class="o-flag__image o-flag--top">
													<i class="popbox-dropdown-nav__icon fa fa-heart-o"></i>
												</div>
												<div class="o-flag__body">
													<div class="popbox-dropdown-nav__name"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_MY_FAVOURITES');?></div>
													<ol class="g-list-inline g-list-inline--delimited popbox-dropdown-nav__meta-lists">
														<li><?php echo JText::sprintf('COM_EASYDISCUSS_TOOLBAR_MY_FAVOURITES_POST', $my->getTotalFavourites()); ?></li>
													</ol>
												</div>
											</div>
										</a>
									</div>
									<?php } ?>

									<div class="popbox-dropdown-nav__item">
										<a href="<?php echo EDR::_('view=subscription');?>" class="popbox-dropdown-nav__link">
											<div class="o-flag">
												<div class="o-flag__image o-flag--top">
													<i class="popbox-dropdown-nav__icon fa fa-inbox"></i>
												</div>
												<div class="o-flag__body">
													<div class="popbox-dropdown-nav__name"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_MY_SUBSCRIPTION'); ?></div>
													<ol class="g-list-inline g-list-inline--delimited popbox-dropdown-nav__meta-lists">
														<li><?php echo JText::sprintf('COM_EASYDISCUSS_TOOLBAR_MY_SUBSCRIPTION_POST', $my->getTotalSubscriptions()); ?></li>
													</ol>
												</div>
											</div>
										</a>
									</div>

									<?php if (($acl->allowed('manage_holiday') && $config->get('main_work_schedule')) || $acl->allowed('manage_pending') || ED::isSiteAdmin()) { ?>
									<div class="popbox-dropdown-nav__item">
										<a href="<?php echo EDR::_('view=dashboard');?>" class="popbox-dropdown-nav__link">
											<div class="o-flag">
												<div class="o-flag__image o-flag--top">
													<i class="popbox-dropdown-nav__icon fa fa-dashboard"></i>
												</div>
												<div class="o-flag__body">
													<div class="popbox-dropdown-nav__name"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_DASHBOARD');?></div>
													<ol class="g-list-inline g-list-inline--delimited popbox-dropdown-nav__meta-lists">
														<li><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_DASHBOARD_DESC');?></li>
													</ol>
												</div>
											</div>
										</a>
									</div>
									<?php } ?>


									<div class="popbox-dropdown-nav__item">
										<a href="javascript:void(0);" class="popbox-dropdown-nav__link" data-ed-toolbar-logout>
											<div class="o-flag">
												<div class="o-flag__image o-flag--top">
													<i class="popbox-dropdown-nav__icon fa fa-power-off"></i>
												</div>
												<div class="o-flag__body">
													<div class="popbox-dropdown-nav__name"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_LOGOUT');?></div>
													<ol class="g-list-inline g-list-inline--delimited popbox-dropdown-nav__meta-lists">
														<li><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_LOGOUT_DESC');?></li>
													</ol>
												</div>
											</div>
										</a>
									</div>
									<form method="post" action="<?php echo JRoute::_('index.php');?>" data-ed-toolbar-logout-form>
										<input type="hidden" value="com_users"  name="option">
										<input type="hidden" value="user.logout" name="task">
										<input type="hidden" name="<?php echo ED::getToken();?>" value="1" />
										<input type="hidden" value="<?php echo EDR::getLogoutRedirect(); ?>" name="return" />
									</form>
								</div>
							</div>

							<div class="popbox-dropdown__ft">
								<div class="popbox-dropdown__note">
									<?php echo JText::sprintf('COM_EASYDISCUSS_TOOLBAR_LAST_LOGIN_NOTE', $my->getLastOnline()); ?>
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
				data-ed-popbox-target="[data-ed-toolbar-signin-dropdown]" 
				data-ed-provide="tooltip" 
				data-original-title="<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_SIGN_IN');?>">
					<i class="fa fa-lock"></i> <span class="mod-ed-menu-bar__link-text"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_SIGN_IN');?></span>
				</a>
				<div class="t-hidden" data-ed-toolbar-signin-dropdown>
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
								<div class="form-group">
									<label for="ed-username"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_USERNAME');?>:</label>
									<input name="username" type="text" class="form-control" id="ed-username" placeholder="<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_USERNAME');?>" />
								</div>
								<div class="form-group">
									<label for="ed-password"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_PASSWORD');?>:</label>
									<input name="password" type="password" class="form-control" id="ed-password" placeholder="<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_PASSWORD');?>" />
								</div>
								<div class="o-row">
									<div class="o-col o-col--8">
										<div class="o-checkbox o-checkbox--sm">
											<input type="checkbox" id="ed-remember" name="remember" />
											<label for="ed-remember"><?php echo JText::_('COM_EASYDISCUSS_REMEMBER_ME');?></label>
										</div>
									</div>
									<div class="o-col">
										<button class="btn btn-primary btn-sm pull-right"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_SIGN_IN');?></button>
									</div>
								</div>
								<?php if ($config->get('integrations_jfbconnect') && ED::jfbconnect()->exists()) { ?>
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
							<a href="<?php echo ED::getRemindUsernameLink();?>" class="popbox-dropdown__note pull-left"><?php echo JText::_('COM_EASYDISCUSS_FORGOT_USERNAME');?></a>
						</div>
						<div class="popbox-dropdown__ft">
							<a href="<?php echo ED::getResetPasswordLink();?>" class="popbox-dropdown__note pull-left"><?php echo JText::_('COM_EASYDISCUSS_FORGOT_PASSWORD');?></a>
						</div>
					</div>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
</div>