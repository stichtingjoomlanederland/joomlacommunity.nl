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
<?php if ($post->canEdit() || $post->canFeature() || $post->canPrint() || $post->canDelete() || $post->canResolve() || $post->canLock() || $post->canReport() || $post->canReply()) { ?>
<div class="ed-adminbar" data-ed-post-actions-bar data-id="<?php echo $post->id;?>">

	<?php if ($post->canBranch()) { ?>
	<div class="btn-group">
		<a href="javascript:void(0);" class="btn btn-default btn-xs"
			data-ed-provide="tooltip"
			data-original-title="<?php echo JText::_('COM_EASYDISCUSS_BRANCH_TOOLTIP');?>"
			data-placement="bottom"
			data-ed-post-branch
		>
			<i class="fa fa-leaf"></i>
		</a>
	</div>
	<?php } ?>

	<?php if ($post->canReport()) { ?>
	<div class="btn-group">
		<a href="javascript:void(0);" class="btn btn-default btn-xs" rel="ed-tooltip" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_REPORT_TOOLTIP', true);?>"
			 data-ed-provide="tooltip"
			 data-placement="bottom"
			 data-ed-post-report
		><i class="fa fa-warning"></i></a>
	</div>
	<?php } ?>

	<?php if ($post->canPrint()) { ?>
	<div class="btn-group">
		<a href="<?php echo EDR::getPrintRoute( $post->id );?>"
			onclick="window.open(this.href,'win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no'); return false;"
			class="btn btn-default btn-xs" rel="ed-tooltip" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_PRINT', true);?>"><i class="fa fa-print"></i></a>
	</div>
	<?php } ?>

	<?php if ($post->isReply() && $post->canBanAuthor()) { ?>
	<div class="btn-group">
		<a href="javascript:void(0);" class="btn btn-default btn-xs"
		   data-ed-provide="tooltip"
		   data-original-title="<?php echo JText::_('COM_EASYDISCUSS_BAN_TOOLTIP');?>"
		   data-placement="bottom"
		   data-ed-post-ban-user
		><i class="fa fa-ban"></i></a>
	</div>
	<?php } ?>

	<?php if ($post->canAcceptAsAnswer()) { ?>	
	<div class="btn-group">
		<?php if (!$post->isAnswer()) { ?>
		<a class="btn btn-default btn-xs btn-ed-accept" data-ed-post-qna data-task="confirmAccept">
			<?php echo JText::_('COM_EASYDISCUSS_REPLY_ACCEPT');?>
		</a>
		<?php } ?>

		<?php if ($post->isAnswer()) { ?>
		<a class="btn btn-default btn-xs" data-ed-post-qna data-task="confirmReject">
			<?php echo JText::_('COM_EASYDISCUSS_REPLY_REJECT');?>
		</a>
		<?php } ?>
	</div>
	<?php } ?>

	<?php if ($post->canReply()) { ?>
	<div class="btn-group">
		<a href="javascript:void(0);" class="btn btn-default btn-xs" rel="ed-tooltip" data-ed-post-quote
			data-original-title="<?php echo JText::_('COM_EASYDISCUSS_QUOTE', true);?>"><i class="fa fa-quote-left"></i>
			<input type="hidden" class="raw_message" value="<?php echo $this->escape($post->content);?>" />
			<input type="hidden" class="raw_author" value="<?php echo $this->escape($post->getOwner()->getName());?>" /></a>
	</div>
	<?php } ?>

	<?php if ($post->canResolve()) { ?>
	<div class="btn-group">
		<a class="btn btn-default btn-xs ed-btn-resolve" href="javascript:void(0);" data-ed-post-resolve data-task="confirmAccept"><?php echo JText::_('COM_EASYDISCUSS_ENTRY_MARK_RESOLVED'); ?></a>
		<a class="btn btn-default btn-xs ed-btn-unresolve" href="javascript:void(0);" data-ed-post-resolve data-task="confirmReject"><?php echo JText::_('COM_EASYDISCUSS_ENTRY_MARK_UNRESOLVED'); ?></a>
	</div>
	<?php } ?>


	<?php if ($post->canEdit() || $post->canDelete() || $post->canMove() || $post->canFeature() || $post->canLock()) { ?>
	<div class="btn-group">
		<?php if ($post->canEdit()) { ?>

			<?php if ($post->isQuestion()) { ?>
				<a href="<?php echo EDR::getEditRoute($post->id);?>" class="btn btn-default btn-xs">
			<?php } else { ?>
				<?php if ($this->config->get('layout_editor') == 'bbcode'){ ?>
					<a href="javascript:void(0);" class="btn btn-default btn-xs" data-ed-edit-reply>
				<?php }else{ ?>
					<a href="<?php echo EDR::_('index.php?option=com_easydiscuss&view=post&layout=edit&id='. $post->id); ?>" class="btn btn-default btn-xs">
				<?php } ?>
			<?php } ?>
			<?php echo JText::_('COM_EASYDISCUSS_ENTRY_EDIT'); ?></a>
		<?php } ?>

		<?php if ($post->canDelete()) { ?>
			<a href="javascript:void(0);" class="btn btn-default btn-xs" data-ed-post-delete>
				<?php echo JText::_('COM_EASYDISCUSS_ENTRY_DELETE'); ?>
			</a>
		<?php } ?>

		<?php if ($post->canMove()) { ?>
		<a href="javascript:void(0);" class="btn btn-default btn-xs" data-ed-post-move>
			<?php echo JText::_('COM_EASYDISCUSS_MOVE_POST'); ?>
		</a>
		<a href="javascript:void(0);" class="btn btn-default btn-xs" data-ed-post-merge>
			<?php echo JText::_('COM_EASYDISCUSS_MERGE_POST'); ?>
		</a>		
		<?php } ?>

		<?php if ($post->canLock()) { ?>
		<a href="javascript:void(0);" class="btn btn-default btn-xs ed-btn-lock" data-ed-post-lock-buttons data-task="lock">
			<?php echo JText::_('COM_EASYDISCUSS_ENTRY_LOCK'); ?>
		</a>
		<a href="javascript:void(0);" class="btn btn-default btn-xs ed-btn-unlock" data-ed-post-lock-buttons data-task="unlock">
			<?php echo JText::_('COM_EASYDISCUSS_ENTRY_UNLOCK'); ?>
		</a>
		<?php } ?>

		<?php if (($post->canFeature() && $post->isQuestion()) || ($post->canLock() && $post->isQuestion())) { ?>
		<a class="btn btn-default btn-xs dropdown-toggle ed-adminbar__dropdown-toggle" data-ed-toggle="dropdown">
			<span class="caret"></span>
			<span><?php echo JText::_('COM_EASYDISCUSS_MODERATION_TOOLS'); ?></span>
		</a>

		<ul class="dropdown-menu  ed-adminbar__dropdown-menu-tools">

			<?php if ($post->canFeature()) { ?>
			<li>
				<a href="javascript:void(0);" class="ed-btn-featured" data-ed-post-feature data-task="feature">
					<?php echo JText::_('COM_EASYDISCUSS_ENTRY_FEATURE_THIS');?>
				</a>

				<a href="javascript:void(0);" class="ed-btn-unfeatured" data-ed-post-feature data-task="unfeature">
					<?php echo JText::_('COM_EASYDISCUSS_ENTRY_UNFEATURE_THIS');?>
				</a>
			</li>
			<li class="divider"></li>
			<?php } ?>

			<?php if ($post->canSetStatus('hold') && !$post->isPostOnhold()) { ?>
				<li>
					<a class="admin-on-hold" href="javascript:void(0);" data-ed-post-status data-status="hold">
						<?php echo JText::_('COM_EASYDISCUSS_ACL_OPTION_MARK_ON_HOLD'); ?>
					</a>
				</li>
			<?php } ?>

			<?php if ($post->canSetStatus('accepted') && !$post->isPostAccepted()) { ?>
				<li>
					<a class="admin-accepted" href="javascript:void(0);" data-ed-post-status data-status="accepted">
						<?php echo JText::_('COM_EASYDISCUSS_ACL_OPTION_MARK_ACCEPTED'); ?>
					</a>
				</li>
			<?php } ?>

			<?php if ($post->canSetStatus('working') && !$post->isPostWorkingOn()) { ?>
				<li>
					<a class="admin-workingon" href="javascript:void(0);" data-ed-post-status data-status="working">
						<?php echo JText::_('COM_EASYDISCUSS_ACL_OPTION_MARK_WORKING_ON'); ?>
					</a>
				</li>
			<?php } ?>

			<?php if ($post->canSetStatus('rejected') && !$post->isPostRejected()) { ?>
				<li>
					<a class="admin-reject" href="javascript:void(0);" data-ed-post-status data-status="rejected">
						<?php echo JText::_('COM_EASYDISCUSS_ACL_OPTION_MARK_REJECT'); ?>
					</a>
				</li>
			<?php } ?>

			<?php if ($post->canSetStatus('none') && $post->hasStatus()) { ?>
				<li>
					<a class="admin-no-status" href="javascript:void(0);" data-ed-post-status data-status="none">
						<?php echo JText::_('COM_EASYDISCUSS_ACL_OPTION_MARK_NO_STATUS'); ?>
					</a>
				</li>
			<?php } ?>

			<?php if ($post->canBanAuthor()) { ?>
			<li class="divider"></li>
			<li>
				<a class="admin-no-status" href="javascript:void(0);" data-ed-post-ban-user>
					<?php echo JText::_('COM_EASYDISCUSS_ACL_OPTION_BAN_THIS_USER'); ?>
				</a>
			</li>
			<?php } ?>
		</ul>
		<?php } ?>
	</div>
	<?php } ?>
</div>
<?php } ?>
