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
<form action="index.php" method="post" name="adminForm" id="adminForm" data-grid-ed>

	<div class="app-filter-bar">
		<div class="app-filter-bar__cell app-filter-bar__cell--auto-size">
			<div class="app-filter-bar__filter-wrap">
				<?php echo $this->html('table.filter', 'filter_version', $version, $versions); ?>
			</div>
		</div>

		<div class="app-filter-bar__cell app-filter-bar__cell--divider-left"></div>

		<div class="app-filter-bar__cell app-filter-bar__cell--last t-text--center">
			<div class="app-filter-bar__filter-wrap app-filter-bar__filter-wrap--limit">
				&nbsp;
			</div>
		</div>
	</div>

	<div class="app-content-table">
		<table class="app-table table">
			<thead>
				<tr>
					<th width="5" class="center">
						<?php echo $this->html('table.checkall'); ?>
					</th>

					<th class="title" nowrap="nowrap" style="text-align:left;">
						<?php echo JText::_('COM_EASYDISCUSS_MAINTENANCE_COLUMN_TITLE'); ?>
					</th>
					<th width="15%" class="center">
						<?php echo JText::_('COM_EASYDISCUSS_MAINTENANCE_COLUMN_VERSION'); ?>
					</th>

				</tr>
			</thead>
			<tbody>

			<?php if ($scripts) { ?>
				<?php $i = 0; ?>
				<?php foreach ($scripts as $script) { ?>
				<tr>
					<td class="center">
						<?php echo JHTML::_('grid.id', $i++, $script->key); ?>
					</td>

					<td>
						<div><b><?php echo $script->title; ?></b></div>
						<div><?php echo $script->description; ?></div>
					</td>
					<td class="center"><?php echo $script->version; ?></td>
				</tr>
				<?php } ?>
			<?php } else { ?>
				<tr>
					<td colspan="9" align="center" class="center">
						<?php echo JText::_('COM_EASYDISCUSS_MAINTENANCE_SCRIPT_NOT_FOUND');?>
					</td>
				</tr>
			<?php } ?>
			</tbody>

			<tfoot>
				<tr>
					<td colspan="9">
						<div class="footer-pagination">
							<?php echo $pagination->getListFooter(); ?>
						</div>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>
	<?php echo $this->html('form.token'); ?>
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="com_easydiscuss" />
	<input type="hidden" name="view" value="maintenance" />
	<input type="hidden" name="layout" />
	<input type="hidden" name="filter_order" value="<?php echo $order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="" />
</form>
