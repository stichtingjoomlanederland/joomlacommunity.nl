<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2020 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

/** @var JdidealgatewayViewLogs $this */

HTMLHelper::_('behavior.modal');
HTMLHelper::_('formbehavior.chosen');

// Add check script
HTMLHelper::_('script', 'com_jdidealgateway/result.js', ['version' => 'auto', 'relative' => true]);

HTMLHelper::_('formbehavior.chosen', '#filter_origin', null,
	['placeholder_text_single' => Text::_('COM_ROPAYMENTS_SELECT_ORIGIN')]
);
HTMLHelper::_('formbehavior.chosen', '#filter_psp', null,
	['placeholder_text_single' => Text::_('COM_ROPAYMENTS_SELECT_PSP')]
);
HTMLHelper::_('formbehavior.chosen', '#filter_card', null,
	['placeholder_text_single' => Text::_('COM_ROPAYMENTS_SELECT_CARD')]
);
HTMLHelper::_('formbehavior.chosen', '#filter_currency', null,
	['placeholder_text_single' => Text::_('COM_ROPAYMENTS_SELECT_CURRENCY')]
);
HTMLHelper::_('formbehavior.chosen', '#filter_result', null,
	['placeholder_text_single' => Text::_('COM_ROPAYMENTS_SELECT_RESULT')]
);

// Supported payment providers for checking result
$noPaymentProviders = ['easy', 'internetkassa', 'lite', 'omnikassa', 'ogone'];

?>
<form name="adminForm" id="adminForm" method="post" action="index.php?option=com_jdidealgateway&view=logs" class="form-horizontal">

	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
		<?php
			echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]);
		?>
		<table class="table table-striped table-condensed">
			<thead>
				<tr>
					<th><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>
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
					<th><?php echo Text::_('COM_ROPAYMENTS_HISTORY'); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="12"><?php echo $this->pagination->getListFooter(); ?></td>
				</tr>
			</tfoot>
			<tbody>
				<?php
				foreach ($this->items as $i => $entry)
				{
					// Pseudo entry for satisfying Joomla
					$entry->checked_out = 0;
					$checked            = HTMLHelper::_('grid.checkedout', $entry, $i, 'id');

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
						<td><?php echo $checked; ?></td>
						<td><?php echo $componentLink === '' ? $componentName : HTMLHelper::_('link', $componentLink, $componentName, 'target=_new'); ?></td>
						<td><?php echo $orderLink === '' ? $entry->order_id : HTMLHelper::_('link', $orderLink, $entry->order_id, 'target=_new'); ?></td>
						<td><?php echo $entry->order_number; ?></td>
						<td><?php echo $entry->currency; ?></td>
						<td class="amount"><?php echo number_format($entry->amount, 2, ',', '.'); ?></td>
						<td><?php echo $entry->alias; ?></td>
						<td><?php echo $entry->card; ?></td>
						<td>
							<div>
							<?php
								if ($entry->trans
									&& $entry->paymentId
									&& !in_array(strtolower($entry->psp), $noPaymentProviders, true)
									&& in_array(strtoupper($entry->result), ['OPEN', 'TRANSFER', ''], true)
								)
								{
									echo HTMLHelper::_(
										'link',
										'index.php?option=com_jdidealgateway&view=logs',
										'<span class="icon-refresh"></span>',
										'onclick="checkResult(' . $entry->id . ',\'' . Session::getFormToken() . '\'); return false;"'
									);
								}

								echo '<span id="paymentResult' . $entry->id . '">' . $entry->result . '</span>';
							?>
							</div>
						</td>
						<td>
							<?php
								$link = $entry->trans;

								if ($entry->psp === 'mollie' && $entry->paymentId)
								{
									$link = HTMLHelper::_(
											'link',
											'https://www.mollie.com/dashboard/payments/' . $entry->paymentId,
											$entry->trans,
											'target="_blank"'
									);
								}

								echo $link;
							?>
						</td>
						<td>
							<?php
								echo HTMLHelper::_('date', $entry->date_added, 'd-m-Y H:i:s', true);
							?>
						</td>
						<td>
							<?php
								$attribs = 'class="modal" onclick="" rel="{handler: \'iframe\', size: {x: 950, y: 500}}"';
								echo HTMLHelper::_(
									'link',
									Route::_('index.php?option=com_jdidealgateway&task=logs.history&tmpl=component&log_id=' . $entry->id),
									Text::_('COM_ROPAYMENTS_VIEW'),
									$attribs
								);
							?>
						</td>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>
	</div>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
