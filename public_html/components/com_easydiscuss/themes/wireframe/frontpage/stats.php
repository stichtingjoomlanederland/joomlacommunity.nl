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
<div class="discuss-statistics">
	<div class="media">
		<div class="media-object pull-left">
			<span class="discuss-widget-title"><?php echo JText::_( 'COM_EASYDISCUSS_BOARD_STATISTICS' );?></span>
		</div>
		<div class="media-body small">
			<ul class="unstyled nav-pills">
				<li>
					<i class="icon-columns"></i>
					<span><?php echo JText::_( 'COM_EASYDISCUSS_TOTAL_POSTS' ); ?>:</span>
					<span><?php echo $totalPosts; ?></span>
				</li>
				<li>
					<span><?php echo JText::_( 'COM_EASYDISCUSS_TOTAL_RESOLVED_POSTS' ); ?>:</span>
					<span><?php echo $resolvedPosts; ?></span>
				</li>
				<li>
					<span><?php echo JText::_( 'COM_EASYDISCUSS_TOTAL_UNRESOLVED_POSTS' ); ?>:</span>
					<span><?php echo $unresolvedPosts; ?></span>
				</li>

			</ul>

			<ul class="unstyled nav-pills">
				<li>
					<i class="icon-user"></i>
					<span><?php echo JText::_( 'COM_EASYDISCUSS_TOTAL_USERS' ); ?>:</span>
					<span><?php echo $totalUsers; ?></span>
				</li>
				<li>
					<span><?php echo JText::_( 'COM_EASYDISCUSS_LATEST_MEMBER' ); ?>:</span>
					<span>
						<a href="<?php echo $latestMember->getLink();?>"><?php echo $latestMember->getName(); ?></a>
					</span>
				</li>
			</ul>

			<ul class="unstyled nav-pills">
				<li>
					<i class="icon-bolt"></i>
					<span><?php echo JText::_( 'COM_EASYDISCUSS_ONLINE_USERS' ); ?>:</span>

					<?php if( $onlineUsers ){ ?>
						<?php for( $i = 0; $i < count( $onlineUsers ); $i++ ){ ?>
							<span><a href="<?php echo $onlineUsers[ $i ]->getLink();?>"><?php echo $onlineUsers[ $i ]->getName();?></a></span>

							<?php if( next( $onlineUsers ) !== false ){ ?> , <?php } ?>
						<?php } ?>
					<?php } ?>

					<?php if( $totalGuests && $onlineUsers ){ ?>
						<?php echo JText::_( 'COM_EASYDISCUSS_AND' ); ?>
					<?php } ?>

					<?php if( $totalGuests ){ ?>
					<?php echo JText::sprintf( 'COM_EASYDISCUSS_OTHER_GUESTS' , $totalGuests ); ?>
					<?php } ?>
				</li>
			</ul>

		</div>
	</div>
</div>