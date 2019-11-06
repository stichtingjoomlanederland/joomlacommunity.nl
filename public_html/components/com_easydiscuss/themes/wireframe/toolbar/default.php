<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>

<?php if ($renderToolbarModule) { ?>
	<?php echo ED::renderModule('easydiscuss-before-header'); ?>
<?php } ?>

<?php if ($showHeader) { ?>
<div class="ed-head t-lg-mb--lg">
	<div class="ed-head__info">
		<h2 class="ed-head__title"><?php echo $headers->title;?></h2>
		<div class="ed-head__desp"><?php echo $headers->desc;?></div>
	</div>

	<div class="ed-subscribe">

		<?php if ($this->config->get('main_rss')) { ?>
		<a href="<?php echo ED::feeds()->getFeedUrl('view=index');?>" class="t-lg-mr--md" target="_blank">
			<i class="fa fa-rss-square ed-subscribe__icon t-lg-mr--sm"></i> <?php echo JText::_("COM_EASYDISCUSS_TOOLBAR_SUBSCRIBE_RSS");?>
		</a>
		<?php } ?>

		<?php if ($this->config->get('main_sitesubscription')) { ?>
		<?php echo ED::subscription()->html($this->my->id, '0', 'site'); ?>
		<?php } ?>
	</div>
</div>
<?php } ?>

<?php if ($renderToolbarModule) { ?>
<?php echo ED::renderModule('easydiscuss-after-header'); ?>

<?php echo ED::renderModule('easydiscuss-before-toolbar'); ?>
<?php } ?>

