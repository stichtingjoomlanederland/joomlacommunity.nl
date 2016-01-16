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
<h2 class="discuss-component-title">
	<img src="<?php echo $badge->getAvatar();?>" border="0" class="mr-10" />
	<?php echo $badge->get( 'title' );?>
</h2>
<div class="mb-10 ">
	<?php echo $badge->get('description');?>
</div>
<hr/>

<h3><?php echo JText::_( 'COM_EASYDISCUSS_BADGE_ACHIEVERS' );?></h3>
<?php if( $users ){ ?>
<ul class="discuss-badge-list achievers clearfix mt-15">
	<?php foreach( $users as $user ){ ?>
	<li>
		<div class="media">
			<div class="media-object pull-left">
				<div class="discuss-avatar avatar-medium pull-left">
					<a href="<?php echo $user->getLink();?>">
						<?php if( $system->config->get( 'layout_avatar' ) ) { ?>
						<img src="<?php echo $user->getAvatar();?>" border="0" width="40" class="mr-10"/>
						<?php } else { ?>
						<?php echo $user->getName();?>
						<?php } ?>
					</a>
				</div>
			</div>
			<div class="media-body">
				<a href="<?php echo $user->getLink();?>" class="badge-name"><?php echo $user->getName();?></a>
				<div class="date-obtained">
					<small><?php echo JText::sprintf( 'COM_EASYDISCUSS_ACHIEVED_ON' , $badge->getAchievedDate( $user->id ) );?></small>
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
