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
<script type="text/javascript">
EasyDiscuss
.require()
.script( 'toolbar' )
.done(function($){

	<?php if( $system->my->id > 0 && $system->config->get( 'main_conversations') && $system->config->get( 'main_conversations_notification' ) ){ ?>
	discuss.conversation.interval = <?php echo $system->config->get( 'main_conversations_notification_interval' ) * 1000 ?>;
	discuss.conversation.startMonitor();
	<?php } ?>

	<?php if( $system->my->id > 0 && $system->config->get( 'main_notifications' ) ){ ?>
	discuss.notifications.interval = <?php echo $system->config->get( 'main_notifications_interval' ) * 1000 ?>;
	discuss.notifications.startMonitor();
	<?php } ?>

	// Implement toolbar controller.
	$( '.discuss-toolbar' ).implement( EasyDiscuss.Controller.Toolbar );

	<?php if( $system->config->get( 'main_responsive' ) ){ ?>

	$.responsive($('.discuss-toolbar'), {
		elementWidth: function() {
			return $('.discuss-toolbar').outerWidth(true) - 80;

		},
		conditions: {
			at: (function() {
				var listWidth = 0;

				$('.discuss-toolbar .nav > li').each(function(i, element) {
					listWidth += $(element).outerWidth(true);
				});
				return listWidth;

			})(),
			alsoSwitch: {
				'.discuss-toolbar' : 'narrow'
			},
			targetFunction: function() {
				$('.discuss-toolbar').removeClass('wide');
			},
			reverseFunction: function() {
				$('.discuss-toolbar').addClass('wide');
			}
		}

	});

	<?php } ?>

	$('.btn-navbar').click(function() {
		$('.ed-nav-collapse').toggleClass("collapse in",250); //transition effect required jQueryUI
		return false;
	});
});
</script>
<?php echo DiscussHelper::renderModule( 'easydiscuss-before-header' ); ?>
<div class="discuss-head">

	<?php if( $system->config->get('layout_headers') || $system->config->get( 'main_rss' ) || $system->config->get( 'main_sitesubscription' ) ){ ?>
	<div class="row-fluid mb-20">
		<div class="discuss-site-headers pull-left">
			<h1 class="discuss-site-title"><?php echo $headers->title; ?></h1>
			<p><?php echo $headers->desc;?></p>
		</div>

		<?php if( $system->config->get( 'main_rss' ) || $system->config->get( 'main_sitesubscription' ) ){ ?>
		<div class="discuss-subscribe pull-right mt-20">
			<div class="pull-left">

				<div class="pull-left mr-10">
				<?php echo $this->loadTemplate( 'toolbar.subscription.email.php' ); ?>
				</div>

				<?php if( $system->config->get( 'main_rss') ){ ?>
				<span class="pull-left">
					<a href="<?php echo DiscussHelper::getHelper( 'Feeds' )->getFeedURL( 'index.php?option=com_easydiscuss&view=index' );?>">
						<i class="icon-ed-rss"></i>
						<?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_VIA_RSS'); ?>
					</a>
				</span>
				<?php } ?>

			</div>
		</div>
		<?php } ?>
	</div>
	<?php } ?>

	<?php echo $this->loadTemplate( 'searchbar.php'); ?>

</div>
<?php echo DiscussHelper::renderModule( 'easydiscuss-after-header' ); ?>

