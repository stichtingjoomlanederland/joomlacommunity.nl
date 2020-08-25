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
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die;

/** @var JdidealgatewayViewCustomers $this */

HTMLHelper::_('formbehavior.chosen');

$listOrdering  = $this->escape($this->state->get('list.ordering'));
$listDirection = $this->escape($this->state->get('list.direction'));

?>
<form name="adminForm" id="adminForm" method="post" action="index.php?option=com_jdidealgateway&view=customers">
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10 j-toggle-main">
		<?php
		echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));
		?>
		<table class="table table-striped">
			<thead>
				<tr>
					<th><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>
					<th><?php echo HTMLHelper::_('searchtools.sort', Text::_('COM_ROPAYMENTS_CUSTOMER_NAME'), 'customers.name', $listDirection, $listOrdering); ?></th>
					<th><?php echo HTMLHelper::_('searchtools.sort', Text::_('COM_ROPAYMENTS_CUSTOMER_EMAIL'), 'customers.email', $listDirection, $listOrdering); ?></th>
					<th><?php echo HTMLHelper::_('searchtools.sort', Text::_('COM_ROPAYMENTS_CUSTOMER_CUSTOMERID'), 'customers.customerId', $listDirection, $listOrdering); ?></th>
					<th><?php echo HTMLHelper::_('searchtools.sort', Text::_('COM_ROPAYMENTS_CUSTOMER_CREATED'), 'customers.created', $listDirection, $listOrdering); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="5"><?php echo $this->pagination->getListFooter(); ?></td>
				</tr>
			</tfoot>
			<tbody>
				<?php
					foreach ($this->items as $i => $item)
					{
					?>
						<tr>
							<td>
								<?php echo HTMLHelper::_('grid.checkedout',  $item, $i, 'id'); ?>
							</td>
							<td>
								<?php
									echo HTMLHelper::_(
										'link',
										Route::_('index.php?option=com_jdidealgateway&task=customer.edit&id=' . $item->id),
										$item->name
									);
								?>
							</td>
							<td><?php echo $item->email; ?></td>
							<td><?php echo $item->customerId; ?></td>
							<td><?php echo HTMLHelper::_('date', $item->created, 'd-m-Y'); ?></td>
						</tr>
				<?php
					}
				?>
			</tbody>
		</table>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo HTMLHelper::_('form.token'); ?>
	</div>
</form>
