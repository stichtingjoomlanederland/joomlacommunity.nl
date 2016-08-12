<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2016 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div class="onelineblockoptions">
	<span class="acyblocktitle"><?php echo JText::_('CRON'); ?></span>
	<table class="acymailing_table" cellspacing="1">
		<tr>
			<td colspan="2">
				<?php
				if($this->config->get('cron_last', 0) < (time() - 43200)){
					acymailing_display(JText::_('CREATE_CRON_REMINDER').'<br />'.$this->elements->cron_edit, 'warning');
				}else{
					echo $this->elements->cron_edit;
				}
				?>
			</td>
		</tr>
		<tr>
			<td class="acykey">
				<?php echo acymailing_tooltip(JText::_('CRON_URL_DESC'), JText::_('CRON_URL'), '', JText::_('CRON_URL')); ?>
			</td>
			<td>
				<a href="<?php echo $this->escape($this->elements->cron_url); ?>" target="_blank"><?php echo $this->elements->cron_url; ?></a>
			</td>
		</tr>
	</table>
</div>
<div class="onelineblockoptions">
	<span class="acyblocktitle"><?php echo JText::_('REPORT'); ?></span>
	<table cellspacing="1" width="100%">
		<tr>
			<td class="acykey">
				<?php echo acymailing_tooltip(JText::_('REPORT_SEND_DESC'), JText::_('REPORT_SEND'), '', JText::_('REPORT_SEND')); ?>
			</td>
			<td>
				<?php echo $this->elements->cron_sendreport; ?>
			</td>
			<td class="acykey">
				<?php echo acymailing_tooltip(JText::_('REPORT_SAVE_DESC'), JText::_('REPORT_SAVE'), '', JText::_('REPORT_SAVE')); ?>
			</td>
			<td>
				<?php echo $this->elements->cron_savereport; ?>
			</td>
		</tr>
		<tr>
			<td valign="top" colspan="2" width="50%">
				<div class="acyblockoptions" id="cronreportdetail">
					<table class="acymailing_table" cellspacing="1">
						<tr>
							<td class="acykey">
								<?php echo acymailing_tooltip(JText::_('REPORT_SEND_TO_DESC'), JText::_('REPORT_SEND_TO'), '', JText::_('REPORT_SEND_TO')); ?>
							</td>
							<td>
								<input class="inputbox" type="text" name="config[cron_sendto]" style="width:200px" value="<?php echo $this->escape($this->config->get('cron_sendto')); ?>">
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<?php echo $this->elements->editReportEmail; ?>
							</td>
						</tr>
					</table>
				</div>
			</td>
			<td valign="top" colspan="2">
				<div class="acyblockoptions" id="cronreportsave">
					<table class="acymailing_table" cellspacing="1">
						<tr>
							<td class="acykey">
								<?php echo acymailing_tooltip(JText::_('REPORT_SAVE_TO_DESC'), JText::_('REPORT_SAVE_TO'), '', JText::_('REPORT_SAVE_TO')); ?>
							</td>
							<td>
								<input class="inputbox" type="text" name="config[cron_savepath]" style="width:300px" value="<?php echo $this->escape($this->config->get('cron_savepath')); ?>">
							</td>
						</tr>
						<tr>
							<td colspan="2" id="toggleDelete">
								<?php echo $this->elements->deleteReport; ?>
								<?php echo $this->elements->seeReport; ?>
							</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
	</table>
</div>
<div class="onelineblockoptions">
	<span class="acyblocktitle"><?php echo JText::_('LAST_CRON'); ?></span>
	<table class="acymailing_table" cellspacing="1">
		<tr>
			<td class="acykey">
				<?php echo acymailing_tooltip(JText::_('LAST_RUN_DESC'), JText::_('LAST_RUN'), '', JText::_('LAST_RUN')); ?>
			</td>
			<td>
				<?php $diff = intval((time() - $this->config->get('cron_last', 0)) / 60);
				if($diff > 500){
					echo acymailing_getDate($this->config->get('cron_last'));
					echo ' <span style="font-size:10px">(Your current time is '.acymailing_getDate(time()).')</span>';
				}else{
					echo JText::sprintf('MINUTES_AGO', $diff);
				} ?>
			</td>
		</tr>
		<tr>
			<td class="acykey">
				<?php echo acymailing_tooltip(JText::_('CRON_TRIGGERED_IP_DESC'), JText::_('CRON_TRIGGERED_IP'), '', JText::_('CRON_TRIGGERED_IP')); ?>
			</td>
			<td>
				<?php echo $this->config->get('cron_fromip'); ?>
			</td>
		</tr>
		<tr>
			<td class="acykey">
				<?php echo acymailing_tooltip(JText::_('REPORT_DESC'), JText::_('REPORT'), '', JText::_('REPORT')); ?>
			</td>
			<td>
				<?php echo nl2br($this->config->get('cron_report')); ?>
			</td>
		</tr>
	</table>
</div>
