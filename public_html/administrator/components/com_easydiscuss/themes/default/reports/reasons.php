<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');
?>

<div id="discuss-wrapper">
	<table class="app-table table" data-ed-table>
		<thead>
			<tr>
				<th width="5" class="center">#</th>
				<th class="title" nowrap="nowrap" style="text-align:left;"><?php echo JText::_( 'COM_EASYDISCUSS_REPORTED_REASON' ); ?></th>
				<th width="20%" class="center"><?php echo JText::_( 'COM_EASYDISCUSS_REPORTED_BY' ); ?></th>
				<th width="20%" class="center"><?php echo JText::_( 'COM_EASYDISCUSS_REPORT_DATE' ); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php if ($reasons) { ?>
			<?php $i = 1; ?>
			<?php foreach ($reasons as $row) { ?>
				<tr class="">
					<td class="center">
						<?php echo $i++; ?>
					</td>
					<td>
						<?php echo $this->escape($row->reason); ?>
					</td>
					<td class="center" align="center">
						<?php if ($row->created_by == '0') : ?>
							<?php echo JText_('COM_EASYDISCUSS_GUEST'); ?>
						<?php else : ?>
							<?php echo $row->user->name; ?>
						<?php endif; ?>
					</td>
					<td class="center" align="center">
						<?php echo ED::date($row->created)->toSql(); ?>
					</td>
				</tr>
			<?php } ?>
		<?php } else { ?>
				<tr>
					<td colspan="9" align="center" class="center">
						<?php echo JText::_('COM_EASYDISCUSS_NO_REPORTS');?>
					</td>
				</tr>
		<?php } ?>
	</table>
</div>
