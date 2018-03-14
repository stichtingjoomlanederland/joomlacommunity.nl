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
defined('_JEXEC') or die('Restricted access');

if (!isset($post->assignment)) {
	$post->getAssignment();
}

?>
<div class="small" data-ed-post-id=<?php echo $post->id;?>>
	<?php if (!$post->assignment->assignee_id){ ?>
		<?php echo JText::_('COM_EASYDISCUSS_ASSIGNMENT_NOT_ASSIGNED_YET'); ?>
	<?php } else { ?>
		<?php echo JText::_('COM_EASYDISCUSS_ASSIGNMENT_POST_TO'); ?>:
		<a href="<?php echo $post->assignee->getLink(); ?>"><?php echo $post->assignee->getName(); ?></a>
	<?php } ?>
	<div class="dropdown " style="display:inline-block">
		<a class="btn btn-default btn-xs dropdown-toggle" data-ed-post-moderator-button data-ed-toggle="dropdown">
		<i class="fa fa-plus-circle"></i>
			<?php if (!$post->assignment->assignee_id) { ?>
				<?php echo JText::_('COM_EASYDISCUSS_ASSIGN_MODERATOR'); ?>
			<?php } else { ?>
				<?php echo JText::_('COM_EASYDISCUSS_REASSIGN_MODERATOR'); ?>
			<?php } ?>
		</a>

		<ul class="dropdown-menu moderatorList" data-ed-post-moderator-listing>
			<?php if (!empty($moderators)) { ?>
				<?php echo $this->output('site/post/post.assignment.item', array('moderators' => $moderators, 'postId' => $post->id)); ?>
			<?php } else { ?>
				<div class="ed-moderator-list is-loading" data-ed-post-moderator-loading>
			  		<a href="javascript:void(0)" class="btn btn-ed-moderator btn-xs"><i class="fa fa-spinner fa-spin"></i> <?php echo JText::_('COM_EASYDISCUSS_LOADING');?></a>
				</div>				
			<?php } ?>
		</ul>
	</div>
</div>
