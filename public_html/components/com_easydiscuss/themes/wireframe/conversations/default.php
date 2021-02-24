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
<div class="ed-conversations" data-ed-conversations-wrapper>
	<a href="javascript:void(0);" class="o-btn o-btn--default-o ed-convo-sidebar-toggle t-mb--lg" data-ed-conversations-toggle><?php echo JText::_('COM_EASYDISCUSS_TOGGLE_SIDEBAR');?></a>

	<div class="ed-convo">
		<div class="ed-convo__sidebar" data-ed-conversations-sidebar>
			<div class="ed-convo__sidebar-hd">
				<ul class="ed-convo-sidebar-tab">

					<li class="<?php echo $type == 'inbox' || !$type ? ' active' : '';?>" data-ed-conversations-tab data-type="inbox">
						<a href="<?php echo EDR::_('view=conversation');?>">
							<?php echo JText::sprintf('COM_EASYDISCUSS_TAB_CONVERSATIONS', $countInbox);?>
						</a>
					</li>
					<li class="<?php echo $type == 'archives' ? ' active' : '';?>" data-ed-conversations-tab data-type="archives">
						<a href="<?php echo EDR::_('view=conversation&type=archives');?>">
							<?php echo JText::sprintf('COM_EASYDISCUSS_TAB_ARCHIVES', $countArchives);?>
						</a>
					</li>
				</ul>
			</div>

			<div class="ed-convo__sidebar-scroll-area" style="height: 800px">
				<div class="ed-convo-list <?php echo !$lists ? ' is-empty' : '';?>" data-ed-conversations-list>
					<div class="o-loader-wrapper">
						<div class="o-loader"></div>
					</div>
					
					<div class="o-empty">
						<div class="o-empty__content">
							<div class="o-empty__text">
								<?php echo JText::_('COM_EASYDISCUSS_CONVERSATION_EMPTY_LIST');?>
							</div>
						</div>
					</div>
					
					<div class="ed-convo-list-items" data-ed-conversations-list-items>
						<?php echo $this->output('site/conversations/default.lists', array('lists' => $lists, 'activeConversation' => $activeConversation, 'type' => $type)); ?>
					</div>
					
				</div>
			</div>
		</div>

		<div class="ed-convo__content <?php echo $activeConversation ? ' has-active' : '';?>" data-ed-content-wrapper>

			<div class="ed-convo__content-hd">
				<div class="ed-convo__content-hd-title" data-ed-conversation-title>
					<?php if ($activeConversation) { ?>
						<?php echo $activeConversation->getParticipant()->getName();?>
					<?php } else { ?>
					&nbsp;
					<?php } ?>
				</div>
				<div class="ed-convo__content-action">

					<a href="<?php echo EDR::_('view=conversation&layout=compose');?>" class="o-btn o-btn--primary o-btn--sm"><?php echo JText::_('COM_EASYDISCUSS_NEW_MESSAGE');?></a>

					<div class="o-btn-group conversation-actions">
						<button class="o-btn o-btn--default-o o-btn--sm" type="button" data-ed-conversation-unread>
							<?php echo JText::_('COM_EASYDISCUSS_CONVERSATION_MARK_UNREAD');?>
						</button>
						<button class="o-btn o-btn--default-o o-btn--sm" type="button" data-ed-conversation-archive>
							<?php echo JText::_('COM_EASYDISCUSS_CONVERSATION_ARCHIVE');?>
						</button>
						<button class="o-btn o-btn--default-o o-btn--sm" type="button" data-ed-conversation-delete>
							<?php echo JText::_('COM_EASYDISCUSS_CONVERSATION_DELETE');?>
						</button>
					</div>
				</div>
			</div>


			<div class="ed-convo__content-scroll-area" style="height: 540px;" data-ed-conversation-contents-scroller>
				<div class="ed-convo-messages">
					<div class="<?php echo !$activeConversation ? ' is-empty' : '';?>" data-ed-conversation-contents-wrapper>

						<div class="message-list">
							<div class="t-mt--md" data-ed-conversation-first></div>

							<div data-ed-conversation-contents>
								<?php if ($activeConversation) { ?>
									<?php foreach ($activeConversation->getMessages() as $message) { ?>
										<?php echo $this->output('site/conversations/message', array('message' => $message)); ?>
									<?php } ?>
								<?php } ?>
							</div>

							<div style="margin-bottom: 15px;display: block;" data-ed-conversation-latest></div>
						</div>

						<div class="o-empty">
							<div class="o-empty__content">
								<div class="o-empty__text">
									<?php echo JText::_('COM_EASYDISCUSS_CONVERSATION_EMPTY');?>
								</div>
								<div class="o-empty__action">
									<a href="<?php echo EDR::_('view=conversation&layout=compose');?>" class="btn btn-primary">
										<i class="fa fa-comments"></i>&nbsp; <?php echo JText::_('COM_EASYDISCUSS_NEW_MESSAGE');?>
									</a>
								</div>
							</div>
						</div>

						<div class="o-loader">
							<div class="o-loader__content">
							</div>
						</div>
					</div>
				</div>
			</div>

			<form name="ed-convo-reply" action="<?php echo JRoute::_('index.php');?>" method="post" data-ed-conversation-reply-form data-id="<?php echo $activeConversation ? $activeConversation->id : '';?>" class="ed-convo-reply <?php echo !$activeConversation ? 't-hidden' : '';?>">
				<div class="ed-convo-composer">
					<div class="ed-convo-composer__bd">
						<div class="ed-convo-composer__editor t-mb--md t-p--md">
							<textarea class="input-sm form-control ed-convo-composer__textarea" name="message" autocomplete="off" placeholder="<?php echo JText::_('COM_EASYDISCUSS_WRITE_MESSAGE_PLACEHOLDER');?>" style="height: 150px;" data-ed-conversation-reply-textarea></textarea>
						</div>
					</div>

					<div class="ed-convo-composer__ft t-d--flex t-align-items--c">
						<div class="t-flex-grow--1 t-font-size--01" data-ed-conversation-reply-form-notice></div>
						<button class="o-btn o-btn--default-o  reply-button" data-ed-conversation-reply-button><?php echo JText::_('COM_EASYDISCUSS_BUTTON_REPLY'); ?></button>
					</div>
				</div>
			</form>

		</div>
	</div>
</div>
