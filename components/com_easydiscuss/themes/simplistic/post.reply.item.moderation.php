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
<a name="<?php echo JText::_('COM_EASYDISCUSS_REPLY_PERMALINK') . '-' . $post->id;?>"></a>
<div id="dc_reply_<?php echo $post->id;?>" class="discuss-item discussReplyItem mt-10<?php echo $post->minimize ? ' is-minimized' : '';?>" data-id="<?php echo $post->id;?>">

	<!-- Discussion left side bar -->
	<div class="discuss-item-left discuss-user discuss-user-role-<?php echo $post->getOwner()->roleid; ?>">


		<a href="<?php echo $post->getOwner()->link;?>">
			<?php if ($system->config->get( 'layout_avatar' ) && $system->config->get( 'layout_avatar_in_post' )) { ?>
				<div class="discuss-avatar avatar-medium">
					<img src="<?php echo $post->getOwner()->avatar;?>" alt="<?php echo $this->escape( $post->getOwner()->name );?>" />
					<div class="discuss-role-title <?php echo $post->getOwner()->rolelabel; ?>"><?php echo $this->escape($post->getOwner()->role); ?></div>
				</div>
			<?php } ?>
			<div class="discuss-user-name mv-5">
				<?php echo $post->getOwner()->name; ?>
			</div>
		</a>

		<?php if( empty( $post->user_id ) ) { ?>
			<span class="fs-11">
				<?php echo $post->poster_name; ?>
			</span>
		<?php } else { ?>
			<?php echo $this->loadTemplate( 'ranks.php' , array( 'userId' => $post->getOwner()->id ) ); ?>

		<?php } ?>

		<?php echo $this->loadTemplate( 'post.conversation.php' , array( 'userId' => $post->getOwner()->id ) ); ?>
	</div>

	<!-- Discussion content area -->
	<div class="discuss-item-right">

		<div class="discuss-story">
			<div class="discuss-story-hd">
				<div class="discuss-action-options-1 fs-11">

					<div class="discuss-clock ml-10 pull-left">
						<i class="icon-ed-time"></i> <?php echo $this->formatDate( $system->config->get('layout_dateformat', '%A, %B %d %Y, %I:%M %p') , $post->created);?>
					</div>
				</div>
			</div>

			<div class="discuss-story-bd mb-10">

				<div class="ph-10">

					<div class="discuss-content">
						<div class="discuss-content-item">
							<?php echo DiscussHelper::bbcodeHtmlSwitcher( $post, 'reply', false ); ?>
							<br />
							<div id="comment-notification-<?php echo $post->id; ?>">
								<div class="alert alert-error" style="padding: 5px">
									<?php echo JText::_( 'COM_EASYDISCUSS_REPLY_UNDER_MODERATE' ); ?>
								</div>
							</div>

						</div>
					</div>

				</div>
			</div>

			<div class="fd-cf">
				<a class="pull-right" href="#<?php echo JText::_( 'COM_EASYDISCUSS_TOP_ANCHOR' , true );?>" title="<?php echo JText::_( 'COM_EASYDISCUSS_BACK_TO_TOP' , true );?>"><i class="icon-circle-arrow-up"></i></a>
			</div>
		</div>

	</div>

</div>
