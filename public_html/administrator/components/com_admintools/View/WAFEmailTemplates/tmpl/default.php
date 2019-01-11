<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use Akeeba\AdminTools\Admin\Helper\Html;
use Akeeba\AdminTools\Admin\Helper\Select;
use FOF30\Utils\FEFHelper\Html as FEFHtml;

/** @var $this \Akeeba\AdminTools\Admin\View\WAFEmailTemplates\Html */

defined('_JEXEC') or die;

$js = FEFHtml::jsOrderingBackend($this->order);
$this->getContainer()->template->addJSInline($js);

?>
<form action="index.php" method="post" name="adminForm" id="adminForm" class="akeeba-form">

	<section class="akeeba-panel--33-66 akeeba-filter-bar-container">
		<div class="akeeba-filter-bar akeeba-filter-bar--left akeeba-form-section akeeba-form--inline">
			<div class="akeeba-filter-element akeeba-form-group">
				<input type="text" name="reason" placeholder="<?php echo \JText::_('COM_ADMINTOOLS_LBL_SECURITYEXCEPTION_REASON_SELECT'); ?>"
					   id="filter_reason" onchange="document.adminForm.submit();"
					   value="<?php echo $this->escape($this->filters['reason']); ?>"
					   title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_SECURITYEXCEPTION_REASON_SELECT'); ?>"/>
			</div>

			<div class="akeeba-filter-element akeeba-form-group">
				<input type="text" name="subject" placeholder="<?php echo \JText::_('COM_ADMINTOOLS_WAFEMAILTEMPLATES_FIELD_SUBJECT_LBL'); ?>"
					   id="filter_subject" onchange="document.adminForm.submit();"
					   value="<?php echo $this->escape($this->filters['subject']); ?>"
					   title="<?php echo \JText::_('COM_ADMINTOOLS_WAFEMAILTEMPLATES_FIELD_SUBJECT_LBL'); ?>"/>
			</div>

			<div class="akeeba-filter-element akeeba-form-group">
				<?php echo Select::published($this->filters['enabled'],'enabled', ['onchange' => 'document.adminForm.submit()'])?>
			</div>

			<div class="akeeba-filter-element akeeba-form-group">
				<?php echo Select::languages('language', $this->filters['language'], ['onchange' => 'document.adminForm.submit()'])?>
			</div>
		</div>

		<?php echo FEFHtml::selectOrderingBackend($this->getPagination(), $this->sortFields, $this->order, $this->order_Dir)?>

	</section>

	<table class="akeeba-table akeeba-table--striped" id="itemsList">
		<thead>
		<tr>
			<th width="32">
				<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);"/>
			</th>
			<th style="width: 130px;">
				<?php echo \JHtml::_('grid.sort', 'COM_ADMINTOOLS_LBL_SECURITYEXCEPTION_REASON', 'reason', $this->order_Dir, $this->order, 'browse'); ?>
			</th>
			<th>
				<?php echo \JHtml::_('grid.sort', 'COM_ADMINTOOLS_WAFEMAILTEMPLATES_FIELD_SUBJECT_LBL', 'subject', $this->order_Dir, $this->order, 'browse'); ?>
			</th>
			<th style="width:8%">
				<?php echo \JHtml::_('grid.sort', 'JPUBLISHED', 'enabled', $this->order_Dir, $this->order, 'browse'); ?>
			</th>
			<th style="width: 20%">
				<?php echo \JHtml::_('grid.sort', 'COM_ADMINTOOLS_COMMON_LANGUAGE', 'language', $this->order_Dir, $this->order, 'browse'); ?>
			</th>

		</tr>
		</thead>
		<tfoot>
		<tr>
			<td colspan="11" class="center">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
		</tfoot>
		<tbody>
		<?php if (!count($this->items)):?>
			<tr>
				<td colspan="6">
					<?php echo JText::_('COM_ADMINTOOLS_MSG_COMMON_NOITEMS')?>
				</td>
			</tr>
		<?php endif;?>
		<?php
		if ($this->items):
			$i = 0;
			foreach($this->items as $row):
				$edit 	 = 'index.php?option=com_admintools&view=WAFEmailTemplates&task=edit&id='.$row->admintools_waftemplate_id;
				$enabled = $this->container->platform->getUser()->authorise('core.edit.state', 'com_admintools')
				?>
				<tr>
					<td><?php echo \JHtml::_('grid.id', ++$i, $row->admintools_waftemplate_id); ?></td>
					<td>
						<a href="<?php echo $edit?>">
							<?php echo $row->reason?>
						</a>
					</td>
					<td>
						<a href="<?php echo $edit?>">
							<?php echo $row->subject?>
						</a>
					</td>
					<td>
						<?php echo JHTML::_('jgrid.published', $row->enabled, $i, '', $enabled, 'cb')?>
					</td>
					<td>
						<?php echo Html::language($row->language); ?>
					</td>
				</tr>
			<?php
			endforeach;
		endif; ?>
		</tbody>

	</table>

	<div class="akeeba-hidden-fields-container">
		<input type="hidden" name="option" id="option" value="com_admintools"/>
		<input type="hidden" name="view" id="view" value="WAFEmailTemplates"/>
		<input type="hidden" name="boxchecked" id="boxchecked" value="0"/>
		<input type="hidden" name="task" id="task" value="browse"/>
		<input type="hidden" name="filter_order" id="filter_order" value="<?php echo $this->escape($this->order); ?>"/>
		<input type="hidden" name="filter_order_Dir" id="filter_order_Dir" value="<?php echo $this->escape($this->order_Dir); ?>"/>
		<input type="hidden" name="<?php echo $this->container->platform->getToken(true); ?>" value="1"/>
	</div>
</form>
