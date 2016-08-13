<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2016 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div class="acytabsystem">
	<?php echo $this->tabs->startPane('mail_tab'); ?>
	<?php echo $this->tabs->startPanel(JText::_('INFOS'), 'mail_infos'); ?>
	<br style="font-size:1px"/>

	<div class="onelineblockoptions">
		<table class="acymailing_smalltable" width="100%">
			<tr>
				<td class="paramlist_key">
					<label for="subject">
						<?php echo JText::_('JOOMEXT_SUBJECT'); ?>
					</label>
				</td>
				<td class="paramlist_value">
					<input type="text" name="data[mail][subject]" id="subject" class="inputbox" style="width:80%" value="<?php echo $this->escape(@$this->mail->subject); ?>"/>
				</td>
			</tr>
			<tr>
				<td class="paramlist_key">
					<?php echo JText::_('SEND_HTML'); ?>
				</td>
				<td class="paramlist_value">
					<?php echo JHTML::_('acyselect.booleanlist', "data[mail][html]", 'onchange="updateAcyEditor(this.value)"', $this->mail->html); ?>
				</td>
			</tr>
			<?php
			$jflanguages = acymailing_get('type.jflanguages');
			if($jflanguages->multilingue){ ?>
				<tr>
					<td class="paramlist_key">
						<label for="jlang">
							<?php echo JText::_('ACY_LANGUAGE'); ?>
						</label>
					</td>
					<td class="paramlist_value">
						<?php
						$jflanguages->sef = true;
						echo $jflanguages->displayJLanguages('data[mail][language]', empty($this->mail->language) ? '' : $this->mail->language);
						?>
					</td>
				</tr>
			<?php } ?>
		</table>
	</div>
	<?php echo $this->tabs->endPanel(); ?>
	<?php echo $this->tabs->startPanel(JText::_('ATTACHMENTS'), 'mail_attachments'); ?>
	<br style="font-size:1px"/>

	<div class="acyblockoptions" style="float:none;">
		<?php if(!empty($this->mail->attach)){
			echo '<div class="acyblockoptions" style="float:none;">
				<span class="acyblocktitle">'.JText::_('ATTACHED_FILES').'</span>';
			foreach($this->mail->attach as $idAttach => $oneAttach){
				$idDiv = 'attach_'.$idAttach;
				echo '<div id="'.$idDiv.'">'.$oneAttach->filename.' ('.(round($oneAttach->size / 1000, 1)).' Ko)';
				echo $this->toggleClass->delete($idDiv, $this->mail->mailid.'_'.$idAttach, 'mail');
				echo '</div>';
			}

			echo '</div>';
		} ?>
		<div id="loadfile">
			<?php
			$uploadfileType = acymailing_get('type.uploadfile');
			for($i = 0; $i < 10; $i++){
				echo '<div'.($i == 0 ? '' : ' style="display:none;"').' id="attachmentsdiv'.$i.'">'.$uploadfileType->display(false, 'attachments', $i).'</div>';
			}
			?>
		</div>
		<a href="javascript:void(0);" onclick='addFileLoader()'><?php echo JText::_('ADD_ATTACHMENT'); ?></a>
		<?php echo JText::sprintf('MAX_UPLOAD', $this->values->maxupload); ?>
	</div>
	<?php echo $this->tabs->endPanel();
	echo $this->tabs->startPanel(JText::_('SENDER_INFORMATIONS'), 'mail_sender'); ?>
	<br style="font-size:1px"/>

	<div class="onelineblockoptions">
		<table width="100%" class="acymailing_smalltable">
			<tr>
				<td class="paramlist_key">
					<?php echo JText::_('FROM_NAME'); ?>
				</td>
				<td class="paramlist_value">
					<input placeholder="<?php echo JText::_('USE_DEFAULT_VALUE'); ?>" class="inputbox" type="text" id="fromname" name="data[mail][fromname]" style="width:200px" value="<?php echo $this->escape($this->mail->fromname); ?>"/>
				</td>
			</tr>
			<tr>
				<td class="paramlist_key">
					<?php echo JText::_('FROM_ADDRESS'); ?>
				</td>
				<td class="paramlist_value">
					<input onchange="validateEmail(this.value, '<?php echo addslashes(JText::_('FROM_ADDRESS')); ?>')" placeholder="<?php echo JText::_('USE_DEFAULT_VALUE'); ?>" class="inputbox" type="text" id="fromemail" name="data[mail][fromemail]" style="width:200px" value="<?php echo $this->escape($this->mail->fromemail); ?>"/>
				</td>
			</tr>
			<tr>
				<td class="paramlist_key">
					<?php echo JText::_('REPLYTO_NAME'); ?>
				</td>
				<td class="paramlist_value">
					<input placeholder="<?php echo JText::_('USE_DEFAULT_VALUE'); ?>" class="inputbox" type="text" id="replyname" name="data[mail][replyname]" style="width:200px" value="<?php echo $this->escape($this->mail->replyname); ?>"/>
				</td>
			</tr>
			<tr>
				<td class="paramlist_key">
					<?php echo JText::_('REPLYTO_ADDRESS'); ?>
				</td>
				<td class="paramlist_value">
					<input onchange="validateEmail(this.value, '<?php echo addslashes(JText::_('REPLYTO_ADDRESS')); ?>')" placeholder="<?php echo JText::_('USE_DEFAULT_VALUE'); ?>" class="inputbox" type="text" id="replyemail" name="data[mail][replyemail]" style="width:200px" value="<?php echo $this->escape($this->mail->replyemail); ?>"/>
				</td>
			</tr>
		</table>
	</div>
	<?php echo acymailing_getFunctionsEmailCheck();

	echo $this->tabs->endPanel();
	$this->config = acymailing_config();
	if(acymailing_level(3) && acymailing_isAllowed($this->config->get('acl_newsletters_inbox_actions', 'all')) && JPluginHelper::isEnabled('acymailing', 'plginboxactions')) include(ACYMAILING_BACK.'views'.DS.'newsletter'.DS.'tmpl'.DS.'inboxactions.php');
	echo $this->tabs->endPane(); ?>
</div>
