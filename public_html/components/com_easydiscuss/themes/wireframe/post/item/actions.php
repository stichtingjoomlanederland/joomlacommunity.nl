<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php if ($post->canEdit() || $post->canFeature() || $post->canPrint() || $post->canDelete() || $post->canResolve() || $post->canLock() || $post->canReport() || $post->canReply() || ($post->isPending() && $post->canModerate())) { ?>
<div class="ed-entry-actions-toolbar" role="toolbar" 
	data-ed-actions
	data-id="<?php echo $post->id;?>"
	data-editor="<?php echo $this->config->get('layout_editor'); ?>"
>
	<?php echo $this->html('triggers.html', 'easydiscuss', 'beforeDisplayActions', [&$post]); ?>
	
	<div class="ed-entry-actions-group sm:t-flex-grow--1 sm:t-justify-content--se" role="group">

		<?php if ($post->canReply()) { ?>
		<button type="button" class="o-btn"
			data-ed-post-quote
			data-ed-provide="tooltip"
			data-title="<?php echo JText::_('COM_EASYDISCUSS_QUOTE', true);?>"
		>
			<i class="fas fa-quote-left"></i>
			<input type="hidden" class="raw_message" value="<?php echo $this->escape($post->content);?>" />
			<input type="hidden" class="raw_author" value="<?php echo $this->escape($post->getOwner()->getName());?>" /></a>
		</button>
		<?php } ?>


		<?php if ($post->canPrint()) { ?>
		<a href="<?php echo EDR::getPrintRoute($post->id);?>" target="_blank" 
			onclick="window.open(this.href,'win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no'); return false;"
			class="o-btn"
			data-ed-provide="tooltip"
			data-title="<?php echo JText::_('COM_EASYDISCUSS_PRINT', true);?>"
		>
			<i class="fas fa-print"></i>
		</a>
		<?php } ?>

		<?php if ($post->canReport()) { ?>
		<button type="button" class="o-btn"
			data-ed-dialogs
			data-namespace="site/views/reports/dialog"
			data-ed-provide="tooltip"
			data-title="<?php echo JText::_('COM_EASYDISCUSS_REPORT_TOOLTIP', true);?>"
		>
			<i class="fas fa-exclamation-triangle"></i>
		</button>
		<?php } ?>
	</div>

	<?php if ($post->canAcceptAsAnswer()) { ?>
	
		<?php if (!$post->isAnswer()) { ?>
		<div class="ed-entry-actions-group t-border--200" role="group">
			<button type="button" class="o-btn o-btn--success o-btn--accepted" 
				data-ed-dialogs 
				data-namespace="site/views/post/confirmAccept"
			>
				<?php echo JText::_('COM_EASYDISCUSS_REPLY_ACCEPT');?>
			</button>
		</div>
		<?php } ?>

		<?php if ($post->isAnswer()) { ?>
		<div class="ed-entry-actions-group" role="group">
			<button type="button" class="o-btn o-btn--reject" 
				data-ed-dialogs 
				data-namespace="site/views/post/confirmReject"
			>
				<?php echo JText::_('COM_EASYDISCUSS_REPLY_REJECT');?>
			</button>
		</div>
		<?php } ?>
	
	<?php } ?>

	<?php if ($this->config->get('main_labels') && $post->isQuestion() && $post->canLabel() && $labels) { ?>
		<?php echo $this->output('site/post/item/actions.labels', ['currentLabel' => $post->getCurrentLabel(), 'labels' => $labels]); ?>
	<?php } ?>

	<?php if ($post->canEdit() || $post->canResolve() || $post->canFeature() || $post->canBranch() || $post->canLock() || $post->canMove() || $post->canBanAuthor() || $post->canDelete()) { ?>
	<div class="ed-entry-actions-group o-dropdown" role="group">
		<button id="post-actions" type="button" class="o-btn dropdown-toggle_" data-ed-toggle="dropdown">
			<i class="fas fa-ellipsis-h"></i>
		</button>

		<ul class="o-dropdown-menu o-dropdown-menu-right t-mt--2xs" aria-labelledby="post-actions">
			<?php if ($post->canEdit()) { ?>
			<li>
				<?php if ($post->isQuestion()) { ?>
					<a href="<?php echo EDR::getEditRoute($post->id);?>" class="o-dropdown__item">
				<?php } ?>

				<?php if ($post->isReply()) { ?>
					<?php if ($this->config->get('layout_editor') == 'bbcode'){ ?>
						<a href="javascript:void(0);" class="o-dropdown__item" data-ed-edit-reply>
					<?php }else{ ?>
						<a href="<?php echo EDR::_('index.php?option=com_easydiscuss&view=post&layout=edit&id='. $post->id); ?>" class="o-dropdown__item">
					<?php } ?>
				<?php } ?>
				<?php echo JText::_('COM_EASYDISCUSS_ENTRY_EDIT'); ?></a>
			</li>
			<?php } ?>

			<?php if ($post->canResolve()) { ?>
			<?php echo $this->html('dropdown.divider'); ?>
			<li>
				<a href="javascript:void(0);" class="o-dropdown__item o-dropdown__item--resolve" 
					data-ed-dialogs 
					data-namespace="site/views/post/confirmAccept"
				>
					<?php echo JText::_('COM_EASYDISCUSS_ENTRY_MARK_RESOLVED'); ?>
				</a>
				<a href="javascript:void(0);" class="o-dropdown__item o-dropdown__item--unresolve" 
					data-ed-dialogs
					data-namespace="site/views/post/confirmReject"
				>
					<?php echo JText::_('COM_EASYDISCUSS_ENTRY_MARK_UNRESOLVED'); ?>
				</a>
			</li>
			<?php } ?>

			<?php if ($post->canFeature()) { ?>
			<?php echo $this->html('dropdown.divider'); ?>
			<li>
				<a href="javascript:void(0);" class="o-dropdown__item o-dropdown__item--feature" 
					data-ed-dialogs 
					data-namespace="site/views/post/feature"
				>
					<?php echo JText::_('COM_EASYDISCUSS_ENTRY_FEATURE_THIS');?>
				</a>

				<a href="javascript:void(0);" class="o-dropdown__item o-dropdown__item--unfeature" 
					data-ed-dialogs 
					data-namespace="site/views/post/unfeature"
				>
					<?php echo JText::_('COM_EASYDISCUSS_ENTRY_UNFEATURE_THIS');?>
				</a>
			</li>
			<?php } ?>

			<?php if ($post->canBranch()) { ?>
			<?php echo $this->html('dropdown.divider'); ?>
			<li>
				<a href="javascript:void(0);" class="o-dropdown__item" 
					data-ed-dialogs
					data-namespace="site/views/post/branchForm"
				>
					<?php echo JText::_('COM_ED_BRANCH');?>
				</a>
			</li>
			<?php } ?>

			<?php if ($post->canLock()) { ?>
			<?php echo $this->html('dropdown.divider'); ?>
			<li>
				<a href="javascript:void(0);" class="o-dropdown__item o-dropdown__item--lock" data-ed-post-lock-buttons data-task="lock">
					<?php echo JText::_('COM_EASYDISCUSS_ENTRY_LOCK'); ?>
				</a>
				<a href="javascript:void(0);" class="o-dropdown__item o-dropdown__item--unlock" data-ed-post-lock-buttons data-task="unlock">
					<?php echo JText::_('COM_EASYDISCUSS_ENTRY_UNLOCK'); ?>
				</a>
			</li>
			<?php } ?>

			<?php if ($post->canMove()) { ?>
			<?php echo $this->html('dropdown.divider'); ?>	
			<li>
				<a href="javascript:void(0);" class="o-dropdown__item" 
					data-ed-dialogs
					data-namespace="site/views/post/move"
				>
					<?php echo JText::_('COM_EASYDISCUSS_MOVE_POST'); ?>
				</a>
			</li>
			<li>
				<a href="javascript:void(0);" class="o-dropdown__item" data-ed-post-merge>
					<?php echo JText::_('COM_EASYDISCUSS_MERGE_POST'); ?>
				</a>
			</li>
			<?php } ?>

			<?php if ($post->canBanAuthor()) { ?>
				<?php echo $this->html('dropdown.divider'); ?>
				<li>
					<a href="javascript:void(0);" class="o-dropdown__item" 
						data-ed-dialogs
						data-namespace="site/views/post/banForm"
					>
						<?php echo JText::_('COM_EASYDISCUSS_ACL_OPTION_BAN_THIS_USER'); ?>
					</a>
				</li>
			<?php } ?>

			<?php if ($post->isPending() && $post->canModerate()) { ?>
				<?php echo $this->html('dropdown.divider'); ?>
				<li>
					<a href="javascript:void(0);" class="o-dropdown__item" 
						data-ed-dialogs 
						data-namespace="site/views/post/confirmApprovePending"
					>
						<?php echo JText::_('COM_EASYDISCUSS_BUTTON_APPROVE_REPLY'); ?>
					</a>
				</li>
				<li>
					<a href="javascript:void(0);" class="o-dropdown__item"
						data-ed-dialogs
						data-namespace="site/views/post/confirmRejectPending"
					>
						<?php echo JText::_('COM_EASYDISCUSS_BUTTON_REJECT_REPLY'); ?>
					</a>
				</li>
			<?php } ?>

			<?php if ($post->canDelete()) { ?>
				<?php echo $this->html('dropdown.divider'); ?>
				<li>
					<a href="javascript:void(0);" class="o-dropdown__item" 
						data-ed-dialogs
						data-namespace="site/views/post/confirmDelete"
					>
						<?php echo JText::_('COM_EASYDISCUSS_ENTRY_DELETE'); ?>
					</a>
				</li>
			<?php } ?>
			<?php echo $this->html('triggers.html', 'easydiscuss', 'afterDisplayActionsDropdown', [&$post]); ?>
		</ul>
	</div>
	<?php } ?>

	<?php echo $this->html('triggers.html', 'easydiscuss', 'afterDisplayActions', [&$post]); ?>
</div>
<?php } ?>
