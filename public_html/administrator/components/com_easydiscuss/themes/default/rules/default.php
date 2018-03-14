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
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<div class="post-app-filter-bar">
		<div class="app-filter-bar">
			<?php echo $this->html('table.search', 'search', $search); ?>
		</div>

		<div class="app-filter-bar">
			<div class="app-filter-bar__cell">
				<div class="form-inline">
					<div class="form-group">
						<div class="app-filter-select-group">
							<?php echo $this->html('table.filter', 'filter_state', $state, array('P' => 'COM_EASYDISCUSS_PUBLISHED', 'U' => 'COM_EASYDISCUSS_UNPUBLISHED')); ?>
						</div>
					</div>
				</div>
			</div>
			<div class="app-filter-bar__cell app-filter-bar__cell--last">
				<div class="form-inline">
					<div class="app-filter-select-group">
						<?php echo $this->html('table.limit', $pagination); ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="app-content-table">
		<table class="app-table table" data-ed-table>
			<thead>
				<tr>
					<th width="5">
						<?php echo JText::_('Num'); ?>
					</th>
					<th width="5">
						<?php echo $this->html('table.checkall'); ?>					
					</th>
					<th class="title" style="text-align:left;"><?php echo JHTML::_('grid.sort', 'Title', 'a.title', $orderDirection, $order); ?></th>
					<th width="1%"><?php echo JText::_( 'Command' ); ?></th>
					<th width="10%" nowrap="nowrap"><?php echo JHTML::_('grid.sort', JText::_('Date'), 'a.created', $orderDirection, $order); ?></th>
					<th width="1%" nowrap="nowrap"><?php echo JHTML::_('grid.sort', 'ID', 'a.id', $orderDirection, $order); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php if ($rules) {
				$k = 0;
				$x = 0;
				for ($i=0, $n = count($rules); $i < $n; $i++) {
					$row = $rules[$i];
					$date = ED::date($row->created);?>
				<tr class="<?php echo "row$k"; ?>">
					<td>
						<?php echo $pagination->getRowOffset($i); ?>
					</td>
					<td width="7">
						<?php echo JHTML::_('grid.id', $x++, $row->id); ?>
					</td>
					<td align="left">
						<?php echo $row->title; ?>
					</td>
					<td align="center">
						<?php echo $row->command;?>
					</td>
					<td align="center">
						<?php echo $date->toMySQL(true);?>
					</td>
					<td align="center">
						<?php echo $row->id; ?>
					</td>
				</tr>
				<?php $k = 1 - $k; } ?>
			<?php } else { ?>
				<tr>
					<td colspan="6" align="center">
						<?php echo JText::_('No rules created yet');?>
					</td>
				</tr>
			<?php } ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="10">
						<div class="footer-pagination">
							<?php echo $pagination->getPagesLinks(); ?>
						</div>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>

<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="option" value="com_easydiscuss" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="rules" />
<input type="hidden" name="view" value="rules" />
<input type="hidden" name="filter_order" value="<?php echo $order; ?>" />
<input type="hidden" name="filter_order_Dir" value="" />
<?php echo JHTML::_( 'form.token' ); ?>
</form>
