<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2016 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><table<?php if(!$this->app->isAdmin()){
	echo ' class="acymailing_table" style="margin: 10px 0px;"';
} ?> width="100%">
	<tr>
		<td class="acykey" id="subjectkey">
			<label for="subject">
				<?php echo JText::_('JOOMEXT_SUBJECT'); ?>
			</label>
		</td>
		<td id="subjectinput">
			<input type="text" name="data[mail][subject]" id="subject" class="inputbox" style="width:80%" value="<?php echo $this->escape(@$this->mail->subject); ?>" onClick="zoneToTag='subject';"/>
		</td>
		<td class="acykey" id="publishedkey">
			<label for="published">
				<?php echo JText::_('ACY_PUBLISHED'); ?>
			</label>
		</td>
		<td id="publishedinput">
			<?php echo ($this->mail->published == 2) ? JText::_('SCHED_NEWS') : JHTML::_('acyselect.booleanlist', "data[mail][published]", '', $this->mail->published, JText::_('JOOMEXT_YES'), JText::_('JOOMEXT_NO')); ?>
		</td>
	</tr>
	<tr>
		<td class="acykey" id="aliaskey">
			<label for="alias">
				<?php echo JText::_('JOOMEXT_ALIAS'); ?>
			</label>
		</td>
		<td id="aliasinput">
			<input class="inputbox" type="text" name="data[mail][alias]" id="alias" style="width:80%" value="<?php echo @$this->mail->alias; ?>" <?php echo($this->type == 'joomlanotification' ? 'readonly' : ''); ?>/>
		</td>
		<?php if ($this->type != 'joomlanotification'){ ?>
		<td class="acykey" id="visiblekey">
			<label for="visible">
				<?php echo JText::_('JOOMEXT_VISIBLE'); ?>
			</label>
		</td>
		<td id="visibleinput">
			<?php echo JHTML::_('acyselect.booleanlist', "data[mail][visible]", '', $this->mail->visible, JText::_('JOOMEXT_YES'), JText::_('JOOMEXT_NO')); ?>
		</td>
	</tr>
	<tr>
		<td class="acykey" id="picturekey" valign="top">
			<label for="pictureinput">
				<?php echo JText::_('ACY_THUMBNAIL'); ?>
			</label>
		</td>
		<td id="pictureinput" valign="top">
			<?php
			$uploadfileType = acymailing_get('type.uploadfile');
			echo $uploadfileType->display(true, 'thumb', $this->mail->thumb, 'data[mail][thumb]');
			?>
		</td>
		<?php } ?>
		<td class="acykey" id="sendhtmlkey">
			<label for="data_mail_htmlfieldset">
				<?php echo JText::_('SEND_HTML'); ?>
			</label>
		</td>
		<td id="sendhtmlinput">
			<?php echo JHTML::_('acyselect.booleanlist', "data[mail][html]", 'onclick="updateAcyEditor(this.value); initTagZone(this.value);"', $this->mail->html, JText::_('JOOMEXT_YES'), JText::_('JOOMEXT_NO')); ?>
		</td>
	</tr>
	<?php if($this->type != 'joomlanotification'){ ?>
		<tr>
			<td class="acykey" id="summarykey" valign="top">
				<label for="summaryfield">
					<?php echo JText::_('ACY_SUMMARY'); ?>
				</label>
			</td>
			<td id="summaryinput">
				<textarea placeholder="<?php echo JText::_('ACY_SUMMARY_PLACEHOLDER') ?>" style="width:80%;height:60px;" id="summaryfield" name="data[mail][summary]"><?php echo $this->escape(@$this->mail->summary); ?></textarea>
			</td>
			<td class="acykey" id="createdkey" valign="top">
				<label for="createdinput">
					<?php echo JText::_('CREATED_DATE'); ?>
				</label>
			</td>
			<td id="createdinput" valign="top">
				<?php echo acymailing_getDate(@$this->mail->created); ?>
			</td>
		</tr>
		<?php if(!empty($this->mail->senddate)){ ?>
			<tr>
				<td class="acykey" id="senddatekey">
					<label for="senddateinput">
						<?php echo JText::_('SEND_DATE'); ?>
					</label>
				</td>
				<td id="senddateinput">
					<?php echo acymailing_getDate(@$this->mail->senddate); ?>
				</td>
				<td class="acykey" id="sentbykey">
					<label for="sentbyinput">
						<?php if(!empty($this->mail->sentby)) echo JText::_('SENT_BY'); ?>
					</label>
				</td>
				<td id="sentbyinput">
					<?php echo @$this->sentbyname; ?>
				</td>
			</tr>
		<?php }
	}
	$jflanguages = acymailing_get('type.jflanguages');
	if($jflanguages->multilingue){
		?>
		<tr>
			<td class="acykey" id="languagekey">
				<label for="jlang">
					<?php echo JText::_('ACY_LANGUAGE'); ?>
				</label>
			</td>
			<td id="languageinput" colspan="3">
				<?php
				$jflanguages->sef = true;
				echo $jflanguages->displayJLanguages('data[mail][language]', empty($this->mail->language) ? '' : $this->mail->language);
				?>
			</td>
		</tr>
	<?php } ?>
</table>
