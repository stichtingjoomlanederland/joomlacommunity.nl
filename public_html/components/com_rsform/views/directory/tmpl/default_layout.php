<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access'); 
$listOrder	= $this->escape($this->filter_order);
$listDirn	= $this->escape($this->filter_order_Dir); ?>
<table class="table table-condensed table-striped category directoryTable">
	<thead>
		<tr>
			<?php if ($this->directory->enablecsv) { ?>
				<th align="center" class="center" width="1%"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>
			<?php } ?>
			<?php foreach ($this->viewableFields as $field) { ?>
				<th align="center" class="center directoryHead directoryHead<?php echo $this->getFilteredName($field->FieldName); ?>"><?php echo JHtml::_('grid.sort', $field->FieldCaption, $field->FieldName, $listDirn, $listOrder); ?></th>
			<?php } ?>
			<th align="center" class="center">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<?php if ($this->items) { ?>
		<?php foreach ($this->items as $i => $item) { ?>
		<tr class="row<?php echo $i % 2; ?> directoryRow">
			<?php if ($this->directory->enablecsv) { ?>
				<td align="center" class="center directoryGrid"><?php echo JHtml::_('grid.id', $i, $item->SubmissionId); ?></td>
			<?php } ?>
			<?php foreach ($this->viewableFields as $field) { ?>
				<td align="center" class="center directoryCol directoryCol<?php echo $this->getFilteredName($field->FieldName); ?>"><?php echo $this->getValue($item, $field); ?></td>
			<?php } ?>
			<td align="center" class="center directoryActions">
				<?php if ($this->hasDetailFields) { ?>
				<a class="<?php echo $this->tooltipClass; ?> directoryDetail" title="<?php echo RSFormProHelper::getTooltipText(JText::_('RSFP_SUBM_DIR_VIEW')); ?>" href="<?php echo JRoute::_('index.php?option=com_rsform&view=directory&layout=view&id='.$item->SubmissionId); ?>">
					<img src="<?php echo JURI::root().'components/com_rsform/assets/images/view.png'; ?>" alt="" />
				</a>
				<?php } ?>
				<?php if (RSFormProHelper::canEdit($this->params->get('formId'),$item->SubmissionId)) { ?>
				<a class="<?php echo $this->tooltipClass; ?> directoryEdit" title="<?php echo RSFormProHelper::getTooltipText(JText::_('RSFP_SUBM_DIR_EDIT')); ?>" href="<?php echo JRoute::_('index.php?option=com_rsform&&view=directory&layout=edit&id='.$item->SubmissionId); ?>">
					<img src="<?php echo JURI::root().'components/com_rsform/assets/images/edit.png'; ?>" alt="" />
				</a>
				<?php } ?>
				<?php if ($this->directory->enablepdf) { ?>
				<a class="<?php echo $this->tooltipClass; ?> directoryPdf" title="<?php echo RSFormProHelper::getTooltipText(JText::_('RSFP_SUBM_DIR_PDF')); ?>" href="<?php echo $this->pdfLink($item->SubmissionId); ?>">
					<img src="<?php echo JURI::root().'components/com_rsform/assets/images/pdf.png'; ?>" alt="" />
				</a>
				<?php } ?>
			</td>
		</tr>
		<?php } ?>
		<?php } ?>
	</tbody>
</table>