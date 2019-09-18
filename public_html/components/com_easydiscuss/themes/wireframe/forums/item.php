<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>

<?php foreach ($thread as $post) { ?>
	<div class="ed-forum-item<?php echo $post->getHeaderClass(); ?>" <?php echo $post->getPriority() ? 'style="border-left: 3px solid ' . $post->getPriority()->color . ';"' : '';?>>
		<div class="o-row">

			<?php if ($this->config->get('post_priority')) { ?>
			<div class="o-col-sm o-col--top ed-forum-item__col-post-type">
				<i class="fa fa-file ed-forum-item__priority-icon"
					style="<?php echo $post->getPriority() ? 'color:' . $post->getPriority()->color : '';?>"
					<?php if ($post->getPriority()) { ?>
					data-ed-provide="tooltip"
					data-original-title="<?php echo JText::_($post->getPriority()->title);?>"
					<?php } ?>
				></i>
			</div>
			<?php } ?>

			<div class="o-col o-col--top">

				<h2 class="ed-forum-item__title">
					<?php if ($post->isFeatured()) { ?><i class="fa fa-star t-mr--sm"></i><?php } ?>
					<?php if ($post->isLocked()) { ?><i class="fa fa-lock t-mr--sm"></i><?php } ?>
					<?php if ($post->isProtected()) { ?><i class="fa fa-key t-mr--sm"></i><?php } ?>
					<?php if ($post->isPrivate()) { ?><i class="fa fa-eye t-mr--sm"></i><?php } ?>
					<a href="<?php echo $post->getPermalink();?>"><?php echo $post->getTitle(); ?></a>
				</h2>

				<div class="t-mt--sm">
					<ol class="g-list-inline ed-post-item__post-meta">
						<?php if ($post->isResolved() && $this->config->get('main_qna')) { ?>
						<li>
							<span class="o-label o-label--success-o"><?php echo JText::_('COM_EASYDISCUSS_RESOLVED'); ?></span>
						</li>
						<?php } ?>

						<?php if ($post->isStillNew()) { ?>
							<li><span class="o-label o-label--info-o"><?php echo JText::_('COM_EASYDISCUSS_NEW');?></span></li>
						<?php } ?>

						<!-- post status here: accepted, onhold, working rejected -->
						<?php if ($post->isPostRejected()) { ?>
							<li><span class="o-label o-label--info-o"><?php echo JText::_('COM_EASYDISCUSS_POST_STATUS_REJECT');?></span></li>
						<?php } ?>
						<?php if ($post->isPostOnhold()) { ?>
							<li><span class="o-label o-label--info-o"><?php echo JText::_('COM_EASYDISCUSS_POST_STATUS_ON_HOLD');?></span></li>
						<?php } ?>
						<?php if ($post->isPostAccepted()) { ?>
							<li><span class="o-label o-label--info-o"><?php echo JText::_('COM_EASYDISCUSS_POST_STATUS_ACCEPTED');?></span></li>
						<?php } ?>
						<?php if ($post->isPostWorkingOn()) { ?>
							<li><span class="o-label o-label--info-o"><?php echo JText::_('COM_EASYDISCUSS_POST_STATUS_WORKING_ON');?></span></li>
						<?php } ?>

						<!-- post type here -->
						<?php if ($post->getPostType()) { ?>
							<li><span class="o-label o-label--clean-o <?php echo $post->getTypeSuffix(); ?>"><?php echo $post->getPostType(); ?></span></li>
						<?php } ?>
					</ol>
				</div>

				<?php if ($this->config->get('main_master_tags') && $this->config->get('layout_categories_tags')) { ?>
					<?php if ($post->getTags()) { ?>
					<ol class="g-list-inline ed-post-meta-tag t-lg-mt--md">
						<?php foreach ($post->getTags() as $tag) { ?>
						<li>
							<a href="<?php echo EDR::getTagRoute($tag->id);?>">
								<i class="fa fa-tag"></i>&nbsp; <?php echo $tag->title;?>
							</a>
						</li>
						<?php } ?>
					</ol>
					<?php } ?>
				<?php } ?>

			</div>

			<div class="o-col-sm ed-forum-item__col-time">
				<div class="ed-forum-item__meta"><?php echo ED::date()->toLapsed($post->lastupdate); ?></div>
			</div>

			<div class="o-col-sm ed-forum-item__col-avatar t-text--center">
				<?php if (!$post->isAnonymous()) { ?>
					<?php echo $this->html('user.avatar', $post->getOwner(), array('rank' => false, 'status' => true, 'size' => 'sm')); ?>
				<?php } ?>
				<?php if ($post->isAnonymous()) { ?>
					<?php echo $this->html('user.anonymous', $post->getOwner(), $post->isAnonymous()); ?>
				<?php } ?>
			</div>

			<div class="o-col-sm ed-forum-item__col-avatar t-text--center">

			<?php if ($post->getLastReplier()) { ?>
				<?php echo $this->html('user.avatar', $post->getLastReplier(), array('rank' => false, 'status' => true, 'size' => 'sm'), $post->isAnonymous()); ?>
			<?php } else { ?>
				<?php echo JText::_('COM_EASYDISCUSS_FORUMS_NO_REPLIES'); ?>
			<?php } ?>
			</div>
		</div>
	</div>
<?php } ?>
