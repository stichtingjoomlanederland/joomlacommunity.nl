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
<form action="index.php" method="post" name="adminForm" id="adminForm" data-ed-form>
	
	<div class="panel-table">
		<table class="app-table table" data-ed-table>
			<thead>
				<tr>
					<th width="1%">
						<?php echo $this->html('table.checkall'); ?>
					</th>

					<th>
						<?php echo JText::_('COM_EASYDISCUSS_TABLE_COLUMN_FILENAME'); ?>
					</th>
					<th>
						<?php echo JText::_('COM_EASYDISCUSS_TABLE_COLUMN_FILE_DESCRIPTION'); ?>
					</th>
					<th width="5%" class="center">
						<?php echo JText::_('COM_EASYDISCUSS_TABLE_COLUMN_MODIFIED'); ?>
					</th>
				</tr>
			</thead>

			<tbody>
			<?php if ($mails) { ?>
				<?php $i = 0; ?>
				<?php foreach ($mails as $file) { ?>
					<tr>

						<td class="center" width="1%">
							<?php echo $this->html('table.checkbox', $i++, base64_encode($file->relative)); ?>
						</td>

						<td width="30%">
							<a href="index.php?option=com_easydiscuss&view=emails&layout=edit&file=<?php echo urlencode($file->name);?>"><?php echo $file->name; ?></a>
						</td>
						<td>
							<?php echo $file->desc;?>
						</td>
						<td class="center">
							<?php echo $this->html('table.state', 'emails', $file, 'override', 'emails', false); ?>
						</td>
					</tr>
					<?php $i++; ?>
				<?php } ?>
			<?php } else { ?>
				<tr>
					<td colspan="6" class="empty">
						<?php echo JText::_('COM_EASYDISCUSS_NO_MAILS');?>
					</td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
	</div>

	<?php echo $this->html('form.action', 'emails', 'emails'); ?>
</form>
