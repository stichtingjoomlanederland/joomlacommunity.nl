<?php
/**
 * @package		ACL Manager for Joomla
 * @copyright 	Copyright (c) 2011-2016 Sander Potjer
 * @license 	GNU General Public License version 3 or later
 */

// No direct access.
defined('_JEXEC') or die;

// Initialise variable
$id = $this->state->get('filter.group_id');
?>

<form action="<?php echo JRoute::_('index.php?option=com_aclmanager&view=group&id='.$id);?>" method="post" name="adminForm">
	<div id="resetinfo">
		<div class="width-100">
			<h2><?php echo JText::_('COM_ACLMANAGER_RESET_TITLE'); ?></h2>
			<p class="reset"><?php echo JText::_('COM_ACLMANAGER_RESET_DESC'); ?></p>
			<div class="cpanel">
				<div class="icon">
					<a href="#" onclick="Joomla.submitform('group.reset', this.form);">
						<img alt="" src="templates/bluestork/images/header/icon-48-revert.png">
						<span><?php echo JText::_('COM_ACLMANAGER_RESET_REVERT'); ?></span>
					</a>
				</div>
			</div>
			<div class="cpanel">
				<div class="icon">
					<a href="#" onclick="Joomla.submitform('group.clear', this.form);">
						<img alt="" src="templates/bluestork/images/header/icon-48-clear.png">
						<span><?php echo JText::_('COM_ACLMANAGER_RESET_CLEAR'); ?></span>
					</a>
				</div>
			</div>
		</div>
		<div id="reset">
			<div class="width-100 fltrt">
				<table id="global-aclmanager" class="adminlist aclmanager">
					<!-- Begin table rows -->
					<?php echo $this->loadTemplate('table_permissions'); ?>
					<!-- End table rows -->
				</table>
			</div>
		</div>
	</div>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="id" value="<?php echo ($id);?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>