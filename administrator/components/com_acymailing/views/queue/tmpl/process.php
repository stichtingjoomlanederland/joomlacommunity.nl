<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2016 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php acymailing_display(JText::sprintf('QUEUE_STATUS', acymailing_getDate(time())), 'info'); ?>
<form action="index.php?tmpl=component&amp;option=<?php echo ACYMAILING_COMPONENT ?>" method="post" name="adminForm" id="adminForm" autocomplete="off">
	<div>

		<?php if(!empty($this->queue)){ ?>
			<div class="onelineblockoptions">
				<span class="acyblocktitle"><?php echo JText::_('QUEUE_READY'); ?></span>
				<table class="acymailing_table" cellspacing="1" align="center">
					<tbody>
					<?php $k = 0;
					$total = 0;
					foreach($this->queue as $mailid => $row){
						$total += $row->nbsub;
						?>

						<tr class="<?php echo "row$k"; ?>">
							<td>
								<?php
								echo JText::sprintf('EMAIL_READY', $row->mailid, $row->subject, $row->nbsub);
								?>
							</td>
						</tr>
						<?php
						$k = 1 - $k;
					} ?>
					</tbody>
				</table>
				<br/>
				<input type="hidden" name="totalsend" value="<?php echo $total; ?>"/>
				<input class="acymailing_button_grey" type="submit" onclick="document.adminForm.task.value='continuesend';" value="<?php echo JText::_('SEND'); ?>">
			</div>
		<?php } ?>

		<?php if(!empty($this->schedNews)){ ?>
			<div class="onelineblockoptions">
				<span class="acyblocktitle"><?php echo JText::_('SCHEDULE_NEWS'); ?></span>
				<table class="acymailing_table" cellspacing="1" align="center">
					<tbody>
					<?php $k = 0;
					$sendButton = false;
					foreach($this->schedNews as $row){
						if($row->senddate < time()) $sendButton = true; ?>
						<tr class="<?php echo "row$k"; ?>">
							<td>
								<?php
								echo JText::sprintf('QUEUE_SCHED', $row->mailid, $row->subject, acymailing_getDate($row->senddate));
								?>
							</td>
						</tr>
						<?php
						$k = 1 - $k;
					} ?>
					</tbody>
				</table>
				<?php if($sendButton){ ?><br/><input class="btn btn-primary" onclick="document.adminForm.task.value='genschedule';" type="submit" value="<?php echo JText::_('GENERATE', true); ?>"><?php } ?>
			</div>
		<?php } ?>

		<?php if(!empty($this->nextqueue)){ ?>
			<div class="onelineblockoptions">
				<span class="acyblocktitle"><?php echo JText::sprintf('QUEUE_STATUS', acymailing_getDate(time())); ?></span>
				<table class="acymailing_table" cellspacing="1" align="center">
					<tbody>
					<?php $k = 0;
					foreach($this->nextqueue as $mailid => $row){ ?>
						<tr class="<?php echo "row$k"; ?>">
							<td>
								<?php
								echo JText::sprintf('EMAIL_READY', $row->mailid, $row->subject, $row->nbsub);
								echo '<br />'.JText::sprintf('QUEUE_NEXT_SCHEDULE', acymailing_getDate($row->senddate));
								?>
							</td>
						</tr>
						<?php
						$k = 1 - $k;
					} ?>
					</tbody>
				</table>
			</div>
		<?php } ?>
	</div>
	<div class="clr"></div>
	<input type="hidden" name="mailid" value="<?php echo $this->infos->mailid; ?>"/>
	<input type="hidden" name="option" value="<?php echo ACYMAILING_COMPONENT; ?>"/>
	<input type="hidden" name="task" value="continuesend"/>
	<input type="hidden" name="ctrl" value="send"/>
	<?php echo JHTML::_('form.token'); ?>
</form>
