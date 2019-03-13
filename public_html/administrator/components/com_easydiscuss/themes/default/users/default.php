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

	<div class="app-filter-bar">
		<div class="app-filter-bar__cell app-filter-bar__cell--search">
			<?php echo $this->html('table.search', 'search', $search); ?>
		</div>
		
		<div class="app-filter-bar__cell app-filter-bar__cell--divider-left"></div>

		<div class="app-filter-bar__cell app-filter-bar__cell--divider-left app-filter-bar__cell--last t-text--center">
			<div class="app-filter-bar__filter-wrap app-filter-bar__filter-wrap--limit">
				<?php echo $this->html('table.limit', $pagination->limit); ?>
			</div>
		</div>
	</div>

	<?php if (!$browse) { ?>
		<?php echo $this->html('table.notice', 'COM_EASYDISCUSS_USERS_MANAGEMENT_DELETE_NOTICE'); ?>
	<?php } ?>

	<div class="panel-table">
		<table class="app-table table" data-ed-table>
			<thead>
				<tr>
					<?php if (!$browse) { ?>
					<th width="1%">
						<?php echo $this->html('table.checkall'); ?>
					</th>
					<?php } ?>

					<th style="text-align:left;">
						<?php echo JHTML::_('grid.sort', JText::_('COM_EASYDISCUSS_NAME'), 'u.name', $orderDirection, $order ); ?>
					</th>
					<th width="15%" class="center">
						<?php echo JHTML::_('grid.sort', JText::_('COM_EASYDISCUSS_USERNAME'), 'u.username', $orderDirection, $order ); ?>
					</th>

					<?php if (!$browse) { ?>
					<th width="10%" class="center">
						<?php echo JText::_('COM_EASYDISCUSS_GROUP'); ?>
					</th>
					<th width="15%" class="center">
						<?php echo JHTML::_('grid.sort', JText::_('COM_EASYDISCUSS_EMAIL'), 'u.email', $orderDirection, $order); ?>
					</th>
					<th width="10%" class="center">
						<?php echo JText::_('COM_EASYDISCUSS_TOTAL_DISCUSSIONS'); ?>
					</th>
					<?php } ?>

					<th width="1%" class="center">
						<?php echo JHTML::_('grid.sort', Jtext::_('COM_EASYDISCUSS_ID'), 'u.id', $orderDirection, $order); ?>
					</th>
				</tr>
			</thead>

			<tbody>
			<?php if ($users) { ?>
				<?php $i = 0; ?>
				<?php foreach ($users as $user) { ?>
				<tr>
					<?php if (!$browse) { ?>
					<td>
						<?php echo $this->html('table.checkbox', $i++, $user->id); ?>
					</td>
					<?php } ?>

					<td>
						<?php if ($browse) { ?>
							<a href="javascript:void(0);" onclick="parent.<?php echo $browsefunction; ?>('<?php echo $user->id;?>','<?php echo addslashes($this->escape($user->name));?>','<?php echo $prefix; ?>');"><?php echo $user->name;?></a>
						<?php } else { ?>
							<a href="index.php?option=com_easydiscuss&view=users&layout=form&id=<?php echo $user->id;?>"><?php echo $user->name;?></a>
						<?php } ?>
					</td>
					<td class="center">
						<?php echo $user->username;?>
					</td>

					<?php if (!$browse) { ?>
					<td class="center">
						<?php echo $user->usergroups; ?>
					</td>
					<td class="center">
						<?php echo $user->email;?>
					</td>
					<td class="center">
						<?php echo $user->totalTopics;?>
					</td>
					<?php } ?>

					<td class="center">
						<?php echo $user->id;?>
					</td>
				</tr>
				<?php } ?>
			<?php } else { ?>
				<tr>
					<td colspan="7" align="center">
						<?php echo JText::_('COM_EASYDISCUSS_NO_USERS_YET');?>
					</td>
				</tr>
			<?php } ?>
			</tbody>

			<tfoot>
				<tr>
					<td colspan="7">
						<div class="footer-pagination center">
							<?php echo $pagination->getListFooter(); ?>
						</div>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>

	<?php if ($browse) { ?>
	<input type="hidden" name="browse" value="1" />
	<input type="hidden" name="browsefunction" value="<?php echo $browsefunction; ?>" />
	<input type="hidden" name="tmpl" value="component" />
	<input type="hidden" name="prefix" value="<?php echo $prefix; ?>" />
	<?php } ?>

	<input type="hidden" name="filter_order" value="<?php echo $order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="" />

	<?php echo $this->html('form.hidden', 'user', 'users'); ?>
</form>
