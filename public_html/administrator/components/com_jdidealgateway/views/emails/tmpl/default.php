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
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die;

/** @var JdidealgatewayViewEmails $this */

?>
<form name="adminForm" id="adminForm" method="post" action="index.php?option=com_jdidealgateway&view=emails">
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">

		<?php if ($this->canDo->get('core.create')) : ?>
			<?php echo Text::_('COM_ROPAYMENTS_TESTMAIL_ADDRESS'); ?>
			<div id="testmail">
				<input type="text" name="email" value="" size="50" />
			</div>
		<?php endif; ?>

		<table class="table table-striped">
			<thead>
				<tr>
					<th><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>
					<th><?php echo Text::_('COM_ROPAYMENTS_TRIGGER'); ?></th>
					<th><?php echo Text::_('COM_ROPAYMENTS_SUBJECT'); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="3"><?php echo $this->pagination->getListFooter(); ?></td>
				</tr>
			</tfoot>
			<tbody>
				<?php foreach ($this->items as $i => $item) : ?>
					<tr>
						<td>
							<?php echo HTMLHelper::_('grid.checkedout',  $item, $i, 'id'); ?>
						</td>
						<td>
							<?php
								echo HTMLHelper::_(
									'link',
									Route::_('index.php?option=com_jdidealgateway&task=email.edit&id=' . $item->id),
									Text::_('COM_ROPAYMENTS_' . $item->trigger)
								);
							?>
						</td>
						<td><?php echo $item->subject; ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo HTMLHelper::_('form.token'); ?>
	</div>
</form>
