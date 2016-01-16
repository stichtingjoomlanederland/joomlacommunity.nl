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
<li>
	<div class="discuss-item discuss-flyout">

		<div class="discuss-flyout-content">
			<?php echo $this->loadTemplate( 'post.conversation.php' , array( 'userId' => $user->id ) ); ?>
		</div>
		<?php if( $system->config->get( 'main_rss') ){ ?>
		<div class="discuss-item-hd">

			<div class="discuss-story">
				<div class="discuss-story-bd clearfix">
					<div class="pr-10 pt-10">
						<a href="<?php echo $user->getRSS();?>" class="btn btn-small btn-warning pull-right" rel="ed-tooltip" data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_USER_TOOLTIP_RSS' );?>"><i class="icon-rss"></i></a>
					</div>
				</div>
			</div>
		</div>
		<?php } ?>
		<div class="discuss-item-left discuss-user">

			<div class="discuss-avatar avatar-large mb-10">
				<a class="" href="<?php echo $user->getLink();?>">
					<?php if( $system->config->get( 'layout_avatar' ) ) { ?>
					<img alt="<?php echo $this->escape( $user->getName() );?>" src="<?php echo $user->getAvatar( false );?>" />
					<?php } else { ?>
					<?php echo $this->escape( $user->getName() );?>
					<?php } ?>
				</a>
				<?php echo $this->loadTemplate( 'online.php' , array( 'user' => $user ) ); ?>
				<?php if($system->config->get( 'layout_profile_roles' ) && $user->getRole() ) { ?>
				<div class="discuss-role-title <?php echo $user->getRoleLabelClassname(); ?>"><?php echo $this->escape($user->getRole()); ?></div>
				<?php } ?>

			</div>
			<h3><a class="discuss-user-name" href="<?php echo $user->getLink();?>"><?php echo $user->getName();?></a></h3>


			<?php if( $system->config->get( 'main_ranking' ) ) { ?>
			<div class="discuss-user-rank fs-11 mt-5"><?php echo DiscussHelper::getUserRanks( $user->id ); ?></div>

			<div class="discuss-user-graph">
				<div class="rank-bar mini" title="<?php echo $this->escape( DiscussHelper::getUserRanks( $user->id ) ); ?>">
					<div class="rank-progress" style="width: <?php echo DiscussHelper::getUserRankScore( $user->id ); ?>%"></div>
				</div>
			</div>
			<?php } ?>

			<div class="discuss-story">
				<div class="discuss-story-bd clearfix">
					<ul class="nav-bar unstyled">
						<li>
							<a href="<?php echo $user->getLink( '#questions' );?>" rel="ed-tooltip" data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_USER_TOOLTIP_QUESTIONS' );?>"><i class="icon-book"></i> <?php echo $user->getNumTopicPosted();?> </a>
						</li>
						<li>
							<a href="<?php echo $user->getLink( '#replies');?>" rel="ed-tooltip" data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_USER_TOOLTIP_REPLIES' );?>"><i class="icon-comments"></i> <?php echo $user->getNumTopicAnswered();?> </a>
						</li>
						<li>
							<a href="<?php echo $user->getLink( '#achievements');?>" rel="ed-tooltip" data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_USER_TOOLTIP_BADGES' );?>"><i class="icon-trophy"></i> <?php echo $user->getTotalBadges();?> </a>
						</li>
					</ul>
				</div>
			</div>
		</div>

		<div class="discuss-item-ft">
			<!-- <div class="discuss-action-options"> -->
				<?php if( $system->config->get( 'main_signature_visibility' ) ){ ?>
				<!-- <div class="discuss-signature"> -->
					<?php //echo $user->getSignature(); ?>
				<!-- </div> -->
				<?php } ?>
			<!-- </div> -->
		</div>

	</div>
</li>
