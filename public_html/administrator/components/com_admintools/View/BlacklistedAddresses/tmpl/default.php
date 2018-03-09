<?php
/**
 * @package   AdminTools
 * @copyright Copyright (c)2010-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/** @var $this \Akeeba\AdminTools\Admin\View\BlacklistedAddresses\Html */
use FOF30\Utils\FEFHelper\Html as FEFHtml;

defined('_JEXEC') or die;

$js = FEFHtml::jsOrderingBackend($this->order);
$this->getContainer()->template->addJSInline($js);

// Let's check if the system plugin is correctly installed AND published
echo $this->loadAnyTemplate('admin:com_admintools/ControlPanel/plugin_warning');
echo $this->loadAnyTemplate('admin:com_admintools/BlacklistedAddresses/feature_warning');
echo $this->loadAnyTemplate('admin:com_admintools/BlacklistedAddresses/toomanyips_warning');

?>
<form action="index.php" method="post" name="adminForm" id="adminForm" class="akeeba-form">

	<section class="akeeba-panel--33-66 akeeba-filter-bar-container">
		<div class="akeeba-filter-bar akeeba-filter-bar--left akeeba-form-section akeeba-form--inline">
			<div class="akeeba-filter-element akeeba-form-group">
				<input type="text" name="ip" placeholder="<?php echo \JText::_('COM_ADMINTOOLS_LBL_BLACKLISTEDADDRESS_IP'); ?>"
					   id="filter_ip" onchange="document.adminForm.submit();"
					   value="<?php echo $this->escape($this->filters['ip']); ?>"
					   title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_BLACKLISTEDADDRESS_IP'); ?>"/>
			</div>

			<div class="akeeba-filter-element akeeba-form-group">
				<input type="text" name="description" placeholder="<?php echo \JText::_('COM_ADMINTOOLS_LBL_BLACKLISTEDADDRESS_DESCRIPTION'); ?>"
					   id="filter_description" onchange="document.adminForm.submit();"
					   value="<?php echo $this->escape($this->filters['description']); ?>"
					   title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_BLACKLISTEDADDRESS_DESCRIPTION'); ?>"/>
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
			<th>
				<?php echo \JHtml::_('grid.sort', 'COM_ADMINTOOLS_LBL_BLACKLISTEDADDRESS_IP', 'ip', $this->order_Dir, $this->order, 'browse'); ?>
			</th>
			<th>
				<?php echo \JHtml::_('grid.sort', 'COM_ADMINTOOLS_LBL_BLACKLISTEDADDRESS_DESCRIPTION', 'description', $this->order_Dir, $this->order, 'browse'); ?>
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
					<?php echo JText::_('COM_ADMINTOOLS_ERR_BLACKLISTEDADDRESS_NOITEMS')?>
				</td>
			</tr>
		<?php endif;?>
		<?php
		if ($this->items):
			$i = 0;
			foreach($this->items as $row):
				$edit = 'index.php?option=com_admintools&view=BlacklistedAddresses&task=edit&id='.$row->id;
				?>
				<tr>
					<td><?php echo \JHtml::_('grid.id', ++$i, $row->id); ?></td>
					<td>
						<a href="<?php echo $edit?>">
							<?php echo $row->ip?>
						</a>
					</td>
					<td>
						<?php echo $row->description ?>
					</td>
				</tr>
			<?php
			endforeach;
		endif; ?>
		</tbody>

	</table>

	<div class="akeeba-hidden-fields-container">
		<input type="hidden" name="option" id="option" value="com_admintools"/>
		<input type="hidden" name="view" id="view" value="BlacklistedAddresses"/>
		<input type="hidden" name="boxchecked" id="boxchecked" value="0"/>
		<input type="hidden" name="task" id="task" value="browse"/>
		<input type="hidden" name="filter_order" id="filter_order" value="<?php echo $this->escape($this->order); ?>"/>
		<input type="hidden" name="filter_order_Dir" id="filter_order_Dir" value="<?php echo $this->escape($this->order_Dir); ?>"/>
		<input type="hidden" name="<?php echo $this->container->platform->getToken(true); ?>" value="1"/>
	</div>
</form>
