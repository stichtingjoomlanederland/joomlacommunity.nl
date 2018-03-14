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
<ul class="discuss-badges-list unstyled">
	<?php foreach( $badges as $badge ){ ?>
	<li rel="ed-tooltip" data-original-title="<?php echo $badge->custom ? strip_tags( $badge->custom ) : strip_tags( $badge->description );?>">
		<a href="<?php echo DiscussRouter::getBadgeRoute( $badge->id );?>" class="badge-icon float-l">
			<img src="<?php echo $badge->getAvatar();?>" width="48" />
		</a>
		<div class="badge-text">
			<a href="<?php echo DiscussRouter::getBadgeRoute( $badge->id );?>" class="badge-name"><?php echo JText::_( $badge->title );?></a>
			<div class="date-obtained small ttu"><?php echo JText::sprintf( 'COM_EASYDISCUSS_ACHIEVED_ON' , $badge->getAchievedDate( $profile->id ) );?></div>
		</div>
	</li>
	<?php } ?>
</ul>
<?php } else { ?>
<div class="empty">
	<?php echo JText::_( 'COM_EASYDISCUSS_NO_BADGES_YET' );?>
</div>
<?php } ?>
