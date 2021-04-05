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
<?php if ($renderToolbarModule) { ?>
	<?php echo ED::renderModule('easydiscuss-before-header'); ?>
<?php } ?>

<?php if ($renderToolbarModule) { ?>
<?php echo ED::renderModule('easydiscuss-after-header'); ?>

<?php echo ED::renderModule('easydiscuss-before-toolbar'); ?>
<?php } ?>

<?php if ($showToolbar) { ?>
<div class="ed-toolbar t-mb--lg" data-ed-toolbar>
	<?php if ($showHome) { ?>
	<div class="ed-toolbar__item ed-toolbar__item--home">
		<nav class="o-nav ed-toolbar__o-nav">
			<div class="o-nav__item <?php echo $active == 'index' ? ' is-active' : '';?>">
				<a href="<?php echo EDR::_('view=index');?>" class="o-nav__link ed-toolbar__link">
					<i class="fa fa-home"></i>
				</a>
			</div>
		</nav>
	</div>
	<?php } ?>

	<?php if ($showCategories || $showTags || $showUsers || $showBadges) { ?>
	<div class="ed-toolbar__item ed-toolbar__item--home-submenu" data-ed-toolbar-menu>
		<div class="o-nav ed-toolbar__o-nav">
			<?php if ($showCategories) { ?>
			<div class="o-nav__item <?php echo $active == 'categories' ? ' is-active' : '';?>">
				<a href="<?php echo EDR::_('view=categories');?>" class="o-nav__link ed-toolbar__link">
					<span>
						<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_CATEGORIES');?>
					</span>
				</a>
			</div>
			<?php } ?>

			<?php if ($showTags) { ?>
			<div class="o-nav__item <?php echo $active == 'tags' ? ' is-active' : '';?>">
				<a href="<?php echo EDR::_('view=tags');?>" class="o-nav__link ed-toolbar__link">
					<span>
						<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_TAGS');?>
					</span>
				</a>
			</div>
			<?php } ?>

			<?php if ($showUsers) { ?>
			<div class="o-nav__item <?php echo $active == 'users' ? ' is-active' : '';?>">
				<a href="<?php echo $userMenuLink;?>" class="o-nav__link ed-toolbar__link">
					<span>
						<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_USERS');?>
					</span>
				</a>
			</div>
			<?php } ?>

			<?php if ($showBadges) { ?>
			<div class="o-nav__item <?php echo $active == 'badges' ? ' is-active' : '';?>">
				<a href="<?php echo EDR::_('view=badges');?>" class="o-nav__link ed-toolbar__link">
					<span>
						<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_BADGES');?>
					</span>
				</a>
			</div>
			<?php } ?>

			<?php if ($this->config->get('main_rss') || $this->config->get('main_sitesubscription')) { ?>
			<div class="o-nav__item"
				data-ed-subscriptions
				data-ed-popbox
				data-ed-popbox-position="bottom-right"
				data-ed-popbox-toggle="click"
				data-ed-popbox-offset="12"
				data-ed-popbox-type="navbar-subscribe"
				data-ed-popbox-component="popbox--navbar"
				data-ed-popbox-cache="0"
				data-ed-popbox-target="[data-ed-popbox-subscribe]"
			>
				<a href="javascript:void(0);" class="o-nav__link ed-toolbar__link">
					<span>
						<?php echo JText::_('COM_ED_SUBSCRIBE');?>
						&nbsp;<i class="fa fa-caret-down"></i>
					</span>
				</a>
			</div>

			<div class="t-d--none" data-ed-popbox-subscribe>
				<div class="popbox-dropdown">
					<?php if ($this->config->get('main_rss')) { ?>
					<div class="popbox-dropdown__bd">
						<a href="<?php echo ED::feeds()->getFeedUrl('view=index');?>" class="t-text--700 l-stack l-spaces--sm">
							<div>
								<b>
									<i class="fa fa-rss-square"></i>&nbsp; <?php echo JText::_('COM_ED_SUBSCRIBE_RSS');?>
								</b>
							</div>
							<div class="t-font-size--01">
								<?php echo JText::_('COM_ED_SUBSCRIBE_RSS_INFO');?>
							</div>
						</a>
					</div>
					<?php } ?>
					
					<?php if ($this->config->get('main_sitesubscription')) { ?>
					<div class="popbox-dropdown__ft">
						<?php if (!$isSubscribed) { ?>
						<a href="javascript:void(0);" class="t-text--700" 
							data-ed-subscribe
							data-type="site"
							data-cid="0"
						>
						<?php } ?>
						<div class="l-stack l-spaces--sm">
							<div>
								<b>
									<i class="fa fa-at"></i>&nbsp; <?php echo JText::_('COM_ED_RECEIVE_EMAIL_UPDATES');?>
								</b>
							</div>

							<?php if ($isSubscribed) { ?>
							<div class="t-font-size--01">
								<?php echo JText::_('COM_ED_TOOLBAR_ALREADY_SUBSCRIBED');?>
							</div>
							<div class="t-d--flex">
								<div>
									<a href="<?php echo EDR::_('index.php?option=com_easydiscuss&view=subscription'); ?>" class="si-link">
										<?php echo JText::_('COM_ED_MANAGE_YOUR_SUBSCRIPTION'); ?>
									</a>
								</div>
							</div>
							<?php } else { ?>
							<div class="t-font-size--01">
								<?php echo JText::_('COM_ED_RECEIVE_EMAIL_UPDATES_INFO');?>
							</div>
							<?php } ?>
						</div>
						<?php if (!$isSubscribed) { ?>
						</a>
						<?php } ?>
					</div>
					<?php } ?>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
	<?php } ?>

	<div class="ed-toolbar__item ed-toolbar__item--search" data-toolbar-search>
		<div id="ed-toolbar-search" class="ed-toolbar__search">
			<form name="discuss-toolbar-search" data-search-toolbar-form method="post" action="<?php echo JRoute::_('index.php'); ?>" class="ed-toolbar__search-form">
				<input type="text" placeholder="<?php echo JText::_('COM_EASYDISCUSS_SEARCH_DEFAULT');?>" autocomplete="off" class="ed-toolbar__search-input" data-search-input name="query" value="<?php echo ED::string()->escape($query); ?>" />
				<input type="hidden" name="option" value="com_easydiscuss" />
				<input type="hidden" name="controller" value="search" />
				<input type="hidden" name="task" value="query" />
				<input type="hidden" name="Itemid" value="<?php echo EDR::getItemId('search'); ?>" />

				<?php echo $this->html('form.token'); ?>

				<div class="ed-toolbar__search-submit-btn">
					<button class="o-btn o-btn--default-o btn-toolbar-search" type="submit">
						<i class="fa fa-search"></i>&nbsp; <?php echo JText::_('COM_EASYDISCUSS_SEARCH');?>
					</button>
				</div>
				<div class="ed-toolbar__search-close-btn">
					<a href="javascript:void(0);" class="" data-ed-toolbar-search-toggle=""><i class="fa fa-times"></i></a>
				</div>

			</form>
		</div>
	</div>

	<div class="ed-toolbar__item ed-toolbar__item--action">
		<nav class="o-nav ed-toolbar__o-nav">

			<?php if ($this->acl->allowed('add_question')) { ?>
			<div class="o-nav__item" 
				data-ed-provide="tooltip"
				data-placement="bottom"
				data-original-title="<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_NEW_DISCUSSION');?>">
				<a href="<?php echo EDR::_('view=ask');?>" class="o-nav__link ed-toolbar__link has-composer">
					<i class="fas fa-pen"></i>
					<span class="ed-toolbar__link-text"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_NEW_DISCUSSION');?></span>
				</a>
			</div>
			<?php } ?>

			<?php if ($showSearch) { ?>
			<div class="o-nav__item" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_SEARCH');?>" 
					data-ed-provide="tooltip"
					data-placement="bottom"
			>
				<a href="javascript:void(0);" class="o-nav__link ed-toolbar__link" data-ed-toolbar-search-toggle><i class="fa fa-search"></i></a>
			</div>
			<?php } ?>

			<?php if ($this->my->id) { ?>
				<?php if ($this->config->get('main_conversations') && $showConversation) { ?>
				<div class="o-nav__item"
					data-ed-conversations-wrapper
					data-ed-popbox="ajax://site/views/conversation/popbox"
					data-ed-popbox-position="bottom-right"
					data-ed-popbox-toggle="click"
					data-ed-popbox-offset="32"
					data-ed-popbox-type="navbar-conversations"
					data-ed-popbox-component="popbox--navbar"
					data-ed-popbox-cache="0"
					data-ed-provide="tooltip"
					data-placement="bottom"
					data-original-title="<?php echo JText::_('COM_EASYDISCUSS_CONVERSATIONS');?>"
					>
					<a href="javascript:void(0);" class="o-nav__link ed-toolbar__link no-active-state <?php echo $conversationsCount ? 'has-new' : '';?>">
						<i class="fa fa-envelope"></i>
						<span class="ed-toolbar__link-text"><?php echo JText::_('COM_EASYDISCUSS_CONVERSATIONS');?></span>
						<span class="ed-toolbar__link-bubble" data-counter=""><?php echo $conversationsCount;?></span>
					</a>
				</div>
				<?php } ?>

				<?php if ($showNotification) { ?>
				<div class="o-nav__item"
					data-ed-popbox="ajax://site/views/notifications/popbox"
					data-ed-popbox-position="bottom-right"
					data-ed-popbox-toggle="click"
					data-ed-popbox-offset="32"
					data-ed-popbox-type="navbar-notifications"
					data-ed-popbox-component="popbox--navbar"
					data-ed-popbox-cache="0"

					data-ed-provide="tooltip"
					data-placement="bottom"
					data-original-title="<?php echo JText::_('COM_EASYDISCUSS_NOTIFICATIONS');?>"
				>
					<a href="javascript:void(0);" class="o-nav__link ed-toolbar__link no-active-state <?php echo $notificationsCount ? 'has-new' : '';?>" data-ed-notifications-wrapper>
						<i class="fa fa-bell"></i>
						<span class="ed-toolbar__link-bubble"></span>
					</a>
				</div>
				<?php } ?>

				<?php if ($showSettings) { ?>
				<div class="o-nav__item is-signin dropdown_">
					<a href="javascript:void(0);" class="o-nav__link ed-toolbar__link has-avatar dropdown-toggle_" data-ed-toggle="dropdown">
						<div class="ed-toolbar__avatar">
							<?php echo $this->html('user.avatar', $this->profile, array(), false, true); ?>
						</div>
					</a>

					<div class="o-dropdown-menu ed-toolbar__dropdown-menu ed-toolbar__dropdown-menu--action dropdown-menu bottom-right
						<?php echo ED::easyblog()->hasToolbar() && ED::easysocial()->hasToolbar($this->my->id) ? 't-w--100' : '';?>
						<?php echo !ED::easyblog()->hasToolbar() && !ED::easysocial()->hasToolbar($this->my->id) ? 't-w--33' : '';?>
						<?php echo ED::easyblog()->hasToolbar() && !ED::easysocial()->hasToolbar($this->my->id) ? 't-w--66' : '';?>
						<?php echo !ED::easyblog()->hasToolbar() && ED::easysocial()->hasToolbar($this->my->id) ? 't-w--66' : '';?>
							"
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

											<div class="ed-user-rank o-label t-ml--sm" style="background-color: <?php echo $this->profile->getRoleLabelColour();?> !important;">
												<?php echo $this->profile->getRole(); ?>
											</div>

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
										</div>
									</div>

									<?php echo ED::badges()->getToolbarHtml();?>
								</div>
							</div>
							<div class="ed-toolbar-profile__bd">
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
										</a>
									</div>

									<div class="ed-toolbar-dropdown-nav__item ">
										<a href="<?php echo $this->profile->getPermalink();?>"  class="ed-toolbar-dropdown-nav__link">
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
												<?php echo $this->profile->getTotalQuestions(); ?>
											</span>
										</a>
									</div>

									<?php if ($this->config->get('main_postassignment') && ED::isModerator()) { ?>
									<div class="ed-toolbar-dropdown-nav__item ">
										<a href="<?php echo EDR::_('view=assigned');?>"  class="ed-toolbar-dropdown-nav__link">
											<div class="ed-toolbar-dropdown-nav__name">
												<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_MY_ASSIGNED_POSTS'); ?>
											</div>

											<span class="ed-toolbar-dropdown-nav__badge">
												<?php echo $this->profile->getTotalAssigned(); ?>
											</span>
										</a>
									</div>
									<?php } ?>

									<?php if ($this->config->get('main_favorite')) { ?>
									<div class="ed-toolbar-dropdown-nav__item ">
										<a href="<?php echo EDR::_('view=favourites');?>"  class="ed-toolbar-dropdown-nav__link">
											<div class="ed-toolbar-dropdown-nav__name">
												<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_MY_FAVOURITES'); ?>
											</div>

											<span class="ed-toolbar-dropdown-nav__badge">
												<?php echo $this->profile->getTotalFavourites(); ?>
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
												<?php echo $this->profile->getTotalSubscriptions(); ?>
											</span>
										</a>
									</div>
									<?php } ?>

									<?php if ($this->acl->allowed('manage_pending') || ED::isSiteAdmin()) { ?>
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
								<a href="javascript:void(0);"  class="ed-toolbar-dropdown-nav__link" data-ed-toolbar-logout>
									<div class="ed-toolbar-dropdown-nav__name">
										<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_LOGOUT'); ?>
									</div>

									<i class="fas fa-sign-out-alt"></i>
								</a>
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
					data-ed-popbox-position="bottom-right"
					data-ed-popbox-offset="32"
					data-ed-popbox-type="navbar-signin"
					data-ed-popbox-component="popbox--navbar"
					data-ed-popbox-target="[data-ed-toolbar-signin-dropdown]"
					>
					<a href="javascript:void(0);" class="o-nav__link ed-toolbar__link">
						<i class="fa fa-user-lock"></i>
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
									<?php echo $this->html('form.floatingLabel', $usernameField, 'username'); ?>
									<?php echo $this->html('form.floatingLabel', 'COM_EASYDISCUSS_TOOLBAR_PASSWORD', 'password', 'password'); ?>

									<?php if (ED::isTwoFactorEnabled()) { ?>
										<?php echo $this->html('form.floatingLabel', 'COM_ED_TOOLBAR_SECRET_KEY', 'secretkey'); ?>
									<?php } ?>

									<div class="t-d--flex t-align-items--c">
										<div class="t-flex-grow--1">
											<div class="o-form-check">
												<input type="checkbox" id="ed-remember" name="remember" class="o-form-check-input" />
												<label for="ed-remember" class="o-form-check-label"><?php echo JText::_('COM_EASYDISCUSS_REMEMBER_ME');?></label>
											</div>
										</div>
										<div class="">
											<button class="o-btn o-btn--primary o-btn--s"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_SIGN_IN');?></button>
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
								<a href="<?php echo ED::getRemindUsernameLink();?>" class="popbox-dropdown__note si-link"><?php echo JText::_('COM_EASYDISCUSS_FORGOT_USERNAME');?></a>
							</div>
							<div class="popbox-dropdown__ft">
								<a href="<?php echo ED::getResetPasswordLink();?>" class="popbox-dropdown__note si-link"><?php echo JText::_('COM_EASYDISCUSS_FORGOT_PASSWORD');?></a>
							</div>
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if ($showNavigationMenu && $this->isMobile() || $this->isTablet()) { ?>
			<div class="o-nav__item ed-toolbar__item--mobile-toggle">
				<a href="#ed-canvas" class="o-nav__link ed-toolbar__link" data-ed-toolbar-toggle>
					<i class="fa fa-bars"></i>
				</a>
			</div>

			<nav id="ed-canvas">
				<ul>
					<li class="mm-divider">
						<?php echo JText::_("COM_ED_NAVIGATION");?>
					</li>
					<li>
						<a href="<?php echo EDR::_('view=index');?>">
							<?php echo JText::_('COM_EASYDISCUSS_RECENT_POSTS');?>
						</a>
					</li>
					<?php if ($showCategories) { ?>
					<li>
						<a href="<?php echo EDR::_('view=categories');?>">
							<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_CATEGORIES');?>
						</a>
					</li>
					<?php } ?>
					<?php if ($showTags) { ?>
					<li>
						<a href="<?php echo EDR::_('view=tags');?>">
							<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_TAGS');?>
						</a>
					</li>
					<?php } ?>
					<?php if ($showUsers) { ?>
					<li>
						<a href="<?php echo $userMenuLink;?>">
							<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_USERS');?>
						</a>
					</li>
					<?php } ?>
					<?php if ($showBadges) { ?>
					<li>
						<a href="<?php echo EDR::_('view=badges');?>">
							<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_BADGES');?>
						</a>
					</li>
					<?php } ?>
					
					<?php if ($this->my->id) { ?>
						<li class="mm-divider">
							<?php echo JText::_("COM_EASYDISCUSS_ACCOUNT");?>
						</li>

						<li>
							<a href="<?php echo $this->profile->getEditProfileLink();?>">
								<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_EDIT_PROFILE'); ?>
							</a>
						</li>

						<li>
							<a href="<?php echo $this->profile->getPermalink();?>">
								<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_MY_PROFILE'); ?>
							</a>
						</li>

						<li class="ed-toolbar-dropdown-nav__item ">
							<a href="<?php echo EDR::_('view=mypost');?>">
								<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_MY_POSTS'); ?>
							</a>
						</li>

						<?php if (ED::isModerator()) { ?>
							<li>
								<a href="<?php echo EDR::_('view=assigned');?>"  class="ed-toolbar-dropdown-nav__link">
									<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_MY_ASSIGNED_POSTS'); ?>
								</a>
							</li>
						<?php } ?>

						<?php if ($this->config->get('main_favorite')) { ?>
							<li>
								<a href="<?php echo EDR::_('view=favourites');?>">
									<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_MY_FAVOURITES'); ?>
								</a>
							</li>
						<?php } ?>

						<?php if ($showManageSubscription) { ?>
							<li>
								<a href="<?php echo EDR::_('view=subscription');?>">
									<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_MY_SUBSCRIPTION'); ?>
								</a>
							</li>
						<?php } ?>

						<?php if ($this->acl->allowed('manage_pending') || ED::isSiteAdmin()) { ?>
							<li>
								<a href="<?php echo EDR::_('view=dashboard');?>">
									<?php echo JText::_('COM_ED_MANAGE_SITE');?>
								</a>
							</li>
						<?php } ?>

						<li>
							<a href="javascript:void(0);" data-ed-toolbar-logout>
								<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_LOGOUT');?>
							</a>
						</li>

						<?php if (ED::easyblog()->hasToolbar()) { ?>
							<?php echo ED::easyblog()->getToolbarDropdown(); ?>
						<?php } ?>

						<?php if (ED::easysocial()->hasToolbar()) { ?>
							<?php echo ED::easysocial()->getToolbarDropdown(); ?>
						<?php } ?>
					<?php } ?>
				</ul>
			</nav>
			<?php } ?>
		</nav>
	</div>
</div>

<form method="post" action="<?php echo JRoute::_('index.php');?>" data-ed-toolbar-logout-form>
	<input type="hidden" value="com_users"  name="option">
	<input type="hidden" value="user.logout" name="task">
	<input type="hidden" name="<?php echo ED::getToken();?>" value="1" />
	<input type="hidden" value="<?php echo EDR::getLogoutRedirect(); ?>" name="return" />
</form>
<?php } ?>

<?php if ($renderToolbarModule) { ?>
<?php echo ED::renderModule('easydiscuss-after-toolbar'); ?>
<?php } ?>