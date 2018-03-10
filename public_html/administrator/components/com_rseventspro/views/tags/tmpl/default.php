<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive');

$listOrder	= $this->escape($this->state->get('list.ordering','name'));
$listDirn	= $this->escape($this->state->get('list.direction','asc')); ?>

<form method="post" action="<?php echo JRoute::_('index.php?option=com_rseventspro&view=tags'); ?>" name="adminForm" id="adminForm">
	<div class="row-fluid">
		<div id="j-sidebar-container" class="span2">
			<?php echo JHtmlSidebar::render(); ?>
		</div>
		<div id="j-main-container" class="span10 j-main-container">
			<?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
			
			<table class="table table-striped adminlist">
				<thead>
					<th width="1%" align="center" class="small"><input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this);"/></th>
					<th><?php echo JHtml::_('searchtools.sort', 'JGLOBAL_TITLE', 'name', $listDirn, $listOrder); ?></th>
					<th width="30%"><?php echo JText::_('COM_RSEVENTSPRO_TAG_USED_IN'); ?></th>
					<th width="1%" style="min-width:55px" class="nowrap center"><?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'published', $listDirn, $listOrder); ?></th>
					<th width="1%" class="nowrap hidden-phone"><?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder); ?></th>
				</thead>
				<tbody id="rseprocontainer">
					<?php foreach ($this->items as $i => $item) { ?>
						<tr class="row<?php echo $i % 2; ?>">
							<td class="center">
								<?php echo JHtml::_('grid.id', $i, $item->id); ?>
							</td>
							<td class="nowrap has-context">
								<a href="<?php echo JRoute::_('index.php?option=com_rseventspro&task=tag.edit&id='.$item->id); ?>"><?php echo $item->name; ?></a>
							</td>
							<td class="center hidden-phone">
								<?php echo $this->getEvents($item->id); ?>
							</td>
							<td class="center">
								<?php echo JHtml::_('jgrid.published', $item->published, $i, 'tags.'); ?>
							</td>
							<td class="center hidden-phone">
								<?php echo (int) $item->id; ?>
							</td>
						</tr>
					<?php } ?>
				</tbody>
				<tfoot>
				<tr>
					<td colspan="5" style="text-align: center;">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			</table>
		</div>
	</div>
	
	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="task" value="" />
</form>