<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<form method="post" action="<?php echo $this->escape(JUri::getInstance()); ?>" id="adminForm" name="adminForm">
	<?php if ($this->logs) { ?>
	<div class="pull-left">
		<div class="input-append" style="margin-bottom:0;">
			<input type="text" id="search" name="search" value="<?php echo $this->escape(JFactory::getApplication()->input->getString('search')); ?>" placeholder="Search" />
			<button type="button" class="btn" onclick="this.form.submit();"><i class="icon-search"></i></button>
		</div>
		<button type="button" class="btn" onclick="jQuery('#search').val('');this.form.submit();"><?php echo JText::_('COM_RSEVENTSPRO_CLEAR'); ?></button>
	</div>
	<div class="pull-right">
		<button type="button" class="btn btn-danger" onclick="if (confirm('<?php echo JText::_('COM_RSEVENTSPRO_SYNC_CLEAR_LOG_INFO',true); ?>')) Joomla.submitbutton('settings.clear<?php echo ucfirst(JFactory::getApplication()->input->get('from')); ?>Log');"><?php echo JText::_('COM_RSEVENTSPRO_SYNC_CLEAR_LOG'); ?></button>
	</div>
	<div class="clearfix"></div>
	<br />
	<table class="table table-striped">
		<tbody>
			<?php foreach ($this->logs as $log) { ?>
			<tr class="<?php echo $log->imported ? 'success' : 'error'; ?>">
				<td><?php echo rseventsproHelper::showDate($log->date); ?></td>
				<td>
					<?php 
						if (!in_array($log->page, array('0','1')) && JFactory::getApplication()->input->get('from') == 'facebook') {
							echo $log->page == 'FBUSER' ? '['.JText::_('COM_RSEVENTSPRO_SYNC_LOG_USER').'] ' : '['.JText::sprintf('COM_RSEVENTSPRO_SYNC_LOG_PAGE', $log->page).'] ';
						}
						if ($log->imported == 1) {
							echo JText::sprintf('COM_RSEVENTSPRO_SYNC_LOG_'.strtoupper(JFactory::getApplication()->input->get('from')).'_1', $log->name, $log->from);
						} else {
							echo JText::sprintf('COM_RSEVENTSPRO_SYNC_LOG_'.strtoupper(JFactory::getApplication()->input->get('from')).'_2', $log->name, $log->from, $log->message);
						}
					?>
				</td>
			</tr>
			<?php } ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="2" class="center"><?php echo $this->pagination->getListFooter(); ?></td>
			</tr>
		</tfoot>
	</table>
	<?php } else { ?>
		<div class="well">
			<?php echo JText::_('COM_RSEVENTSPRO_SYNC_LOG_EMPTY'); ?>
		</div>
	<?php } ?>
	
	<input type="hidden" name="task" value="" />
</form>