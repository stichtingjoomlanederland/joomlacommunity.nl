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
<a name="<?php echo JText::_('COM_EASYDISCUSS_TOP_ANCHOR');?>"></a>
<div class="discussQuestion discuss-read<?php echo $post->islock ? ' is-locked' : '';?><?php echo !empty($post->password) ? ' is-protected' : '';?><?php echo $post->isresolve ? ' is-resolved' : '';?><?php echo $post->isFeatured() ? ' is-featured' : '';?><?php echo $post->isPollLocked() ? ' is-poll-lock' : '';?>" data-id="<?php echo $post->id;?>">
	<div class="feed-body">
		<header>
			<?php if( $system->config->get( 'main_allowquestionvote' ) ){ ?>
				<?php echo $this->loadTemplate( 'post.vote.php' , array( 'access' => $access , 'post' => $post ) ); ?>
			<?php } ?>

			<span class="mark-locked"><i data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_LOCKED_DESC' );?>" data-placement="top" rel="ed-tooltip" class="i i-lock"></i> &nbsp;</span>

			<?php if( !empty($post->password) ) { ?>
			<i data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_PROTECTED_DESC' );?>" data-placement="top" rel="ed-tooltip" class="i i-key"></i>
			<?php } ?>

			<b><?php echo $this->loadTemplate( 'author.name.php' , array( 'post' => $post ) ); ?></b>

			<span class="muted"><?php echo $this->loadTemplate( 'online.php' , array( 'user' => $post->user ) ); ?></span>

			<?php echo JText::_( 'COM_EASYDISCUSS_POSTED_IN' ); ?>
			
			<a href="<?php echo DiscussRouter::getCategoryRoute( $category->id );?>"><?php echo $category->getTitle();?></a>

			<time datetime="<?php echo $this->formatDate( $system->config->get( 'layout_dateformat', '%a, %B %d %Y, %I:%M %p') , $post->created);?>" class="muted">
				<?php echo $this->formatDate( 'D, %B %d %Y, %I:%M %p' , $post->created ); ?>
			</time>

			<h2 class="discuss-post-title">
				<?php echo $post->title; ?>
			</h2>
			
			<div class="discuss-meta">
				<span class="status">
					<i class="i i-retweet"></i>
					<a href="#replies">
						<b class="item-count"><?php echo $totalReplies;?></b>
						<span class="muted"><?php echo JText::_( 'COM_EASYDISCUSS_REPLIES' );?></span>
					</a>
				</span>
				<span class="views">
					<i class="i i-eye"></i>
					<a href="javascript:void(0);">
						<b class="item-count"><?php echo $post->hits;?></b>
						<span class="muted"><?php echo JText::_( 'COM_EASYDISCUSS_VIEWS' );?></span>
					</a>
				</span>
								<span class="votes">
					<i class="i i-bar-chart-o"></i>
					<a href="javascript:void(0);">
						<b class="item-count"> <?php echo $post->sum_totalvote;?></b>
						<span class="muted"><?php echo $this->getNouns( 'COM_EASYDISCUSS_VOTES_TEXT' , $post->sum_totalvote );?></span>
					</a>
				</span>
				<span class="likes">
					<i class="i i-thumbs-o-up"></i>
					<a href="javascript:void(0);">
						<b class="item-count"> <?php echo $post->num_likes;?></b>
						<span class="muted"><?php echo JText::_( 'COM_EASYDISCUSS_LIKES' );?></span>
					</a>
				</span>
			</div>
		</header>

		<div class="discuss-status">
			<b class="butt butt-s mark-featured">
				<i class="i i-star"></i>
				&nbsp;
				<?php echo JText::_( 'COM_EASYDISCUSS_FEATURED' , true );?>
			</b>
			<b class="butt butt-s mark-resolved">
				<i class="i i-check"></i>
				&nbsp;
				<?php echo JText::_( 'COM_EASYDISCUSS_RESOLVED' , true );?>
			</b>

			<?php if ( $post->getPostType() ) { ?>
			<span class="butt butt-label butt-s postType label-post_type<?php echo $post->getPostTypeSuffix(); ?>" ><?php echo $post->getPostType(); ?></span>
			<?php } ?>
			<?php if ( $post->getStatusMessage() ) { ?>
			<span class="butt butt-label butt-s postStatus label-info label-post_status<?php echo $post->getStatusClass();?>"><?php echo $post->getStatusMessage();?></span>
			<?php } ?>
		</div>
		<hr>

		<?php if( !$post->isProtected() || DiscussHelper::isModerator( $post->category_id ) ){ ?>
		<article class="discuss-post-article">
			<?php echo $post->content;?>

			<?php echo DiscussHelper::showSocialButtons( $post, 'horizontal' ); ?>
		</article>

		<?php echo $this->getFieldHTML( true , $post ); ?>

		<?php echo $this->loadTemplate( 'post.customfields.php' ); ?>

		<?php echo $this->loadTemplate( 'post.tags.php' , array( 'tags' => $tags ) ); ?>

		<?php echo $this->loadTemplate( 'post.likes.php' , array( 'post' => $post ) ); ?>

		<?php echo $this->loadTemplate( 'post.comments.php' , array( 'reply' => $post, 'question' => $post  ) ); ?>

		<?php echo $this->loadTemplate( 'post.location.php' , array( 'post' => $post ) ); ?>

		<?php echo $this->loadTemplate( 'post.signature.php' , array( 'signature' => $post->getOwner()->signature ) ); ?>

		<?php } else { ?>
		<article class="discuss-post-password"><?php echo $this->loadTemplate( 'entry.password.php' , array( 'post' => $post ) ); ?></article>
		<?php } ?>

		<footer class="clearfix">
			<hr>
			<div class="float-l">
				<?php echo $this->loadTemplate( 'post.favourites.php' , array( 'post' => $post ) ); ?>
			</div>
			<div class="float-r">
				<?php if( !$post->isProtected() ){ ?>
					<?php echo DiscussHelper::getSubscriptionHTML($system->my->id, $post->id, 'post'); ?>
				<?php } ?>
			</div>
		</footer>

		<?php echo $this->loadTemplate( 'post.reply.comments.php' , array( 'post' => $post ) ); ?>
	</div>
</div>
