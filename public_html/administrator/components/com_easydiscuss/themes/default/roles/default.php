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
		<div class="app-filter-bar__cell app-filter-bar__cell--auto-size">
			<div class="app-filter-bar__filter-wrap">
				<?php echo $this->html('table.filter', 'filter_state', $filter); ?>
			</div>
		</div>
		
		<div class="app-filter-bar__cell app-filter-bar__cell--divider-left"></div>

		<div class="app-filter-bar__cell app-filter-bar__cell--divider-left app-filter-bar__cell--last t-text--center">
			<div class="app-filter-bar__filter-wrap app-filter-bar__filter-wrap--limit">
				<?php echo $this->html('table.limit', $pagination); ?>
			</div>
		</div>
	</div>

	<div class="panel-table">
		<table class="app-table table" data-ed-table>
			<thead>
				<tr>
					<th width="1%">
						<?php echo $this->html('table.checkall'); ?>
					</th>
					<th style="text-align:left;">
						<?php echo JHTML::_('grid.sort', JText::_('COM_EASYDISCUSS_ROLE_TITLE') , 'title', $orderDirection, $order); ?>
					</th>
					<th width="1%" class="center">
						<?php echo JText::_('COM_EASYDISCUSS_ROLE_PUBLISHED'); ?>
					</th>
					<th width="10%" class="center">
						<?php echo JText::_('COM_EASYDISCUSS_ROLES_LABEL_COLOUR'); ?>
					</th>
					<th width="10%" class="center">
						<?php echo JHTML::_('grid.sort', JText::_( 'COM_EASYDISCUSS_USERGROUP' ) , 'usergroup_id', $orderDirection, $order); ?>
					</th>
					<th width="6%" class="center">
						<?php echo JHTML::_('grid.sort', JText::_( 'COM_EASYDISCUSS_ROLE_ID' ) , 'id', $orderDirection, $order); ?>
					</th>
				</tr>
			</thead>

			<tbody>
			<?php if ($roles) { ?>
				<?php $i = 0; ?>
				<?php foreach ($roles as $role) { ?>
				<tr>
					<td>
						<?php echo $this->html('table.checkbox', $i++, $role->id); ?>
					</td>
					<td style="text-align:left;">
						<a href="<?php echo JRoute::_('index.php?option=com_easydiscuss&view=roles&layout=form&id=' . $role->id); ?>"><?php echo $role->title; ?></a>
					</td>
					<td class="center">
						<?php echo $this->html('table.state', 'roles', $role, 'published'); ?>
					</td>
					<td class="center">
						<span class="o-label o-label--<?php echo $role->colorcode;?>"><?php echo JText::_($role->title);?></span>
					</td>
					<td class="center">
						<?php echo $role->usergroup_title; ?>
					</td>
					<td class="center">
						<?php echo $role->id; ?>
					</td>
				</tr>
				<?php } ?>
			<?php } else { ?>
				<tr>
					<td colspan="6" class="empty">
						<i class="fa fa-user-secret"></i>
						<?php echo JText::_('COM_EASYDISCUS_ROLE_NOT_CREATED');?>
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

	<?php echo $this->html('form.hidden', 'roles', 'roles'); ?>
	<input type="hidden" name="filter_order" value="<?php echo $order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="" />
</form>