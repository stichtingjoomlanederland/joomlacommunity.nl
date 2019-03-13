<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use Akeeba\AdminTools\Admin\Helper\Html;
use FOF30\Utils\FEFHelper\Html as FEFHtml;

/** @var $this Akeeba\AdminTools\Admin\View\AutoBannedAddresses\Html */

defined('_JEXEC') or die;

$js = FEFHtml::jsOrderingBackend($this->order);
$this->getContainer()->template->addJSInline($js);

// Let's check if the system plugin is correctly installed AND published
echo $this->loadAnyTemplate('admin:com_admintools/ControlPanel/plugin_warning');
?>
<form action="index.php" method="post" name="adminForm" id="adminForm" class="akeeba-form">

	<section class="akeeba-panel--33-66 akeeba-filter-bar-container">
		<div class="akeeba-filter-bar akeeba-filter-bar--left akeeba-form-section akeeba-form--inline">
			<div class="akeeba-filter-element akeeba-form-group">
				<input type="text" name="ip" placeholder="<?php echo \JText::_('COM_ADMINTOOLS_LBL_AUTOBANNEDADDRESS_IP'); ?>"
					   id="filter_ip" onchange="document.adminForm.submit();"
					   value="<?php echo $this->escape($this->filters['ip']); ?>"
					   title="<?php echo \JText::_('COM_ADMINTOOLS_LBL_AUTOBANNEDADDRESS_IP'); ?>"/>
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
				<?php echo \JHtml::_('grid.sort', 'COM_ADMINTOOLS_LBL_AUTOBANNEDADDRESS_IP', 'ip', $this->order_Dir, $this->order, 'browse'); ?>
			</th>
			<th>
				<?php echo \JHtml::_('grid.sort', 'COM_ADMINTOOLS_LBL_AUTOBANNEDADDRESS_REASON', 'reason', $this->order_Dir, $this->order, 'browse'); ?>
			</th>
			<th>
				<?php echo \JHtml::_('grid.sort', 'COM_ADMINTOOLS_LBL_AUTOBANNEDADDRESS_UNTIL', 'until', $this->order_Dir, $this->order, 'browse'); ?>
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
					<?php echo JText::_('COM_ADMINTOOLS_ERR_AUTOBANNEDADDRESS_NOITEMS')?>
				</td>
			</tr>
		<?php endif;?>
		<?php
		if ($this->items):
			$i = 0;
			foreach($this->items as $row):
				$ip 	= Html::IpLookup($row->ip);
				$reason = JText::_('COM_ADMINTOOLS_LBL_SECURITYEXCEPTION_REASON_' . strtoupper($row->reason));
				$until  = Html::localisedDate($row->until, 'Y-m-d H:i:s T', false)
			?>
				<tr>
					<td><?php echo \JHtml::_('grid.id', ++$i, $row->ip); ?></td>
					<td>
						<?php echo $ip ?>
					</td>
					<td>
						<?php echo $reason?>
					</td>
					<td>
						<?php echo $until?>
					</td>
				</tr>
			<?php
			endforeach;
		endif; ?>
		</tbody>

	</table>

	<div class="akeeba-hidden-fields-container">
		<input type="hidden" name="option" id="option" value="com_admintools"/>
		<input type="hidden" name="view" id="view" value="AutoBannedAddresses"/>
		<input type="hidden" name="boxchecked" id="boxchecked" value="0"/>
		<input type="hidden" name="task" id="task" value="browse"/>
		<input type="hidden" name="filter_order" id="filter_order" value="<?php echo $this->escape($this->order); ?>"/>
		<input type="hidden" name="filter_order_Dir" id="filter_order_Dir" value="<?php echo $this->escape($this->order_Dir); ?>"/>
		<input type="hidden" name="<?php echo $this->container->platform->getToken(true); ?>" value="1"/>
	</div>
</form>
