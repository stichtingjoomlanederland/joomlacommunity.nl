<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form action="index.php" method="post" name="adminForm" id="adminForm" data-ed-form>
	
	<?php echo $this->html('table.notice', 'Please remember to setup the cronjobs if you are not sending emails on page load. <a href="https://stackideas.com/docs/easydiscuss/administrators/cronjobs" target="_blank" class="btn btn-default btn-sm">Setting Up Cronjob</a>'); ?>

	<div class="app-filter filter-bar form-inline">
	    <div class="form-group">
	    	<?php echo $this->html('table.filter', 'filter_state', $filter, array('P' => 'COM_EASYDISCUSS_SENT', 'U' => 'COM_EASYDISCUSS_PENDING')); ?>
	    </div>
	    <div class="form-group">
	    	<?php echo $this->html('table.limit', $pagination); ?>
	    </div>
	</div>

	<div class="panel-table">
		<table class="app-table app-table-middle table table-striped" data-ed-table>
			<thead>
				<tr>
					<td width="1%">
						<?php echo $this->html('table.checkall'); ?>
					</td>
					<td style="text-align:left;">
						<?php echo JText::_('COM_EASYDISCUSS_RECIPIENT'); ?>
					</td>
					<td style="text-align:left;">
						<?php echo JText::_('COM_EASYDISCUSS_SUBJECT'); ?>
					</td>
					<td width="5%" class="center">
						<?php echo JText::_('COM_EASYDISCUSS_STATE'); ?>
					</td>
					<td width="10%" class="center">
						<?php echo JText::_('COM_EASYDISCUSS_CREATED'); ?>
					</td>
					<td width="5%" class="center">
						<?php echo JText::_('COM_EASYDISCUSS_ID'); ?>
					</td>
				</tr>
			</thead>

			<tbody>
			<?php if ($mails) { ?>
				<?php $i = 0; ?>
				<?php foreach ($mails as $mail) { ?>
					<tr>
						<td class="center">
							<?php echo JHTML::_('grid.id', $i++, $mail->id); ?>
						</td>
						<td style="text-align:left;">
							<?php echo $mail->recipient;?>
						</td>
						<td style="text-align:left;">
							<a href="javascript:void(0);" data-mailer-preview data-id="<?php echo $mail->id;?>"><?php echo $mail->subject;?></a>
						</td>
						<td class="center">
							<?php echo $this->html('table.state', 'spools', $mail, 'status', 'spools', false); ?>
						</td>
						<td class="center">
							<?php echo $mail->date; ?>
						</td>
						<td class="center">
							<?php echo $mail->id;?>
						</td>
					</tr>
				<?php } ?>
			<?php } else { ?>
				<tr>
					<td colspan="6" class="empty">
						<?php echo JText::_('COM_EASYDISCUSS_NO_MAILS');?>
					</td>
				</tr>
			<?php } ?>
			</tbody>
			
			<tfoot>
				<tr>
					<td colspan="6">
						<div class="footer-pagination center">
							<?php echo $pagination->getListFooter(); ?>
						</div>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>

	<?php echo $this->html('form.hidden', 'spools', 'spools'); ?>
</form>
