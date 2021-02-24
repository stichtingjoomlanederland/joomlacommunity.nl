<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
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
			<?php echo $this->html('table.search', 'search', $search, JText::_('COM_ED_COMMENT_SEARCH_TOOLTIP')); ?>
		</div>

		<div class="app-filter-bar__cell app-filter-bar__cell--auto-size app-filter-bar__cell--divider-left">
			<div class="app-filter-bar__filter-wrap">
				<?php echo $this->html('table.filter', 'filter_state', $state, array('P' => 'COM_EASYDISCUSS_PUBLISHED', 'U' => 'COM_EASYDISCUSS_UNPUBLISHED')); ?>
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
						<?php echo $this->html('table.checkall'); ?>
					</th>
					<th style="text-align: left;">
						<?php echo JHTML::_('grid.sort', JText::_('Comment') , 'comment', $orderDirection, $order); ?>
					</th>
					<th width="1%" class="center">
						<?php echo JText::_('COM_EASYDISCUSS_PUBLISHED'); ?>
					</th>					
					<th width="20%" class="center">
						<?php echo JHTML::_('grid.sort', JText::_('COM_ED_AUTHOR'), 'user_id', $orderDirection, $order); ?>
					</th>
					<th width="20%" class="center">
						<?php echo JText::_('COM_EASYDISCUSS_DATE'); ?>
					</th>									
					<th width="1%" class="center">
						<?php echo JText::_('COM_EASYDISCUSS_ID');?>
					</th>
				</tr>
			</thead>

			<tbody>
			<?php if ($comments) { ?>
				<?php $i = 0; ?>
				<?php foreach ($comments as $comment) { ?>
				<tr>
					<td>
						<?php echo $this->html('table.checkbox', $i++, $comment->id); ?>
					</td>
					<td align="left">
						<span class="editlinktip hasTip">
							<a href="<?php echo JRoute::_('index.php?option=com_easydiscuss&view=comments&layout=form&id='. $comment->id); ?>"><?php echo EDJString::substr($comment->comment, 0, 100) . JText::_('COM_ED_ELLIPSES'); ?></a>
						</span>

						<div style="font-size: 11px;">
							<span style="padding-right: 5px;border-right: 1px solid #d7d7d7;">
								<?php echo $comment->postLabel;?>: <a href="<?php echo $comment->postLink;?>" target="_blank"><?php echo $comment->postTitle;?></a>
							</span>

							<span style="padding-left: 6px;">
								<?php echo JText::_('COM_ED_IP_ADDRESS');?>: <?php echo $comment->ip;?>
							</span>
						</div>						
					</td>

					<td class="center">
						<?php echo $this->html('table.state', 'comments', $comment, 'published'); ?>
					</td>
					
					<td class="center">
						<?php if ($comment->user_id) { ?>
							<a href="<?php echo JRoute::_('index.php?option=com_easydiscuss&view=user&layout=form&id=' . $comment->user_id); ?>"><?php echo $comment->name; ?></a>						
						<?php } else { ?>
							<?php echo $comment->name; ?>
						<?php } ?>
					</td>

					<td class="center">
						<?php echo ED::date($comment->modified)->display(JText::_('DATE_FORMAT_LC5'));?>
					</td>

					<td class="center">
						<?php echo $comment->id;?>
					</td>
				</tr>
				<?php } ?>
			<?php } else { ?>
				<tr>
					<td colspan="6" class="center">
						<?php echo JText::_('COM_ED_NO_COMMENTS_CREATED_YET');?>
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

	<?php echo $this->html('form.action', 'comments', 'comments'); ?>
</form>
