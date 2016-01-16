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
require_once DISCUSS_HELPERS . '/jc.php';

$readCss	= '';
$isRead		= false;
if( $system->profile->id != 0)
{
	$readCss	= 	( $system->profile->isRead( $post->id ) || $post->legacy ) ? ' is-read' : ' is-unread';
	$isRead		=  ( $system->profile->isRead( $post->id ) || $post->legacy ) ? false : true;
}

$isRecent	= ( $post->isnew ) ? ' is-recent' : '';
?>
<li>
	<div class="reply clearfix<?php echo $post->islock ? ' is-locked' : '';?><?php echo !empty($post->password) ? ' is-protected' : '';?><?php echo $post->isresolve ? ' is-resolved' : '';?><?php echo $post->isFeatured ? ' is-featured' : '';?> <?php echo $readCss . $isRecent; ?> user-role-<?php echo $post->user->getRoleId(); ?>">
	
	
			<?php if ($system->config->get( 'layout_avatar' ) && $system->config->get( 'layout_avatar_in_post' )) { ?>
			<?php
$layout	= new JLayoutFile('avatar');
echo $layout->render($post->user->id);
?>
			<?php } ?>
			
			<div class="topic-info">
			<div class="forum-reacties">
				<span class="number"><?php if($post->isresolve):?><span class="icon jc-solved"></span> <?php endif;?><?php echo $replies = !empty( $post->reply ) ? $post->totalreplies : 0; ?></span>
				<?php echo $this->getNouns('COM_EASYDISCUSS_REPLIES', $replies); ?>
			</div>
			<div class="page-header">
				<h2 itemprop="name">
					<a href="<?php echo DiscussRouter::getPostRoute( $post->id );?>">
					<?php echo $post->title; ?>
					</a>
					
				</h2>
				<time datetime="<?php echo $this->formatDate( '%Y-%m-%d' , $post->created ); ?>" class="muted">
					<?php echo DiscussJcHelper::time_delta('now', $post->created, 0); ?>
				</time>
				<?php echo JText::_( 'door' ); ?>
				<a href="<?php echo $post->getOwner()->link;?>">
				<?php echo $this->loadTemplate( 'author.name.php' , array( 'post' => $post ) ); ?>
				</a>
				<?php echo JText::_( ' in' ); ?>
		
				<a href="<?php echo DiscussRouter::getCategoryRoute( $post->category_id ); ?>"><?php echo $post->category; ?></a>
				
				<div class="forum-labels">
					<?php if ( $post->getPostType() ) { ?>
					<span class="label label-<?php echo $post->getPostTypeSuffix(); ?>"><span class="icon jc-joomla"></span> <?php echo $post->getPostType(); ?></span>
					<?php } ?>
					<?php if( $isRead ) { ?>
						<span class="label label-success"><?php echo JText::_( 'COM_EASYDISCUSS_NEW' );?></span>
					<?php } ?>
				</div>
				
		
				
				
				
				
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
			
			
	
			
			<!--
<div class="discuss-replied media">
				<div class="btn-group btn-group-sm pull-right">
					
					<?php if( isset( $post->reply ) ){ ?>
					<?php $lastReply = DiscussHelper::getModel( 'Posts' )->getLastReply( $post->id ); ?>
					<a href="<?php echo DiscussRouter::getPostRoute( $post->id ) . '#' . JText::_('COM_EASYDISCUSS_REPLY_PERMALINK') . '-' . $lastReply->id;?>" class="btn btn-default">
						<?php echo JText::_( 'COM_EASYDISCUSS_VIEW_LAST_REPLY' );?>
					</a>
					<?php } ?>
					<a href="<?php echo DiscussRouter::getPostRoute( $post->id );?>" class="btn btn-default">
						<?php echo $replies = !empty( $post->reply ) ? $post->totalreplies : 0; ?> <?php echo $this->getNouns('COM_EASYDISCUSS_REPLIES', $replies); ?>
					</a>
					
				</div>
				<?php if( isset( $post->reply ) ){ ?>
				<?php if( $post->reply->id ){ ?>
				<?php 	if( $system->config->get( 'layout_avatar' ) ) { ?>
				<a href="<?php echo $post->reply->getLink();?>" title="<?php echo $post->reply->getName(); ?>" class="discuss-avatar  pull-right ">
					<img src="<?php echo $post->reply->getAvatar();?>" alt="<?php echo $this->escape( $post->reply->getName() );?>" width="30" height="30" class="avatar" />
				</a>
				<?php 	} ?>
				<?php } else { ?>
					<?php // echo $post->reply->poster_name; ?>
				<?php } ?>
				<?php } ?>
			</div>
-->
			
	
		</div>
	</div><!--/.discuss-item -->
</li>
