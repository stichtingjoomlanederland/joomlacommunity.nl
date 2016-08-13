<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
?>

<?php if (!$this->isComponent) { ?>
<div class="alert alert-info"><?php echo JText::_('RSFP_FORM_MAPPINGS_INFO'); ?></div>
<br />
<div id="mappingcontent" style="overflow: auto;">
<?php } ?>
	<button type="button" class="btn btn-primary" onclick="openRSModal('<?php echo JRoute::_('index.php?option=com_rsform&view=mappings&formId='.$this->formId.'&tmpl=component'); ?>', 'Mappings', '1000x800')"><?php echo JText::_('RSFP_FORM_MAPPINGS_NEW'); ?></button>
	
	<br /><br />

	<table class="adminlist table table-hover table-striped" id="mappingTable">
		<thead>
			<tr>
				<th width="1%" nowrap="nowrap"><?php echo JText::_('RSFP_FORM_MAPPINGS_DATABASE_TABLE'); ?></th>
				<th align="center"><?php echo JText::_('RSFP_FORM_MAPPINGS_QUERY'); ?></th>
				<th class="title"><?php echo JText::_('Ordering'); ?></th>
				<th width="1%" class="title" nowrap="nowrap"><?php echo JText::_('RSFP_FORM_MAPPINGS_ACTIONS'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php $i = 0; $k = 0; $n = count($this->mappings); ?>
			<?php // hack to show order down icon
				$n++; 
			?>
			<?php if (!empty($this->mappings)) { ?>
			<?php foreach ($this->mappings as $row) { ?>
			<tr class="row<?php echo $k; ?>" style="cursor: move;">
				<td width="1%" nowrap="nowrap">
					<input type="checkbox" style="display: none;" value="<?php echo $row->id; ?>" name="mpid[]" id="mp<?php echo $i; ?>">
					<?php echo !empty($row->database) ? $this->escape($row->database).'.' : ''; ?>`<?php echo $this->escape($row->table); ?>` (<?php echo $row->connection ? JText::_('RSFP_FORM_MAPPINGS_CONNECTION_REMOTE') : JText::_('RSFP_FORM_MAPPINGS_CONNECTION_LOCAL'); ?>)
				</td>
				<td>
					<?php 
					try {
						echo wordwrap($this->escape(RSFormProHelper::getMappingQuery($row)), 150, '<br />', true);
					} catch (Exception $e) {
						echo $this->escape(JText::sprintf('RSFP_DB_ERROR', $e->getMessage()));
					}
					?>
				</td>
				<td class="order">
					<span><?php echo str_replace(array('cb'.$i,'listItemTask'),array('mp'.$i,'orderMapping'),$this->mpagination->orderUpIcon( $i, true, 'orderup', 'Move Up', 'ordering')); ?></span>
					<span><?php echo str_replace(array('cb'.$i,'listItemTask'),array('mp'.$i,'orderMapping'),$this->mpagination->orderDownIcon( $i, $n, true, 'orderdown', 'Move Down', 'ordering' )); ?></span>
					<input type="text" name="mporder[]" size="5" value="<?php echo $row->ordering; ?>" disabled="disabled" class="text_area" style="text-align:center" />
				</td>
				<td align="center" width="20%" nowrap="nowrap">
					<button type="button" class="btn pull-left" onclick="openRSModal('<?php echo JRoute::_('index.php?option=com_rsform&view=mappings&cid='.$row->id.'&tmpl=component&formId='.$this->formId.'&cid='.$row->id); ?>', 'Mappings', '1000x800')"><?php echo JText::_('RSFP_EDIT'); ?></button>
					<button type="button" class="btn btn-danger pull-left" onclick="mappingdelete(<?php echo $this->formId; ?>,<?php echo $row->id; ?>);"><?php echo JText::_('RSFP_DELETE'); ?></button>
				</td>
			</tr>
			<?php $k=1-$k; ?>
			<?php $i++; ?>
			<?php } ?>
			<?php } ?>
		</tbody>
	</table>
<?php if (!$this->isComponent) { ?>
</div>
<?php } ?>