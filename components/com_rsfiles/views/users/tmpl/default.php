<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access'); 
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction')); ?>

<div class="rsfiles-layout">
<form action="<?php echo JRoute::_('index.php?option=com_rsfiles&view=users&tmpl=component');?>" method="post" name="adminForm" id="adminForm">
	<fieldset class="filter">
		<div id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left">
				<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>
				<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" />
			</div>
			<div class="btn-group pull-left">
				<button class="btn tip <?php echo rsfilesHelper::tooltipClass(); ?>" type="submit" title="<?php echo rsfilesHelper::tooltipText(JText::_('JSEARCH_FILTER_SUBMIT')); ?>"><i class="rsicon-search"></i></button>
				<button class="btn tip <?php echo rsfilesHelper::tooltipClass(); ?>" type="button" onclick="document.id('filter_search').value='';this.form.submit();" title="<?php echo rsfilesHelper::tooltipText(JText::_('JSEARCH_FILTER_CLEAR')); ?>"><i class="rsicon-delete"></i></button>
			</div>
			<div class="btn-group pull-right">
				<?php echo JHtml::_('access.usergroup', 'filter_group_id', $this->state->get('filter.group_id'), 'onchange="this.form.submit()"'); ?>
			</div>
		</div>
	</fieldset>

	<table class="table table-striped table-condensed">
		<thead>
			<tr>
				<th align="left">
					<?php echo JHtml::_('grid.sort', 'COM_RSFILES_HEADING_NAME', 'a.name', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap" width="25%">
					<?php echo JHtml::_('grid.sort', 'JGLOBAL_USERNAME', 'a.username', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap" width="25%">
					<?php echo JHtml::_('grid.sort', 'COM_RSFILES_HEADING_GROUPS', 'group_names', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="3">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php
			if (!empty($this->items)) {
			$i = 0;
			foreach ($this->items as $item) { ?>
			<tr class="row<?php echo $i % 2; ?>">
				<td>
					<a class="pointer" onclick="if (window.parent) window.parent.rsf_create_briefcase('<?php echo $this->escape(addslashes(JURI::root())); ?>','<?php echo $item->id; ?>');">
						<?php echo $item->name; ?>
					</a>
				</td>
				<td align="center">
					<?php echo $item->username; ?>
				</td>
				<td align="left">
					<?php echo nl2br($item->group_names); ?>
				</td>
			</tr>
		<?php } } ?>
		</tbody>
	</table>
	
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
</div>