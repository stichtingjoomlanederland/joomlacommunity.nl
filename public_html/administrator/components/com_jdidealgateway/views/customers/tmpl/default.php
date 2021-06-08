<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

/** @var JdidealgatewayViewCustomers $this */

if (JVERSION < 4)
{
	HTMLHelper::_('formbehavior.chosen');
}

$listOrdering  = $this->escape($this->state->get('list.ordering'));
$listDirection = $this->escape($this->state->get('list.direction'));

?>
<form name="adminForm" id="adminForm" method="post" action="index.php?option=com_jdidealgateway&view=customers">
	<?php if (JVERSION < 4) : ?>
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>
	<?php endif; ?>
	<div id="j-main-container" class="span10 j-toggle-main">
		<?php echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]);  ?>
		<?php if (empty($this->items)) : ?>
			<div class="alert alert-no-items alert-info">
				<?php if (JVERSION >= 4) : ?>
					<span class="fas fa-info-circle" aria-hidden="true"></span><span class="sr-only"><?php echo Text::_('INFO'); ?></span>
				<?php endif; ?>
				<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
			</div>
		<?php else : ?>
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
				<?php if (JVERSION < 4) : ?>
					<tfoot>
						<tr>
							<td colspan="5"><?php echo $this->pagination->getListFooter(); ?></td>
						</tr>
					</tfoot>
				<?php endif; ?>
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
			<?php if (JVERSION >= 4) : ?>
				<?php echo $this->pagination->getListFooter(); ?>
			<?php endif; ?>
		<?php endif; ?>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo HTMLHelper::_('form.token'); ?>
	</div>
</form>
