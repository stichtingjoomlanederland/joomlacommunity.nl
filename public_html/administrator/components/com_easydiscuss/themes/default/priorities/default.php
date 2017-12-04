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
<form action="index.php" method="post" name="adminForm" id="adminForm" data-ed-form>
	<div class="app-filter-bar">
		<div class="app-filter-bar__cell app-filter-bar__cell--search">
			<?php echo $this->html('table.search', 'search', $search); ?>
		</div>

		<div class="app-filter-bar__cell app-filter-bar__cell--divider-left"></div>

		<div class="app-filter-bar__cell app-filter-bar__cell--last t-text--center">
			<div class="app-filter-bar__filter-wrap app-filter-bar__filter-wrap--limit">
				&nbsp;
			</div>
		</div>
	</div>
	
	<div class="panel-table">
		<table class="app-table table" data-ed-table>
			<thead>
				<tr>
					<th width="1%" class="center">
						<?php echo $this->html('table.checkall'); ?>
					</th>
					<th style="text-align:left;">
						<?php echo JText::_('COM_EASYDISCUSS_TABLE_COLUMN_PRIORITY_TITLE');?>
					</th>
					<th width="20%" class="center">
						<?php echo JText::_('COM_EASYDISCUSS_TABLE_COLUMN_COLOR');?>
					</th>
					<th width="20%" class="center">
						<?php echo JText::_('COM_EASYDISCUSS_TABLE_COLUMN_CREATED');?>
					</th>
					<th width="1%" class="center">
						<?php echo JText::_('COM_EASYDISCUSS_TABLE_COLUMN_ID');?>
					</th>
				</tr>
			</thead>

		<tbody>
		<?php if ($priorities) { ?>
			<?php $i = 0; ?>
			<?php foreach ($priorities as $priority) { ?>
				<tr>
					<td class="center">
						<?php echo $this->html('table.checkbox', $i++, $priority->id); ?>
					</td>

					<td style="text-align:left;">
						<a href="<?php echo 'index.php?option=com_easydiscuss&view=priorities&layout=form&id=' . $priority->id; ?>"><?php echo $priority->title; ?></a>
					</td>

					<td class="text-center">
						<span style="width: 100px;display: inline-block;border: 1px dashed #ccc;padding:4px 10px;background: <?php echo $priority->color;?>;">&nbsp;</span>
					</td>

					<td class="text-center">	
						<?php echo $priority->created;?>
					</td>

					<td class="text-center">
						<?php echo $priority->id;?>
					</td>
				</tr>
				<?php $i++;?>
			<?php } ?>
		<?php } else { ?>
			<tr>
				<td colspan="5" class="center">
					<?php echo JText::_('COM_EASYDISCUSS_NO_POST_PRIORITIES_CREATED_YET');?>
				</td>
			</tr>
		<?php } ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="5">
					<div class="footer-pagination center">
						<?php echo $pagination->getListFooter(); ?>
					</div>
				</td>
			</tr>
		</tfoot>
		</table>
	</div>

	<?php echo $this->html('form.hidden', 'priorities', 'priorities'); ?>
</form>