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
						<?php echo JText::_('COM_EASYDISCUSS_TABLE_COLUMN_REPLY'); ?>
					</td>

					<td width="5%" class="text-center">
						<?php echo JText::_('COM_EASYDISCUSS_PUBLISHED'); ?>
					</td>

					<td width="1%" class="text-center">
						<?php echo JText::_('COM_EASYDISCUSS_POSTS_VOTES'); ?>
					</th>
					<td width="20%" class="text-center">
						<?php echo JText::_('COM_EASYDISCUSS_USER'); ?>
					</th>
					<td width="10%" class="text-center">
						<?php echo JText::_('COM_EASYDISCUSS_DATE');?>
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
						<p>
							<?php echo $post->getContent(); ?>
						</p>

						<div style="font-size: 11px;">
							<span style="padding-right: 5px;border-right: 1px solid #d7d7d7;">
								<?php echo JText::_('COM_EASYDISCUSS_POST');?>: <a href="index.php?option=com_easydiscuss&view=post&layout=edit&id=<?php echo $post->getParent()->id;?>"><?php echo $post->getParent()->title;?></a>
							</span>

							<span style="padding-left: 6px;">
								<?php echo JText::_('COM_EASYDISCUSS_IP_ADDRESS');?>: <?php echo $post->ip;?>
							</span>
						</div>
					</td>

					<td class="center">
						<?php echo $this->html('table.publish', $post, $i-1); ?>
					</td>

					<td class="center">
						<?php echo $post->sum_totalvote; ?>
					</td>

					<td class="center">
						<?php if ($post->user_id && $post->user_id != '0') {?>
							<a href="index.php?option=com_easydiscuss&amp;view=user&amp;task=edit&amp;id=<?php echo $post->user_id;?>"><?php echo $post->getOwner()->getName(); ?></a>
						<?php } else { ?>
							<?php echo $post->poster_name; ?>
							&lt;<a href="mailto:<?php echo $post->poster_email;?>" target="_blank"><?php echo $post->poster_email; ?></a>&gt;
						<?php } ?>
					</td>					

					<td class="center">
						<?php echo $post->getDateObject()->toSql(true); ?>
					</td>

					<td class="center">
						<?php echo $post->id; ?>
					</td>
				</tr>
				<?php } ?>
			<?php } else { ?>
				<tr>
					<td colspan="12" class="center empty">
						<?php echo JText::_('COM_EASYDISCUSS_NO_REPLIES_ON_THE_SITE_YET'); ?>
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
	
	<input type="hidden" name="layout" value="replies" />
	<input type="hidden" name="from" value="replies" />
	
	<?php echo $this->html('form.hidden', 'posts', 'posts'); ?>
</form>