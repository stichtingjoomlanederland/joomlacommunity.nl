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

$readCss	= '';
$isRead		= false;
if( $system->profile->id != 0)
{
	$readCss	= 	( $system->profile->isRead( $post->id ) || $post->legacy ) ? ' is-read' : ' is-unread';
	$isRead		=  ( $system->profile->isRead( $post->id ) || $post->legacy ) ? false : true;
}

$isRecent	= ( $post->isnew ) ? ' is-recent' : '';
?>
<li class="postItem">
	<div class="discuss-item<?php echo $post->islock ? ' is-locked' : '';?><?php echo !empty($post->password) ? ' is-protected' : '';?><?php echo $post->isresolve ? ' is-resolved' : '';?><?php echo $post->isFeatured ? ' is-featured' : '';?> <?php echo $readCss . $isRecent; ?>">

		<div class="discuss-status">
			<i class="icon-ed-featured" rel="ed-tooltip" data-placement="top" data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_FEATURED' , true );?>"></i>
			<i class="icon-ed-resolved" rel="ed-tooltip" data-placement="top" data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_RESOLVED' , true );?>"></i>

		</div>

		<div class="discuss-item-left">

			<a href="<?php echo DiscussRouter::getPostRoute( $post->id );?>">
				<h2 class="discuss-post-title" itemprop="name">

					<i class="icon-lock" rel="ed-tooltip" data-placement="top" data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_LOCKED' , true );?>" ></i>

					<?php if( !empty($post->password) ) { ?>
					<i class="icon-key" rel="ed-tooltip" data-placement="top" data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_PROTECTED' , true );?>" ></i>
					<?php } ?>

					<?php echo $post->title; ?>

					<?php if ($post->private) { ?>
						<span class="label label-important" rel="tooltip" data-placement="bottom" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_PRIVATE_POST_DESC', true);?>"><?php echo JText::_('COM_EASYDISCUSS_PRIVATE_POST');?></span>
					<?php } ?>

					<?php if( $isRead ) { ?>
						<span class="label label-info"><?php echo JText::_( 'COM_EASYDISCUSS_NEW' );?></span>
					<?php } ?>
				</h2>
			</a>

			<div class="discuss-date fs-11">
				<span class="mr-10">
					<i class="icon-ed-time"></i> <?php echo $post->duration;?>
					<time datetime="<?php echo $this->formatDate( '%Y-%m-%d' , $post->created ); ?>"></time>
				</span>

				<?php if( isset( $post->reply ) ){ ?>
				<span>
					<?php $lastReply = DiscussHelper::getModel( 'Posts' )->getLastReply( $post->id ); ?>
					<i class="icon-user"></i> <?php echo JText::_('COM_EASYDISCUSS_LAST_REPLIED_BY');?> <a href="<?php echo $post->reply->getLink();?>" title="<?php echo $post->reply->getName(); ?>"><?php echo $post->reply->poster_name; ?></a>
				</span>
				<?php } ?>
			</div>

			<hr class="title-separator" />

		</div>


		<div class="discuss-item-right">
			<div class="discuss-story">

				<div class="discuss-story-bd">
					<div class="ph-10">

						<?php if($system->config->get( 'layout_enableintrotext' ) ){ ?>
						<div class="discuss-intro-text">
							<?php echo $post->introtext; ?>
						</div>
						<?php } ?>
						<?php if( $system->config->get( 'main_master_tags' ) ){ ?>
							<?php if( $system->config->get( 'main_tags' ) && $post->tags ){ ?>
							<div class="discuss-tags">
								<?php foreach( $post->tags as $tag ){ ?>
									<a class="label" href="<?php echo DiscussRouter::getTagRoute( $tag->id ); ?>"><i class="icon-tag"></i><?php echo $tag->title; ?></a>
								<?php } ?>
							</div>
							<?php } ?>
						<?php } ?>
					</div>
				</div>



				<div class="discuss-story-ft">
					<div class="discuss-action-options">

						<div class="discuss-statistic pull-left">
							<a href="<?php echo DiscussRouter::getPostRoute( $post->id );?>" rel="ed-tooltip" data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_STAT_TOTAL_REPLIES' , true );?>">
								<i class="icon-comments"></i> <?php echo $replies = !empty( $post->reply ) ? $post->totalreplies : 0; ?>
							</a>
							<a href="<?php echo DiscussRouter::getPostRoute( $post->id );?>" rel="ed-tooltip" data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_STAT_TOTAL_HITS' , true );?>">
								<i class="icon-bar-chart"></i> <?php echo $post->hits; ?>
							</a>
							<a href="<?php echo DiscussRouter::getPostRoute( $post->id );?>" rel="ed-tooltip" data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_STAT_TOTAL_FAVOURITES' , true ); ?>">
								<i class="icon-heart"></i> <?php echo $post->totalFavourites ?>
							</a>
							<a href="<?php echo DiscussRouter::getPostRoute( $post->id );?>" rel="ed-tooltip" data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_STAT_TOTAL_VOTES' , true ); ?>">
								<i class="icon-thumbs-up"></i> <?php echo $post->sum_totalvote; ?>
							</a>

						</div><!-- discuss-statistic -->

						<div class="pull-right discuss-user-footer">
							<a href="<?php echo $post->user->getLink();?>">
								<?php if ($system->config->get( 'layout_avatar' ) && $system->config->get( 'layout_avatar_in_post' )) { ?>
								<span class="discuss-avatar avatar-small avatar-rounded">
									<img src="<?php echo $post->user->getAvatar();?>" alt="<?php echo $this->escape( $post->user->getName() );?>"<?php echo DiscussHelper::getHelper( 'EasySocial' )->getPopbox( $post->user->id );?> />
								</span>
								<?php }?>
								<?php echo $post->user->getName();?>
							</a>
						</div>

					</div>
				</div>

			</div>
		</div>

	</div><!-- item -->
</li>
