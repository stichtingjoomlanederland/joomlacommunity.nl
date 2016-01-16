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
			<i class="icon-ed-locked" rel="ed-tooltip" data-placement="top" data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_LOCKED' , true );?>" ></i>

			<?php //if( !empty($post->password) ) { ?>
			<i class="icon-ed-key" rel="ed-tooltip" data-placement="top" data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_PROTECTED' , true );?>" ></i>
			<?php //} ?>
		</div>

		<div class="discuss-item-left discuss-user discuss-user-role-<?php echo $post->user->getRoleId(); ?>">

			<a href="<?php echo $post->user->getLink();?>" class="" title="<?php echo $this->escape( $post->user->getName() );?>">
				<?php if ($system->config->get( 'layout_avatar' ) && $system->config->get( 'layout_avatar_in_post' )) { ?>
				<div class="discuss-avatar avatar-medium <?php echo $post->user->getRoleLabelClassname(); ?>">
					<img src="<?php echo $post->user->getAvatar();?>" alt="<?php echo $this->escape( $post->user->getName() );?>"<?php echo DiscussHelper::getHelper( 'EasySocial' )->getPopbox( $post->user->id );?> />

					<?php if($system->config->get( 'layout_profile_roles' ) && $post->user->getRole() ) { ?>
					<div class="discuss-role-title"><?php echo $this->escape($post->user->getRole()); ?></div>
					<?php } ?>

				</div>
				<?php } ?>

			</a>

			<?php echo $this->loadTemplate( 'ranks.php' , array( 'userId' => $post->user->id ) ); ?>

			<?php echo $this->loadTemplate( 'online.php' , array( 'user' => $post->user ) ); ?>

			<?php if( $post->user->id ){ ?>
				<?php echo $this->loadTemplate( 'post.badges.php' , array( 'badges' => $post->user->getBadges() ) ); ?>
			<?php } ?>

			<?php echo $this->loadTemplate( 'post.conversation.php' , array( 'userId' => $post->user->id ) ); ?>
		</div>


		<div class="discuss-item-right">
			<div class="discuss-story">


				<div class="discuss-statistic pull-right">
					<div class="discuss-resolvedbar">
						<?php echo JText::_( 'COM_EASYDISCUSS_RESOLVED' ); ?>
					</div>

					<div class="status">
						<a href="<?php echo DiscussRouter::getPostRoute( $post->id );?>">
						<span class="item-count"><?php echo $replies = !empty( $post->reply ) ? $post->totalreplies : 0; ?></span>

						<span><?php echo $this->getNouns('COM_EASYDISCUSS_REPLIES', $replies); ?></span>
						</a>
					</div>
					<div class="views">
						<a href="<?php echo DiscussRouter::getPostRoute( $post->id );?>">
						<span class="item-count"><?php echo $post->hits; ?></span>
						<span><?php echo $this->getNouns( 'COM_EASYDISCUSS_VIEWS' , $post->hits );?></span>
						</a>
					</div>

					<?php if( $system->config->get( 'main_allowquestionvote' ) ){ ?>
					<div class="votes">
						<a href="<?php echo DiscussRouter::getPostRoute( $post->id );?>">
						<span class="item-count"> <?php echo $post->sum_totalvote; ?></span>
						<span><?php echo $this->getNouns( 'COM_EASYDISCUSS_VOTES_STRING' , $post->sum_totalvote );?></span>
						</a>
					</div>
					<?php } ?>

					<div class="likes">
						<a href="<?php echo DiscussRouter::getPostRoute( $post->id );?>">
						<span class="item-count"> <?php echo $post->num_likes; ?></span>
						<span><?php echo $this->getNouns( 'COM_EASYDISCUSS_LIKES_STRING' , $post->num_likes );?></span>
						</a>
					</div>

				</div>


				<!-- Introtext -->
				<div class="discuss-story-bd">
					<div class="ph-10">
						<div class="postStatus label label-info label-post_status-<?php echo $post->getStatusClass(); ?>"><?php echo $post->getStatusMessage(); ?></div>
						<div class="postType label label-important label-post_type<?php echo $post->post_type_suffix; ?>"><?php echo $post->post_type_title; ?></div>
						<a class="" href="<?php echo DiscussRouter::getPostRoute( $post->id );?>">
							<h2 class="discuss-post-title" itemprop="name">
								<?php echo $post->title; ?>
								<?php if( $isRead ) { ?>
								<span class="label label-unread"><?php echo JText::_( 'COM_EASYDISCUSS_NEW' );?></span>
								<?php } ?>
							</h2>
						</a>

						<?php echo $this->loadTemplate( 'post.tags.php' , array( 'tags' => $post->tags ) ); ?>

						<div class="small mb-5">
							<!-- <i class="icon-inbox"></i> -->
							<?php echo JText::_( 'COM_EASYDISCUSS_POSTED_BY' ); ?>
							<strong class="discuss-user-name mv-5">
								<?php if( !$post->user_id ){ ?>
									<?php echo $post->poster_name; ?>
								<?php } else { ?>
									<?php echo $post->user->getName();?>
								<?php } ?>
							</strong>



							<?php echo JText::_( 'COM_EASYDISCUSS_POSTED_IN' ); ?>
							<a href="<?php echo DiscussRouter::getCategoryRoute( $post->category_id ); ?>"><?php echo $post->category; ?></a>
						</div>

						<div class="discuss-action-options mt-5">

							<div class="discuss-date fs-11 pull-left mr-10">
								<i class="icon-ed-time"></i> <?php echo $post->duration; ?>
								<time datetime="<?php echo $this->formatDate( '%Y-%m-%d' , $post->created ); ?>"></time>

								<?php if( $post->polls ){ ?>
									<span class="with-polls">- <?php echo JText::_( 'COM_EASYDISCUSS_WITH_POLLS' );?> <i class="icon-tasks"></i></span>
								<?php } ?>

								<?php if( $post->attachments ){ ?>
									<span class="with-attachments">- <?php echo JText::_( 'COM_EASYDISCUSS_WITH_ATTACHMENTS' );?> <i class="icon-file"></i></span>
								<?php } ?>
							</div>

							<div class="discuss-statistic pull-left">

							</div><!-- discuss-statistic -->

							<div class="discuss-last-replied">
								<div class=" pull-">
									<?php if( isset( $post->reply ) ){ ?>
										<?php if( $post->reply->id ){ ?>
											<?php if( $system->config->get( 'layout_avatar' ) ) { ?>
											<a href="<?php echo $post->reply->getLink();?>" class="pull-left ml-5" title="<?php echo $post->reply->getName(); ?>">
												<img src="<?php echo $post->reply->getAvatar();?>" alt="<?php echo $this->escape( $post->reply->getName() );?>"<?php echo DiscussHelper::getHelper( 'EasySocial' )->getPopbox( $post->reply->id );?> />
											</a>
											<?php } ?>
										<?php } else { ?>
											<?php echo $post->reply->poster_name; ?>
										<?php } ?>

										<?php $lastReply = DiscussHelper::getModel( 'Posts' )->getLastReply( $post->id ); ?>
										<a class="ml-5" href="<?php echo DiscussRouter::getPostRoute( $post->id ) . '#' . JText::_('COM_EASYDISCUSS_REPLY_PERMALINK') . '-' . $lastReply->id;?>" title="<?php echo JText::_('COM_EASYDISCUSS_VIEW_LAST_REPLY'); ?>"><?php echo JText::_( 'COM_EASYDISCUSS_VIEW_LAST_REPLY' );?></a>

									<?php } ?>
								</div>
							</div><!-- pull-right -->

						</div><!-- discuss-action-options -->


						<?php if($system->config->get( 'layout_enableintrotext' ) ){ ?>
						<div class="discuss-intro-text">
							<?php echo $post->introtext; ?>
						</div>
						<?php } ?>



					</div>
				</div>

				<div class="discuss-story-ft">


				</div>

			</div>
		</div>

	</div><!-- item -->
</li>
