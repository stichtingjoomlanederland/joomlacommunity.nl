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
			<?php echo $this->html('table.search', 'search', $search); ?>
		</div>

		<div class="app-filter-bar__cell app-filter-bar__cell--auto-size app-filter-bar__cell--divider-left">
			<div class="app-filter-bar__filter-wrap">
				<?php echo $this->html('table.filter', 'filter_state', $filter, array('published' => 'COM_EASYDISCUSS_PUBLISHED', 'unpublished' => 'COM_EASYDISCUSS_UNPUBLISHED')); ?>
			</div>
		</div>

		<div class="app-filter-bar__cell app-filter-bar__cell--auto-size app-filter-bar__cell--divider-left">
			<div class="app-filter-bar__filter-wrap">
				<?php echo $categoryFilter; ?>
			</div>
		</div>

		<div class="app-filter-bar__cell app-filter-bar__cell--auto-size app-filter-bar__cell--divider-left">
			<div class="app-filter-bar__filter-wrap">
				<?php echo $postLabelFilter; ?>
			</div>
		</div>

		<div class="app-filter-bar__cell app-filter-bar__cell--empty"></div>

		<div class="app-filter-bar__cell app-filter-bar__cell--divider-left app-filter-bar__cell--last t-text--center">
			<div class="app-filter-bar__filter-wrap app-filter-bar__filter-wrap--limit">
				<?php echo $this->html('table.limit', $limit); ?>
			</div>
		</div>
	</div>

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
						<?php echo JHTML::_('grid.sort', 'Title', 'a.title', $orderDirection, $order); ?>
					</th>

					<?php if (!$browse) { ?>
					<th width="8%" class="center">
						<?php echo JText::_('COM_ED_TABLE_COLUMN_INFO'); ?>
					</th>
					
					<th width="8%" class="center">
						<?php echo JText::_('COM_EASYDISCUSS_FEATURED'); ?>
					</th>

					<th width="8%" class="center">
						<?php echo JText::_('COM_EASYDISCUSS_PUBLISHED'); ?>
					</th>

					<th width="8%" class="center">
						<?php echo JText::_('COM_EASYDISCUSS_POSTS_VOTES'); ?>
					</th>

					<th width="15%" class="center">
						<?php echo JText::_('COM_EASYDISCUSS_USER'); ?>
					</th>

					<th width="10%" class="center">
						<?php echo JHTML::_('grid.sort', JText::_('COM_EASYDISCUSS_DATE'), 'a.created', $orderDirection, $order); ?>
					</th>
					<?php } ?>

					<th width="1%" class="center">
						<?php echo JText::_('COM_EASYDISCUSS_COLUMN_ID');?>
					</th>
				</tr>
			</thead>
			<tbody>

			<?php if ($posts) { ?>
				<?php $i = 0; ?>
				<?php foreach ($posts as $post) { ?>
				<tr>
					<?php if (!$browse) { ?>
					<td class="center">
						<?php echo $this->html('table.checkbox', $i++, $post->id); ?>
					</td>
					<?php } ?>

					<td >
						<?php if ($browse) { ?>
							<a href="javascript:void(0);" onclick="parent.<?php echo $browseFunction; ?>('<?php echo $post->id;?>','<?php echo addslashes($this->escape($post->getTitle()));?>');"><?php echo $post->getTitle();?></a>
						<?php } else { ?>
							<a href="<?php echo $post->editLink; ?>" target="_blank"><?php echo $post->title; ?></a>
						<?php } ?>

						<div class="t-mt--sm">
							<ol class="g-list-inline g-list-inline--delimited small">
								<li>
									<?php echo JText::_('COM_EASYDISCUSS_CATEGORY'); ?>: <a href="<?php echo JRoute::_('index.php?option=com_easydiscuss&view=categories&layout=form&id=' . $post->category->id); ?>"><?php echo $this->escape($post->category->title);?></a>
								</li>
								<li data-breadcrumb="|">
									<?php echo JText::_('COM_EASYDISCUSS_IP_ADDRESS');?>: <?php echo $post->ip ? $post->ip : '&mdash;';?>
								</li>
							</ol>
						</div>
					</td>

					<?php if (!$browse) { ?>
						<td width="5%" class="center small">
							<?php if ($this->config->get('main_password_protection') && $post->password) { ?>
							<span class="ed-state-protected t-mr--sm" data-ed-provide="tooltip" data-original-title="<?php echo JText::_('COM_EASYDISCUSS_THIS_POST_PASSWORD_PROTECTED');?>"></span>
							<?php } ?>
							
							<?php if ($post->isLocked()) { ?>
							<span class="ed-state-locked" data-ed-provide="tooltip" data-original-title="<?php echo JText::_('COM_ED_POST_INFO_LOCKED');?>"></span>
							<?php } ?>
						</td>

						<td class="center">
							<?php echo $this->html('table.featured', 'posts', $post, 'featured'); ?>
						</td>
						<td class="center">
							<?php echo $this->html('table.publish', $post, $i-1); ?>
						</td>

						<td class="center">
							<?php echo $post->sum_totalvote;?>
						</td>

						<td class="center">
							<?php if ($post->user_id && $post->user_id != '0') {?>
								<a href="index.php?option=com_easydiscuss&view=users&layout=form&task=edit&id=<?php echo $post->user_id;?>"><?php echo $post->getOwner()->getName(); ?></a>
							<?php } else { ?>
								<?php echo $post->poster_name; ?> (Guest)
							<?php } ?>
						</td>

						<td class="center">
							<?php echo $post->displayDate; ?>
						</td>
					<?php } ?>

					<td class="center">
						<?php echo $post->id; ?>
					</td>
				</tr>
				<?php } ?>
			<?php } else { ?>
				<tr>
					<td colspan="11" class="center empty">
						<?php echo JText::_('COM_EASYDISCUSS_NO_DISCUSSIONS_YET'); ?>
					</td>
				</tr>
			<?php } ?>
			</tbody>


			<tfoot>
				<tr>
					<td colspan="11">
						<div class="footer-pagination center<?php echo (!$browse) ? ' is-loading': ''; ?>" data-questions-pagination>
							<?php if ($browse) { ?>
							<?php echo $pagination->getListFooter(); ?>
							<?php } else { ?>
								<div class="o-loading">
									<div class="o-loading__content">
										<i class="fa fa-spinner fa-spin"></i>
									</div>
								</div>
							<?php } ?>
						</div>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>

	<?php if ($browse) { ?>
	<input type="hidden" name="tmpl" value="component" />
	<?php } ?>

	<input type="hidden" name="browse" value="<?php echo $browse;?>" />
	<input type="hidden" name="browsefunction" value="<?php echo $browseFunction;?>" />
	<input type="hidden" name="from" value="questions" />
	<input type="hidden" name="move_category" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $orderDirection; ?>" />

	<?php echo $this->html('form.action', 'posts', 'posts'); ?>
</form>
