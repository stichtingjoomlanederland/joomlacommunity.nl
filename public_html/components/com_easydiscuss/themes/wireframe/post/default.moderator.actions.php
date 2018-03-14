<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<?php if ($post->isPending() && $post->canModerate()) { ?>
<div class="ed-adminbar" data-ed-post-actions-bar data-id="<?php echo $post->id;?>">
	<div class="btn-group">
		<?php if ($this->config->get('layout_editor') == 'bbcode'){ ?>
			<a href="javascript:void(0);" class="btn btn-default btn-xs" data-ed-edit-reply>
		<?php } else { ?>
			<a href="<?php echo EDR::_('index.php?option=com_easydiscuss&view=post&layout=edit&id='. $post->id); ?>" class="btn btn-default btn-xs">
		<?php } ?>
		<?php echo JText::_('COM_EASYDISCUSS_ENTRY_EDIT'); ?></a>
	</div>

	<div class="btn-group">
		<a class="btn btn-success btn-xs" data-ed-post-moderation data-task="confirmApprovePending">
			<?php echo JText::_('COM_EASYDISCUSS_BUTTON_APPROVE_REPLY'); ?>
		</a>
	</div>
	<div class="btn-group">
		<a class="btn btn-danger btn-xs" data-ed-post-moderation data-task="confirmRejectPending">
			<?php echo JText::_('COM_EASYDISCUSS_BUTTON_REJECT_REPLY'); ?>
		</a>
	</div>
</div>
<?php } ?>