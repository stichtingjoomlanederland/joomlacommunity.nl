<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2016 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div>
	<?php echo $this->tabs->startPane('mail_tab'); ?>
	<?php echo $this->tabs->startPanel(JText::_('ATTACHMENTS'), 'mail_attachments'); ?>
	<br style="font-size:1px"/>
	<?php if(!empty($this->mail->attach)){ ?>
		<div class="onelineblockoptions">
			<span class="acyblocktitle"><?php echo JText::_('ATTACHED_FILES'); ?></span>
			<?php
			foreach($this->mail->attach as $idAttach => $oneAttach){
				$idDiv = 'attach_'.$idAttach;
				echo '<div id="'.$idDiv.'">'.$oneAttach->filename.' ('.(round($oneAttach->size / 1000, 1)).' Ko)';
				echo $this->toggleClass->delete($idDiv, $this->mail->mailid.'_'.$idAttach, 'mail');
				echo '</div>';
			}
			?>
		</div>
	<?php } ?>
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
	<?php echo $this->tabs->endPanel();
	echo $this->tabs->startPanel(JText::_('SENDER_INFORMATIONS'), 'mail_sender'); ?>
	<br style="font-size:1px"/>
	<table width="100%" class="acymailing_table">
		<tr>
			<td class="paramlist_key">
				<?php echo JText::_('FROM_NAME'); ?>
			</td>
			<td class="paramlist_value">
				<input placeholder="<?php echo JText::_('USE_DEFAULT_VALUE'); ?>" class="inputbox" id="fromname" type="text" name="data[mail][fromname]" style="width:200px" value="<?php echo $this->escape(@$this->mail->fromname); ?>"/>
			</td>
		</tr>
		<tr>
			<td class="paramlist_key">
				<?php echo JText::_('FROM_ADDRESS'); ?>
			</td>
			<td class="paramlist_value">
				<input onchange="validateEmail(this.value, '<?php echo addslashes(JText::_('FROM_ADDRESS')); ?>')" placeholder="<?php echo JText::_('USE_DEFAULT_VALUE'); ?>" class="inputbox" id="fromemail" type="text" name="data[mail][fromemail]" style="width:200px" value="<?php echo $this->escape(@$this->mail->fromemail); ?>"/>
			</td>
		</tr>
		<tr>
			<td class="paramlist_key">
				<?php echo JText::_('REPLYTO_NAME'); ?>
			</td>
			<td class="paramlist_value">
				<input placeholder="<?php echo JText::_('USE_DEFAULT_VALUE'); ?>" class="inputbox" id="replyname" type="text" name="data[mail][replyname]" style="width:200px" value="<?php echo $this->escape(@$this->mail->replyname); ?>"/>
			</td>
		</tr>
		<tr>
			<td class="paramlist_key">
				<?php echo JText::_('REPLYTO_ADDRESS'); ?>
			</td>
			<td class="paramlist_value">
				<input onchange="validateEmail(this.value, '<?php echo addslashes(JText::_('REPLYTO_ADDRESS')); ?>')" placeholder="<?php echo JText::_('USE_DEFAULT_VALUE'); ?>" class="inputbox" id="replyemail" type="text" name="data[mail][replyemail]" style="width:200px" value="<?php echo $this->escape(@$this->mail->replyemail); ?>"/>
			</td>
		</tr>
	</table>

	<?php echo $this->tabs->endPanel();
	echo $this->tabs->startPanel(JText::_('META_DATA'), 'mail_metadata'); ?>
	<br style="font-size:1px"/>
	<table width="100%" class="acymailing_table" id="metadatatable">
		<tr>
			<td class="paramlist_key">
				<label for="metakey"><?php echo JText::_('META_KEYWORDS'); ?></label>
			</td>
			<td class="paramlist_value">
				<textarea id="metakey" name="data[mail][metakey]" rows="5" cols="30"><?php echo @$this->mail->metakey; ?></textarea>
			</td>
		</tr>
		<tr>
			<td class="paramlist_key">
				<label for="metadesc"><?php echo JText::_('META_DESC'); ?></label>
			</td>
			<td class="paramlist_value">
				<textarea id="metadesc" name="data[mail][metadesc]" rows="5" cols="30"><?php echo @$this->mail->metadesc; ?></textarea>
			</td>
		</tr>
	</table>
	<?php
	echo acymailing_getFunctionsEmailCheck(array('save', 'apply', 'savepreview', 'replacetags'));

	echo $this->tabs->endPanel();
	echo $this->tabs->endPane(); ?>
</div>
