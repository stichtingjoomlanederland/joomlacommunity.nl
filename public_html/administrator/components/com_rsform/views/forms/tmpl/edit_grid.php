<?php
/**
 * @package RSForm! Pro
 * @copyright (C) 2007-2019 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

JHtml::_('jquery.ui', array('core', 'sortable'));
JHtml::script('com_rsform/admin/jquery.ui.resizable.js', array('relative' => true, 'version' => 'auto'));
JHtml::stylesheet('com_rsform/admin/jquery.ui.resizable.css', array('relative' => true, 'version' => 'auto'));

JText::script('RSFP_ROW_OPTIONS');
JText::script('RSFP_ADD_NEW_ROW');
JText::script('RSFP_DELETE_ROW');
JText::script('RSFP_GRID_CANNOT_REMOVE_ROW');
JText::script('RSFP_GRID_REMOVE_ROW_CONFIRM');
JText::script('RSFP_GRID_CUT');
JText::script('RSFP_GRID_NOTHING_TO_PASTE');
JText::script('RSFP_GRID_PASTE_ITEMS');
JText::script('RSFP_GRID_NOTHING_TO_PUBLISH');
JText::script('RSFP_GRID_PUBLISHED');
JText::script('RSFP_GRID_UNPUBLISHED');
JText::script('RSFP_GRID_CANT_CHANGE_REQUIRED');
JText::script('RSFP_GRID_SET_AS_REQUIRED');
JText::script('RSFP_GRID_SET_AS_NOT_REQUIRED');

list($rows, $hidden) = $this->buildGrid();

echo JHtml::_('bootstrap.renderModal', 'gridModal', array(
	'title' => JText::_('RSFP_GRID_OPTIONS'),
	'footer' => $this->loadTemplate('grid_modal_footer'),
	'closeButton' => false,
	'backdrop' => 'static'
),
$this->loadTemplate('grid_modal_body'));
?>
<div id="rsfp-grid-loader">
	<div class="spinner">
		<div class="rect1"></div>
		<div class="rect2"></div>
		<div class="rect3"></div>
		<div class="rect4"></div>
		<div class="rect5"></div>
	</div>
</div>
<div class="rsfp-grid-check-all">
	<label for="toggleAllFields"><input type="checkbox" id="toggleAllFields" onclick="Joomla.checkAll(this);"/> <?php echo JText::_('RSFP_CHECK_ALL'); ?></label>
</div>
<div id="rsfp-grid-row-container">
	<?php
	$i = 0;
	foreach ($rows as $row_index => $row)
	{
		$has_pagebreak = !empty($row['has_pagebreak']);
		?>
		<div class="rsfp-grid-row<?php if ($has_pagebreak) { ?> rsfp-grid-page-container<?php } ?>">
			<?php
			foreach ($row['columns'] as $column_index => $fields)
			{
				$size = isset($row['sizes'][$column_index]) ? $row['sizes'][$column_index] : 12;
				$last_column = $column_index == count($row['columns']) - 1;
				?>
				<div class="rsfp-grid-column rsfp-grid-column<?php echo $size; ?><?php if ($last_column) { ?> rsfp-grid-column-unresizable<?php } ?><?php if ($has_pagebreak) { ?> rsfp-grid-column-unconnectable<?php } ?>">
				<h3><?php echo $size; ?>/12</h3>
				<?php foreach ($fields as $field) { ?>
					<?php
					$fieldClasses = array();

					if ($field->type_id == RSFORM_FIELD_PAGEBREAK)
					{
						$fieldClasses[] = 'rsfp-grid-field-unsortable';
					}
					if (!$field->published)
					{
						$fieldClasses[] = 'rsfp-grid-unpublished-field';
					}
					if (!empty($field->hasRequired))
					{
						$fieldClasses[] = 'rsfp-grid-can-be-required';
						if ($field->required)
						{
							$fieldClasses[] = 'rsfp-grid-required-field';
						}
						else
						{
							$fieldClasses[] = 'rsfp-grid-unrequired-field';
						}
					}
					?>
					<div id="rsfp-grid-field-id-<?php echo $field->id; ?>" class="rsfp-grid-field<?php echo $fieldClasses ? ' ' . implode(' ', $fieldClasses) : ''; ?>">
						<strong class="pull-left rsfp-grid-field-name"><?php echo JHtml::_('grid.id', $i, $field->id); ?> <?php echo $this->escape($this->show_caption ? $field->caption : $field->name); ?><?php if ($field->required) { ?> (*)<?php } ?></strong>
						<div class="btn-group pull-right rsfp-grid-field-buttons">
							<button type="button" class="btn btn-small" onclick="displayTemplate('<?php echo $field->type_id; ?>','<?php echo $field->id; ?>');"><?php echo JText::_('RSFP_EDIT'); ?></button>
							<button type="button" class="btn btn-small btn-danger" onclick="if (confirm(Joomla.JText._('RSFP_REMOVE_COMPONENT_CONFIRM').replace('%s', '<?php echo $this->escape($field->name); ?>'))) removeComponent('<?php echo $this->form->FormId; ?>','<?php echo $field->id; ?>');"><?php echo JText::_('RSFP_DELETE'); ?></button>
						</div>
						<div class="clearfix"></div>
						<?php if ($this->show_previews) { ?>
						<hr />
						<?php echo $this->adjustPreview($field->preview); ?>
						<div class="clearfix"></div>
						<?php } ?>
						<input type="hidden" data-rsfpgrid value="<?php echo $field->id; ?>" />
					</div>
					<?php $i++; ?>
				<?php } ?>
				</div>
			<?php
			}
			?>
			<div class="clearfix"></div>
			<div class="rsfp-row-controls">
				<?php if (!$has_pagebreak) { ?>
				<button type="button" class="btn" onclick="RSFormPro.gridModal.open(this);"><?php echo JText::_('RSFP_ROW_OPTIONS'); ?></button>
				<?php } ?>
				<button type="button" class="btn btn-success" onclick="RSFormPro.gridModal.open(this, true);"><?php echo JText::_('RSFP_ADD_NEW_ROW'); ?></button>
				<?php if (!$has_pagebreak) { ?>
				<button type="button" class="btn btn-danger" onclick="RSFormPro.Grid.deleteRow(this);"><?php echo JText::_('RSFP_DELETE_ROW'); ?></button>
				<?php } ?>
			</div>
		</div>
	<?php
	}
	?>
	<?php if ($hidden) { ?>
	<div class="rsfp-grid-row rsfp-grid-row-unsortable">
		<div id="rsfp-grid-hidden-container">
		<h3><?php echo JText::_('RSFP_GRID_HIDDEN_FIELDS'); ?></h3>
		<?php foreach ($hidden as $field) { ?>
		<div id="rsfp-grid-field-id-<?php echo $field->id; ?>" class="rsfp-grid-field<?php if (!$field->published) { ?> rsfp-grid-unpublished-field<?php } ?>">
			<strong class="pull-left rsfp-grid-field-name"><?php echo JHtml::_('grid.id', $i, $field->id); ?> <?php echo $this->escape($this->show_caption ? $field->caption : $field->name); ?><?php if ($field->required) { ?> (*)<?php } ?></strong>
			<div class="btn-group pull-right">
				<button type="button" class="btn btn-small" onclick="displayTemplate('<?php echo $field->type_id; ?>','<?php echo $field->id; ?>');"><?php echo JText::_('RSFP_EDIT'); ?></button>
				<button type="button" class="btn btn-small btn-danger" onclick="if (confirm(Joomla.JText._('RSFP_REMOVE_COMPONENT_CONFIRM').replace('%s', '<?php echo $this->escape($field->name); ?>'))) removeComponent('<?php echo $this->form->FormId; ?>','<?php echo $field->id; ?>');"><?php echo JText::_('RSFP_DELETE'); ?></button>
			</div>
			<?php if ($this->show_previews) { ?>
				<div class="clearfix"></div>
			<?php echo $this->adjustPreview($field->preview); ?>
			<?php } ?>
			<div class="clearfix"></div>
			<input type="hidden" data-rsfpgrid value="<?php echo $field->id; ?>" />
		</div>
			<?php $i++; ?>
		<?php } ?>
		</div>
	</div>
	<?php } ?>
</div>

<input type="hidden" name="GridLayout" value="<?php echo $this->escape($this->form->GridLayout); ?>" />

<script>
jQuery(function($) {
	// Let's save the JSON first if we've added new elements
	RSFormPro.Grid.toJson();

	$('#componentscontent').on('components.shown', function() {
		if (!RSFormPro.Grid.initialized && jQuery('.rsfp-grid-row').width() != 98)
		{
			RSFormPro.Grid.initialize();

			jQuery('#rsfp-grid-loader').fadeOut(200, function(){
				jQuery(this).remove();
			});
		}
	});

	$(window).on('resize', RSFormPro.Grid.resize);
});
</script>