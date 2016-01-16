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
	<div class="discuss-item clearfix<?php echo $post->islock ? ' is-locked' : '';?><?php echo !empty($post->password) ? ' is-protected' : '';?><?php echo $post->isresolve ? ' is-resolved' : '';?><?php echo $post->isFeatured ? ' is-featured' : '';?> <?php echo $readCss . $isRecent; ?> user-role-<?php echo $post->user->getRoleId(); ?>">
		<div class="row-table">
			<div class="col-cell feed-head">
				<?php if ($system->config->get( 'layout_avatar' ) && $system->config->get( 'layout_avatar_in_post' )) { ?>
				<div class="discuss-avatar">
					<a href="<?php echo $post->user->getLink();?>" class="" title="<?php echo $this->escape( $post->user->getName() );?>">
						<img src="<?php echo $post->user->getAvatar();?>" alt="<?php echo $this->escape( $post->user->getName() );?>" <?php echo DiscussHelper::getHelper( 'EasySocial' )->getPopbox( $post->user->id );?> width="70" height="70"class="avatar" />
					</a>
				</div>
				<?php } ?>
				<?php echo $this->loadTemplate( 'ranks.php' , array( 'userId' => $post->user->id ) ); ?>
			</div>

			<div class="col-cell feed-body">
				<div class="discuss-headline">
					<span class="mark-locked"><i class="i i-lock" rel="ed-tooltip" data-placement="top" data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_LOCKED' , true );?>" ></i> &nbsp;</span>
					<b>
						<?php if( !$post->user_id ){ ?>
							<?php echo $post->poster_name; ?>
						<?php } else { ?>
							<?php echo $post->user->getName();?>
						<?php } ?>
					</b>
					
					<?php echo $this->loadTemplate( 'online.php' , array( 'user' => $post->user ) ); ?>

					<?php if($system->config->get( 'layout_profile_roles' ) && $post->user->getRole() ) { ?>
					<span class="discuss-role <?php echo ' ' . $post->user->getRoleLabelClassname(); ?>"><?php echo $this->escape($post->user->getRole()); ?></span>
					<?php } ?>
					
					<?php echo JText::_( 'COM_EASYDISCUSS_POSTED_IN' ); ?>
					<a href="<?php echo DiscussRouter::getCategoryRoute( $post->category_id ); ?>"><?php echo $post->category; ?></a>
					<div class="float-r h-mobile">
						<span class="mark-featured"><i class="i i-star" rel="ed-tooltip" data-placement="top" data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_FEATURED' , true );?>"></i></span>
						<span class="mark-resolved"><i class="i i-check" rel="ed-tooltip" data-placement="top" data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_RESOLVED' , true );?>"></i></span>
						<time datetime="<?php echo $this->formatDate( '%Y-%m-%d' , $post->created ); ?>" class="muted">
							<?php echo $post->duration; ?>
						</time>
					</div>
					<div class="discuss-timestamp v-mobile">
						<time datetime="<?php echo $this->formatDate( '%Y-%m-%d' , $post->created ); ?>" class="muted">
							<?php echo $post->duration; ?>
						</time>
						<span class="mark-featured muted">
							<b>&middot;</b>
							<?php echo JText::_( 'COM_EASYDISCUSS_FEATURED' , true );?>
						</span>
						<span class="mark-resolved muted">
							<b>&middot;</b>
							<?php echo JText::_( 'COM_EASYDISCUSS_RESOLVED' , true );?>
						</span>
					</div>
				</div>

				<div class="clear v-mobile"></div>

				<h3 class="discuss-title" itemprop="name">
					<a href="<?php echo DiscussRouter::getPostRoute( $post->id );?>">

					<?php if( !empty($post->password) ) { ?>
					<i class="i i-key" rel="ed-tooltip" data-placement="top" data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_PROTECTED' , true );?>" ></i>
					<?php } ?>

					<?php echo $post->title; ?>
					<?php if( $isRead ) { ?>
					<span class="label label-unread"><?php echo JText::_( 'COM_EASYDISCUSS_NEW' );?></span>
					<?php } ?>
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

				<?php if( $system->config->get( 'main_master_tags' && $system->config->get( 'main_tags' ) && $post->tags ) ){ ?>
				<div class="discuss-tags">
					<?php foreach( $post->tags as $tag ){ ?>
					<a class="butt butt-tag butt-default butt-s" href="<?php echo DiscussRouter::getTagRoute( $tag->id ); ?>"><?php echo $tag->title; ?></a>
					<?php } ?>
				</div>
				<?php } ?>

				<?php if( isset( $post->reply ) ){ ?>
				<div class="discuss-replied media">
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

				<?php if( $post->polls || $post->attachments ){ ?>
				<div class="discuss-attach">
					<?php if( $post->polls ){ ?>
						<span class="butt butt-s with-polls"><i class="i i-tasks"></i> <?php echo JText::_( 'COM_EASYDISCUSS_WITH_POLLS' );?></span>
					<?php } ?>

					<?php if( $post->attachments ){ ?>
						<span class="butt butt-s with-attachments"><i class="i i-file"></i> <?php echo JText::_( 'COM_EASYDISCUSS_WITH_ATTACHMENTS' );?></span>
					<?php } ?>
				</div>
				<?php } ?>
			</div>
		</div>
	</div><!--/.discuss-item -->
</li>
