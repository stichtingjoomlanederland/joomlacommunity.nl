<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form action="index.php" method="post" name="adminForm" id="adminForm" data-ed-form>
	<div class="app-filter filter-bar form-inline">
		<div class="form-group">
			<?php echo $this->html('table.search', 'search', $search); ?>
		</div>
		<div class="form-group">
			<?php echo $this->html('table.filter', 'filter_state', $state, array('P' => 'COM_EASYDISCUSS_PUBLISHED', 'U' => 'COM_EASYDISCUSS_UNPUBLISHED')); ?>
		</div>
		<div class="form-group">
			<?php echo $this->html('table.limit', $pagination); ?>
		</div>
	</div>

	<div class="panel-table">
		<table class="app-table app-table-middle table table-striped" data-ed-table>
			<thead>
				<tr>
					<td width="1%">
						<?php echo $this->html('table.checkall'); ?>
					</td>
					<td style="text-align: left;">
						<?php echo JHTML::_('grid.sort', JText::_('Title') , 'title', $orderDirection, $order ); ?>
					</td>
					<td width="1%" class="center">
						<?php echo JText::_('COM_EASYDISCUSS_PUBLISHED' ); ?>
					</td>
					<td width="10%" class="center">
						<?php echo JText::_( 'COM_EASYDISCUSS_DISCUSSIONS' ); ?>
					</td>
					<td width="20%" class="center">
						<?php echo JHTML::_('grid.sort', JText::_( 'COM_EASYDISCUSS_TAGS_AUTHOR' ) , 'user_id', $orderDirection, $order); ?>
					</td>
					<td width="1%" class="center">
						<?php echo JText::_('COM_EASYDISCUSS_ID');?>
					</td>
				</tr>
			</thead>

			<tbody>
			<?php if ($tags) { ?>
				<?php $i = 0; ?>
				<?php foreach ($tags as $tag) { ?>
				<tr>
					<td>
						<?php echo $this->html('table.checkbox', $i++, $tag->id); ?>
					</td>
					<td align="left">
						<span class="editlinktip hasTip">
							<a href="<?php echo JRoute::_('index.php?option=com_easydiscuss&view=tags&layout=form&id='. $tag->id); ?>"><?php echo $tag->title; ?></a>
						</span>
					</td>

					<td class="center">
						<?php echo $this->html('table.state', 'tags', $tag, 'published'); ?>
					</td>

					<td class="center">
						<?php echo $tag->count;?>
					</td>
					<td class="center">
						<a href="<?php echo JRoute::_('index.php?option=com_easydiscuss&controller=user&id=' . $tag->user_id . '&task=edit'); ?>"><?php echo $tag->user->name; ?></a>
					</td>
					<td class="center">
						<?php echo $tag->id;?>
					</td>
				</tr>
				<?php } ?>
			<?php } else { ?>
				<tr>
					<td colspan="6" class="center">
						<?php echo JText::_('COM_EASYDISCUSS_NO_TAGS_CREATED_YET');?>
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

	<input type="hidden" name="filter_order" value="<?php echo $order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="" />

	<?php echo $this->html('form.hidden', 'tags', 'tags'); ?>
</form>
