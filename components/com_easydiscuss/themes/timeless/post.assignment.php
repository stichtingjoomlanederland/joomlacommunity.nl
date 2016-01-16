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
<script type="text/javascript">
EasyDiscuss.ready(function($){
	$( '.post-moderator-list' ).click( function() {
		var userId = $(this).data('userid');
		var postId = $(this).data('postid');

		EasyDiscuss.ajax( "site.views.post.ajaxModeratorAssign",
			{
				"userId": userId,
				"postId": postId
			},
			{
				success: function(message) {
					$( '.discuss-post-assign' ).html(message);
				},
				fail: function(message) {
					$( '.discuss-post-assign' ).html(message);
				}
			});

	});
});
</script>
<?php if( !$post->assignment->assignee_id ){ ?>
	<?php echo JText::_( 'COM_EASYDISCUSS_ASSIGNMENT_NOT_ASSIGNED_YET' ); ?>
<?php } else { ?>
	<?php //echo JText::_('COM_EASYDISCUSS_ASSIGNMENT_POST_TO'); ?>
	<div class="discuss-assigned-name">
		Assigned to:
		<a href="<?php echo $post->assignee->getLink(); ?>"><?php echo $post->assignee->getName(); ?></a>
	</div>
<?php } ?>
<div class="dropdown_" style="display:block">
	<a class="btn btn-mini ml-5" data-foundry-toggle="dropdown">
		<i class="icon-plus-sign"></i>
		<?php if( !$post->assignment->assignee_id ){ ?>
		<?php echo JText::_( 'COM_EASYDISCUSS_ASSIGN_MODERATOR' ); ?>
		<?php } else { ?>
			<?php echo JText::_( 'COM_EASYDISCUSS_REASSIGN_MODERATOR' ); ?>
		<?php } ?>
	</a>
	<ul class="dropdown-menu">
		<?php if( !empty($moderators) ) { ?>
			<?php foreach ($moderators as $userid => $name) { ?>
				<li>
					<a href="javascript:void(0);" class="post-moderator-list" data-userid="<?php echo $userid; ?>" data-postid="<?php echo $post->id; ?>"><?php echo $name; ?></a>
				</li>
			<?php } ?>
		<?php } ?>
	</ul>
</div>
