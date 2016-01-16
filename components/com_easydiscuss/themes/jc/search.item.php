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

// Don't panic, this is only used in AJAX calls, that is why it doesn't have
// a UL
?>
<?php if ( !empty( $posts ) ) : ?>
<?php foreach( $posts as $post ) :

$isUnRead		= false;
if( ($post->itemtype == 'posts' || $post->itemtype == 'replies') && ( $system->profile->id != 0 ) )
{
	$isUnRead		=  ( $system->profile->isRead( $post->id ) || $post->legacy ) ? false : true;
}


$permalink	= '';
if( $post->itemtype == 'posts' )
{
	$permalink	= DiscussRouter::_('index.php?option=com_easydiscuss&view=post&id=' . $post->id);
}
else if( $post->itemtype == 'replies' )
{
    $permalink	= DiscussRouter::_('index.php?option=com_easydiscuss&view=post&id=' . $post->parent_id);
	$permalink  = $permalink . '#' . JText::_('COM_EASYDISCUSS_REPLY_PERMALINK') . '-' . $post->id;
}
else if($post->itemtype == 'category' )
{
    $permalink	= DiscussRouter::_( 'index.php?option=com_easydiscuss&view=categories&layout=listings&category_id=' . $post->id );
}

?>
<li class="<?php echo (DiscussHelper::isMine( $post->user->id )) ? 'mypost' : ''; ?>">

	<div class="discuss-item clearfix<?php echo $post->islock ? ' is-locked' : '';?><?php echo $post->isresolve ? ' is-resolved' : '';?><?php echo $post->isFeatured ? ' is-featured' : '';?>">
		<div class="feed-head float-l">
			<?php if ($system->config->get( 'layout_avatar' ) && $system->config->get( 'layout_avatar_in_post' )) { ?>
			<div class="discuss-avatar">
				<a href="<?php echo $post->user->getLink();?>" class="" title="<?php echo $this->escape( $post->user->getName() );?>">
					<img src="<?php echo $post->user->getAvatar();?>" alt="<?php echo $this->escape( $post->user->getName() );?>" <?php echo DiscussHelper::getHelper( 'EasySocial' )->getPopbox( $post->user->id );?> width="70" height="70"class="avatar" />
					
				</a>
			</div>
			<?php } ?>
			<?php echo $this->loadTemplate( 'ranks.php' , array( 'userId' => $post->user->id ) ); ?>
		</div>

		<div class="feed-body">
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
				<div class="discuss-role<?php echo ' ' . $post->user->getRoleLabelClassname(); ?>"><?php echo $this->escape($post->user->getRole()); ?></div>
				<?php } ?>

				<?php echo JText::_( 'COM_EASYDISCUSS_POSTED_IN' ); ?>
				<a href="<?php echo DiscussRouter::getCategoryRoute( $post->category_id ); ?>"><?php echo $post->category; ?></a>
				<div class="float-r">
					<span class="mark-featured"><i class="i i-star" rel="ed-tooltip" data-placement="top" data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_FEATURED' , true );?>"></i></span>
					<span class="mark-resolved"><i class="i i-check" rel="ed-tooltip" data-placement="top" data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_RESOLVED' , true );?>"></i></span>
					<time datetime="<?php echo $this->formatDate( '%Y-%m-%d' , $post->created ); ?>" class="muted">
						<?php echo $post->duration; ?>
					</time>
				</div>
			</div>
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
				<?php echo strip_tags($post->introtext); ?>

				<?php if( ($post->itemtype == 'posts' || $post->itemtype == 'replies') && $isUnRead ) { ?>
				<small class="label"><?php echo JText::_( 'COM_EASYDISCUSS_UNREAD' );?></small>
				<?php } ?>
				<div class="small">[<?php echo JText::_('COM_EASYDISCUSS_SEARCH_ITEM_' . strtoupper( $post->itemtype ) . '_TYPE'); ?>]</div>
			</div>
			<?php } ?>

			<?php if( $system->config->get( 'main_master_tags' && $system->config->get( 'main_tags' ) && $post->tags ) ){ ?>
			<div class="discuss-tags">
				<?php foreach( $post->tags as $tag ){ ?>
				<a class="butt butt-tag butt-default butt-s" href="<?php echo DiscussRouter::getTagRoute( $tag->id ); ?>"><?php echo $tag->title; ?></a>
				<?php } ?>
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
</li>
<?php endforeach; ?>
<?php else: ?>
<li>
	<div class="alert alert-error"><?php echo JText::_('COM_EASYDISCUSS_NO_RECORDS_FOUND') ?></div>
</li>
<?php endif; ?>
