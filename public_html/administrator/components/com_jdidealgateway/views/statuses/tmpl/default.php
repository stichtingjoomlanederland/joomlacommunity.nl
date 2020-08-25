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

/** @var JdidealgatewayViewStatuses $this */

?>
<form action="index.php?option=com_jdidealgateway&view=statuses" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
		<?php if (empty($this->items)) : ?>
			<div class="alert alert-no-items">
				<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
			</div>
		<?php else : ?>
			<table class="table table-striped">
				<thead>
				<tr>
					<th width="1%" class="nowrap center">
						<?php echo HTMLHelper::_('grid.checkall'); ?>
					</th>
					<th class="left">
						<?php echo Text::_('COM_ROPAYMENTS_STATUSES_NAME'); ?>
					</th>
					<th class="nowrap">
						<?php echo Text::_('COM_ROPAYMENTS_STATUSES_JDIDEAL'); ?>
					</th>
					<th class="nowrap">
						<?php echo Text::_('COM_ROPAYMENTS_STATUSES_EXTENSION'); ?>
					</th>
				</tr>
				</thead>
				<tfoot>
				<tr>
					<td colspan="15">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
				</tfoot>
				<tbody>
				<?php
					$canEdit   = $this->canDo->get('core.edit');
					$canChange = $this->loggedInUser->authorise('core.edit.state',	'com_users');

					foreach ($this->items as $i => $item) :
					?>
					<tr>
						<td class="center">
							<?php if ($canEdit || $canChange) : ?>
								<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
							<?php endif; ?>
						</td>
						<td>
							<div class="name break-word">
								<?php if ($canEdit) : ?>
                                    <?php $link = Route::_('index.php?option=com_jdidealgateway&task=status.edit&id=' . (int) $item->id); ?>
                                    <?php $title = Text::sprintf('COM_ROPAYMENTS_EDIT_STATUS', $this->escape($item->name)); ?>
                                    <?php echo HTMLHelper::_('link', $link, $this->escape($item->name), 'title="' . $title . '"'); ?>
								<?php else : ?>
									<?php echo $this->escape($item->name); ?>
								<?php endif; ?>
							</div>
						</td>
						<td class="break-word">
							<?php
								switch (strtoupper($item->jdideal))
								{
									case 'C':
										echo Text::_('COM_ROPAYMENTS_STATUS_SUCCESS');
										break;
									case 'P':
										echo Text::_('COM_ROPAYMENTS_STATUS_PENDING');
										break;
									case 'X':
										echo Text::_('COM_ROPAYMENTS_STATUS_CANCELLED');
										break;
									case 'F':
										echo Text::_('COM_ROPAYMENTS_STATUS_FAILURE');
										break;
									case 'R':
										echo Text::_('COM_ROPAYMENTS_STATUS_REFUNDED');
										break;
									case 'B':
										echo Text::_('COM_ROPAYMENTS_STATUS_CHARGEBACK');
										break;
									case 'O':
										echo Text::_('COM_ROPAYMENTS_STATUS_OPEN');
										break;
									case 'T':
										echo Text::_('COM_ROPAYMENTS_STATUS_TRANSFER');
										break;
									case 'E':
										echo Text::_('COM_ROPAYMENTS_STATUS_EXPIRED');
										break;
									default:
										echo $this->escape($item->jdideal);
										break;
								}
							?>
						</td>
						<td class="break-word">
							<?php echo $this->escape($item->extension); ?>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
	</div>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo HTMLHelper::_('form.token'); ?>
</form>
