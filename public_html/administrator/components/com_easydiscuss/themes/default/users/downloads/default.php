<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
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

					<th style="text-align:left;">
						<?php echo JText::_('COM_ED_TABLE_COLUMN_NAME'); ?>
					</th>

					<th width="15%" style="text-align:left;">
						<?php echo JText::_('COM_ED_TABLE_COLUMN_DOWNLOAD'); ?>
					</th>

					<th width="15%" style="text-align:left;">
						<?php echo JText::_('COM_ED_TABLE_COLUMN_STATUS'); ?>
					</th>

					<th width="15%" style="text-align:left;">
						<?php echo JText::_('COM_EASYDISCUSS_TABLE_COLUMN_CREATED'); ?>
					</th>

					<th width="1%" class="center">
						<?php echo  Jtext::_('COM_EASYDISCUSS_TABLE_COLUMN_ID'); ?>
					</th>
				</tr>
			</thead>

			<tbody>
			<?php if ($requests) { ?>
				<?php $i = 0; ?>
				<?php foreach ($requests as $request) { ?>
				<tr>
					<td>
						<?php echo $this->html('table.checkbox', $i++, $request->id); ?>
					</td>

					<td>
						<a href="index.php?option=com_easydiscuss&view=users&layout=form&id=<?php echo $request->getRequester()->id;?>"><?php echo $request->getRequester()->getName();?></a>
					</td>

					<td>
						<?php if ($request->isReady()) { ?>
							<a href="index.php?option=com_easydiscuss&view=users&layout=downloadData&id=<?php echo $request->id;?>"><?php echo JText::_('COM_ED_DOWNLOAD');?></a>
						<?php } else { ?>
							&mdash;
						<?php } ?>
					</td>

					<td>
						<?php echo $request->getStateLabel(); ?>
					</td>

					<td>
						<?php echo ED::date($request->created)->format();?>
					</td>

					<td class="center">
						<?php echo $request->id;?>
					</td>
				</tr>
				<?php } ?>
			<?php } else { ?>
				<tr>
					<td colspan="6" align="center">
						<?php echo JText::_('COM_ED_USER_DOWNLOAD_NO_ITEMS');?>
					</td>
				</tr>
			<?php } ?>
			</tbody>

			<tfoot>
				<tr>
					<td colspan="6">
						<div class="footer-pagination center">
							<?php echo $pagination->getListFooter(); ?>
						</div>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>

	<?php echo $this->html('form.hidden', 'user', 'users'); ?>
</form>
