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
<?php foreach ($lists as $list) { ?>
	<div class="ed-convo__sidebar-item 
		<?php echo $list->id == $activeConversation->id ? ' is-active' : '';?>
		<?php echo $list->isNew() ? ' is-unread' : '';?>
		" 
		data-ed-conversations-item 
		data-id="<?php echo $list->id;?>"
	>
		<div class="o-media o-media--top">
			<a href="<?php echo EDR::_('view=conversation&id=' . $list->id);?>" data-link>
				<div class="o-media__image ">
					<?php echo $this->html('user.avatar', $list->getParticipant(), array('size' => 'sm')); ?>    
				</div>
				<div class="o-media__body">
					<div class="ed-user-name t-lg-mb--sm">
						<?php echo $list->getParticipant()->getName();?>
					</div>
					
					<div class="ed-convo-text t-lg-mb--sm">
						<?php echo $list->getLastMessage(); ?>
					</div>
					
					<div class="o-meta">
						<?php echo $list->getElapsedTime();?>
					</div>
				</div>
			</a>
			<div class="ed-convo__sidebar-action">
				<div class="pull-right">
					<a href="javascript:void(0);" class="o-btn o-btn--default-o o-btn--sm dropdown-toggle" data-ed-toggle="dropdown">
						<i class="fa fa-angle-down"></i>
					</a>

					<ul class="o-dropdown-menu o-dropdown-menu--right">
						<li data-ed-conversation-menu data-type="unread">
							<a class="o-dropdown__item " href="javascript:void(0);"><?php echo JText::_('COM_EASYDISCUSS_CONVERSATION_MARK_UNREAD');?></a>
						</li>
						<li data-ed-conversation-menu data-type="<?php echo $type; ?>">
							<a class="o-dropdown__item " href="javascript:void(0);"><?php echo JText::_('COM_EASYDISCUSS_CONVERSATION_' . strtoupper($type));?></a>
						</li>
						<li class="divider"></li>
						<li data-ed-conversation-menu data-type="delete">
							<a class="o-dropdown__item " href="javascript:void(0);"><?php echo JText::_('COM_EASYDISCUSS_CONVERSATION_DELETE');?></a>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
<?php } ?>
