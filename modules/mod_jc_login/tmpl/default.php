<?php
defined('_JEXEC') or die('Restricted access');
$config 	= DiscussHelper::getConfig();

if(!$profile->id) {
	$login = ' Inloggen / Registeren';
} else {
	$login = $profile->getName();
}

?>

<ul class="nav navbar-nav navbar-right navbar-user">
	<li class="dropdown">
		<a data-toggle="dropdown" class="dropdown-toggle profile" href="#">
			<img src="<?php echo $profile->getAvatar();?>" class="avatar">
		</a>

		<ul class="dropdown-menu">
		<?php if( $isLoggedIn ){ ?>
			<?php if( $params->get( 'show_my_discussions' , 1 ) ){ ?>
			<li>
				<a class="user-discussions" href="<?php echo $profile->getLink(); ?>"><span><?php echo JText::_( 'Mijn profiel' );?></span></a>
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
			<li class="divider"></li>
			<li>
				<i class="ico"></i><a class="discuss-logout" href="<?php echo JRoute::_('index.php?option='.$userComponent->option.'&task='.$userComponent->logout. '&' . DiscussHelper::getToken() . '=1&return='.$return);?>"><span><?php echo JText::_( 'MOD_EASYDISCUSS_WELCOME_SIGN_OUT' );?></span></a>
			</li>
		<?php } else if( $params->get( 'enable_login') ) { ?>
		<li style="padding: 15px; padding-bottom: 0px;">
			<form action="<?php echo JRoute::_( 'index.php', true, $params->get('usesecure')); ?>" method="post" name="login" id="form-login" >
				<div class="form-group">
					<input type="text" id="username" name="username" class="form-control" placeholder="Gebruikersnaam" style="margin-bottom:15px;">
				</div>
				<div class="form-group">
					<input type="password" id="password" name="password" class="form-control" placeholder="Wachtwoord" style="margin-bottom:15px;">
				</div>
				<div class="row">
					<div class="col-12">
						<input type="checkbox" id="modlgn_remember" name="remember" value="yes" title="<?php echo JText::_( 'MOD_EASYDISCUSS_WELCOME_REMEMBER_ME' );?>" alt="<?php echo JText::_( 'MOD_EASYDISCUSS_WELCOME_REMEMBER_ME' );?>">
						<label for="modlgn_remember"><?php echo JText::_( 'Blijf aangemeld' );?></label>
						<input type="submit" value="<?php echo JText::_( 'Login' );?>" name="Submit" class="btn btn-primary btn-small pull-right">
					</div>
				</div>

				<input type="hidden" name="option" value="<?php echo $userComponent->option; ?>" />
				<input type="hidden" name="task" value="<?php echo $userComponent->login; ?>" />
				<input type="hidden" name="return" value="<?php echo $return; ?>" />
				<?php echo JHTML::_( 'form.token' ); ?>
			</form>
		</li>
		<li class="divider"></li>
		<li>
			<a href="<?php echo JRoute::_( 'index.php?option='.$userComponent->option.'&view=reset' ); ?>"><?php echo JText::_( 'Wachtwoord vergeten?' );?></a>
		</li>
		<li>
			<a href="<?php echo JRoute::_( 'index.php?option='.$userComponent->option.'&view=remind' ); ?>"><?php echo JText::_( 'Gebruikersnaam vergeten?' );?></a>
		</li>
		<li class="divider"></li>
		<li>
		<?php if( $userComponent->allowRegister ){ ?>
			<a href="<?php echo JRoute::_( 'index.php?option='.$userComponent->option.'&view='.$userComponent->register ); ?>"><?php echo JText::_( 'Maak een account aan' );?></a>
		<?php } ?>
		</li>
		<?php } ?>
		</ul>
	</li>
</ul>
