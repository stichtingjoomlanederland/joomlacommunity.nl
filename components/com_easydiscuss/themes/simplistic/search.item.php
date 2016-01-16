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

	<div class="discuss-item<?php echo $post->islock ? ' is-locked' : '';?><?php echo $post->isresolve ? ' is-resolved' : '';?><?php echo $post->isFeatured ? ' is-featured' : '';?>">
		<div class="discuss-status">
			<i class="icon-ed-featured" rel="ed-tooltip" data-placement="top" data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_FEATURED' , true );?>"></i>
			<i class="icon-ed-locked" rel="ed-tooltip" data-placement="top" data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_LOCKED' , true );?>" ></i>
		</div>

		<div class="discuss-item-left discuss-user discuss-user-role-<?php echo $post->user->getRoleId(); ?>">
			<a href="<?php echo $post->user->getLink();?>" class="" title="<?php echo $this->escape( $post->user->getName() );?>">
				<?php if ( $system->config->get( 'layout_avatar' ) && $system->config->get( 'layout_avatar_in_post' )) { ?>
				<div class="discuss-avatar avatar-medium">
					<img src="<?php echo $post->user->getAvatar();?>" alt="<?php echo $this->escape( $post->user->getName() );?>" />

					<?php if($system->config->get( 'layout_profile_roles' ) && $post->user->getRole() ) { ?>
					<div class="discuss-role-title <?php echo $post->user->getRoleLabelClassname(); ?>"><?php echo $this->escape($post->user->getRole()); ?></div>
					<?php } ?>

				</div>

				<?php } ?>
				<div class="discuss-user-name mv-5">
					<?php if( !$post->user_id ){ ?>
						<?php echo $post->poster_name; ?>
					<?php } else { ?>
						<?php echo $post->user->getName();?>
					<?php } ?>
				</div>
			</a>

			<!-- User ranks -->
			<span class="discuss-user-rank fs-11">
				<?php if($system->config->get( 'main_ranking' )){ ?>
					<?php echo DiscussHelper::getUserRanks( $post->user->id ); ?>
				<?php } ?>
			</span>

			<!-- User graph -->
			<div class="discuss-user-graph">
				<div class="rank-bar mini" title="<?php echo $this->escape( DiscussHelper::getUserRanks( $post->user->id ) ); ?>">
					<div class="rank-progress" style="width: <?php echo DiscussHelper::getUserRankScore( $post->user->id ); ?>%"></div>
				</div>
			</div>

			<?php if( $system->config->get('main_badges') ){ ?>
			<ul class="unstyled discuss-badges-list">
				<?php foreach( $post->badges as $badge ){ ?>
				<li class="">
					<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=badges&layout=listings&id=' . $badge->get( 'id' ) );?>" >
						<img src="<?php echo $badge->getAvatar();?>" width="32" data-placement="top" rel="ed-tooltip" data-original-title="<?php echo $this->escape( $badge->get( 'title' ) );?>" />
					</a>
				</li>
				<?php } ?>
			</ul>
			<?php } ?>

			<?php echo $this->loadTemplate( 'post.conversation.php' , array( 'userId' => $post->user->id ) ); ?>

		</div>

		<div class="discuss-item-right">
			<div class="discuss-story">

				<!-- Discussion Title -->
				<div class="discuss-story-hd">
					<div class="ph-10">
						<?php if( $post->itemtype == 'posts' || $post->itemtype == 'replies') { ?>
						<div class="discuss-date fs-11">
							<i class="icon-ed-time"></i>
							<?php echo $post->duration; ?>
							<time datetime="<?php echo $this->formatDate( '%Y-%m-%d' , $post->created ); ?>"></time>
						</div>
						<?php } ?>

						<div class="small mb-5">
							<i class="icon-inbox"></i>
							<?php echo JText::_( 'COM_EASYDISCUSS_POSTED_IN' ); ?>
							<a href="<?php echo DiscussRouter::getCategoryRoute( $post->category_id ); ?>"><?php echo $post->category; ?></a>
						</div>

						<a class="" href="<?php echo $permalink; ?>">
							<h2 class="discuss-post-title" itemprop="name">
								<?php echo $post->title; ?>
								<?php if( ($post->itemtype == 'posts' || $post->itemtype == 'replies') && $isUnRead ) { ?>
								<small class="label"><?php echo JText::_( 'COM_EASYDISCUSS_UNREAD' );?></small>
								<?php } ?>
								<div class="small">[<?php echo JText::_('COM_EASYDISCUSS_SEARCH_ITEM_' . strtoupper( $post->itemtype ) . '_TYPE'); ?>]</div>
							</h2>
						</a>

						<?php if( $post->itemtype == 'replies' && !$system->config->get( 'layout_enableintrotext' )) { ?>
						<div><?php echo $post->content; ?></div>
						<?php } ?>

					</div>
				</div>

				<!-- Introtext -->
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
								<span><?php echo JText::_( 'COM_EASYDISCUSS_TAGS' );?>:</span>
								<?php foreach( $post->tags as $tag ){ ?>
									<a class="label" href="<?php echo DiscussRouter::getTagRoute( $tag->id ); ?>"><i class="icon-tag"></i><?php echo $tag->title; ?></a>
								<?php } ?>
							</div>
							<?php } ?>
						<?php } ?>
					</div>
				</div>


			</div>
		</div>

	</div>





</li>
<?php endforeach; ?>
<?php else: ?>
<li>
	<div class="alert alert-error"><?php echo JText::_('COM_EASYDISCUSS_NO_RECORDS_FOUND') ?></div>
</li>
<?php endif; ?>
