<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction')); ?>

<form method="post" action="<?php echo JRoute::_('index.php?option=com_rseventspro&view=payments'); ?>" name="adminForm" id="adminForm">
<div class="row-fluid">
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
		<?php echo $this->filterbar->show(); ?>
		<table class="table table-striped adminlist">
			<thead>
				<th width="1%" align="center" class="center"><input type="checkbox" name="checkall-toggle" id="rscheckbox" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this);"/></th>
				<th><?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'name', $listDirn, $listOrder); ?></th>
				<th width="1%" style="min-width:55px" class="nowrap center" align="center"><?php echo JHtml::_('grid.sort', 'JSTATUS', 'published', $listDirn, $listOrder); ?></th>
				<th width="1%" class="nowrap hidden-phone center" align="center"><?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder); ?></th>
			</thead>
			<tbody id="rseprocontainer">
				<?php foreach ($this->plugins as $j => $plugin) { ?>
					<tr class="row<?php echo $j % 2; ?>">
						<td class="center"></td>
						<td class="nowrap has-context">
							<a href="<?php echo JRoute::_('index.php?option=com_plugins&task=plugin.edit&extension_id='.$plugin->id); ?>"><?php echo $plugin->name; ?></a>
						</td>
						<td class="center">
							<?php echo $plugin->published ? JText::_('JPUBLISHED') : JText::_('JUNPUBLISHED'); ?>
						</td>
						<td class="center hidden-phone">
							<?php echo (int) $plugin->id; ?>
						</td>
					</tr>
				<?php } ?>
				<?php foreach ($this->items as $i => $item) { ?>
					<tr class="row<?php echo $i % 2; ?>">
						<td class="center" align="center">
							<?php echo JHtml::_('grid.id', $i, $item->id); ?>
						</td>
						<td class="nowrap has-context">
							<a href="<?php echo JRoute::_('index.php?option=com_rseventspro&task=payment.edit&id='.$item->id); ?>"><?php echo $item->name; ?></a>
						</td>
						<td class="center" align="center">
							<?php echo JHtml::_('jgrid.published', $item->published, $i, 'payments.'); ?>
						</td>
						<td class="center hidden-phone" align="center">
							<?php echo (int) $item->id; ?>
						</td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</div>
	
	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="task" value="" />
</form>