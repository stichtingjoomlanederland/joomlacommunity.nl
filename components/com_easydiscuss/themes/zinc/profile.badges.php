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
<h3><?php echo JText::_( 'COM_EASYDISCUSS_BADGES' );?></h3>
<hr />
<?php if( $badges ){ ?>
<ul class="profile-badges reset-ul">
	<?php foreach( $badges as $badge ){ ?>
	<li class="media">
		<a href="<?php echo DiscussRouter::getBadgeRoute( $badge->id );?>" class="badge-icon float-l">
			<img src="<?php echo $badge->getAvatar();?>" width="64" height="64" />
		</a>
		<div class="media-body">
			<b><a href="<?php echo DiscussRouter::getBadgeRoute( $badge->id );?>" class="badge-name"><?php echo JText::_( $badge->title );?></a></b>

			<div class="badge-obtained muted"><?php echo JText::sprintf( 'COM_EASYDISCUSS_ACHIEVED_ON' , $badge->getAchievedDate( $profile->id ) );?></div>

			<div class="badge-desc">
				<?php echo $badge->custom ? strip_tags( $badge->custom ) : strip_tags( $badge->description );?>
			</div>
		</div>
	</li>
	<?php } ?>
</ul>
<?php } else { ?>
<div class="empty">
	<?php echo JText::_( 'COM_EASYDISCUSS_NO_BADGES_YET' );?>
</div>
<?php } ?>
