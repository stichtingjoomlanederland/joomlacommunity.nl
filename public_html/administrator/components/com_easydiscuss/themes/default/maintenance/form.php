<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form action="index.php" method="post" name="adminForm" id="adminForm" data-maintenance-form>

	<div class="app-content-table" data-maintenance-container>
		<table class="app-table table">
			<thead>
				<tr>
					<th class="title" nowrap="nowrap" style="text-align:left;">
						<?php echo JText::_('COM_EASYDISCUSS_MAINTENANCE_COLUMN_TITLE'); ?>
					</th>
					<th width="10%" class="center">
						<?php echo JText::_('COM_EASYDISCUSS_MAINTENANCE_COLUMN_STATUS'); ?>
					</th>
				</tr>
			</thead>
			<tbody>

			<?php foreach ($scripts as $script) { ?>
				<tr data-row data-key="<?php echo $script->key; ?>">
					<td><?php echo $script->title; ?></td>
					<td class="center"><span class="label label-warning" data-status><i data-icon class="fa fa-wrench"></i></span></td>
				</tr>
			<?php } ?>

			</tbody>

		</table>
	</div>

	<?php echo $this->html('form.hidden', 'maintenance'); ?>
</form>
