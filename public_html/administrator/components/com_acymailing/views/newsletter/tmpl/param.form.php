<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2016 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
if(acymailing_isAllowed($this->config->get('acl_newsletters_lists', 'all')) || acymailing_isAllowed($this->config->get('acl_newsletters_attachments', 'all')) || acymailing_isAllowed($this->config->get('acl_newsletters_sender_informations', 'all')) || acymailing_isAllowed($this->config->get('acl_newsletters_meta_data', 'all')) || (acymailing_isAllowed($this->config->get('acl_newsletters_inbox_actions', 'all')) && JPluginHelper::isEnabled('acymailing', 'plginboxactions'))){ ?>
	<div id="newsletterparams" class="acytabsystem">

		<?php echo $this->tabs->startPane('mail_tab');

		if(!acymailing_isAllowed($this->config->get('acl_newsletters_lists', 'all')) || $this->type == 'joomlanotification'){
			$doc = JFactory::getDocument();
			$doc->addStyleDeclaration(" .mail_receivers_acl{display:none;} ");
			echo '<div class="mail_receivers_acl">';
		}else{
			echo $this->tabs->startPanel(JText::_('LISTS'), 'mail_receivers');
		} ?>
		<?php
		if(empty($this->lists)){
			echo '<span>'.JText::_('LIST_CREATE').'</span>';
		}else{
			echo '<span>'.JText::_('LIST_RECEIVERS').'</span>';
			include_once(ACYMAILING_BACK.'views'.DS.'newsletter'.DS.'tmpl'.DS.'filter.lists.php');

			if(acymailing_level(2) && acymailing_isAllowed($this->config->get('acl_lists_filter', 'all'))) include_once(dirname(__FILE__).DS.'filters.php');
		}
		if(!acymailing_isAllowed($this->config->get('acl_newsletters_lists', 'all')) || $this->type == 'joomlanotification'){
			echo '</div>';
		}else echo $this->tabs->endPanel();

		if(acymailing_isAllowed($this->config->get('acl_newsletters_attachments', 'all'))){
			JHTML::_('behavior.modal', 'a.modal');
			echo $this->tabs->startPanel(JText::_('ATTACHMENTS'), 'mail_attachments');
			if(!empty($this->mail->attach)){
				echo '<div class="onelineblockoptions">
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
			<?php echo $this->tabs->endPanel();
		}

		if(!acymailing_isAllowed($this->config->get('acl_newsletters_sender_informations', 'all'))){
			$doc = JFactory::getDocument();
			$doc->addStyleDeclaration(" .mail_sender_acl{display:none;} ");
			echo '<div id="mail_sender_acl" style="display:none" >';
		}else{
			echo $this->tabs->startPanel(JText::_('SENDER_INFORMATIONS'), 'mail_sender');
		} ?>
		<table width="100%" class="acymailing_table" id="senderinformationfieldset">
			<tr>
				<td class="paramlist_key">
					<label for="fromname"><?php echo JText::_('FROM_NAME'); ?></label>
				</td>
				<td class="paramlist_value">
					<input placeholder="<?php echo JText::_('USE_DEFAULT_VALUE'); ?>" class="inputbox" id="fromname" type="text" name="data[mail][fromname]" style="width:200px; max-width:80%;" value="<?php echo $this->escape(@$this->mail->fromname); ?>"/>
				</td>
			</tr>
			<tr>
				<td class="paramlist_key">
					<label for="fromemail"><?php echo JText::_('FROM_ADDRESS'); ?></label>
				</td>
				<td class="paramlist_value">
					<input onchange="validateEmail(this.value, '<?php echo addslashes(JText::_('FROM_ADDRESS')); ?>')" placeholder="<?php echo JText::_('USE_DEFAULT_VALUE'); ?>" class="inputbox" id="fromemail" type="text" name="data[mail][fromemail]" style="width:200px; max-width:80%;" value="<?php echo $this->escape(@$this->mail->fromemail); ?>"/>
				</td>
			</tr>
			<tr>
				<td class="paramlist_key">
					<label for="replyname"><?php echo JText::_('REPLYTO_NAME'); ?></label>
				</td>
				<td class="paramlist_value">
					<input placeholder="<?php echo JText::_('USE_DEFAULT_VALUE'); ?>" class="inputbox" id="replyname" type="text" name="data[mail][replyname]" style="width:200px; max-width:80%;" value="<?php echo $this->escape(@$this->mail->replyname); ?>"/>
				</td>
			</tr>
			<tr>
				<td class="paramlist_key">
					<label for="replyemail"><?php echo JText::_('REPLYTO_ADDRESS'); ?></label>
				</td>
				<td class="paramlist_value">
					<input onchange="validateEmail(this.value, '<?php echo addslashes(JText::_('REPLYTO_ADDRESS')); ?>')" placeholder="<?php echo JText::_('USE_DEFAULT_VALUE'); ?>" class="inputbox" id="replyemail" type="text" name="data[mail][replyemail]" style="width:200px; max-width:80%;" value="<?php echo $this->escape(@$this->mail->replyemail); ?>"/>
				</td>
			</tr>
			<tr>
				<td class="paramlist_key">
					<label for="replyemail">BCC</label>
				</td>
				<td class="paramlist_value">
					<input placeholder="address@example.com" class="inputbox" id="bccaddresses" type="text" name="data[mail][bccaddresses]" style="width:200px; max-width:80%;" value="<?php echo $this->escape(@$this->mail->bccaddresses); ?>"/>
				</td>
			</tr>
			<?php
			if(acymailing_level(1)){
				JHTML::_('behavior.modal', 'a.modal');
				echo '<tr>
					<td class="paramlist_key">'.JText::_('FAVICON').'</td><td class="paramlist_value">';
				if(!empty($this->mail->favicon) && !empty($this->mail->favicon->filename)){
					echo '<div id="attach_favicon">'.$this->mail->favicon->filename.' ('.(round($this->mail->favicon->size / 1000, 1)).' Ko)';
					echo $this->toggleClass->delete('attach_favicon', $this->mail->mailid.'_favicon', 'favicon');
					echo '</div>';
				}
				?>
				<div id="loadfile">
					<?php
					echo '<div id="favicondiv">'.$uploadfileType->display(false, 'favicon', '').'</div>';
					?>
				</div>
				<?php echo JText::sprintf('MAX_UPLOAD', $this->values->maxupload);
				echo '</td></tr>';
			} ?>
		</table>

		<?php echo acymailing_getFunctionsEmailCheck();

		if(!acymailing_isAllowed($this->config->get('acl_newsletters_sender_informations', 'all'))){
			echo '</div>';
		}else{
			echo $this->tabs->endPanel();
		}

		if($this->type == 'joomlanotification'){
			$doc = JFactory::getDocument();
			$doc->addStyleDeclaration(" .mail_metadata_jnotif{display:none;} ");
			echo '<div class="mail_metadata_jnotif">';
		}else{
			if(acymailing_isAllowed($this->config->get('acl_newsletters_meta_data', 'all'))){
				echo $this->tabs->startPanel(JText::_('META_DATA'), 'mail_metadata'); ?>
				<table width="100%" class="acymailing_table" id="metadatatable">
					<tr>
						<td class="paramlist_key">
							<label for="metakey"><?php echo JText::_('META_KEYWORDS'); ?></label>
						</td>
						<td class="paramlist_value">
							<textarea id="metakey" name="data[mail][metakey]" rows="5" style="width:200px; max-width:80%;"><?php echo @$this->mail->metakey; ?></textarea>
						</td>
					</tr>
					<tr>
						<td class="paramlist_key">
							<label for="metadesc"><?php echo JText::_('META_DESC'); ?></label>
						</td>
						<td class="paramlist_value">
							<textarea id="metadesc" name="data[mail][metadesc]" rows="5" style="width:200px; max-width:80%;"><?php echo @$this->mail->metadesc; ?></textarea>
						</td>
					</tr>
				</table>
				<?php
				echo $this->tabs->endPanel();
			}
		}
		if($this->type == 'joomlanotification') echo '</div>';
		if(acymailing_level(3) && acymailing_isAllowed($this->config->get('acl_newsletters_inbox_actions', 'all')) && JPluginHelper::isEnabled('acymailing', 'plginboxactions')) include(dirname(__FILE__).DS.'inboxactions.php');
		echo $this->tabs->endPane(); ?>
	</div>
<?php } ?>
