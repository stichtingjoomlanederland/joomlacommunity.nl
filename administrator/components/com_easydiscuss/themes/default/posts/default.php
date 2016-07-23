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
	<div class="app-filter filter-bar form-inline">
	    <div class="form-group">
	        <?php echo $this->html('table.search', 'search', $search); ?>
	    </div>
	    <div class="form-group">
	    	<?php echo $this->html('table.filter', 'filter_state', $filter, array('published' => 'COM_EASYDISCUSS_PUBLISHED', 'unpublished' => 'COM_EASYDISCUSS_UNPUBLISHED')); ?>
	    	<?php echo $categoryFilter; ?>
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
					<td style="text-align:left;">
						<?php echo JHTML::_('grid.sort', 'Title', 'a.title', $orderDirection, $order); ?>
					</td>
					<td width="15%" class="text-center">
						<?php echo JText::_('COM_EASYDISCUSS_CATEGORY'); ?>
					</td>
					<td width="5%" class="text-center">
						<?php echo JText::_('COM_EASYDISCUSS_FEATURED'); ?>
					</td>
					<td width="5%" class="text-center">
						<?php echo JText::_('COM_EASYDISCUSS_PUBLISHED'); ?>
					</td>

					<td width="1%" class="text-center">
						<?php echo JText::_('COM_EASYDISCUSS_REPLIES'); ?>
					</th>

					<td width="1%" class="text-center">
						<?php echo JText::_('COM_EASYDISCUSS_HITS');?>
					</th>
					<td width="1%" class="text-center">
						<?php echo JText::_('COM_EASYDISCUSS_POSTS_VOTES'); ?>
					</th>
					<td width="10%" class="text-center">
						<?php echo JText::_('COM_EASYDISCUSS_USER'); ?>
					</th>
					<td width="10%" class="text-center">
						<?php echo JHTML::_('grid.sort', JText::_('COM_EASYDISCUSS_DATE'), 'a.created', $orderDirection, $order); ?>
					</th>
					<td width="1%" class="text-center">
						<?php echo JText::_('COM_EASYDISCUSS_COLUMN_ID');?>
					</th>
				</tr>
			</thead>
			<tbody>

			<?php if ($posts) { ?>
				<?php $i = 0; ?>
				<?php foreach ($posts as $post) { ?>
				<tr>
					<td class="center">
						<?php echo $this->html('table.checkbox', $i++, $post->id); ?>
					</td>
					
					<td style="text-align:left;">
						<?php if (empty($parentId)) { ?>
							<a href="<?php echo $post->editLink; ?>"><?php echo $post->title; ?></a>
						<?php } else { ?>
							<?php echo $post->title; ?>
						<?php } ?>

						<div style="font-size: 11px;">
							<?php echo JText::_('COM_EASYDISCUSS_IP_ADDRESS');?>: <?php echo $post->ip;?>
						</div>
						<?php if ($this->config->get('main_password_protection') && $post->password) { ?>
							<span rel="ed-tooltip" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_THIS_POST_PASSWORD_PROTECTED' , true);?>">
								<i class="icon-lock"></i>
							</span>
						<?php } ?>

						<?php if (!empty($parentId)) { ?>
							<p>
								<?php echo $post->content; ?>
							</p>
						<?php } ?>
					</td>

					<td class="center">
						<a href="<?php echo JRoute::_('index.php?option=com_easydiscuss&view=categories&layout=form&id=' . $post->category->id); ?>">
							<?php echo $this->escape($post->category->title);?>
						</a>
					</td>

					<td class="center">
						<?php echo $this->html('table.featured', 'posts', $post, 'featured'); ?>
					</td>
					<td class="center">
						<?php echo $this->html('table.publish', $post, $i-1); ?>
					</td>

					<td class="center">
						<?php echo $post->cnt; ?>
					</td>

					<td class="center">
						<?php echo $post->hits; ?>
					</td>

					<td class="center">
						<?php echo $post->sum_totalvote; ?>
					</td>

					<td class="center">
						<?php if ($post->user_id && $post->user_id != '0') {?>
							<a href="index.php?option=com_easydiscuss&amp;view=user&amp;task=edit"><?php echo $post->creatorName; ?></a>
						<?php } else { ?>
							&lt;<a href="mailto:<?php echo $post->poster_email;?>" target="_blank"><?php echo $post->poster_email; ?></a>&gt;
						<?php } ?>
					</td>

					<td class="center">
						<?php echo $post->displayDate; ?>
					</td>

					<td class="center">
						<?php echo $post->id; ?>
					</td>
				</tr>
				<?php } ?>
			<?php } else { ?>
				<tr>
					<td colspan="12" class="center empty">
						<?php echo JText::_('COM_EASYDISCUSS_NO_DISCUSSIONS_YET'); ?>
					</td>
				</tr>
			<?php } ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="12">
						<div class="footer-pagination center">
							<?php echo $pagination->getListFooter(); ?>
						</div>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>

	<input type="hidden" name="from" value="questions" />
	<input type="hidden" name="move_category" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $orderDirection; ?>" />
	
	<?php echo $this->html('form.hidden', 'posts', 'posts'); ?>
</form>