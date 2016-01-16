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

if( !isset($post->assignment) )
{
	$post->getAssignment();
}
?>
<div>
	<?php if( !$post->assignment->assignee_id ){ ?>
		<?php // echo JText::_( 'COM_EASYDISCUSS_ASSIGNMENT_NOT_ASSIGNED_YET' ); ?>
	<?php } else { ?>
		<?php echo JText::_('COM_EASYDISCUSS_ASSIGNMENT_POST_TO'); ?>:
		<a href="<?php echo $post->assignee->getLink(); ?>"><?php echo $post->assignee->getName(); ?></a>
		&nbsp;
	<?php } ?>
	<div class="dropdown_" style="display:inline-block">
		<a class="butt butt-primary moderatorBtn" data-foundry-toggle="dropdown">
			<i class="i i-plus muted"></i>
			&nbsp;
			<?php if( !$post->assignment->assignee_id ){ ?>
			<?php echo JText::_( 'COM_EASYDISCUSS_ASSIGN_MODERATOR' ); ?>
			<?php } else { ?>
				<?php echo JText::_( 'COM_EASYDISCUSS_REASSIGN_MODERATOR' ); ?>
			<?php } ?>
		</a>
		<ul class="dropdown-menu moderatorList">
			<?php if( !empty($moderators) ) { ?>
				<?php echo $this->loadTemplate( 'post.assignment.item.php' , array( 'moderators' => $moderators, 'postId' => $post->id ) ); ?>
			<?php } else { ?>
				<li style="height:10px;"><div class="discuss-loader" style="margin-left:15px;"></div></li>
			<?php } ?>
		</ul>
	</div>

</div>
