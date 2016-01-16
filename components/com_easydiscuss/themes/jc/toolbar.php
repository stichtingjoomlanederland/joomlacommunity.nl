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
.script( 'toolbar', 'legacy' )
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
	$( '#discuss-nav' ).implement( EasyDiscuss.Controller.Toolbar,
	{
		'{loginLink}': null,
		'{profileLink}': null
	});

	$('.btn-navbar').click(function() {
		$('.nav-collapse').toggleClass("collapse in",250); //transition effect required jQueryUI
		return false;
	});
});
</script>
<?php echo DiscussHelper::renderModule( 'easydiscuss-before-header' ); ?>
<?php if( $system->config->get('layout_headers') || $system->config->get( 'main_rss' ) || $system->config->get( 'main_sitesubscription' ) ){ ?>
<div id="discuss-head">
	<?php if( $system->config->get( 'layout_headers' ) ){ ?>
	<h1 class="discuss-site-title"><?php echo $headers->title; ?></h1>
	<p><?php echo $headers->desc;?></p>
	<?php } ?>

	<?php if( $system->config->get( 'main_rss' ) || $system->config->get( 'main_sitesubscription' ) ){ ?>
		<?php echo $this->loadTemplate( 'toolbar.subscription.email.php' ); ?>
		<?php if( $system->config->get( 'main_rss') ){ ?>
			<a href="<?php echo DiscussHelper::getHelper( 'Feeds' )->getFeedURL( 'index.php?option=com_easydiscuss&view=index' );?>" class="butt butt-default">
				<i class="i i-rss muted"></i>
				&nbsp;
				<?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBE_VIA_RSS'); ?>
			</a>
		<?php } ?>
	<?php } ?>
</div>
<?php } ?>

<?php echo DiscussHelper::renderModule( 'easydiscuss-after-header' ); ?>

