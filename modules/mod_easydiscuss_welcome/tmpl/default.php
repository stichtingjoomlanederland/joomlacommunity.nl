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
$config 	= DiscussHelper::getConfig();
?>
<div id="discuss-welcome" class="discuss-mod discuss-welcome<?php echo $params->get( 'moduleclass_sfx' ) ?>">
<?php if( $isLoggedIn ){ ?>
<ul class="ed-user-options unstyled">
	<li class="discuss-profile">
		<div class="discuss-profile-wrapper">
			<?php if( $params->get( 'display_avatar')){?>
				<a class="avatar" href="<?php echo $profile->getLink();?>">
					<img width="40" src="<?php echo $profile->getAvatar();?>" class="avatar">
				</a>
			<?php } ?>
			<div class="user-title">
				<a class="fsl fwb" href="<?php echo $profile->getLink();?>"><?php echo $profile->getName();?></a>
			</div>
			<?php if( $params->get( 'show_ranks' , true ) ){ ?>
			<div class="small">( <?php echo $ranking; ?> )</div>
			<?php } ?>

		</div>
	</li>
	<?php if( $params->get( 'show_badges' , 1 ) ){ ?>
	<li class="user-badges">
		<div class="user-badges-heading"><?php echo JText::_( 'MOD_EASYDISCUSS_WELCOME_YOUR_BADGES' ); ?></div>
		<?php if( $badges ){ ?>
			<?php foreach( $badges as $badge ){ ?>
			<span>
				<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=badges&layout=listings&id=' . $badge->id . $menuURL );?>">
				<img src="<?php echo $badge->getAvatar();?>" width="22" title="<?php DiscussHelper::getHelper( 'String' )->escape( $badge->title );?>" />
				</a>
			</span>
			<?php }?>
		<?php } else { ?>
		<div class="small">
			<?php echo JText::_( 'MOD_EASYDISCUSS_WELCOME_NO_BADGES_YET' ); ?>
		</div>
		<?php } ?>
	</li>
	<?php } ?>

	<?php if( !$config->get( 'layout_avatarLinking' ) || $config->get( 'layout_avatarIntegration') == 'default' || $config->get( 'layout_avatarIntegration') == 'gravatar' ){ ?>
	<li>
		<a class="edit-profile" href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=profile&layout=edit' . $menuURL );?>"><span><?php echo JText::_( 'MOD_EASYDISCUSS_WELCOME_EDIT_PROFILE' );?></span></a>
	</li>
	<?php } ?>

	<?php if( $params->get( 'show_favourites' , 1 ) ){ ?>
		<li>
			<a class="my-favourites" href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=favourites' . $menuURL ) ?>"><span><?php echo JText::_( 'MOD_EASYDISCUSS_WELCOME_VIEW_FAVOURITE' );?></span></a>
		</li>
	<?php } ?>

	<?php if( $params->get( 'show_subscriptions' , 1 ) ){ ?>
		<li>
			<a class="my-subscriptions" href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=profile' . $menuURL . '#Subscriptions' ) ?>"><span><?php echo JText::_( 'MOD_EASYDISCUSS_WELCOME_VIEW_SUBSCRIPTIONS' );?></span></a>
		</li>
	<?php } ?>

	<?php if( $params->get( 'show_assigned_posts' , 1 ) ){ ?>
		<li>
			<a class="my-assigned" href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=assigned' . $menuURL ) ?>"><span><?php echo JText::_( 'MOD_EASYDISCUSS_WELCOME_VIEW_ASSIGNED_POST' );?></span></a>
		</li>
	<?php } ?>

	<?php if( $params->get( 'show_my_discussions' , 1 ) ){ ?>
		<li>
			<a class="user-discussions" href="<?php echo $profile->getLink(); ?>"><span><?php echo JText::_( 'MOD_EASYDISCUSS_WELCOME_MY_DISCUSSIONS' );?></span></a>
		</li>
	<?php } ?>

	<?php if( $params->get( 'show_browse_discussions' , 1 ) ){ ?>
		<li>
			<a class="all-discussions" href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss' . $menuURL );?>"><span><?php echo JText::_( 'MOD_EASYDISCUSS_WELCOME_BROWSE_DISCUSSIONS' );?></span></a>
		</li>
	<?php } ?>

	<?php if( $params->get( 'show_browse_categories' , 1 ) ){ ?>
		<li>
			<a class="discuss-categories" href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=categories' . $menuURL);?>"><span><?php echo JText::_( 'MOD_EASYDISCUSS_WELCOME_BROWSE_CATEGORIES' );?></span></a>
		</li>
	<?php } ?>

	<?php if( $params->get( 'show_browse_tags' , 1 ) ){ ?>
		<li>
			<i class="ico"></i><a class="discuss-tags" href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=tags' . $menuURL );?>"><span><?php echo JText::_( 'MOD_EASYDISCUSS_WELCOME_BROWSE_TAGS' );?></span></a>
		</li>
	<?php } ?>

	<?php if( $params->get( 'show_browse_badges' , 1 ) ){ ?>
		<li>
			<i class="ico"></i><a class="discuss-badges" href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=badges' . $menuURL );?>"><span><?php echo JText::_( 'MOD_EASYDISCUSS_WELCOME_BROWSE_BADGES' );?></span></a>
		</li>
	<?php } ?>

	<li>
		<i class="ico"></i><a class="discuss-logout" href="<?php echo JRoute::_('index.php?option='.$userComponent->option.'&task='.$userComponent->logout. '&' . DiscussHelper::getToken() . '=1&return='.$return);?>"><span><?php echo JText::_( 'MOD_EASYDISCUSS_WELCOME_SIGN_OUT' );?></span></a>
	</li>
</ul>
<?php } else if( $params->get( 'enable_login') ) { ?>
<form action="<?php echo JRoute::_( 'index.php', true, $params->get('usesecure')); ?>" method="post" name="login" id="form-login" >
	<?php echo $params->get('pretext'); ?>
	<div class="row-fluid">
		<ul class="list-form unstyled">
			<li class="prm">
				<label for="discuss-welcome-username" class="input-label"><?php echo JText::_( 'MOD_EASYDISCUSS_WELCOME_USERNAME' ); ?></label>
				<input type="text" id="discuss-welcome-username" name="username" class="input" size="18">
			</li>
			<li class="prm">
				<label for="discuss-welcome-password" class="input-label"><?php echo JText::_( 'MOD_EASYDISCUSS_WELCOME_PASSWORD' ); ?></label>
				<input type="password" id="discuss-welcome-password" name="<?php echo $userComponent->password; ?>" class="input" size="18" >
			</li>
			<?php if(JPluginHelper::isEnabled('system', 'remember')){ ?>
			<li class="form-inline">
				<input type="checkbox" id="modlgn_remember" name="remember" value="yes" title="<?php echo JText::_( 'MOD_EASYDISCUSS_WELCOME_REMEMBER_ME' );?>" alt="<?php echo JText::_( 'MOD_EASYDISCUSS_WELCOME_REMEMBER_ME' );?>">
				<label for="modlgn_remember"><?php echo JText::_( 'MOD_EASYDISCUSS_WELCOME_REMEMBER_ME' );?></label>
			</li>
			<?php } ?>
			<li>
				<input type="submit" value="<?php echo JText::_( 'MOD_EASYDISCUSS_WELCOME_SIGN_IN' );?>" name="Submit" class="btn btn-primary">
			</li>
		</ul>
	</div>
	<div class="row-fluid account-register">
		<span><?php echo JText::_( 'MOD_EASYDISCUSS_WELCOME_FORGOT_YOUR' );?></span>
		<a href="<?php echo JRoute::_( 'index.php?option='.$userComponent->option.'&view=reset' ); ?>"><?php echo JText::_( 'MOD_EASYDISCUSS_WELCOME_PASSWORD' );?></a> <b><?php echo JText::_( 'MOD_EASYDISCUSS_WELCOME_OR' );?></b>
		<a href="<?php echo JRoute::_( 'index.php?option='.$userComponent->option.'&view=remind' ); ?>"><?php echo JText::_( 'MOD_EASYDISCUSS_WELCOME_USERNAME' );?></a>?<br>
		<?php if( $userComponent->allowRegister ){ ?>
			<a href="<?php echo JRoute::_( 'index.php?option='.$userComponent->option.'&view='.$userComponent->register ); ?>"><?php echo JText::_( 'MOD_EASYDISCUSS_WELCOME_CREATE_ACCOUNT' );?></a>
		<?php } ?>
	</div>
	<?php echo $params->get('posttext'); ?>
	<input type="hidden" name="option" value="<?php echo $userComponent->option; ?>" />
	<input type="hidden" name="task" value="<?php echo $userComponent->login; ?>" />
	<input type="hidden" name="return" value="<?php echo $return; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
<?php } ?>
</div>
