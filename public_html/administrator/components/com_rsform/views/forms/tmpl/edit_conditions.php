<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

JText::script('RSFP_CONDITION_DELETE_SURE');
?>
<?php if (!$this->isComponent) { ?>
    <?php if (!RSFormProHelper::getConfig('global.disable_multilanguage')) { ?>
        <div class="alert alert-info"><?php echo JText::_('RSFP_CONDITION_MULTILANGUAGE_WARNING'); ?></div>
    <?php } ?>

<div id="conditionscontent" style="overflow: auto;">
<?php } ?>
<div>
	<button type="button" class="btn btn-primary" onclick="openRSModal('<?php echo JRoute::_('index.php?option=com_rsform&view=conditions&layout=edit&formId='.$this->formId.'&tmpl=component'); ?>', 'Conditions', '800x600')"><?php echo JText::_('RSFP_FORM_CONDITION_NEW'); ?></button>
</div>

	<table class="adminlist table table-hover table-striped" id="conditionsTable">
	<thead>
		<tr>
			<th nowrap="nowrap"><?php echo JText::_('RSFP_CONDITION_FIELD_NAME'); ?></th>
			<th width="1%" class="title" nowrap="nowrap"><?php echo JText::_('RSFP_CONDITIONS_ACTIONS'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php if (!empty($this->conditions)) { ?>
		<?php foreach ($this->conditions as $row) { ?>
		<tr>
			<td>
				<a href="#" onclick="openRSModal('<?php echo JRoute::_('index.php?option=com_rsform&view=conditions&layout=edit&tmpl=component&formId='.$this->formId.'&cid='.$row->id); ?>', 'Conditions', '800x600'); return false;">(<?php echo JText::_('RSFP_CONDITION_'.$row->action); ?>) <?php echo $this->escape($row->ComponentName); ?></a>
				<?php if (!empty($row->details)) { ?>
					<ul>
						<li><strong><?php echo JText::_('RSFP_CONDITION_' . $row->condition); ?></strong></li>
					<?php foreach ($row->details as $detail) { ?>
						<li><small><?php echo $this->escape($detail->name) . ' ' . JText::_('RSFP_CONDITION_' . $detail->operator) . ' ' . $this->escape($detail->value); ?></small></li>
					<?php } ?>
					</ul>
				<?php } ?>
			</td>
			<td align="center" width="20%" nowrap="nowrap">
				<button type="button" class="btn pull-left" onclick="openRSModal('<?php echo JRoute::_('index.php?option=com_rsform&view=conditions&layout=edit&tmpl=component&formId='.$this->formId.'&cid='.$row->id); ?>', 'Conditions', '800x600')"><?php echo JText::_('RSFP_EDIT'); ?></button>
				<button type="button" class="btn btn-danger pull-left" onclick="if (confirm(Joomla.JText._('RSFP_CONDITION_DELETE_SURE'))) conditionDelete(<?php echo $this->formId; ?>,<?php echo $row->id; ?>);"><?php echo JText::_('RSFP_DELETE'); ?></button>
			</td>
		</tr>
		<?php } ?>
		<?php } ?>
	</tbody>
	</table>
<?php if (!$this->isComponent) { ?>
</div>
<?php } ?>