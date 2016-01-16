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
<li class="user-item">
	<div>
		<?php echo $this->loadTemplate( 'post.conversation.php' , array( 'userId' => $user->id ) ); ?>
		<?php if( $system->config->get( 'main_rss') ){ ?>
		<a href="<?php echo $user->getRSS();?>" class="butt butt-default butt-rss" rel="ed-tooltip" data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_USER_TOOLTIP_RSS' );?>"><i class="i i-rss"></i></a>
		<?php } ?>


		<?php if( $system->config->get( 'layout_avatar' ) ) { ?>
		<a class="discuss-avatar avatar-user" href="<?php echo $user->getLink();?>">
			<img alt="<?php echo $this->escape( $user->getName() );?>" src="<?php echo $user->getAvatar( false );?>" class="avatar" />
		</a>
		<?php } ?>

		<h3 class="discuss-user-name">
			<a href="<?php echo $user->getLink();?>">
				<?php echo $user->getName();?>
				<?php echo $this->loadTemplate( 'online.php' , array( 'user' => $user ) ); ?>
			</a>
		</h3>

		<?php if($system->config->get( 'layout_profile_roles' ) && $user->getRole() ) { ?>
		<div class="discuss-role-title"><?php echo $this->escape($user->getRole()); ?></div>
		<?php } ?>

		<?php if( $system->config->get( 'main_ranking' ) ) { ?>
		<div class="discuss-user-rank fs-11 mt-5"><?php echo DiscussHelper::getUserRanks( $user->id ); ?></div>
		<div class="discuss-user-graph">
			<div class="discuss-rank" title="<?php echo $this->escape( DiscussHelper::getUserRanks( $user->id ) ); ?>">
				<div class="rank-progress" style="width: <?php echo DiscussHelper::getUserRankScore( $user->id ); ?>%"></div>
			</div>
		</div>
		<?php } ?>

		<div class="discuss-user-stats">
			<div rel="ed-tooltip" data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_USER_TOOLTIP_QUESTIONS' );?>">
				<a href="<?php echo $user->getLink( '#questions' );?>">
					<i class="i i-question"></i> <?php echo $user->getNumTopicPosted();?>
				</a>
			</div><div rel="ed-tooltip" data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_USER_TOOLTIP_REPLIES' );?>">
				<a href="<?php echo $user->getLink( '#replies');?>">
					<i class="i i-comments"></i> <?php echo $user->getNumTopicAnswered();?>
				</a>
			</div><div rel="ed-tooltip" data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_USER_TOOLTIP_BADGES' );?>">
				<a href="<?php echo $user->getLink( '#achievements');?>">
					<i class="i i-trophy"></i> <?php echo $user->getTotalBadges();?>
				</a>
			</div>
		</div>
	</div>
</li>
