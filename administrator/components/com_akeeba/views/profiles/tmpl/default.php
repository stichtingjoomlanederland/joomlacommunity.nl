<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2009-2016 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @since 1.3
 */

defined('_JEXEC') or die();

JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');

$configurl = base64_encode(JUri::base().'index.php?option=com_akeeba&view=config');
$token = JFactory::getSession()->getFormToken();
?>
<script type="text/javascript">
	Joomla.orderTable = function ()
	{
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '$order')
		{
			dirn = 'asc';
		}
		else
		{
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn);
	}
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<input type="hidden" name="option" value="com_akeeba" />
	<input type="hidden" name="view" value="profiles" />
	<input type="hidden" name="boxchecked" id="boxchecked" value="0" />
	<input type="hidden" name="task" id="task" value="" />
	<input type="hidden" name="hidemainmenu" id="hidemainmenu" value="0"/>
	<input type="hidden" name="filter_order" id="filter_order"
		   value="<?php echo $this->escape($this->lists->order) ?>"/>
	<input type="hidden" name="filter_order_Dir" id="filter_order_Dir"
		   value="<?php echo $this->escape($this->lists->order_Dir) ?>"/>
	<input type="hidden" name="<?php echo JFactory::getSession()->getFormToken()?>" value="1" />

	<div class="alert alert-info">
		<strong><?php echo JText::_('COM_AKEEBA_CPANEL_PROFILE_TITLE'); ?></strong>:
		#<?php echo $this->profileid; ?> <?php echo $this->profilename; ?>
	</div>

	<?php if (version_compare(JVERSION, '3.0.0', 'ge')): ?>
	<div id="filter-bar" class="btn-toolbar">
		<div class="btn-group pull-right hidden-phone">
			<label for="limit"
			       class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC') ?></label>
			<?php echo $this->getModel()->getPagination()->getLimitBox(); ?>
		</div>
		<?php
		$asc_sel = ($this->getLists()->order_Dir == 'asc') ? 'selected="selected"' : '';
		$desc_sel = ($this->getLists()->order_Dir == 'desc') ? 'selected="selected"' : '';
		?>
		<div class="btn-group pull-right hidden-phone">
			<label for="directionTable"
			       class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC') ?></label>
			<select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
				<option value=""><?php echo JText::_('JFIELD_ORDERING_DESC') ?></option>
				<option
					value="asc" <?php echo $asc_sel ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING') ?></option>
				<option
					value="desc" <?php echo $desc_sel ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING') ?></option>
			</select>
		</div>
		<div class="btn-group pull-right">
			<label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY') ?></label>
			<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
				<option value=""><?php echo JText::_('JGLOBAL_SORT_BY') ?></option>
				<?php echo JHtml::_('select.options', $this->sortFields, 'value', 'text', $this->getLists()->order) ?>
			</select>
		</div>
	</div>
	<?php endif; ?>

	<div class="clearfix"></div>

	<table class="adminlist table table-striped">
		<thead>
			<tr>
				<th width="20px">
					<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
				</th>
				<th width="20px">
					<?php echo JHTML::_('grid.sort', 'JGRID_HEADING_ID', 'id', $this->lists->order_Dir, $this->lists->order, 'browse'); ?>
				</th>
				<th width="20%"></th>
				<th>
					<?php echo JHTML::_('grid.sort', 'COM_AKEEBA_PROFILES_COLLABEL_DESCRIPTION', 'description', $this->lists->order_Dir, $this->lists->order, 'browse'); ?>
				</th>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td class="form-inline">
					<div class="form-inline">
						<input type="text" name="description" id="description"
							   value="<?php echo $this->escape($this->getModel()->getState('description', '')); ?>" size="30"
							   class="input-small" onchange="document.adminForm.submit();"
							   placeholder="<?php echo JText::_('COM_AKEEBA_PROFILES_COLLABEL_DESCRIPTION') ?>"
							/>
						<button class="btn btn-mini" onclick="this.form.submit();">
							<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>
						</button>
						<button class="btn btn-mini" onclick="document.adminForm.description.value='';this.form.submit();">
							<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>
						</button>
					</div>
				</td>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="11">
					<?php echo $this->pagination->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		$i = 1;
		foreach( $this->items as $profile ):
		$id = JHTML::_('grid.id', ++$i, $profile->id);
		$link = 'index.php?option=com_akeeba&amp;view=profiles&amp;task=edit&amp;id='.$profile->id;
		$i = 1 - $i;
		$exportBaseName = F0FStringUtils::toSlug($profile->description);
		?>
			<tr class="row<?php echo $i; ?>">
				<td><?php echo $id; ?></td>
				<td><?php echo $profile->id ?></td>
				<td>
					<button class="btn btn-mini btn-primary" onclick="window.location='index.php?option=com_akeeba&task=switchprofile&profileid=<?php echo $profile->id ?>&returnurl=<?php echo $configurl ?>&<?php echo $token ?>=1'; return false;">
						<i class="icon-cog icon-white"></i>
						<?php echo JText::_('COM_AKEEBA_CONFIG_UI_CONFIG'); ?>
					</button>
					&nbsp;
					<button class="btn btn-mini" onclick="window.location='index.php?option=com_akeeba&view=profile&task=read&id=<?php echo $profile->id ?>&basename=<?php echo $exportBaseName?>&format=json&<?php echo $token ?>=1'; return false;">
						<i class="icon-download"></i>
						<?php echo JText::_('COM_AKEEBA_PROFILES_BTN_EXPORT'); ?>
					</button>
				</td>
				<td>
					<a href="<?php echo $link; ?>">
						<?php echo $this->escape($profile->description); ?>
					</a>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</form>

<form action="index.php" method="post" name="importForm" enctype="multipart/form-data" id="importForm" class="form form-inline well">
	<input type="hidden" name="option" value="com_akeeba" />
	<input type="hidden" name="view" value="profiles" />
	<input type="hidden" name="boxchecked" id="boxchecked" value="0" />
	<input type="hidden" name="task" id="task" value="import" />
	<input type="hidden" name="<?php echo JFactory::getSession()->getFormToken()?>" value="1" />

	<input type="file" name="importfile" class="input-medium" />
	<button class="btn btn-success">
		<i class="icon-upload icon-white"></i>
		<?php echo JText::_('COM_AKEEBA_PROFILES_HEADER_IMPORT');?>
	</button>
	<span class="help-inline">
		<?php echo JText::_('COM_AKEEBA_PROFILES_LBL_IMPORT_HELP');?>
	</span>
</form>