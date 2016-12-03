<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2016 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><table class="acymailing_table" width="100%">
	<tr>
		<td class="acykey">
			<label for="subject">
				<?php echo JText::_('JOOMEXT_SUBJECT'); ?>
			</label>
		</td>
		<td>
			<input type="text" name="data[mail][subject]" id="subject" class="inputbox" style="width:80%" value="<?php echo $this->escape(@$this->mail->subject); ?>"/>
		</td>
		<td class="acykey">
			<label for="published">
				<?php echo JText::_('ACY_PUBLISHED'); ?>
			</label>
		</td>
		<td>
			<?php echo ($this->mail->published == 2) ? JText::_('SCHED_NEWS') : JHTML::_('acyselect.booleanlist', "data[mail][published]", '', $this->mail->published); ?>
		</td>
	</tr>
	<tr>
		<td class="acykey">
			<label for="alias">
				<?php echo JText::_('JOOMEXT_ALIAS'); ?>
			</label>
		</td>
		<td>
			<input class="inputbox" type="text" name="data[mail][alias]" id="alias" style="width:200px" value="<?php echo @$this->mail->alias; ?>"/>
		</td>
		<td class="acykey">
			<label for="html">
				<?php echo JText::_('SEND_HTML'); ?>
			</label>
		</td>
		<td>
			<?php echo JHTML::_('acyselect.booleanlist', "data[mail][html]", 'onclick="updateAcyEditor(this.value)"', $this->mail->html); ?>
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
		<td class="acykey" id="delaykey">
			<label for="delayvalue1">
				<?php echo JText::_('DELAY'); ?>
			</label>
		</td>
		<td>
			<?php echo $this->values->delay->display('data[mail][senddate]', (int)@$this->mail->senddate); ?>
		</td>
		<td class="acykey" id="createdkey">
			<label for="created">
				<?php echo JText::_('CREATED_DATE'); ?>
			</label>
		</td>
		<td>
			<?php echo acymailing_getDate(@$this->mail->created); ?>
		</td>
	</tr>
</table>
