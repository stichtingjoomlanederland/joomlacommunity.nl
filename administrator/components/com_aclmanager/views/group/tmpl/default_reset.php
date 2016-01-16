<?php
/**
 * @package		ACL Manager for Joomla
 * @copyright 	Copyright (c) 2011-2014 Sander Potjer
 * @license 	GNU General Public License version 3 or later
 */

// No direct access.
defined('_JEXEC') or die;
?>

<div class="modal hide fade" id="collapseModal">
	<div class="modal-header">
		<button type="button" role="presentation" class="close" data-dismiss="modal">x</button>
		<h3><?php echo JText::_('COM_ACLMANAGER_RESET_TITLE'); ?></h3>
	</div>
	<div class="modal-body">
		<p><?php echo JText::_('COM_ACLMANAGER_RESET_DESC'); ?></p>
	</div>
	<div class="modal-footer">
		<button class="btn" type="button" data-dismiss="modal">
			<?php echo JText::_('JCANCEL'); ?>
		</button>
		<button class="btn btn-primary" type="submit" onclick="Joomla.submitbutton('group.reset');">
			<?php echo JText::_('COM_ACLMANAGER_RESET_REVERT'); ?>
		</button>
		<button class="btn btn-primary" type="submit" onclick="Joomla.submitbutton('group.clear');">
			<?php echo JText::_('COM_ACLMANAGER_RESET_CLEAR'); ?>
		</button>
	</div>
</div>