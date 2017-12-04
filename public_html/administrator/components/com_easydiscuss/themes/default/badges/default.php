<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

$prefix = JRequest::getCmd('prefix', '');
?>
<form action="index.php" method="post" name="adminForm" id="adminForm" data-ed-form>

	<?php if ($browse) { ?>
	<div class="app-filter filter-bar form-inline">
		<div class="form-group">
			<h2><?php echo JText::_('COM_EASYDISCUSS_BADGES_TITLE');?></h2>
			<?php echo JText::_('COM_EASYDISCUSS_BADGES_DESC');?>
		</div>
	</div>
	<?php } else { ?>
	<div class="app-filter-bar">
		<div class="app-filter-bar__cell app-filter-bar__cell--search">
			<?php echo $this->html('table.search', 'search', $search); ?>
		</div>

		<div class="app-filter-bar__cell app-filter-bar__cell--auto-size app-filter-bar__cell--divider-left">
			<div class="app-filter-bar__filter-wrap">
				<?php echo $this->html('table.filter', 'filter_state', $filter, array('published' => 'COM_EASYDISCUSS_PUBLISHED', 'unpublished' => 'COM_EASYDISCUSS_UNPUBLISHED')); ?>
			</div>
		</div>

		<div class="app-filter-bar__cell app-filter-bar__cell--divider-left"></div>

		<div class="app-filter-bar__cell app-filter-bar__cell--divider-left app-filter-bar__cell--last t-text--center">
			<div class="app-filter-bar__filter-wrap app-filter-bar__filter-wrap--limit">
				<?php echo $this->html('table.limit', $pagination->limit); ?>
			</div>
		</div>
	</div>
	<?php } ?>

	<div class="panel-table">
		<table class="app-table table" data-ed-table>
		<thead>
			<tr>
				<?php if (!$browse) { ?>
					<th width="1%" class="center">
						<?php echo $this->html('table.checkall'); ?>
					</th>
				<?php } ?>

				<th style="text-align: left;">
					<?php echo JHTML::_('grid.sort', JText::_('COM_EASYDISCUSS_BADGE_TITLE'), 'a.title', $orderDirection, $order); ?>
				</th>

				<?php if (!$browse) { ?>
					<th width="1%" class="center">
						<?php echo JText::_('COM_EASYDISCUSS_PUBLISHED'); ?>
					</th>
					<th width="1%" class="center">
						<?php echo JText::_('COM_EASYDISCUSS_ACHIEVERS'); ?>
					</th>
				<?php } ?>

				<th width="10%" class="center">
					<?php echo JText::_('COM_EASYDISCUSS_THUMBNAIL'); ?>
				</th>

				<?php if (!$browse) { ?>
					<th width="10%" class="center">
						<?php echo JHTML::_('grid.sort', JText::_('COM_EASYDISCUSS_DATE'), 'a.created', $orderDirection, $order); ?>
					</th>
				<?php } ?>

				<th width="6%" class="center">
					<?php echo JHTML::_('grid.sort', JText::_('COM_EASYDISCUSS_ID'), 'a.id', $orderDirection, $order); ?>
				</th>
			</tr>
		</thead>

		<tbody>
		<?php if ($badges) { ?>
			<?php $i = 0; ?>
			<?php foreach ($badges as $badge) { ?>
				<tr>
					<?php if (!$browse) { ?>
						<td class="center" style="text-align: center;">
							<?php echo $this->html('table.checkbox', $i++, $badge->id); ?>
						</td>
					<?php } ?>

					<td style="text-align:left;">
						<a href="<?php echo $badge->editLink; ?>"><?php echo $badge->title; ?></a>
					</td>

					<?php if (!$browse) { ?>

						<td class="center">
							<?php echo $this->html('table.state', 'badges', $badge, 'published'); ?>
						</td>

						<td class="center">
							<?php echo $badge->totalUsers;?>
						</td>
					<?php } ?>

					<td class="center">
						<img src="<?php echo JURI::root();?>/media/com_easydiscuss/badges/<?php echo $badge->avatar;?>" width="32" />
					</td>

					<?php if (!$browse) { ?>
						<td class="center">
							<?php echo $badge->date->toSql(); ?>
						</td>
					<?php } ?>

					<td class="center">
						<?php echo $badge->id;?>
					</td>
				</tr>
			<?php } ?>
		<?php } else { ?>
			<tr>
				<td colspan="7" class="center">
					<?php echo JText::_('COM_EASYDISCUSS_NO_BADGES_YET');?>
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

		<?php if ($browse) { ?>
		<input type="hidden" name="browse" value="1" />
		<input type="hidden" name="browseFunction" value="<?php echo $browseFunction; ?>" />
		<input type="hidden" name="tmpl" value="component" />
		<input type="hidden" name="prefix" value="<?php echo $prefix; ?>" />
		<?php } ?>

		<input type="hidden" name="filter_order" value="<?php echo $order; ?>" />
		<input type="hidden" name="filter_order_Dir" value="" />

		<?php echo $this->html('form.hidden', 'badges', 'badges'); ?>

	</div>
</form>