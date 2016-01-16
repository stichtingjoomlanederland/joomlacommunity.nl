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
<div class="discuss-item discussQuestion mt-10 <?php echo $post->islock ? ' is-locked' : '';?><?php echo !empty($post->password) ? ' is-protected' : '';?><?php echo $post->isresolve ? ' is-resolved' : '';?><?php echo $post->isFeatured() ? ' is-featured' : '';?><?php echo $post->isPollLocked() ? ' is-poll-lock' : '';?>" data-id="<?php echo $post->id;?>">
	<!-- Discussion title -->
	<div class="discuss-item-hd">
		<a href="<?php echo DiscussRouter::getPostRoute( $post->id );?>" class="">
			<h2 class="discuss-post-title pull-left">
				<i data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_LOCKED_DESC' );?>" data-placement="top" rel="ed-tooltip" class="icon-lock"></i>
				<?php if( !empty($post->password) ) { ?>
				<i data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_PROTECTED_DESC' );?>" data-placement="top" rel="ed-tooltip" class="icon-key"></i>
				<?php } ?>
				<?php echo $post->title; ?>
			</h2>
		</a>

		<div class="pull-right mt-15 mr-10">
			<?php echo DiscussHelper::getSubscriptionHTML($system->my->id, $post->id, 'post'); ?>
		</div>

		<div class="row-fluid">

		</div>
	</div>

	<div class="discuss-post-sub-header">
		<div class="row-fluid">
			<div class="discuss-category-meta pull-left">
				<div class="pull-left fs-11">
					<i class="icon-inbox"></i>
					<?php echo JText::_( 'COM_EASYDISCUSS_POSTED_IN' );?> <a href="<?php echo DiscussRouter::getCategoryRoute( $category->id );?>"><?php echo $category->getTitle();?></a>
				</div>
			</div>

			<div class="discuss-status">
				<i class="icon-ed-featured" data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_FEATURED_DESC' );?>" data-placement="top" rel="ed-tooltip" ></i>
				<i class="icon-ed-resolved " rel="ed-tooltip" data-placement="top" data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_RESOLVED' , true );?>"></i>
			</div>


			<?php if( $access->canAssign() ) { ?>
				<!-- Post assignments -->
				<div class="discuss-post-assign">
					<?php echo $this->loadTemplate( 'post.assignment.php' , array( 'post' => $post, $moderators ) ); ?>
				</div>
			<?php } ?>

		</div>
	</div>

	<div class="discuss-admin-row">
		<div class="row-fluid">
			<div class="pull-left">
				<div class="discuss-user discuss-user-role-<?php echo $post->getOwner()->roleid; ?>">

					<a class="" href="<?php echo $post->getOwner()->link;?>">
						<?php if ($system->config->get( 'layout_avatar' ) && $system->config->get( 'layout_avatar_in_post' )) { ?>
							<div class="discuss-avatar avatar-medium <?php echo $post->getOwner()->rolelabel; ?>">
								<img src="<?php echo $post->getOwner()->avatar;?>" alt="<?php echo $this->escape( $post->getOwner()->name );?>" />

							</div>
						<?php } ?>

					</a>

					<?php echo $this->loadTemplate( 'ranks.php' , array( 'userId' => $post->getOwner()->id ) ); ?>

					<?php echo $this->loadTemplate( 'online.php' , array( 'user' => $post->user ) ); ?>

					<div class="discuss-role-title"><?php echo $this->escape($post->getOwner()->role); ?></div>



				</div>






			<?php echo $this->loadTemplate( 'post.conversation.php' , array( 'userId' => $post->getOwner()->id ) ); ?>
			</div>
			<!-- <div class="pull-right"> -->
				<div class="pull-right">
					<?php echo $this->loadTemplate( 'post.actions.php' , array( 'access' => $access , 'post' => $post ) ); ?>
				</div>

			<!-- </div> -->



		</div>

		<div class="pl-10">
			<div class="discuss-user-name fs-11">
				<?php echo JText::_('COM_EASYDISCUSS_BY'); ?>
				<a class="" href="<?php echo $post->getOwner()->link;?>">
					<strong>
						<?php if( !$post->user_id ){ ?>
							<?php echo $post->poster_name; ?>
						<?php } else { ?>
							<?php echo $post->getOwner()->name; ?>
						<?php } ?>
					</strong>
				</a>
			</div>
			<div class="discuss-action-options-1 fs-11 pull-">
				<div class="discuss-clock ">
					<?php echo $this->formatDate( $system->config->get('layout_dateformat', '%A, %B %d %Y, %I:%M %p') , $post->created);?>
				</div>
			</div>
		</div>

	</div>

	<!-- Discussion left side bar -->


	<!-- Discussion content area -->
	<div class="discuss-item-right">
		<div class="discuss-story">

			<div class="discuss-story-bd">
				<div class="ph-10">

					<?php if( !$post->isProtected() || DiscussHelper::isModerator( $post->category_id ) ){ ?>
						<div class="discuss-content">
							<?php if( $system->config->get( 'main_allowquestionvote' ) ){ ?>
								<?php echo $this->loadTemplate( 'post.vote.php' , array( 'access' => $access , 'post' => $post ) ); ?>
							<?php } ?>

							<div class="discuss-content-item">
								<?php echo DiscussHelper::bbcodeHtmlSwitcher( $post, 'question', false ); ?>
							</div>

							<!-- polls -->
							<?php echo $this->getFieldHTML( true , $post ); ?>



							<?php echo $this->loadTemplate( 'post.customfields.php' ); ?>

							<?php echo $this->loadTemplate( 'post.tags.php' , array( 'tags' => $tags ) ); ?>
						</div>

						<div class="discuss-users-action row-fluid mb-10">
							<?php echo $this->loadTemplate( 'post.likes.php' , array( 'post' => $post ) ); ?>
						</div>

						<div class="discuss-users-action row-fluid">
							<?php echo $this->loadTemplate( 'post.comments.php' , array( 'reply' => $post, 'question' => $post  ) ); ?>


						</div>



						<?php echo $this->loadTemplate( 'post.reply.comments.php' , array( 'post' => $post ) ); ?>

						<?php echo $this->loadTemplate( 'post.location.php' , array( 'post' => $post ) ); ?>

						<?php echo DiscussHelper::showSocialButtons( $post, 'vertical' ); ?>

					<?php } else { ?>
						<?php echo $this->loadTemplate( 'entry.password.php' , array( 'post' => $post ) ); ?>
					<?php } ?>
				</div>

			</div>

			<?php echo $this->loadTemplate( 'post.signature.php' , array( 'signature' => $post->getOwner()->signature ) ); ?>

			<div style="clear: both;"></div>

			<div class="discuss-story-ft">

				<div class="pull-left">
					<?php echo $this->loadTemplate( 'post.favourites.php' , array( 'post' => $post ) ); ?>
				</div>

			</div>

		</div>
	</div>

</div>
