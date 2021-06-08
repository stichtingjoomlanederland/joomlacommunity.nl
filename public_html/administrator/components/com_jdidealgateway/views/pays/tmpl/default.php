<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

defined('_JEXEC') or die;

/** @var JdidealgatewayViewPays $this */

if (JVERSION < 4)
{
	HTMLHelper::_('formbehavior.chosen');
}
?>
<form name="adminForm" id="adminForm" method="post" action="index.php?option=com_jdidealgateway&view=pays">
	<?php if (JVERSION < 4) : ?>
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>
	<?php endif; ?>
	<div id="j-main-container" class="span10">
		<?php echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>
		<?php if (empty($this->items)) : ?>
			<div class="alert alert-no-items alert-info">
				<?php if (JVERSION >= 4) : ?>
					<span class="fas fa-info-circle" aria-hidden="true"></span><span class="sr-only"><?php echo Text::_('INFO'); ?></span>
				<?php endif; ?>
				<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
			</div>
		<?php else : ?>
			<table class="table table-striped table-condensed">
				<thead>
					<tr>
						<th><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>
						<th><?php echo Text::_('COM_ROPAYMENTS_ORDERID'); ?></th>
						<th><?php echo Text::_('COM_ROPAYMENTS_USEREMAIL'); ?></th>
						<th><?php echo Text::_('COM_ROPAYMENTS_AMOUNT'); ?></th>
						<th><?php echo Text::_('COM_ROPAYMENTS_RESULT'); ?></th>
						<th><?php echo Text::_('COM_ROPAYMENTS_REMARK'); ?></th>
						<th><?php echo Text::_('COM_ROPAYMENTS_DATE_ADDED'); ?></th>
					</tr>
				</thead>
				<?php if (JVERSION < 4) : ?>
				<tfoot>
					<tr>
						<td colspan="10"><?php echo $this->pagination->getListFooter(); ?></td>
					</tr>
				</tfoot>
				<?php endif; ?>
				<tbody>
					<?php
						foreach ($this->items as $i => $item)
						{
							// Pseudo entry for satisfying Joomla
							$item->checked_out = 0;
							$checked           = HTMLHelper::_('grid.checkedout', $item, $i, 'id');

							?>
							<tr>
								<td><?php echo $checked; ?></td>
								<td><?php echo $item->id; ?></td>
								<td><?php echo $item->user_email; ?></td>
								<td class="amount">&euro; <?php echo number_format($item->amount, 2); ?></td>
								<td><?php echo Text::_('COM_ROPAYMENTS_RESULT_' . $item->status); ?></td>
								<td><?php echo $item->remark; ?></td>
								<td>
									<?php
									$jnow = Factory::getDate($item->cdate);
									echo $jnow->format('d-m-Y H:i:s');
									?>
								</td>
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
	</div>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
