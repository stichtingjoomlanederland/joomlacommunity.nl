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
		<div class="app-filter-bar__cell">
			<?php echo $this->html('table.search', 'search', $search); ?>
		</div>
		
		<div class="app-filter-bar__cell app-filter-bar__cell--empty"></div>

		<div class="app-filter-bar__cell app-filter-bar__cell--divider-left app-filter-bar__cell--last t-text--center">
			<div class="app-filter-bar__filter-wrap">
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
					<th style="text-align:left;">
						<?php echo JText::_('COM_ED_COLUMN_TITLE'); ?>
					</th>

					<th width="5%" class="center">
						<?php echo JText::_('COM_EASYDISCUSS_TABLE_COLUMN_TYPE'); ?>
					</th>

					<th width="15%" class="center">
						<?php echo JText::_('COM_ED_TABLE_COLUMN_CATEGORY'); ?>
					</th>

					<th width="15%" class="center">
						<?php echo JText::_('COM_EASYDISCUSS_IP_ADDRESS'); ?>
					</th>

					<th width="15%" class="center">
						<?php echo JText::_('COM_EASYDISCUSS_USER'); ?>
					</th>

					<th width="15%" class="center">
						<?php echo JText::_('COM_EASYDISCUSS_DATE'); ?>
					</th>

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
					<td class="center">
						<?php echo $this->html('table.checkbox', $i++, $post->id); ?>
					</td>
					
					<td>

						<?php if ($post->isQuestion()) { ?>
							<a target="_blank" href="<?php echo $post->editLink;?>"><?php echo $post->title;?></a>
						<?php } else { ?>
							<?php echo $post->title; ?>
						<?php } ?>

						<?php if ($post->isReply()) { ?>
							<a target="_blank" href="<?php echo $post->editLink;?>"><?php echo JText::_('COM_EASYDISCUSS_VIEW_REPLY'); ?></a>
						<?php } ?>

						<div class="small">
							<ol class="g-list-inline g-list-inline--dashed t-mb--md">
								<?php if ($post->isReply()) { ?>
								<li>
									<?php echo JText::_('COM_EASYDISCUSS_POST');?>: <a target="_blank"  href="<?php echo $post->getPermalink(true); ?>"><?php echo $post->getParent()->title;?></a>
								</li>
								<?php } ?>
							</ol>
						</div>
					</td>

					<td class="center">
						<?php if ($post->isQuestion()) { ?>
							<?php echo JText::_('COM_ED_TYPE_QUESTION'); ?>
						<?php } else { ?>
							<?php echo JText::_('COM_ED_TYPE_REPLY'); ?>
						<?php } ?>
					</td>

					<td class="center">
						<a href="index.php?option=com_easydiscuss&view=categories&layout=form&id=<?php echo $post->getCategory()->id;?>"><?php echo $post->getCategory()->getTitle();?></a>
					</td>

					<td class="center">
						<?php echo $post->ip;?>
					</td>

					<td class="center">
						<?php if ($post->user_id && $post->user_id != '0') {?>
							<a href="index.php?option=com_easydiscuss&view=users&layout=form&id=<?php echo $post->user_id;?>">
								<?php echo $post->getOwner()->getName(); ?>
							</a>
						<?php } else { ?>
							<?php echo $post->poster_name; ?>
							&lt;<a href="mailto:<?php echo $post->poster_email;?>" target="_blank"><?php echo $post->poster_email; ?></a>&gt;
						<?php } ?>
					</td>

					<td class="center">
						<?php echo $post->getDateObject()->toSql(); ?>
					</td>

					<td class="center">
						<?php echo $post->id; ?>
					</td>
				</tr>
				<?php } ?>
			<?php } else { ?>
				<tr>
					<td colspan="8" class="center empty">
						<?php echo JText::_('COM_EASYDISCUSS_NO_PENDING_POSTS_YET'); ?>
					</td>
				</tr>
			<?php } ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="8">
						<div class="footer-pagination center">
							<?php echo $pagination->getListFooter(); ?>
						</div>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>

	<input type="hidden" name="layout" value="pending" />
	<input type="hidden" name="from" value="pending" />
	
	<?php echo $this->html('form.action', 'posts', 'posts'); ?>
</form>