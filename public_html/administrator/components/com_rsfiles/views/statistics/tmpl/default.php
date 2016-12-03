<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.keepalive'); ?>

<form method="post" action="<?php echo JRoute::_('index.php?option=com_rsfiles&view=statistics'); ?>" name="adminForm" id="adminForm">
<div class="row-fluid">
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
		<?php echo $this->filterbar->show(); ?>
		<table class="table table-striped adminlist">
			<thead>
				<th width="1%" align="center" class="center"><input type="checkbox" name="checkall-toggle" id="rscheckbox" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this);"/></th>
				<th><?php echo JText::_('COM_RSFILES_FILE_PATH'); ?></th>
				<th width="3%" align="center" class="center hidden-phone"><?php echo JText::_('COM_RSFILES_STATISTICS_HITS'); ?></th>
				<th width="3%" align="center" class="center hidden-phone"><?php echo JText::_('COM_RSFILES_STATISTICS_DOWNLOADS'); ?></th>
			</thead>
			<tbody>
				<?php foreach ($this->items as $i => $item) { ?>
					<tr class="row<?php echo $i % 2; ?>">
						<td class="center" align="center">
							<?php echo JHtml::_('grid.id', $i, $item->IdFile); ?>
						</td>
						<td class="nowrap has-context">
							<a href="<?php echo JRoute::_('index.php?option=com_rsfiles&view=statistics&layout=view&id='.$item->IdFile); ?>"><?php echo $item->FilePath; ?></a>
						</td>
						
						<td align="center" class="center hidden-phone">
							<?php echo $item->hits; ?>
						</td>
						<td align="center" class="center hidden-phone">
							<?php echo $item->downloads; ?>
						</td>
					</tr>
				<?php } ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="4" align="center"><?php echo $this->pagination->getListFooter(); ?></td>
				</tr>
			</tfoot>
		</table>
	</div>
</div>
	
	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="return" value="<?php echo $this->return; ?>" />
</form>