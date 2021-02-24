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
	<div class="app-filter-bar">
		<div class="app-filter-bar__cell app-filter-bar__cell--search">
			<?php echo $this->html('table.search', 'search', $search, 'COM_ED_SEARCH_MODULES_TOOLTIP'); ?>
		</div>

		<div class="app-filter-bar__cell app-filter-bar__cell--auto-size app-filter-bar__cell--divider-left">
			<div class="app-filter-bar__filter-wrap">
				<select class="o-form-control" name="published" id="filterType" data-table-filter>
					<option value=""<?php echo $published == '' ? ' selected="selected"' : '';?>><?php echo JText::_('Select Install State'); ?></option>
					<option value="installed"<?php echo $published == 'installed' ? ' selected="selected"' : '';?>><?php echo JText::_('Installed'); ?></option>
					<option value="notinstalled"<?php echo $published == 'notinstalled' ? ' selected="selected"' : '';?>><?php echo JText::_('Not Installed'); ?></option>
					<option value="updating"<?php echo $published == 'updating' ? ' selected="selected"' : '';?>><?php echo JText::_('Requires Updating'); ?></option>
				</select>
			</div>
		</div>

		<div class="app-filter-bar__cell app-filter-bar__cell--empty"></div>

		<div class="app-filter-bar__cell app-filter-bar__cell--divider-left app-filter-bar__cell--last t-text--center">
			<div class="app-filter-bar__filter-wrap app-filter-bar__filter-wrap--limit">
				<?php echo $this->html('table.limit', $pagination->limit); ?>
			</div>
		</div>
	</div>

	<div class="panel-table">
		<table class="app-table table" data-ed-table>
			<thead>
				<tr>
					<th width="1%">
						<?php echo $this->html('table.checkAll'); ?>
					</th>
					<th>
						<?php echo JHTML::_('grid.sort', JText::_('COM_ED_TABLE_COLUMN_TITLE'), 'a.title', $direction, $ordering); ?>
					</th>
					<th width="15%" class="center">
						<?php echo JText::_('COM_ED_TABLE_COLUMN_INSTALLED'); ?>
					</th>
					<th width="10%" class="center">
						<?php echo JText::_('COM_ED_TABLE_COLUMN_INSTALLED_VERSION'); ?>
					</th>
					<th width="10%" class="center">
						<?php echo JText::_('COM_ED_TABLE_COLUMN_LATEST_VERSION'); ?>
					</th>
					<th width="10%">
						<?php echo JText::_('COM_ED_TABLE_COLUMN_ELEMENT'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php if ($modules) { ?>

					<?php $i = 0; ?>

					<?php foreach ($modules as $module) { ?>
					<tr>
						<td class="center">
							<?php echo $this->html('table.checkbox', $i, $module->id); ?>
						</td>
						<td>
							<b><?php echo $module->title; ?></b>
							<div>
								<?php echo $module->description;?>
							</div>
						</td>
						<td class="center">
							<?php if ($module->state == ED_LANGUAGES_INSTALLED) { ?>
							<span class="t-text--success">
								<b><?php echo JText::_('Installed'); ?></b>
							</span>
							<?php } ?>

							<?php if ($module->state == ED_LANGUAGES_NEEDS_UPDATING) { ?>
							<span class="t-text--danger">
								<b><?php echo JText::_('Requires Updating'); ?></b>
							</span>

							<?php } ?>

							<?php if ($module->state == ED_LANGUAGES_NOT_INSTALLED) { ?>
								<b><?php echo JText::_('Not Installed'); ?></b>
							<?php } ?>
						</td>
						<td class="center">
							<?php if (!$module->installed) { ?>
								&mdash;
							<?php } else { ?>
								<b><?php echo $module->installed;?></b>
							<?php } ?>
						</td>
						<td class="center">
							<b><?php echo $module->version;?></b>
						</td>
						<td>
							<?php echo $module->element;?>
						</td>
					</tr>
					<?php $i++; ?>
					<?php } ?>
				<?php } ?>
			</tbody>

			<tfoot>
				<tr>
					<td colspan="6">
						<div class="footer-pagination">
							<?php echo $pagination->getListFooter(); ?>
						</div>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>

	<input type="hidden" name="filter_order" value="<?php echo $ordering; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $direction; ?>" />
	
	<?php echo $this->html('form.action', 'modules', 'modules'); ?>
</form>