<?php echo DiscussHelper::renderModule( 'easydiscuss-before-toolbar' ); ?>
<?php if( $system->config->get( 'layout_enabletoolbar' ) ){ ?>
<div id="discuss-nav">
	<ul class="discuss-menu reset-ul float-li clearfix">
		<li class="dropdown_">
			<a data-foundry-toggle="dropdown" class="dropdown-toggle_" href="javascript:void(0);">
				<i class="i i-reorder"></i>
				Navigation
			</a>
			<ul class="reset-ul nav-drop">
				<?php if($config->get('layout_toolbardiscussion', 1)){ ?>
				<li class="toolbarItem<?php echo $views->index;?>">
					<a href="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&view=index'); ?>">
						<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_HOME'); ?>
					</a>
				</li>
				<?php } ?>

				<?php if( $config->get( 'layout_toolbarcategories' , 1) ){ ?>
				<li class="toolbarItem<?php echo $views->categories;?>">
					<a href="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&view=categories'); ?>">
						<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_CATEGORIES'); ?>
					</a>
				</li>
				<?php } ?>

				<?php if( $config->get( 'main_master_tags' ) ){ ?>
				<?php if($config->get('layout_toolbartags', 1)){ ?>
				<li class="toolbarItem<?php echo $views->tags;?>">
					<a href="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&view=tags'); ?>">
						<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_TAGS'); ?>
					</a>
				</li>
				<?php } ?>
				<?php } ?>

				<?php if($config->get('layout_toolbarusers', 1)){ ?>
				<li class="toolbarItem<?php echo $views->users;?>">
					<a href="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&view=users'); ?>">
						<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_USERS'); ?>
					</a>
				</li>
				<?php } ?>

				<?php if( $config->get( 'layout_toolbarbadges' ) && $config->get( 'main_badges' ) ){ ?>
				<li class="toolbarItem<?php echo $views->badges;?>">
					<a href="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&view=badges'); ?>">
						<?php echo JText::_('COM_EASYDISCUSS_TOOLBAR_BADGES'); ?>
					</a>
				</li>
				<?php } ?>				
			</ul>
		</li>

		<?php if( $system->profile->id <= 0 && $system->config->get( 'layout_toolbarlogin' ) ){ ?>
		<li class="dropdown_ float-r">
			<a data-foundry-toggle="dropdown" class="dropdown-toggle_" href="javascript:void(0);">
				<i class="i i-lock"></i>
			</a>
			<div class="nav-drop nav-login">
				<form method="post" action="<?php echo JRoute::_( 'index.php' );?>">
					<div class="form-group">
						<label for="nav-login-username" >
							<a tabindex="105" class="float-r" href="<?php echo DiscussHelper::getRegistrationLink();?>"><?php echo JText::_( 'COM_EASYDISCUSS_REGISTER' );?></a>
							<?php echo JText::_( 'COM_EASYDISCUSS_USERNAME' );?>
						</label>
						<input type="text" tabindex="101" id="nav-login-username" name="username" class="form-control" size="18" autocomplete="off" />
					</div>
					<div class="form-group">
						<label for="nav-login-password">
							<a tabindex="106" class="float-r" href="<?php echo DiscussHelper::getResetPasswordLink();?>"><?php echo JText::_( 'COM_EASYDISCUSS_FORGOT_PASSWORD' );?></a>
							<?php echo JText::_( 'COM_EASYDISCUSS_PASSWORD' );?>
						</label>
						<?php if( DiscussHelper::getJoomlaVersion() >= '1.6' ){ ?>
						<input type="password" tabindex="102" id="nav-login-password" class="form-control" name="password" autocomplete="off" />
						<?php } else { ?>
						<input type="password" tabindex="102" id="nav-login-password" class="form-control" name="passwd" autocomplete="off" />
						<?php } ?>
					</div>
					<div class="form-group">
						<div class="checkbox float-l">
							<label>
								<input type="checkbox" tabindex="103" id="remember" name="remember" value="yes" />
								<?php echo JText::_( 'COM_EASYDISCUSS_REMEMBER_ME' );?>
							</label>
						</div>
						<input type="submit" tabindex="104" value="<?php echo JText::_( 'COM_EASYDISCUSS_LOGIN' , true);?>" name="Submit" class="butt butt-primary float-r" />
					</div>
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
		
		<?php if( $system->profile->id > 0 && $system->config->get( 'layout_toolbarprofile' ) ){ ?>
		<li class="dropdown_ float-r">
			<a data-foundry-toggle="dropdown" class="dropdown-toggle_" href="javascript:void(0);">
				<i class="i i-cog"></i>
			</a>
			<ul class="nav-drop reset-ul">
				<li>
					<div class="nav-user">
						<?php if( $system->config->get( 'layout_avatar' ) ) { ?>
						<a href="<?php echo $system->profile->getLink();?>" class="nav-avatar float-l">
							<img class="avatar" alt="<?php echo $this->escape( $system->profile->getName() );?>" src="<?php echo $system->profile->getAvatar();?>" width="60" height="60" />
						</a>
						<?php } ?>
						<div>
							<a href="<?php echo $system->profile->getLink(); ?>">
								<b><?php echo $system->profile->getName();?></b>
							</a>
							<br>
							<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=profile&layout=edit' );?>" class="muted">
								<?php echo JText::_( 'COM_EASYDISCUSS_EDIT_PROFILE' ); ?>
							</a>
						</div>
					</div>
				</li>


				<?php if( $system->config->get( 'main_favorite' ) ){ ?>
				<li>
					<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=favourites' );?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_VIEW_MY_FAVOURITES' );?>
					</a>
				</li>
				<?php } ?>

				<li>
					<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=profile' );?>#Subscriptions">
						<?php echo JText::_( 'COM_EASYDISCUSS_VIEW_MY_SUBSCRIPTIONS' );?>
					</a>
				</li>

				<?php if( DiscussHelper::isModerator() ){ ?>
				<li>
					<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=assigned' );?>">
						<?php echo JText::_( 'COM_EASYDISCUSS_VIEW_MY_TICKETS' );?>
					</a>
				</li>
				<?php } ?>

				<?php if( $config->get( 'main_badges' ) ){ ?>
				<li>
					<div class="nav-badges">
						<b><?php echo JText::_( 'COM_EASYDISCUSS_MYBADGES' );?>: </b>
						<?php $badges = $system->profile->getBadges(); ?>
						<?php if( $badges ){ ?>
						<div>
							<?php foreach( $badges as $badge ){ ?>
								<a href="<?php echo DiscussRouter::getBadgeRoute( $badge->id );?>"><img src="<?php echo $badge->getAvatar();?>" width="40" rel="ed-tooltip" data-placement="top" data-original-title="<?php echo JText::_( $badge->title , true );?>" /></a>
							<?php } ?>
						</div>
						<?php } else { ?>
						<div><?php echo JText::_( 'COM_EASYDISCUSS_NO_BADGES_YET' ); ?></div>
						<?php } ?>
					</div>
				</li>
				<?php } ?>

				<li>
					<form id="logoutForm" action="<?php echo JRoute::_( 'index.php' );?>" style="padding: 15px;">
						<?php if( DiscussHelper::getJoomlaVersion() >= '1.6' ){ ?>
						<input type="hidden" value="com_users"  name="option">
						<input type="hidden" value="user.logout" name="task">
						<?php } else { ?>
						<input type="hidden" value="com_user"  name="option">
						<input type="hidden" value="logout" name="task">
						<?php } ?>
						<input type="hidden" value="<?php echo base64_encode( JRequest::getURI() ); ?>" name="return" />
						<?php echo JHTML::_( 'form.token' ); ?>

						<button class="butt butt-primary butt-block logoutButton"><?php echo JText::_( 'COM_EASYDISCUSS_LOGOUT' ); ?></button>
					</form>
				</li>
			</ul>
		</li>
		<?php } ?>

		<?php if( $system->my->id && $system->config->get( 'main_conversations' ) ){ ?>
			<?php echo $this->loadTemplate( 'toolbar.conversation.php' , array( 'totalMessages' , $totalMessages ) ); ?>
		<?php } ?>

		<?php if( $system->my->id && $system->config->get( 'main_notifications') ){ ?>
			<?php echo $this->loadTemplate( 'toolbar.notification.php' , array( 'totalNotifications' , $totalNotifications ) ); ?>
		<?php } ?>
	</ul>
</div>
<?php } ?>
<?php echo DiscussHelper::renderModule( 'easydiscuss-after-toolbar' ); ?>


<?php echo $this->loadTemplate( 'searchbar.php'); ?>


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
