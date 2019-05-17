<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access'); ?>

<div class="rscomments-my-comments <?php if ($this->config->modal == 2) echo 'rscomments-popup-padding'; ?>">
	<?php if ($this->comments) { ?>
	<?php foreach ($this->comments as $comment) { ?>
	<?php $name	= RSCommentsHelper::name($comment, $this->permissions); ?>
	<?php $ownComment = $this->user->guest ? ($comment->sid == $this->sid) : ($comment->uid == $this->user->id); ?>
	<?php $canDelete = ((isset($this->permissions['delete_own_comment']) && $this->permissions['delete_own_comment']) && $ownComment ) || (isset($this->permissions['delete_comments']) && $this->permissions['delete_comments']); ?>
	<div class="rscomments-my-comment">
		<div class="rscomments-my-comment-title">
			<i class="fa fa-user"></i> <strong><?php echo $name['cleanname']; ?> <?php if ($comment->email) { ?>(<?php echo $comment->email; ?>)<?php } ?></strong> <?php echo JText::_('COM_RSCOMMENTS_WROTE_ON'); ?> <i class="fa fa-calendar"></i> <strong><?php echo  RSCommentsHelper::showDate($comment->date); ?></strong>
		</div>
		<div class="rscomments-my-comment-text">
			<?php echo RSCommentsHelper::parseComment($comment->comment, $this->permissions, true); ?>
		</div>
		<div class="rscomments-my-comment-options">
			<?php if ($comment->published) { ?>
			<a href="<?php echo JURI::root().base64_decode($comment->url); ?>#rscomment<?php echo $comment->IdComment; ?>" onclick="window.parent.jQuery('#rscomments-mycomments').modal('hide');" class="btn btn-info" target="_top"><i class="fa fa-eye"></i> <?php echo JText::_('COM_RSCOMMENTS_VIEW'); ?></a>
			<?php } ?>
			<?php if ($canDelete) { ?>
			<a href="<?php echo JRoute::_('index.php?option=com_rscomments&task=removecomment&id='.$comment->IdComment, false); ?>" class="btn btn-danger"><i class="fa fa-trash"></i> <?php echo JText::_('COM_RSCOMMENTS_DELETE'); ?></a>
			<?php } ?>
		</div>
	</div>
	<?php } ?>
	<div class="pagination center">
		<?php echo $this->pagination->getPagesLinks(); ?>
	</div>
	<?php } else { ?>
	<div class="alert alert-info">
		<?php echo JText::_('COM_RSCOMMENTS_USER_NO_COMMENTS'); ?>
	</div>
	<?php } ?>
</div>