<?php echo DiscussHelper::renderModule( 'easydiscuss-before-toolbar' ); ?>
<?php if( $system->config->get( 'layout_enabletoolbar' ) ){ ?>
<div class="navbar navbar-inverse discuss-toolbar">
	<div class="navbar-inner">
		<a class="btn btn-navbar" href="javascript:void(0);">
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</a>

		<?php if($config->get('layout_toolbardiscussion', 1)){ ?>
		<a href="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&view=index'); ?>" class="brand visible-phone"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_HOME'); ?></a>
		<?php } ?>

		<div class="ed-nav-collapse nav-collapse collapse">
			<ul class="nav">

				<?php if($config->get('layout_toolbardiscussion', 1)){ ?>
				<li class="toolbarItem<?php echo $views->index;?>">
					<a href="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&view=index'); ?>" rel="ed-tooltip" data-placement="top"
						data-original-title="<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_HOME' , true ); ?>"
						data-content="<?php echo JText::_( 'COM_EASYDISCUSS_TOOLBAR_TIPS_DISCUSSIONS_DESC' , true );?>">
						<i class="icon-ed-tb-home"></i> <span class="visible-phone"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_HOME'); ?></span>
					</a>
				</li>
				<li class="divider-vertical"></li>
				<?php } ?>

				<?php if( $config->get( 'layout_toolbarcategories' , 1) ){ ?>
				<li class="toolbarItem<?php echo $views->categories;?>">
					<a href="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&view=categories'); ?>" rel="ed-tooltip" data-placement="top"
						data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_TOOLBAR_TIPS_CATEGORIES' , true );?>"
						data-content="<?php echo JText::_( 'COM_EASYDISCUSS_TOOLBAR_TIPS_CATEGORIES_DESC' , true );?>">
						<i class="icon-ed-tb-categories"></i> <span class="visible-phone"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_CATEGORIES'); ?></span>
					</a>
				</li>
				<li class="divider-vertical"></li>
				<?php } ?>

				<?php if( $system->config->get( 'main_master_tags' ) ){ ?>
					<?php if($config->get('layout_toolbartags', 1)){ ?>
					<li class="toolbarItem<?php echo $views->tags;?>">
						<a href="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&view=tags'); ?>" rel="ed-tooltip" data-placement="top"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_TOOLBAR_TIPS_TAGS' , true );?>"
							data-content="<?php echo JText::_( 'COM_EASYDISCUSS_TOOLBAR_TIPS_TAGS_DESC' , true );?>">
							<i class="icon-ed-tb-tags"></i> <span class="visible-phone"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_TAGS'); ?></span>
						</a>
					</li>
					<li class="divider-vertical"></li>
					<?php } ?>
				<?php } ?>

				<?php if($config->get('layout_toolbarusers', 1)){ ?>
				<li class="toolbarItem<?php echo $views->users;?>">
					<a href="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&view=users'); ?>" rel="ed-tooltip" data-placement="top"
						data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_TOOLBAR_TIPS_MEMBERS' , true );?>"
						data-content="<?php echo JText::_( 'COM_EASYDISCUSS_TOOLBAR_TIPS_MEMBERS_DESC' , true );?>">
						<i class="icon-ed-tb-members"></i> <span class="visible-phone"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_USERS'); ?></span>
					</a>
				</li>
				<li class="divider-vertical"></li>
				<?php } ?>

				<?php if( $config->get( 'layout_toolbarbadges' ) && $config->get( 'main_badges' ) ){ ?>
				<li class="toolbarItem<?php echo $views->badges;?>">
					<a href="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&view=badges'); ?>" rel="ed-tooltip" data-placement="top"
						data-original-title="<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_TIPS_BADGES' , true ); ?>"
						data-content="<?php echo JText::_( 'COM_EASYDISCUSS_TOOLBAR_TIPS_BADGES_DESC' , true );?>">
						<i class="icon-ed-tb-badges"></i> <span class="visible-phone"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_BADGES'); ?></span>
					</a>
				</li>
				<li class="divider-vertical"></li>
				<?php } ?>
			</ul>

			<ul class="nav pull-right">

				<?php if( $system->my->id && $system->config->get( 'main_conversations' ) ){ ?>
				<li class="divider-vertical"></li>
					<?php echo $this->loadTemplate( 'toolbar.conversation.php' , array( 'totalMessages' , $totalMessages ) ); ?>
				<?php } ?>

				<?php if( $system->my->id && $system->config->get( 'main_notifications') ){ ?>
				<li class="divider-vertical"></li>
				<?php echo $this->loadTemplate( 'toolbar.notification.php' , array( 'totalNotifications' , $totalNotifications ) ); ?>
				<?php } ?>

				<?php if( $system->profile->id > 0 && $system->config->get( 'layout_toolbarprofile' ) ){ ?>
				<li class="divider-vertical"></li>
				<li class="dropdown_">
					<a class="dropdown-toggle_ profileLink">
						<i class="icon-ed-tb-user"></i>
						<span class="visible-phone"><?php echo $system->profile->getName();?></span> <b class="caret"></b>
					</a>
					<ul class="dropdown-menu dropdown-menu-large profileDropDown">
						<li>
							<div class="discuss-user-menu">
								<div class="modal-header">
									<h5><?php echo $system->profile->getName();?></h5>
								</div>
								<div class="modal-body">
									<div class="row-fluid">
										<?php if( $system->config->get( 'layout_avatar' ) ) { ?>
										<div class="span3">
											<div class="discuss-avatar avatar-medium">
											<a href="<?php echo DiscussRouter::getUserRoute( $system->profile->id );?>">
												<img class="thumbnail" alt="<?php echo $this->escape( $system->profile->getName() );?>" src="<?php echo $system->profile->getAvatar();?>" />
											</a>
											</div>
										</div>
										<?php } ?>
										<div class="span9">
											<ul class="unstyled discuss-user-links">
												<li>
													<a href="<?php echo DiscussRouter::getUserRoute( $system->profile->id );?>"><i class="icon-user"></i> <?php echo JText::_( 'COM_EASYDISCUSS_VIEW_MY_PROFILE' );?></a>
												</li>
												<li>
													<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=profile&layout=edit' );?>">
														<i class="icon-cog"></i> <?php echo JText::_( 'COM_EASYDISCUSS_EDIT_PROFILE' ); ?>
													</a>
												</li>
												<?php if( $system->config->get( 'main_favorite' ) ){ ?>
												<li>
													<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=favourites' );?>">
														<i class="icon-heart"></i> <?php echo JText::_( 'COM_EASYDISCUSS_VIEW_MY_FAVOURITES' );?>
													</a>
												</li>
												<?php } ?>

												<li>
													<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=profile' );?>#Subscriptions">
														<i class="icon-inbox"></i> <?php echo JText::_( 'COM_EASYDISCUSS_VIEW_MY_SUBSCRIPTIONS' );?>
													</a>
												</li>
												<?php if( DiscussHelper::isModerator() ){ ?>
												<li>
													<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=assigned' );?>">
														<i class="icon-check"></i> <?php echo JText::_( 'COM_EASYDISCUSS_VIEW_MY_TICKETS' );?>
													</a>
												</li>
												<?php } ?>
											</ul>

										</div>
									</div>
									<?php if( $config->get( 'main_badges' ) ){ ?>
									<div class="row-fluid">
										<h5><?php echo JText::_( 'COM_EASYDISCUSS_MYBADGES' );?>: </h5>

										<?php $badges = $system->profile->getBadges(); ?>

										<?php if( $badges ){ ?>
										<ul class="unstyled badges-list">
											<?php foreach( $badges as $badge ){ ?>
											<li class="pull-left mr-10">
												<a href="<?php echo DiscussRouter::getBadgeRoute( $badge->id );?>"><img src="<?php echo $badge->getAvatar();?>" width="32" rel="ed-tooltip" data-placement="top" data-original-title="<?php echo JText::_( $badge->title , true );?>" /></a>
											</li>
											<?php } ?>
										</ul>
										<?php } else { ?>
										<div><?php echo JText::_( 'COM_EASYDISCUSS_NO_BADGES_YET' ); ?></div>
										<?php } ?>
									</div>
									<?php } ?>
								</div>
								<div class="modal-footer">
									<form id="logoutForm" action="<?php echo JRoute::_( 'index.php' );?>" style="display: none;">
										<?php if( DiscussHelper::getJoomlaVersion() >= '1.6' ){ ?>
										<input type="hidden" value="com_users"  name="option">
										<input type="hidden" value="user.logout" name="task">
										<?php } else { ?>
										<input type="hidden" value="com_user"  name="option">
										<input type="hidden" value="logout" name="task">
										<?php } ?>
										<input type="hidden" value="<?php echo base64_encode( JRequest::getURI() ); ?>" name="return" />
										<?php echo JHTML::_( 'form.token' ); ?>
									</form>
									<button class="btn btn-primary logoutButton"><i class="icon-off"></i> <?php echo JText::_( 'COM_EASYDISCUSS_LOGOUT' ); ?></button>
								</div>
							</div>

						</li>
					</ul>
				</li>
				<?php } ?>

				<?php if( $system->profile->id <= 0 && $system->config->get( 'layout_toolbarlogin' ) ){ ?>
				<li class="divider-vertical"></li>
					<li class="dropdown_">
						<a class="dropdown-toggle_ loginLink" href="javascript:void(0);">
							<i class="icon-ed-tb-locked"></i>
							<span class="visible-phone"><?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_LOGIN'); ?></span> <b class="caret"></b>
						</a>
						<div class="dropdown-menu dropdown-menu-medium loginDropDown">
							<form method="post" action="<?php echo JRoute::_( 'index.php' );?>">
								<ul class="discuss-login-menu unstyled">
									<li>
										<a tabindex="105" class="pull-right" href="<?php echo DiscussHelper::getRegistrationLink();?>"><?php echo JText::_( 'COM_EASYDISCUSS_REGISTER' );?></a>
										<label for="username" ><?php echo JText::_( 'COM_EASYDISCUSS_USERNAME' );?></label>
										<input type="text" tabindex="101" id="username" name="username" class="input full-width" size="18" autocomplete="off" />
									</li>
									<li>
										<a tabindex="106" class="pull-right" href="<?php echo DiscussHelper::getResetPasswordLink();?>"><?php echo JText::_( 'COM_EASYDISCUSS_FORGOT_PASSWORD' );?></a>
										<label for="discuss-toolbar-password"><?php echo JText::_( 'COM_EASYDISCUSS_PASSWORD' );?></label>

										<?php if( DiscussHelper::getJoomlaVersion() >= '1.6' ){ ?>
											<input type="password" tabindex="102" id="discuss-toolbar-password" class="input full-width" name="password" autocomplete="off" />
										<?php } else { ?>
											<input type="password" tabindex="102" id="discuss-toolbar-password" class="input full-width" name="passwd" autocomplete="off" />
										<?php } ?>
									</li>
									<li>
										<span class="pull-left">
											<input type="checkbox" tabindex="103" id="remember" name="remember" class="checkbox pull-left mr-5" value="yes" />
											<label for="remember" class="pull-left"><?php echo JText::_( 'COM_EASYDISCUSS_REMEMBER_ME' );?></label>
										</span>
										<input type="submit" tabindex="104" value="<?php echo JText::_( 'COM_EASYDISCUSS_LOGIN' , true);?>" name="Submit" class="btn btn-primary pull-right" />
									</li>
								</ul>
								<?php if( DiscussHelper::getJoomlaVersion() >= '1.6' ){ ?>
								<input type="hidden" value="com_users"  name="option">
								<input type="hidden" value="user.login" name="task">
								<input type="hidden" name="return" value="<?php echo $return; ?>" />
								<?php } else { ?>
								<input type="hidden" value="com_user"  name="option">
								<input type="hidden" value="login" name="task">
								<input type="hidden" name="return" value="<?php echo $return; ?>" />
								<?php } ?>
								<?php echo JHTML::_( 'form.token' ); ?>
							</form>
						</div>
					</li>
				<?php } ?>
			</ul>
		</div>

	</div>
</div>
<?php } ?>
<?php echo DiscussHelper::renderModule( 'easydiscuss-after-toolbar' ); ?>

<?php if( $system->config->get( 'layout_category_tree' ) ){ ?>
	<?php if( $views->current == 'index' ){ ?>
		<?php echo $this->loadTemplate( 'categories.front.php'); ?>
	<?php } ?>
<?php } ?>

<?php if( $messageObject ){ ?>
	<div class="alert alert-<?php echo $messageObject->type;?>">
		<?php echo $messageObject->message; ?>
	</div>
<?php } ?>
