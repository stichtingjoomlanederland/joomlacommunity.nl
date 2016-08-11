<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2016 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><table width="100%">
	<tr>
		<td class="acykey" id="subjectkey">
			<label for="subject">
				<?php echo JText::_('JOOMEXT_SUBJECT'); ?>
			</label>
		</td>
		<td>
			<input type="text" name="data[mail][subject]" id="subject" class="inputbox" style="width:80%" value="<?php echo $this->escape(@$this->mail->subject); ?>"/>
		</td>
		<td class="acykey" id="publishedkey">
			<label for="published">
				<?php echo JText::_('ACY_PUBLISHED'); ?>
			</label>
		</td>
		<td>
			<?php echo JHTML::_('acyselect.booleanlist', "data[mail][published]", '', $this->mail->published); ?>
		</td>
	</tr>
	<tr>
		<td class="acykey" id="aliaskey">
			<label for="alias">
				<?php echo JText::_('JOOMEXT_ALIAS'); ?>
			</label>
		</td>
		<td>
			<input class="inputbox" type="text" name="data[mail][alias]" id="alias" style="width:60%" value="<?php echo $this->escape(@$this->mail->alias); ?>"/>
		</td>
		<td class="acykey" id="htmlkey">
			<label for="html">
				<?php echo JText::_('SEND_HTML'); ?>
			</label>
		</td>
		<td>
			<?php echo JHTML::_('acyselect.booleanlist', "data[mail][html]", 'onchange="updateAcyEditor(this.value)"', $this->mail->html); ?>
		</td>
	</tr>
	<?php
	$jflanguages = acymailing_get('type.jflanguages');
	if($jflanguages->multilingue){ ?>
		<tr>
			<td class="acykey" id="languagekey">
				<label for="language">
					<?php echo JText::_('ACY_LANGUAGE'); ?>
				</label>
			</td>
			<td>
				<?php
				$jflanguages->sef = true;
				echo $jflanguages->displayJLanguages('data[mail][language]', empty($this->mail->language) ? '' : $this->mail->language);
				?>
			</td>
			<td colspan="2"/>
		</tr>
	<?php } ?>
	<tr>
		<td class="acykey" id="senddatekey" valign="top">
			<label for="senddate">
				<?php echo JText::_('NEXT_GENERATE'); ?>
			</label>
		</td>
		<td valign="top">
			<?php if(empty($this->mail->senddate)) $this->mail->senddate = time();
			echo JHTML::_('calendar', acymailing_getDate($this->mail->senddate, '%Y-%m-%d %H:%M'), 'data[mail][senddate]', 'senddate', '%Y-%m-%d %H:%M', array('style' => 'width:160px')); ?>
		</td>
		<td class="acykey" id="frequencykey" valign="top">
			<label for="frequencyType">
				<?php echo JText::_('GENERATE_FREQUENCY'); ?>
			</label>
		</td>
		<td valign="top">
			<?php echo $this->frequencyType->displayFrequency('data[mail][frequency]', @$this->mail->frequency, 3); ?>
		</td>
	</tr>
	<tr>
		<td class="acykey" id="issuenbkey">
			<label for="issuenb">
				<?php echo JText::_('ISSUE_NB'); ?>
			</label>
		</td>
		<td>
			<input class="inputbox" type="text" id="issuenb" name="data[mail][params][issuenb]" style="width:50px" value="<?php echo empty($this->mail->params['issuenb']) ? 1 : $this->mail->params['issuenb']; ?>"/>
		</td>
		<td class="acykey" id="lastgenerateddatekey">
			<label for="lastgenerateddate">
				<?php echo JText::_('LAST_RUN'); ?>
			</label>
		</td>
		<td>
			<input type="text" class="inputbox" id="lastgenerateddate" style="width:200px" value="<?php echo $this->escape(acymailing_getDate(@$this->mail->params['lastgenerateddate'], '%Y-%m-%d %H:%M')); ?>" name="data[mail][params][lastgenerateddate]"/>
		</td>
	</tr>
	<tr>
		<td class="acykey" id="generatekey">
			<label for="datamailparamsgenerate">
				<?php echo JText::_('GENERATE_MODE'); ?>
			</label>
		</td>
		<td>
			<?php echo $this->generatingMode->display('data[mail][params][generate]', @$this->mail->params['generate']); ?>
		</td>
		<td class="acykey" id="generatetokey">
			<label for="generateto">
				<?php echo JText::_('NOTIFICATION_TO'); ?>
			</label>
		</td>
		<td>
			<input class="inputbox" type="text" name="data[mail][params][generateto]" id="generateto" style="width:200px" value="<?php echo $this->escape(@$this->mail->params['generateto']); ?>" placeholder="address@example.com"/>
		</td>
	</tr>
</table>
