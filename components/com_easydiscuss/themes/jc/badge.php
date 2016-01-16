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
<header class="component-header media">
	<img src="<?php echo $badge->getAvatar();?>" border="0" class="float-l" />
	<div class="media-body">
		<h2><?php echo $badge->get( 'title' );?></h2>
		<span class="muted"><?php echo JText::_( 'COM_EASYDISCUSS_EARN_THIS_BADGE_WHEN' );?>:</span>
		<?php echo $badge->get('description');?>
	</div>
</header>

<h3><?php echo JText::_( 'COM_EASYDISCUSS_BADGE_ACHIEVERS' );?></h3>
<?php if( $users ){ ?>
<ul class="discuss-grid grid-achievers reset-ul float-li clearfix">
	<?php foreach( $users as $user ){ ?>
	<li>
		<div class="media">
			<?php if( $system->config->get( 'layout_avatar' ) ) { ?>
			<a href="<?php echo $user->getLink();?>"  class="discuss-avatar float-l">
				<img src="<?php echo $user->getAvatar();?>" border="0" class="avatar" width="60" height="60" />
			</a>
			<?php } ?>
			<div class="media-body">
				<b><a href="<?php echo $user->getLink();?>" class="achiver-name"><?php echo $user->getName();?></a></b>
				<div class="date-obtained muted">
					<?php echo JText::sprintf( 'COM_EASYDISCUSS_ACHIEVED_ON' , $badge->getAchievedDate( $user->id ) );?>
				</div>
			</div>
		</div>
	</li>
	<?php } ?>
</ul>
<?php } else { ?>
<div>
	<?php echo JText::_( 'COM_EASYDISCUSS_BADGES_NO_USERS' ); ?>
</div>
<?php } ?>
