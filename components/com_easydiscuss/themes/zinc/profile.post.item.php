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
$readCss = '';
if( $system->profile->id != 0)
{
	$readCss = 	( $system->profile->isRead( $post->id ) || $post->legacy ) ? ' is-read' : ' is-unread';
}
$isRecent = ( $post->isnew ) ? ' is-recent' : '';
?>
<div class="discuss-item<?php echo $post->islock ? ' is-locked' : '';?><?php echo $post->isresolve ? ' is-resolved' : '';?><?php echo $post->isFeatured ? ' is-featured' : '';?> <?php echo $readCss . $isRecent; ?><?php echo isset( $favourites ) ? ' is-favourited' : '';?>">

	<div class="feed-body">
		<header>
			<span class="mark-locked"><i class="i i-lock" rel="ed-tooltip" data-placement="top" data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_LOCKED' , true );?>" ></i> &nbsp;</span>
			<b>
				<?php if( !$post->user_id ){ ?>
					<?php echo $post->poster_name; ?>
				<?php } else { ?>
					<?php echo $post->user->getName();?>
				<?php } ?>
			</b>
			<?php echo JText::_( 'COM_EASYDISCUSS_POSTED_IN' ); ?>
			<a href="<?php echo DiscussRouter::getCategoryRoute( $post->category_id ); ?>"><?php echo $post->category; ?></a>
			<div class="float-r">
				<span class="mark-featured"><i class="i i-star" rel="ed-tooltip" data-placement="top" data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_FEATURED' , true );?>"></i></span>
				<span class="mark-resolved"><i class="i i-check" rel="ed-tooltip" data-placement="top" data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_RESOLVED' , true );?>"></i></span>
				<time datetime="<?php echo $this->formatDate( '%Y-%m-%d' , $post->created ); ?>" class="muted"></i> <?php echo $post->duration; ?></time>
			</div>
		</header>
		<h3 class="discuss-title" itemprop="name">
			<a href="<?php echo DiscussRouter::getPostRoute( $post->id );?>">
				<?php if( !empty($post->password) ) { ?>
				<i class="i i-key" rel="ed-tooltip" data-placement="top" data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_PROTECTED' , true );?>" ></i>
				<?php } ?>

				<?php echo $post->title; ?>
			</a>
		</h3>

		<div class="discuss-meta">
			<span class="status">
				<i class="i i-retweet"></i>
				<a href="<?php echo DiscussRouter::getPostRoute( $post->id );?>">
					<b class="item-count"><?php echo $replies = !empty( $post->reply ) ? $post->totalreplies : 0; ?></b>
					<span class="muted"><?php echo $this->getNouns('COM_EASYDISCUSS_REPLIES', $replies); ?></span>
				</a>
			</span>
			<span class="views">
				<i class="i i-eye"></i>
				<a href="<?php echo DiscussRouter::getPostRoute( $post->id );?>">
					<b class="item-count"><?php echo $post->hits; ?></b>
					<span class="muted"><?php echo $this->getNouns( 'COM_EASYDISCUSS_VIEWS' , $post->hits );?></span>
				</a>
			</span>
			<?php if( $system->config->get( 'main_allowquestionvote' ) ){ ?>
			<span class="votes">
				<i class="i i-bar-chart-o"></i>
				<a href="<?php echo DiscussRouter::getPostRoute( $post->id );?>">
					<b class="item-count"> <?php echo $post->sum_totalvote; ?></b>
					<span class="muted"><?php echo $this->getNouns( 'COM_EASYDISCUSS_VOTES_STRING' , $post->sum_totalvote );?></span>
				</a>
			</span>
			<?php } ?>
			<?php if($system->config->get( 'main_likes_discussions' )){ ?>
			<span class="likes">
				<i class="i i-thumbs-o-up"></i>
				<a href="<?php echo DiscussRouter::getPostRoute( $post->id );?>">
					<b class="item-count"> <?php echo $post->num_likes; ?></b>
					<span class="muted"><?php echo $this->getNouns( 'COM_EASYDISCUSS_LIKES_STRING' , $post->num_likes );?></span>
				</a>
			</span>
			<?php } ?>
		</div>

		<?php if($system->config->get( 'layout_enableintrotext' ) ){ ?>
		<div class="discuss-intro">
			<?php echo $post->introtext; ?>
		</div>
		<?php } ?>

		<?php if( isset( $post->reply ) ){ ?>
		<div class="discuss-last-replied media">
			<?php if( $post->reply->id ){ ?>
			<?php 	if( $system->config->get( 'layout_avatar' ) ) { ?>
			<a href="<?php echo $post->reply->getLink();?>" title="<?php echo $post->reply->getName(); ?>" class="discuss-avatar float-l">
				<img src="<?php echo $post->reply->getAvatar();?>" alt="<?php echo $this->escape( $post->reply->getName() );?>" width="40" height="40" class="avatar" />
			</a>
			<?php 	} ?>
			<?php } else { ?>
				<?php echo $post->reply->poster_name; ?>
			<?php } ?>
			<div class="media-body">
				<?php $lastReply = DiscussHelper::getModel( 'Posts' )->getLastReply( $post->id ); ?>
				<a href="<?php echo DiscussRouter::getPostRoute( $post->id ) . '#' . JText::_('COM_EASYDISCUSS_REPLY_PERMALINK') . '-' . $lastReply->id;?>" class="butt butt-default">
					<?php echo JText::_( 'COM_EASYDISCUSS_VIEW_LAST_REPLY' );?>
				</a>
			</div>
		</div>
		<?php } ?>
	</div>
</div>
