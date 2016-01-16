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

<?php if( $system->config->get( 'main_frontend_statistics' ) ){ ?>
<?php if( $canViewStatistic ){ ?>
<div class="discuss-statistic">
	<h3><?php echo JText::_( 'COM_EASYDISCUSS_BOARD_STATISTICS' );?></h3>

	<div>
		<i class="i i-file-text-o"></i>
		<span>
			<?php echo JText::_( 'COM_EASYDISCUSS_TOTAL_POSTS' ); ?>:
			<b><?php echo $totalPosts; ?></b>
		</span>
		<span>
			<?php echo JText::_( 'COM_EASYDISCUSS_TOTAL_RESOLVED_POSTS' ); ?>:
			<b><?php echo $resolvedPosts; ?></b>
		</span>
		<span>
			<?php echo JText::_( 'COM_EASYDISCUSS_TOTAL_UNRESOLVED_POSTS' ); ?>:
			<b><?php echo $unresolvedPosts; ?></b>
		</span>
	</div>

	<div>
		<i class="i i-group"></i>
		<span>
			<?php echo JText::_( 'COM_EASYDISCUSS_TOTAL_USERS' ); ?>:
			<b><?php echo $totalUsers; ?></b>
		</span>
		<span>
			<?php echo JText::_( 'COM_EASYDISCUSS_LATEST_MEMBER' ); ?>:
			<a href="<?php echo $latestMember->getLink();?>"><?php echo $latestMember->getName(); ?></a>
		</span>
	</div>

	<div>
		<i class="i i-eye"></i>
		<?php echo JText::_( 'COM_EASYDISCUSS_ONLINE_USERS' ); ?>:
		<?php if( $onlineUsers ){ ?>
		<?php 	for( $i = 0; $i < count( $onlineUsers ); $i++ ){ ?>
			<a href="<?php echo $onlineUsers[ $i ]->getLink();?>"><?php echo $onlineUsers[ $i ]->getName();?></a>
			<?php if( next( $onlineUsers ) !== false ){ ?> , <?php } ?>
		<?php 	} ?>
		<?php } ?>

		<?php if( $totalGuests && $onlineUsers ){ ?>
			<?php echo JText::_( 'COM_EASYDISCUSS_AND' ); ?>
		<?php } ?>

		<?php if( $totalGuests ){ ?>
		<?php echo JText::sprintf( 'COM_EASYDISCUSS_OTHER_GUESTS' , $totalGuests ); ?>
		<?php } ?>
	</div>
</div>
<?php } ?>
<?php } ?>