<?php if ($showToolbar) { ?>
<div class="ed-toolbar t-lg-mb--lg" data-ed-toolbar>
	<?php if ($showHome) { ?>
	<div class="ed-toolbar__item ed-toolbar__item--home">
		<nav class="o-nav ed-toolbar__o-nav">
			<div class="o-nav__item <?php echo $active == 'forums' ? ' is-active' : '';?>">
				<a href="<?php echo EDR::_('view=forums');?>" class="o-nav__link ed-toolbar__link">
					<i class="fa fa-home"></i>
				</a>
			</div>
		</nav>
	</div>
	<?php } ?>

	<div class="ed-toolbar__item ed-toolbar__item--home-submenu" data-ed-toolbar-menu>
		<div class="o-nav ed-toolbar__o-nav">

			<?php if ($showRecent) { ?>
			<div class="o-nav__item <?php echo $active == 'index' ? ' is-active' : '';?>">
				<a href="<?php echo EDR::_('view=index');?>" class="o-nav__link ed-toolbar__link">
					<i class="fa fa-file-text-o t-sm-visible"></i>
					<span>
						<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_RECENT');?>
					</span>
				</a>
			</div>
			<?php } ?>

			<?php if ($showCategories) { ?>
			<div class="o-nav__item <?php echo $active == 'categories' ? ' is-active' : '';?>">
				<a href="<?php echo EDR::_('view=categories');?>" class="o-nav__link ed-toolbar__link">
					<i class="fa fa-folder t-sm-visible"></i>
					<span>
						<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_CATEGORIES');?>
					</span>
				</a>
			</div>
			<?php } ?>

			<?php if ($showTags) { ?>
			<div class="o-nav__item <?php echo $active == 'tags' ? ' is-active' : '';?>">
				<a href="<?php echo EDR::_('view=tags');?>" class="o-nav__link ed-toolbar__link">
					<i class="fa fa-tags t-sm-visible"></i>
					<span>
						<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_TAGS');?>
					</span>
				</a>
			</div>
			<?php } ?>

			<?php if ($showUsers) { ?>
			<div class="o-nav__item <?php echo $active == 'users' ? ' is-active' : '';?>">
				<a href="<?php echo $userMenuLink;?>" class="o-nav__link ed-toolbar__link">
					<i class="fa fa-users t-sm-visible"></i>
					<span>
						<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_USERS');?>
					</span>
				</a>
			</div>
			<?php } ?>

			<?php if ($showBadges && $this->config->get('main_badges')) { ?>
			<div class="o-nav__item <?php echo $active == 'badges' ? ' is-active' : '';?>">
				<a href="<?php echo EDR::_('view=badges');?>" class="o-nav__link ed-toolbar__link">
					<i class="fa fa-certificate t-sm-visible"></i>
					<span>
						<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_BADGES');?>
					</span>
				</a>
			</div>
			<?php } ?>

			<?php if ($group) { ?>
			<div class="o-nav__item <?php echo $active == 'groups' ? ' is-active' : '';?>">
				<a href="<?php echo EDR::_('view=groups');?>" class="o-nav__link ed-toolbar__link">
					<i class="fa fa-user t-sm-visible"></i>
					<span>
						<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_GROUPS');?>
					</span>
				</a>
			</div>
			<?php } ?>

			<?php if (($this->isMobile() || $this->isTablet()) && $this->my->id) { ?>
				<?php echo $this->output('site/toolbar/mobile'); ?>
			<?php } ?>
		</div>
	</div>

	<div class="ed-toolbar__item ed-toolbar__item--search" data-toolbar-search>
		<div id="ed-toolbar-search" class="ed-toolbar__search">
			<form name="discuss-toolbar-search" data-search-toolbar-form method="post" action="<?php echo JRoute::_('index.php'); ?>" class="ed-toolbar__search-form">
				<input type="text" placeholder="<?php echo JText::_('COM_EASYDISCUSS_SEARCH_DEFAULT');?>" autocomplete="off" class="ed-toolbar__search-input" data-search-input name="query" value="<?php echo ED::string()->escape($query); ?>" />
				<input type="hidden" name="option" value="com_easydiscuss" />
				<input type="hidden" name="controller" value="search" />
				<input type="hidden" name="task" value="query" />
				<input type="hidden" name="Itemid" value="<?php echo EDR::getItemId('search'); ?>" />

				<?php echo $this->html('form.token'); ?>

				<?php if ($postTypes) { ?>
				<div class="ed-toolbar__search-select">
					<div class="o-select-group">
						<?php echo $this->output('site/ask/post.types', array('selected' => $postTypeValue)); ?>
						<div class="o-select-group__drop"></div>
					</div>
				</div>
				<?php } ?>

				<div class="ed-toolbar__search-submit-btn">
					<button class="btn btn-toolbar-search" type="submit">
						<i class="fa fa-search"></i>&nbsp; <?php echo JText::_('COM_EASYDISCUSS_SEARCH');?>
					</button>
				</div>

			</form>
		</div>
	</div>

	<div class="ed-toolbar__item ed-toolbar__item--action">
		<nav class="o-nav ed-toolbar__o-nav">

			<?php if ($this->acl->allowed('add_question') && !$post->isUserBanned()) { ?>
			<div class="o-nav__item" data-ed-provide="tooltip"
				data-original-title="<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_NEW_DISCUSSION');?>">
				<a href="<?php echo EDR::_('view=ask');?>" class="o-nav__link ed-toolbar__link has-composer">
					<i class="fa fa-pencil"></i>
					<span class="ed-toolbar__link-text"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_NEW_DISCUSSION');?></span>
				</a>
			</div>
			<?php } ?>

			<?php if (ED::work()->enabled()) { ?>
				<?php echo ED::work(ED::date())->html();?>
			<?php } ?>

			<?php if ($showSearch) { ?>
			<div class="o-nav__item" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_SEARCH');?>" data-placement="top" data-ed-provide="tooltip">
				<a href="javascript:void(0);" class="o-nav__link ed-toolbar__link" data-ed-toolbar-search-toggle><i class="fa fa-search"></i></a>
			</div>
			<?php } ?>

			<?php if ($this->my->id) { ?>
				<?php if ($this->config->get('main_conversations') && $showConversation && $this->acl->allowed('allow_privatemessage')) { ?>
					<?php if ($useExternalConversations) { ?>
						<div class="o-nav__item"
							data-ed-provide="tooltip"
							data-original-title="<?php echo JText::_('COM_EASYDISCUSS_CONVERSATIONS');?>"
						>
							<a href="<?php echo ED::getConversationsRoute();?>" class="o-nav__link ed-toolbar__link no-active-state <?php echo $conversationsCount ? 'has-new' : '';?>"
								data-ck-chat data-ed-external-conversation
							>
								<i class="fa fa-envelope"></i>
								<span class="ed-toolbar__link-text"><?php echo JText::_('COM_EASYDISCUSS_CONVERSATIONS');?></span>
							</a>
						</div>
					<?php } else { ?>
						<div class="o-nav__item"
							data-ed-conversations-wrapper
							data-ed-popbox="ajax://site/views/conversation/popbox"
							data-ed-popbox-position="<?php echo JFactory::getDocument()->getDirection() == 'rtl' ? 'bottom-left' : 'bottom-right';?>"
							data-ed-popbox-toggle="click"
							data-ed-popbox-offset="2"
							data-ed-popbox-type="navbar-conversations"
							data-ed-popbox-component="popbox--navbar"
							data-ed-popbox-cache="0"

							data-ed-provide="tooltip"
							data-original-title="<?php echo JText::_('COM_EASYDISCUSS_CONVERSATIONS');?>"
							>
							<a href="javascript:void(0);" class="o-nav__link ed-toolbar__link no-active-state <?php echo $conversationsCount ? 'has-new' : '';?>">
								<i class="fa fa-envelope"></i>
								<span class="ed-toolbar__link-text"><?php echo JText::_('COM_EASYDISCUSS_CONVERSATIONS');?></span>
								<span class="ed-toolbar__link-bubble" data-counter=""><?php echo $conversationsCount;?></span>
							</a>
						</div>
					<?php } ?>
				<?php } ?>

				<?php if ($showNotification) { ?>
				<div class="o-nav__item"
					data-ed-notifications-wrapper
					data-ed-popbox="ajax://site/views/notifications/popbox"
					data-ed-popbox-position="<?php echo JFactory::getDocument()->getDirection() == 'rtl' ? 'bottom-left' : 'bottom-right';?>"
					data-ed-popbox-toggle="click"
					data-ed-popbox-offset="2"
					data-ed-popbox-type="navbar-notifications"
					data-ed-popbox-component="popbox--navbar"
					data-ed-popbox-cache="0"

					data-ed-provide="tooltip"
					data-original-title="<?php echo JText::_('COM_EASYDISCUSS_NOTIFICATIONS');?>"
				>
					<a href="javascript:void(0);" class="o-nav__link ed-toolbar__link no-active-state <?php echo $notificationsCount ? 'has-new' : '';?>"

					>
						<i class="fa fa-bell"></i> <span class="ed-toolbar__link-text"><?php echo JText::_('COM_EASYDISCUSS_NOTIFICATIONS');?></span>
						<span class="ed-toolbar__link-bubble" data-ed-notifications-counter><?php echo $notificationsCount;?></span>
					</a>

				</div>
				<?php } ?>

				<?php if ($showSettings) { ?>
				<div class="o-nav__item is-signin dropdown_" data-ed-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_MORE_SETTINGS');?>">
					<a href="javascript:void(0);" class="o-nav__link ed-toolbar__link has-avatar dropdown-toggle_" data-ed-toggle="dropdown">
						<div class="ed-toolbar__avatar">
							<?php echo $this->html('user.avatar', $this->profile, array(), false, true); ?>
						</div>
					</a>

					<!-- TODO For dropdown width t-width--100, t-width--66 and t-width--33 -->
					<div class="ed-toolbar__dropdown-menu ed-toolbar__dropdown-menu--action dropdown-menu bottom-right
						<?php echo ED::easyblog()->hasToolbar() && ED::easysocial()->hasToolbar($this->my->id) ? 't-width--100' : '';?>
						<?php echo !ED::easyblog()->hasToolbar() && !ED::easysocial()->hasToolbar($this->my->id) ? 't-width--33' : '';?>
						<?php echo ED::easyblog()->hasToolbar() && !ED::easysocial()->hasToolbar($this->my->id) ? 't-width--66' : '';?>
						<?php echo !ED::easyblog()->hasToolbar() && ED::easysocial()->hasToolbar($this->my->id) ? 't-width--66' : '';?>
							t-width--100"
						data-ed-toolbar-dropdown
					>
						<div class="arrow"></div>
						<div class="ed-toolbar-profile">
							<div class="ed-toolbar-profile__hd <?php echo ED::easysocial()->hasToolbar() ? 'with-cover' : '';?>">

								<?php if (ED::easysocial()->hasToolbar()) { ?>
								<div class="ed-toolbar-profile-cover" style="
									background-image:url('<?php echo ED::easysocial()->getCover()->getSource();?>');
									background-repeat: no-repeat;
									background-position: <?php echo ED::easysocial()->getCover()->getPosition();?>;
									background-size: cover;">
								</div>
								<?php } ?>

								<div class="ed-toolbar-profile-info">
									<div class="o-media o-media--rev">
										<div class="o-media__body o-media__body--text-overflow">

											<?php echo $this->html('user.username', $this->profile, array()); ?>
											<div class="ed-toolbar-profile-meta">
												<div class="ed-toolbar-profile-meta__item">
													<span>
														<?php echo ED::ranks()->getRank($this->profile->getId()); ?>
													</span>
												</div>
											</div>
										</div>
										<div class="o-media__image">
											<?php echo $this->html('user.avatar', $this->profile, array('rank' => true, 'popbox' => false)); ?>
											<div class="o-avatar-status is-online t-hidden">
												<a href="<?php echo $this->profile->getPermalink();?>" class="o-avatar o-avatar--sm" data-user-id="308">
													<img src="<?php echo $this->profile->getAvatar();?>" alt="" width="24" height="24">
												</a>

											</div>
										</div>
									</div>

									<?php echo ED::badges()->getToolbarHtml();?>
								</div>
							</div>

							<div class="ed-toolbar-profile__ft">

								<?php if (ED::easyblog()->hasToolbar()) { ?>
									<?php echo ED::easyblog()->getToolbarDropdown();?>
								<?php } ?>

								<?php if (ED::easysocial()->hasToolbar($this->my->id)) { ?>
									<?php echo ED::easysocial()->getToolbarDropdown();?>
								<?php } ?>

								<div class="ed-toolbar-dropdown-nav">
									<div class="ed-toolbar-dropdown-nav__item ">
										<a href="<?php echo $this->profile->getEditProfileLink();?>"  class="ed-toolbar-dropdown-nav__link">
											<div class="ed-toolbar-dropdown-nav__name">
												<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_EDIT_PROFILE'); ?>
											</div>

											<ol class="g-list-unstyled ed-toolbar-dropdown-nav__meta-lists">
												<li>
													<span>
														<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_PROFILE_ACCOUNT_SETTINGS'); ?>
													</span>
												</li>
											</ol>
										</a>
									</div>

									<div class="ed-toolbar-dropdown-nav__item ">
										<a href="<?php echo EDR::_('view=mypost');?>"  class="ed-toolbar-dropdown-nav__link">
											<div class="ed-toolbar-dropdown-nav__name">
												<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_MY_POSTS'); ?>
											</div>

											<ol class="g-list-unstyled ed-toolbar-dropdown-nav__meta-lists">
												<li>
													<span>
														<?php echo JText::sprintf('COM_EASYDISCUSS_TOTAL_QUESTION_CREATED', $this->profile->getTotalQuestions()); ?>
													</span>
												</li>
											</ol>
										</a>
									</div>

									<?php if (ED::isModerator()) { ?>
									<div class="ed-toolbar-dropdown-nav__item ">
										<a href="<?php echo EDR::_('view=assigned');?>"  class="ed-toolbar-dropdown-nav__link">
											<div class="ed-toolbar-dropdown-nav__name">
												<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_MY_ASSIGNED_POSTS'); ?>
											</div>

											<ol class="g-list-unstyled ed-toolbar-dropdown-nav__meta-lists">
												<li>
													<span>
														<?php echo JText::sprintf('COM_EASYDISCUSS_TOOLBAR_ASSIGNED', $this->profile->getTotalAssigned()); ?>
													</span>

													<?php if ($this->config->get('main_qna')) { ?>
														<span class="t-lg-ml--md"><?php echo JText::sprintf('COM_EASYDISCUSS_TOOLBAR_RESOLVED', $this->profile->getTotalResolved()); ?></span>
													<?php } ?>
												</li>

											</ol>
										</a>
									</div>
									<?php } ?>

									<?php if ($this->config->get('main_favorite')) { ?>
									<div class="ed-toolbar-dropdown-nav__item ">
										<a href="<?php echo EDR::_('view=favourites');?>"  class="ed-toolbar-dropdown-nav__link">
											<div class="ed-toolbar-dropdown-nav__name">
												<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_MY_FAVOURITES'); ?>
											</div>

											<ol class="g-list-unstyled ed-toolbar-dropdown-nav__meta-lists">
												<li>
													<span>
														<?php echo JText::sprintf('COM_EASYDISCUSS_TOOLBAR_MY_FAVOURITES_POST', $this->profile->getTotalFavourites()); ?>
													</span>
												</li>
											</ol>
										</a>
									</div>
									<?php } ?>

									<?php if ($showManageSubscription) { ?>
									<div class="ed-toolbar-dropdown-nav__item ">
										<a href="<?php echo EDR::_('view=subscription');?>"  class="ed-toolbar-dropdown-nav__link">
											<div class="ed-toolbar-dropdown-nav__name">
												<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_MY_SUBSCRIPTION'); ?>
											</div>

											<ol class="g-list-unstyled ed-toolbar-dropdown-nav__meta-lists">
												<li>
													<span>
														<?php echo JText::sprintf('COM_EASYDISCUSS_TOOLBAR_MY_SUBSCRIPTION_POST', $this->profile->getTotalSubscriptions()); ?>
													</span>
												</li>
											</ol>
										</a>
									</div>
									<?php } ?>

									<?php if (($this->acl->allowed('manage_holiday') && $this->config->get('main_work_schedule')) || $this->acl->allowed('manage_pending') || ED::isSiteAdmin()) { ?>
									<div class="ed-toolbar-dropdown-nav__item ">
										<a href="<?php echo EDR::_('view=dashboard');?>"  class="ed-toolbar-dropdown-nav__link">
											<div class="ed-toolbar-dropdown-nav__name">
												<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_DASHBOARD'); ?>
											</div>

											<ol class="g-list-unstyled ed-toolbar-dropdown-nav__meta-lists">
												<li>
													<span>
														<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_DASHBOARD_DESC');?>
													</span>
												</li>
											</ol>
										</a>
									</div>
									<?php } ?>

									<div class="ed-toolbar-dropdown-nav__item ">
										<a href="javascript:void(0);"  class="ed-toolbar-dropdown-nav__link" data-ed-toolbar-logout>
											<div class="ed-toolbar-dropdown-nav__name">
												<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_LOGOUT'); ?>
											</div>

											<ol class="g-list-unstyled ed-toolbar-dropdown-nav__meta-lists">
												<li>
													<span>
														<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_LOGOUT_DESC');?>
													</span>
												</li>
											</ol>
										</a>
										<form method="post" action="<?php echo JRoute::_('index.php');?>" data-ed-toolbar-logout-form>
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
				</div>
				<?php } ?>

			<?php } ?>

			<?php if (!$this->my->id && $showLogin) { ?>
				<div class="o-nav__item"
					data-original-title="<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_SIGN_IN');?>"
					data-placement="top"
					data-ed-provide="tooltip"
					data-ed-popbox
					data-ed-popbox-position="<?php echo JFactory::getDocument()->getDirection() == 'rtl' ? 'bottom-left' : 'bottom-right';?>"
					data-ed-popbox-offset="2"
					data-ed-popbox-type="navbar-signin"
					data-ed-popbox-component="popbox--navbar"
					data-ed-popbox-target="[data-ed-toolbar-signin-dropdown]"
					>
					<a href="javascript:void(0);" class="o-nav__link ed-toolbar__link"><i class="fa fa-lock"></i></a>

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
									<?php echo $this->html('form.floatingLabel', $usernameField, 'username'); ?>

									<?php echo $this->html('form.floatingLabel', 'COM_EASYDISCUSS_TOOLBAR_PASSWORD', 'password', 'password'); ?>

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
									<?php if ($this->config->get('integrations_jfbconnect') && ED::jfbconnect()->exists()) { ?>
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

			<div class="o-nav__item ed-toolbar__item--mobile-toggle">
				<a href="javascript:void(0);" class="o-nav__link ed-toolbar__link" data-ed-toolbar-toggle>
					<i class="fa fa-bars"></i>
				</a>
			</div>
		</nav>
	</div>
</div>
<?php } ?>

<?php echo $header; ?>

<?php if ($renderToolbarModule) { ?>
<?php echo ED::renderModule('easydiscuss-after-toolbar'); ?>
<?php } ?>

<?php if($messageObject) { ?>
	<div class="o-alert o-alert--<?php echo $messageObject->type;?>">
		<?php echo $messageObject->message; ?>
	</div>
<?php } ?>
