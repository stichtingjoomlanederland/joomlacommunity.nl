<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2020 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

/** @var JdidealgatewayViewJdidealgateway $this */

?>
<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
	<table class="table table-striped table-condensed">
		<thead>
		<tr>
			<th><?php echo Text::_('COM_ROPAYMENTS_ORIGIN'); ?></th>
			<th><?php echo Text::_('COM_ROPAYMENTS_ORDERID'); ?></th>
			<th><?php echo Text::_('COM_ROPAYMENTS_ORDERNR'); ?></th>
			<th><?php echo Text::_('COM_ROPAYMENTS_CURRENCY'); ?></th>
			<th><?php echo Text::_('COM_ROPAYMENTS_AMOUNT'); ?></th>
			<th><?php echo Text::_('COM_ROPAYMENTS_ALIAS'); ?></th>
			<th><?php echo Text::_('COM_ROPAYMENTS_CARD'); ?></th>
			<th><?php echo Text::_('COM_ROPAYMENTS_RESULT'); ?></th>
			<th><?php echo Text::_('COM_ROPAYMENTS_TRANSID'); ?></th>
			<th><?php echo Text::_('COM_ROPAYMENTS_DATE'); ?></th>
		</tr>
		</thead>
		<tfoot>
		<tr>
			<td colspan="10"></td>
		</tr>
		</tfoot>
		<tbody>
		<?php
		if ($this->items)
		{
			foreach ($this->items as $i => $entry)
			{
				// Create the link
				$componentName = $entry->origin;
				$componentLink = '';
				$orderLink     = '';

				if ($this->addons->exists($entry->origin))
				{
					try
					{
						$addon         = $this->addons->get($entry->origin);
						$componentName = $addon->getName();
						$componentLink = $addon->getComponentLink();
						$orderLink     = $addon->getAdminOrderLink($entry->order_id);
					}
					catch (Exception $exception)
					{
						?><tr><td colspan="10"><?php echo $exception->getMessage(); ?></td></tr><?php
					}
				}
				?>
				<tr>
					<td><?php echo '' === $componentLink ? $componentName : HTMLHelper::_('link', $componentLink, $componentName, 'target=_new'); ?></td>
					<td><?php echo '' === $orderLink ? $entry->order_id : HTMLHelper::_('link', $orderLink, $entry->order_id, 'target=_new'); ?></td>
					<td><?php echo $entry->order_number; ?></td>
					<td><?php echo $entry->currency; ?></td>
					<td class="amount"><?php echo number_format($entry->amount, 2); ?></td>
					<td><?php echo $entry->alias; ?></td>
					<td><?php echo $entry->card; ?></td>
					<td><?php echo $entry->result; ?></td>
					<td><?php echo $entry->trans; ?></td>
					<td><?php echo HTMLHelper::_('date', $entry->date_added, 'd-m-Y H:i:s', true); ?></td>
				</tr>
				<?php
			}
		}
		?>
		</tbody>
	</table>
</div>
