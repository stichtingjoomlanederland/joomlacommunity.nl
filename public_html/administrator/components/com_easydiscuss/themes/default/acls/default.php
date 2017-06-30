<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
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
	<div class="post-app-filter-bar">
		<div class="app-filter-bar" style="display: none;">
		</div>

		<div class="app-filter-bar">
			<div class="app-filter-bar__cell app-filter-bar__cell--last">
				<div class="form-inline">
					<div class="app-filter-select-group">
						<?php echo $this->html('table.limit', $pagination); ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="panel-table">
		<table class="app-table app-table-middle table table-striped" data-ed-table>
			<thead>
				<tr>
					<td width="1%">&nbsp;</td>
					<td style="text-align:left;">
						<?php echo JText::_('COM_EASYDISCUSS_GROUP_NAME'); ?>
					</td>
					<td width="5%" class="center">
						<?php echo JText::_('COM_EASYDISCUSS_ID'); ?>
					</td>
				</tr>
			</thead>

			<tbody>
			<?php if ($rulesets) { ?>
				<?php $i = 0; ?>
				<?php foreach ($rulesets as $ruleset) { ?>
				<tr>
					<td class="center">&nbsp;</td>
					<td align="left">
						<?php echo str_repeat('<span class="gi">|&mdash;</span>', $ruleset->level); ?>
						<a href="<?php echo $ruleset->editLink;?>"><?php echo $ruleset->name; ?></a>
					</td>
					<td class="center">
						<?php echo $ruleset->id;?>
					</td>
				</tr>
				<?php } ?>
			<?php } ?>
			</tbody>

			<tfoot>
				<tr>
					<td colspan="3">
						<div class="footer-pagination center">
							<?php echo $pagination->getListFooter(); ?>
						</div>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>

	<input type="hidden" name="filter_order" value="<?php echo $sort->order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="" />

	<?php echo $this->html('form.hidden', 'acl', 'acls'); ?>
</form>
