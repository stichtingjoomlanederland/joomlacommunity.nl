<?php
/**
 * @package   AkeebaBackup
 * @copyright Copyright (c)2006-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

$configurl = base64_encode(JUri::base().'index.php?option=com_akeeba&view=Configuration');
?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<input type="hidden" name="option" value="com_akeeba" />
	<input type="hidden" name="view" value="Profiles" />
	<input type="hidden" name="boxchecked" id="boxchecked" value="0" />
	<input type="hidden" name="task" id="task" value="browse" />
	<input type="hidden" name="hidemainmenu" id="hidemainmenu" value="0"/>
	<input type="hidden" name="filter_order" id="filter_order"
		   value="<?php echo $this->escape($this->lists->order); ?>"/>
	<input type="hidden" name="filter_order_Dir" id="filter_order_Dir"
		   value="<?php echo $this->escape($this->lists->order_Dir); ?>"/>
	<input type="hidden" name="<?php echo $this->container->platform->getToken(true); ?>" value="1" />

	<?php echo $this->loadAnyTemplate('admin:com_akeeba/CommonTemplates/ProfileName'); ?>

	<div id="filter-bar" class="btn-toolbar">
		<div class="btn-group pull-right hidden-phone">
			<label for="limit" class="element-invisible">
				<?php echo \JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?>
			</label>
			<?php echo $this->pagination->getLimitBox(); ?>

		</div>
		<div class="btn-group pull-right hidden-phone">
			<label for="directionTable"
			       class="element-invisible">
				<?php echo \JText::_('JFIELD_ORDERING_DESC'); ?>
			</label>
			<select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
				<option value="">
					<?php echo \JText::_('JFIELD_ORDERING_DESC'); ?>
				</option>
				<option value="asc" <?php echo ($this->getLists()->order_Dir == 'asc') ? 'selected="selected"' : ''; ?>>
					<?php echo \JText::_('JGLOBAL_ORDER_ASCENDING'); ?>
				</option>
				<option value="desc" <?php echo ($this->getLists()->order_Dir == 'desc') ? 'selected="selected"' : ''; ?>>
					<?php echo \JText::_('JGLOBAL_ORDER_DESCENDING'); ?>
				</option>
			</select>
		</div>
		<div class="btn-group pull-right">
			<label for="sortTable" class="element-invisible">
				<?php echo \JText::_('JGLOBAL_SORT_BY'); ?>
			</label>
			<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
				<option value="">
					<?php echo \JText::_('JGLOBAL_SORT_BY'); ?>
				</option>
				<?php echo \JHtml::_('select.options', $this->sortFields, 'value', 'text', $this->getLists()->order); ?>
			</select>
		</div>
	</div>

	<div class="clearfix"></div>

	<table class="adminlist table table-striped">
		<thead>
			<tr>
				<th width="20px">
					<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
				</th>
				<th width="20px">
					<?php echo \JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'id', $this->lists->order_Dir, $this->lists->order, 'browse'); ?>
				</th>
				<th width="20%"></th>
				<th>
					<?php echo \JHtml::_('grid.sort', 'COM_AKEEBA_PROFILES_COLLABEL_DESCRIPTION', 'description', $this->lists->order_Dir, $this->lists->order, 'browse'); ?>
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
							   placeholder="<?php echo \JText::_('COM_AKEEBA_PROFILES_COLLABEL_DESCRIPTION'); ?>"
							/>
						<button class="btn btn-mini" onclick="this.form.submit();">
							<?php echo \JText::_('JSEARCH_FILTER_SUBMIT'); ?>
						</button>
						<button class="btn btn-mini" onclick="document.adminForm.description.value='';this.form.submit();">
							<?php echo \JText::_('JSEARCH_FILTER_CLEAR'); ?>
						</button>
					</div>
				</td>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="11">
					<?php echo $this->pagination->getListFooter(); ?>

				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php $i = 1; ?>
		<?php foreach( $this->items as $profile ): ?>
		<?php $i = 1 - $i; ?>
			<tr class="row<?php echo $i; ?>">
				<td>
					<?php echo \JHtml::_('grid.id', ++$i, $profile->id); ?>
				</td>
				<td>
					<?php echo (int) $profile->id; ?>

				</td>
				<td>
					<a class="btn btn-mini btn-primary"
							href="index.php?option=com_akeeba&task=SwitchProfile&profileid=<?php echo (int)$profile->id; ?>&returnurl=<?php echo $configurl ?>&<?php echo $this->container->platform->getToken(true); ?>=1">
						<span class="icon-cog icon-white"></span>
						<?php echo \JText::_('COM_AKEEBA_CONFIG_UI_CONFIG'); ?>
					</a>
					&nbsp;
					<a class="btn btn-mini"
						href="index.php?option=com_akeeba&view=Profile&task=read&id=<?php echo $profile->id ?>&basename=<?php echo FOF30\Utils\StringHelper::toSlug($profile->description); ?>&format=json&<?php echo $this->container->platform->getToken(true); ?>=1">
						<span class="icon-download"></span>
						<?php echo \JText::_('COM_AKEEBA_PROFILES_BTN_EXPORT'); ?>
					</a>
				</td>
				<td>
					<a href="index.php?option=com_akeeba&amp;view=Profiles&amp;task=edit&amp;id=<?php echo (int) $profile->id; ?>">
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
	<input type="hidden" name="view" value="Profiles" />
	<input type="hidden" name="boxchecked" id="boxchecked" value="0" />
	<input type="hidden" name="task" id="task" value="import" />
	<input type="hidden" name="<?php echo $this->container->platform->getToken(true); ?>" value="1" />

	<input type="file" name="importfile" class="input-medium" />
	<button class="btn btn-success">
		<span class="icon-upload icon-white"></span>
		<?php echo \JText::_('COM_AKEEBA_PROFILES_HEADER_IMPORT'); ?>
	</button>
	<span class="help-inline">
		<?php echo \JText::_('COM_AKEEBA_PROFILES_LBL_IMPORT_HELP'); ?>
	</span>
</form>