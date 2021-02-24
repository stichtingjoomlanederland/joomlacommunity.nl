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
<div class="panel-table">
	<table class="app-table table" data-ed-table>
		<thead>
			<tr>
				<th width="5%" class="center">#</th>
				<th>
					<?php echo JText::_('COM_EASYDISCUSS_REPORTED_REASON'); ?>
				</th>
				<th width="20%" class="center">
					<?php echo JText::_('COM_EASYDISCUSS_REPORTED_BY'); ?>
				</th>
				<th width="20%" class="center">
					<?php echo JText::_('COM_EASYDISCUSS_REPORT_DATE' ); ?>
				</th>
			</tr>
		</thead>
		<tbody>

		<?php $i = 1; ?>
		<?php foreach ($reasons as $row) { ?>
			<tr class="">
				<td class="center">
					<?php echo $i++; ?>
				</td>
				<td>
					<?php echo $this->escape($row->reason); ?>
				</td>
				<td class="center">
					<?php if ($row->created_by == '0') : ?>
						<?php echo JText_('COM_EASYDISCUSS_GUEST'); ?>
					<?php else : ?>
						<?php echo $row->user->name; ?>
					<?php endif; ?>
				</td>
				<td class="center">
					<?php echo ED::date($row->created)->toSql(); ?>
				</td>
			</tr>
		<?php } ?>
	</table>
</div>