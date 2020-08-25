<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access');

$listOrder	= $this->escape($this->state->get('list.ordering', 'date'));
$listDirn	= $this->escape($this->state->get('list.direction', 'DESC')); ?>

<form action="<?php echo JRoute::_('index.php?option=com_rscomments&view=comments'); ?>" method="post" name="adminForm" id="adminForm">
	<?php echo RSCommentsAdapterGrid::sidebar(); ?>
		<?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
			
		<table class="table table-striped table-hover">
			<caption id="captionTable" class="sr-only">
				<span id="orderedBy"><?php echo JText::_('JGLOBAL_SORTED_BY'); ?> </span>,
				<span id="filteredBy"><?php echo JText::_('JGLOBAL_FILTERED_BY'); ?></span>
			</caption>
			<thead>
				<tr>
					<th width="2%"><input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this);"/></th>
					<th class="hidden-phone" width="1%"><?php echo JText::_('COM_RSCOMMENTS_COMMENT_ID'); ?></th>
					<th nowrap="nowrap"><?php echo JHtml::_('searchtools.sort', JText::_('COM_RSCOMMENTS_COMMENT_NAME'), 'comment', $listDirn, $listOrder); ?></th>
					<th width="5%" class="center text-center"><?php echo JText::_('COM_RSCOMMENTS_REPORTS'); ?></th>
					<th width="10%" class="center text-center"><?php echo JHtml::_('searchtools.sort', JText::_('COM_RSCOMMENTS_COMMENT_AUTHOR'), 'name', $listDirn, $listOrder); ?></th>
					<th class="hidden-phone center text-center" width="8%"><?php echo JHtml::_('searchtools.sort', JText::_('COM_RSCOMMENTS_COMMENT_COMPONENT'), 'option', $listDirn, $listOrder); ?></th>
					<th width="10%" class="center text-center"><?php echo JHtml::_('searchtools.sort', JText::_('COM_RSCOMMENTS_COMMENT_DATE'), 'date', $listDirn, $listOrder); ?></th>
					<th class="hidden-phone" width="5%"><?php echo JHtml::_('searchtools.sort', JText::_('COM_RSCOMMENTS_COMMENT_PUBLISHED'), 'published', $listDirn, $listOrder); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($this->items as $i => $item) { ?>
			<?php $comment_length = mb_strlen(RSCommentsHelperAdmin::cleanComment(strip_tags($item->comment))); ?>
				<tr class="row<?php echo $i % 2; ?>">
					<td width="2%"><?php echo JHtml::_('grid.id', $i, $item->IdComment); ?></td>
					<td class="hidden-phone" ><?php echo $item->IdComment; ?></td>
					
					<td>
						<span class="rsc_subject">
							<a href="<?php echo JRoute::_('index.php?option=com_rscomments&task=comment.edit&IdComment='.$item->IdComment); ?>">
								<?php echo !empty($item->subject) ? $item->subject : '<i>'.JText::_('COM_RSCOMMENTS_NO_TITLE').'</i>';?>
							</a>
						</span>
						
						<br />
						
						<div class="rsc_comment">
							<?php echo RSCommentsHelperAdmin::cleanComment(strip_tags(mb_substr($item->comment,0,255))); ?>
							<?php echo ($comment_length > 255) ? '<span id="rsc_rest'.$i.'" style="display:none;">'.RSCommentsHelperAdmin::cleanComment(strip_tags(mb_substr($item->comment,255,$comment_length))).'</span>' : ''; ?>
						</div>
						
						<?php if (mb_strlen(RSCommentsHelperAdmin::cleanComment(strip_tags($item->comment))) > 255) echo '<a href="javascript:rsc_show_all('.$i.');" class="rsc_showall" id="show_btn'.$i.'">'.JText::_('COM_RSCOMMENTS_SHOW_ALL').'</a>';?>
					</td>
					
					<td class="center text-center">
						<a href="<?php echo JRoute::_('index.php?option=com_rscomments&view=reports&id='.$item->IdComment); ?>">
							<?php echo $item->reports; ?>
						</a>
					</td>
					
					<td class="center text-center">
						<?php $name = $item->anonymous ? ($item->name ? $item->name : JText::_('COM_RSCOMMENTS_ANONYMOUS')) : $item->name; ?>
						<span class="<?php echo RSTooltip::tooltipClass(); ?>" title="<?php echo RSTooltip::tooltipText($item->email.'<br />'.JText::sprintf('COM_RSCOMMENTS_AUTHOR_INFO_NAME',$name).'<br/>'.JText::sprintf('COM_RSCOMMENTS_AUTHOR_INFO_SITE',$item->website).'<br/>'.JText::sprintf('COM_RSCOMMENTS_AUTHOR_INFO_IP',str_replace(':','&#058;',$item->ip))); ?>">
							<?php if ($item->email) { ?><a href="mailto:<?php echo $item->email; ?>"><?php } ?>
								<i class="fa fa-info-circle"></i> <?php echo $name; ?>
							<?php if ($item->email) { ?></a><?php } ?>
						</span>
					</td>
					
					<td class="hidden-phone center text-center"><?php echo RSCommentsHelperAdmin::component($item->option); ?></td>
					
					<td class="center text-center"><?php echo RSCommentsHelperAdmin::showDate($item->date); ?></td>
					
					<td class="hidden-phone center text-center">
						<?php echo JHtml::_('jgrid.published', $item->published, $i, 'comments.', true, 'cb'); ?>
					</td>
				</tr>
			<?php } ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="8"><?php echo $this->pagination->getListFooter(); ?></td>
				</tr>
			</tfoot>
		</table>
			
		<?php echo JHtml::_( 'form.token' ); ?>
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="task" value="" />
	</div>
</form>

<script type="text/javascript">
function rsc_show_all(id) {
	var all_content = jQuery('#rsc_rest'+id);
	var toggle_btn	= jQuery('#show_btn'+id);
	
	if (all_content.css('display') == 'none') {
		all_content.css('display','block');
		toggle_btn.text('<?php echo JText::_('COM_RSCOMMENTS_HIDE_ALL',true); ?>');
	} else {
		all_content.css('display','none');
		toggle_btn.text('<?php echo JText::_('COM_RSCOMMENTS_SHOW_ALL',true); ?>');
	}
}

function rsc_change_filter() {
	 if (document.getElementById('filter_component_id')) {
		 document.getElementById('filter_component_id').value='';
	 }
}
</script>