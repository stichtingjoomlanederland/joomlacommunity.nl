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

/** @var JdidealgatewayViewEmails $this */

?>
<form name="adminForm" id="adminForm" method="post" action="index.php?option=com_jdidealgateway&view=emails">
	<?php if (JVERSION < 4) : ?>
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>
	<?php endif; ?>
	<div id="j-main-container" class="span10">
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
						<th><?php echo Text::_('COM_ROPAYMENTS_TRIGGER'); ?></th>
						<th><?php echo Text::_('COM_ROPAYMENTS_SUBJECT'); ?></th>
					</tr>
				</thead>
				<?php if (JVERSION < 4) : ?>
					<tfoot>
						<tr>
							<td colspan="3"><?php echo $this->pagination->getListFooter(); ?></td>
						</tr>
					</tfoot>
				<?php endif; ?>
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
										'index.php?option=com_jdidealgateway&task=email.edit&id=' . $item->id,
										Text::_('COM_ROPAYMENTS_' . $item->trigger)
									);
								?>
							</td>
							<td><?php echo $item->subject; ?></td>
						</tr>
					<?php endforeach; ?>
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
<?php
if ($this->emails && $this->canDo->get('core.create')) : ?>
	<?php
	$emailModalData = [
		'selector' => 'testEmail',
		'params'   => [
			'title'  => Text::_('COM_ROPAYMENTS_SEND_TESTMAIL'),
			'footer' => $this->loadTemplate('email_footer'),
		],
		'body'     => $this->loadTemplate('email_body'),
	];
	?>

	<form action="index.php?option=com_jdidealgateway&task=emails.testemail" method="post" class="form-horizontal">
		<?php echo LayoutHelper::render('joomla.modal.main', $emailModalData); ?>
		<?php echo HTMLHelper::_('form.token'); ?>
	</form>
<?php endif;
