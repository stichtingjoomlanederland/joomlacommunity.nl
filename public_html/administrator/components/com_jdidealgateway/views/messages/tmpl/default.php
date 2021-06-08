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

/** @var JdidealgatewayViewMessages $this */

if (JVERSION < 4)
{
	HTMLHelper::_('formbehavior.chosen', '#filter_orderstatus', null, ['placeholder_text_single' => Text::_('COM_ROPAYMENTS_SELECT_PAYMENTSTATUS')]);
	HTMLHelper::_('formbehavior.chosen', '#filter_psp', null, ['placeholder_text_single' => Text::_('COM_ROPAYMENTS_SELECT_PSP')]);
	HTMLHelper::_('formbehavior.chosen', '#filter_language', null, ['placeholder_text_single' => Text::_('COM_ROPAYMENTS_SELECT_LANGUAGE')]);
	HTMLHelper::_('formbehavior.chosen', '#list_limit');
}

$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');
?>
<form name="adminForm" id="adminForm" method="post" action="index.php?option=com_jdidealgateway&view=messages">
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
			<table class="table table-striped">
				<thead>
					<tr>
						<th><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>
						<th><?php echo HTMLHelper::_('searchtools.sort', 'COM_ROPAYMENTS_SUBJECT', 'messages.subject', $listDirn, $listOrder); ?></th>
						<th><?php echo HTMLHelper::_('searchtools.sort', 'COM_ROPAYMENTS_STATUS_LABEL', 'messages.orderstatus', $listDirn, $listOrder); ?></th>
						<th><?php echo HTMLHelper::_('searchtools.sort', 'COM_ROPAYMENTS_MESSAGE_PSP_LABEL', 'profiles.name', $listDirn, $listOrder); ?></th>
						<th><?php echo HTMLHelper::_('searchtools.sort', 'JFIELD_LANGUAGE_LABEL', 'messages.language', $listDirn, $listOrder); ?></th>
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
					<?php if ($this->items) : ?>
	                <?php foreach ($this->items as $i => $item) : ?>
							<tr>
								<td>
									<?php echo HTMLHelper::_('grid.checkedout',  $item, $i, 'id'); ?>
								</td>
								<td>
									<?php
										echo HTMLHelper::_(
											'link',
											Route::_('index.php?option=com_jdidealgateway&task=message.edit&id=' . $item->id),
											$item->subject
										);
									?>
								</td>
								<td> <?php echo Text::_('COM_ROPAYMENTS_STATUS_' . $item->orderstatus); ?> </td>
								<td><?php echo $item->name; ?></td>
								<td><?php echo LayoutHelper::render('joomla.content.language', $item); ?></td>
							</tr>
					<?php endforeach; ?>
					<?php else: ?>
	                    <tr>
	                        <td colspan="5">
	                            <?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
	                        </td>
	                    </tr>
					<?php endif ?>
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
