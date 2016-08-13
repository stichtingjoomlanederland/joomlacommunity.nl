<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2016 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div id="config_interface">
	<div class="onelineblockoptions">
		<span class="acyblocktitle"><?php echo JText::_('MESSAGES'); ?></span>
		<table class="acymailing_table" cellspacing="1">
			<tr>
				<td class="acykey">
					<?php echo acymailing_tooltip(JText::_('DISPLAY_MSG_SUBSCRIPTION_DESC').'<br /><br /><i>'.($this->config->get('require_confirmation', 0) ? JText::_('CONFIRMATION_SENT') : JText::_('SUBSCRIPTION_OK')).'</i>', JText::_('DISPLAY_MSG_SUBSCRIPTION'), '', JText::_('DISPLAY_MSG_SUBSCRIPTION')); ?>
				</td>
				<td>
					<?php echo $this->elements->subscription_message; ?>
				</td>
			</tr>
			<tr>
				<td class="acykey">
					<?php echo acymailing_tooltip(JText::_('DISPLAY_MSG_CONFIRM_DESC').'<br /><br /><i>'.JText::_('SUBSCRIPTION_CONFIRMED').'</i>', JText::_('DISPLAY_MSG_CONFIRM'), '', JText::_('DISPLAY_MSG_CONFIRM')); ?>
				</td>
				<td>
					<?php echo $this->elements->confirmation_message; ?>
				</td>
			</tr>
			<tr>
				<td class="acykey">
					<?php echo acymailing_tooltip(JText::_('DISPLAY_MSG_UNSUBSCRIPTION_DESC'), JText::_('DISPLAY_MSG_UNSUBSCRIPTION'), '', JText::_('DISPLAY_MSG_UNSUBSCRIPTION')); ?>
				</td>
				<td>
					<?php echo $this->elements->unsubscription_message; ?>
				</td>
			</tr>
			<tr>
				<td class="acykey">
					<?php echo acymailing_tooltip(JText::_('DISPLAY_MSG_CONFIRMATION_DESC'), JText::_('DISPLAY_MSG_CONFIRMATION'), '', JText::_('DISPLAY_MSG_CONFIRMATION')); ?>
				</td>
				<td>
					<?php echo $this->elements->confirm_message; ?>
				</td>
			</tr>
			<tr>
				<td class="acykey">
					<?php echo acymailing_tooltip(JText::_('DISPLAY_MSG_WELCOME_DESC'), JText::_('DISPLAY_MSG_WELCOME'), '', JText::_('DISPLAY_MSG_WELCOME')); ?>
				</td>
				<td>
					<?php echo $this->elements->welcome_message; ?>
				</td>
			</tr>
			<tr>
				<td class="acykey">
					<?php echo acymailing_tooltip(JText::_('DISPLAY_MSG_UNSUB_DESC'), JText::_('DISPLAY_MSG_UNSUB'), '', JText::_('DISPLAY_MSG_UNSUB')); ?>
				</td>
				<td>
					<?php echo $this->elements->unsub_message; ?>
				</td>
			</tr>
		</table>
	</div>
	<div class="onelineblockoptions">
		<span class="acyblocktitle">CSS</span>
		<table class="acymailing_table" cellspacing="1">
			<tr>
				<td class="acykey">
					<?php echo acymailing_tooltip(JText::_('CSS_MODULE_DESC'), JText::_('CSS_MODULE'), '', JText::_('CSS_MODULE')); ?>
				</td>
				<td>
					<?php echo $this->elements->css_module; ?>
				</td>
			</tr>
			<tr>
				<td class="acykey">
					<?php echo acymailing_tooltip(JText::_('CSS_FRONTEND_DESC'), JText::_('CSS_FRONTEND'), '', JText::_('CSS_FRONTEND')); ?>
				</td>
				<td>
					<?php echo $this->elements->css_frontend; ?>
				</td>
			</tr>
			<tr>
				<td class="acykey">
					<?php echo acymailing_tooltip(JText::_('ACY_CSS_BACKEND_DESC'), JText::_('ACY_CSS_BACKEND'), '', JText::_('ACY_CSS_BACKEND')); ?>
				</td>
				<td>
					<?php echo $this->elements->css_backend; ?>
				</td>
			</tr>
			<?php if(ACYMAILING_J30){ ?>
				<tr>
					<td class="acykey">
						<?php echo JText::_('USE_BOOTSTRAP_FRONTEND'); ?>
					</td>
					<td>
						<?php echo $this->elements->bootstrap_frontend; ?>
					</td>
				</tr>
			<?php } ?>
		</table>
	</div>
	<div class="onelineblockoptions">
		<span class="acyblocktitle"><?php echo JText::_('FEATURES'); ?></span>
		<table class="acymailing_table" cellspacing="1">
			<tr>
				<td class="acykey">
					<?php echo acymailing_tooltip(JText::_('FORWARD_DESC'), JText::_('FORWARD_FEATURE'), '', JText::_('FORWARD_FEATURE')); ?>
				</td>
				<td>
					<?php echo $this->elements->forward; ?>
				</td>
			</tr>
			<tr>
				<td class="acykey">
					<?php echo acymailing_tooltip(JText::_('USE_SEF_DESC'), JText::_('USE_SEF'), '', JText::_('USE_SEF')); ?>
				</td>
				<td>
					<?php echo $this->elements->use_sef; ?>
				</td>
			</tr>
		</table>
	</div>
	<div class="onelineblockoptions">
		<span class="acyblocktitle"><?php echo JText::_('TRACKING'); ?></span>
		<table class="acymailing_table" cellspacing="1">
			<tr>
				<td class="acykey">
					<?php echo JText::_('TRACKINGSYSTEM'); ?>
				</td>
				<td>
					<?php echo $this->elements->tracking_system; ?>
				</td>
			</tr>
			<tr>
				<td class="acykey">
					<?php echo JText::_('ACY_TRACKINGSYSTEM_EXTERNAL_LINKS'); ?>
				</td>
				<td>
					<?php echo $this->elements->tracking_system_external_website; ?>
				</td>
			</tr>
		</table>
	</div>
	<div class="onelineblockoptions">
		<span class="acyblocktitle"><?php echo JText::_('MENU'); ?></span>
		<table class="acymailing_table" cellspacing="1">
			<tr>
				<td class="acykey">
					<?php echo acymailing_tooltip(JText::_('ACYMAILING_MENU_DESC'), JText::_('ACYMAILING_MENU'), '', JText::_('ACYMAILING_MENU')); ?>
				</td>
				<td>
					<?php echo $this->elements->acymailing_menu; ?>
				</td>
			</tr>
		</table>
	</div>
	<div class="onelineblockoptions">
		<span class="acyblocktitle"><?php echo JText::_('ACY_EDITOR'); ?></span>
		<table class="acymailing_table" cellspacing="1">
			<tr>
				<td class="acykey">
					<?php echo acymailing_tooltip(JText::_('EDITOR_DESC'), JText::_('ACY_EDITOR'), '', JText::_('ACY_EDITOR')); ?>
				</td>
				<td>
					<?php echo $this->elements->editor; ?>
				</td>
			</tr>
		</table>
	</div>
	<div class="onelineblockoptions">
		<span class="acyblocktitle"><?php echo JText::_('ARCHIVE_SECTION'); ?></span>
		<table class="acymailing_table" cellspacing="1">
			<?php
			if(file_exists(ACYMAILING_ROOT.'components'.DS.'com_jcomments'.DS.'jcomments.php')){
				$jcomments = ($this->config->get('comments_feature') == 'jcomments') ? 'checked="checked"' : '';
			}else{
				$jcomments = 'disabled="disabled"';
			}
			if(file_exists(ACYMAILING_ROOT.'components'.DS.'com_rscomments')){
				$rscomments = ($this->config->get('comments_feature') == 'rscomments') ? 'checked="checked"' : '';
			}else{
				$rscomments = 'disabled="disabled"';
			}
			if(file_exists(ACYMAILING_ROOT.'components'.DS.'com_komento')){
				$komento = ($this->config->get('comments_feature') == 'komento') ? 'checked="checked"' : '';
			}else{
				$komento = 'disabled="disabled"';
			}
			if(file_exists(ACYMAILING_ROOT.'plugins'.DS.'content'.DS.'jom_comment_bot.php')){
				$jomcomment = ($this->config->get('comments_feature') == 'jomcomment') ? 'checked="checked"' : '';
			}else{
				$jomcomment = 'disabled="disabled"';
			}
			if($this->config->get('comments_feature') == 'disqus'){
				$disqus = 'checked="checked"';
			}else{
				$disqus = '';
			}
			$no_checked = $this->config->get('comments_feature') ? '' : 'checked="checked"';

			?>
			<tr>
				<td class="acykey">
					<?php echo acymailing_tooltip(JText::_('COMMENTS_ENABLED_DESC'), JText::_('COMMENTS_ENABLED'), '', JText::_('COMMENTS_ENABLED')); ?>
				</td>
				<td>
					<?php if(ACYMAILING_J30) { ?>
					<div class="controls">
						<fieldset id="config_frontend_subject" class="radio btn-group">
							<?php } ?>
							<input onclick="updateCommentsOption();" name="config[comments_feature]" id="config_comments_feature" value="" <?php echo $no_checked; ?> size="1" type="radio"/>
							<label for="config_comments_feature"><?php echo JText::_('JOOMEXT_NO'); ?></label>
							<input onclick="updateCommentsOption();" name="config[comments_feature]" id="config_comments_feature_rscomments" value="rscomments" <?php echo $rscomments; ?> size="1" type="radio"/>
							<label for="config_comments_feature_rscomments">RSComments</label>
							<input onclick="updateCommentsOption();" name="config[comments_feature]" id="config_comments_feature_komento" value="komento" <?php echo $komento; ?> size="1" type="radio"/>
							<label for="config_comments_feature_komento">Komento</label>
							<input onclick="updateCommentsOption();" name="config[comments_feature]" id="config_comments_feature_jcomments" value="jcomments" <?php echo $jcomments; ?> size="1" type="radio"/>
							<label for="config_comments_feature_jcomments">jComments</label>
							<input onclick="updateCommentsOption();" name="config[comments_feature]" id="config_comments_feature_jomcomment" value="jomcomment" <?php echo $jomcomment; ?> size="1" type="radio"/>
							<label for="config_comments_feature_jomcomment">jomComment</label>
							<input onclick="updateCommentsOption();" name="config[comments_feature]" id="config_comments_feature_disqus" value="disqus" <?php echo $disqus; ?> size="1" type="radio"/>
							<label for="config_comments_feature_disqus">Disqus</label>
							<?php if(ACYMAILING_J30) { ?>
						</fieldset>
					</div>
				<?php } ?>
					<label for="config_disqus_shortname" style="display:<?php echo empty($disqus) ? "none" : "inline-block"; ?>;" id="config_disqus_shortname_label">Shortname : </label>
					<input type="text" name="config[disqus_shortname]" id="config_disqus_shortname" value="<?php echo $this->config->get('disqus_shortname'); ?>" size="1" style="width:100px;float:none;<?php if(empty($disqus)) echo "display:none;"; ?>"/>
				</td>
			</tr>
			<tr>
				<td class="acykey">
					<?php echo acymailing_tooltip(JText::_('SUBJECT_DISPLAY_DESC'), JText::_('SUBJECT_DISPLAY'), '', JText::_('SUBJECT_DISPLAY')); ?>
				</td>
				<td>
					<?php echo JHTML::_('acyselect.booleanlist', "config[frontend_subject]", '', $this->config->get('frontend_subject', 1)); ?>
				</td>
			</tr>
			<?php if(!ACYMAILING_J16){ ?>
				<tr>
					<td class="acykey">
						<?php echo acymailing_tooltip(JText::_('FRONTEND_PDF_DESC'), JText::_('FRONTEND_PDF'), '', JText::_('FRONTEND_PDF')); ?>
					</td>
					<td>
						<?php echo JHTML::_('acyselect.booleanlist', "config[frontend_pdf]", '', $this->config->get('frontend_pdf', 0)); ?>
					</td>
				</tr>
			<?php } ?>
			<tr>
				<td class="acykey">
					<?php echo acymailing_tooltip(JText::_('FRONTEND_PRINT_DESC'), JText::_('FRONTEND_PRINT'), '', JText::_('FRONTEND_PRINT')); ?>
				</td>
				<td>
					<?php echo JHTML::_('acyselect.booleanlist', "config[frontend_print]", '', $this->config->get('frontend_print', 0)); ?>
				</td>
			</tr>
			<tr>
				<td class="acykey">
					<?php echo acymailing_tooltip(JText::_('SHOW_DESCRIPTION_DESC'), JText::_('SHOW_DESCRIPTION'), '', JText::_('SHOW_DESCRIPTION')); ?>
				</td>
				<td>
					<?php echo JHTML::_('acyselect.booleanlist', "config[show_description]", '', $this->config->get('show_description', 1)); ?>
				</td>
			</tr>
			<tr>
				<td class="acykey">
					<?php echo acymailing_tooltip(JText::_('SHOW_FILTER_DESC'), JText::_('SHOW_FILTER'), '', JText::_('SHOW_FILTER')); ?>
				</td>
				<td>
					<?php echo JHTML::_('acyselect.booleanlist', "config[show_filter]", '', $this->config->get('show_filter', 1)); ?>
				</td>
			</tr>
			<tr>
				<td class="acykey">
					<?php echo JText::_('ACY_ORDER'); ?>
				</td>
				<td>
					<?php echo JHTML::_('acyselect.booleanlist', "config[show_order]", '', $this->config->get('show_order', 1)); ?>
				</td>
			</tr>
			<tr>
				<td class="acykey">
					<?php echo acymailing_tooltip(JText::_('SHOW_SENDDATE_DESC'), JText::_('SHOW_SENDDATE'), '', JText::_('SHOW_SENDDATE')); ?>
				</td>
				<td>
					<?php echo JHTML::_('acyselect.booleanlist', "config[show_senddate]", '', $this->config->get('show_senddate', 1)); ?>
				</td>
			</tr>
			<?php if(acymailing_level(1)){ ?>
				<tr>
					<td class="acykey">
						<?php echo JText::sprintf('SHOW_COLUMN_X', '<b><i>'.JText::_('RECEIVE_VIA_EMAIL').'</i></b>'); ?>
					</td>
					<td>
						<?php echo JHTML::_('acyselect.booleanlist', "config[show_receiveemail]", '', $this->config->get('show_receiveemail', 0)); ?>
					</td>
				</tr>
			<?php } ?>
			<tr>
				<td class="acykey" valign="top">
					<?php echo acymailing_tooltip(JText::_('OPEN_POPUP_DESC'), JText::_('OPEN_POPUP'), '', JText::_('OPEN_POPUP')); ?>
				</td>
				<td>
					<?php echo JHTML::_('acyselect.booleanlist', "config[open_popup]", '', $this->config->get('open_popup', 1)); ?>
					<div style="margin-top:10px;">
						<?php echo JText::_('CAPTCHA_WIDTH'); ?> <input type="text" name="config[popup_width]" style="float:none;width:30px" value="<?php echo intval($this->config->get('popup_width', 750)); ?>"/> x <?php echo JText::_('CAPTCHA_HEIGHT'); ?> <input type="text" name="config[popup_height]" style="float:none;width:30px"
																																																																		value="<?php echo intval($this->config->get('popup_height', 550)); ?>"/>
					</div>
				</td>
			</tr>
			<tr id="indexfollow">
				<td class="acykey">
					<?php echo acymailing_tooltip(JText::_('ARCHIVE_INDEX_FOLLOW_DESC'), JText::_('ARCHIVE_INDEX_FOLLOW'), '', JText::_('ARCHIVE_INDEX_FOLLOW')); ?>
				</td>
				<td>
					<?php echo $this->elements->indexFollow; ?>
				</td>
			</tr>
		</table>
	</div>
	<div class="onelineblockoptions">
		<span class="acyblocktitle"><?php echo JText::_('UNSUB_PAGE'); ?></span>
		<table class="acymailing_table" cellspacing="1">
			<tr>
				<td class="acykey">
					<?php echo acymailing_tooltip(str_replace('UNSUB_INTRO', JText::_('UNSUB_INTRO'), $this->config->get('unsub_intro', 'UNSUB_INTRO')), JText::_('UNSUB_INTRODUCTION'), '', JText::_('UNSUB_INTRODUCTION')); ?>
				</td>
				<td>
					<textarea style="width:300px;" rows="5" name="config[unsub_intro]"><?php echo $this->config->get('unsub_intro', 'UNSUB_INTRO'); ?></textarea>
				</td>
			</tr>
			<tr>
				<td class="acykey">
					<?php echo JText::_('UNSUB_DISP_CHOICE'); ?>
				</td>
				<td>
					<?php echo JHTML::_('acyselect.booleanlist', "config[unsub_dispoptions]", '', $this->config->get('unsub_dispoptions', 1)); ?>
				</td>
			</tr>
			<tr>
				<td class="acykey">
					<?php echo JText::_('ACY_UNSUB_DISP_OTHER_SUBS'); ?>
				</td>
				<td>
					<?php echo JHTML::_('acyselect.booleanlist', "config[unsub_dispothersubs]", '', $this->config->get('unsub_dispothersubs', 0)); ?>
				</td>
			</tr>
			<tr>
				<td valign="top" class="acykey">
					<?php echo JText::_('UNSUB_DISP_SURVEY'); ?>
				</td>
				<td>
					<?php echo JHTML::_('acyselect.booleanlist', "config[unsub_survey]", 'onclick="displaySurvey(this.value)"', $this->config->get('unsub_survey', 1));
					$reasons = unserialize($this->config->get('unsub_reasons'));
					?>
					<div id="unsub_reasons_area" class="acymailing_deploy" <?php if(!$this->config->get('unsub_survey', 1)) echo 'style="display:none"'; ?> >
						<div id="unsub_reasons">
							<?php
							foreach($reasons as $i => $oneReason){
								if(preg_match('#^[A-Z_]*$#', $oneReason)){
									$trans = JText::_($oneReason);
								}else{
									$trans = $oneReason;
								}
								echo '<span style="font-size:8px">'.$trans.'</span><br /><input type="text" style="width:300px;margin-bottom: 3px;" value="'.$this->escape($oneReason).'" name="unsub_reasons[]" /><br />';
							} ?>
						</div>
						<a onclick="addUnsubReason();return false;" href='#' title="<?php echo $this->escape(JText::_('FIELD_ADDVALUE')); ?>">
							<button class="acymailing_button_grey" onclick="return false">
								<?php echo JText::_('FIELD_ADDVALUE'); ?>
							</button>
						</a>
					</div>
				</td>
			</tr>
		</table>
	</div>
	<div class="onelineblockoptions">
		<span class="acyblocktitle">RSS</span>
		<table class="acymailing_table" cellspacing="1">
			<tr>
				<td class="acykey">
					<?php echo JText::_('ACY_TYPE'); ?>
				</td>
				<td>
					<?php echo $this->elements->acyrss_format; ?>
				</td>
			</tr>
			<tr>
				<td class="acykey">
					<?php echo JText::_('ACY_NAME'); ?>
				</td>
				<td>
					<input type="text" style="width:200px" name="config[acyrss_name]" value="<?php echo $this->escape($this->config->get('acyrss_name', '')); ?>"/>
				</td>
			</tr>
			<tr>
				<td class="acykey">
					<?php echo JText::_('ACY_DESCRIPTION'); ?>
				</td>
				<td>
					<textarea style="width:300px;" rows="5" name="config[acyrss_description]"><?php echo $this->config->get('acyrss_description', ''); ?></textarea>
				</td>
			</tr>
			<tr>
				<td class="acykey">
					<?php echo JText::_('MAX_ARTICLE'); ?>
				</td>
				<td>
					<input type="text" style="width:50px" name="config[acyrss_element]" value="<?php echo intval($this->config->get('acyrss_element', 20)); ?>"/>
				</td>
			</tr>
			<tr>
				<td class="acykey">
					<?php echo JText::_('ACY_ORDER'); ?>
				</td>
				<td>
					<?php echo $this->elements->acyrss_order; ?>
				</td>
			</tr>
		</table>
	</div>
	<?php if(acymailing_level(3)) include(dirname(__FILE__).DS.'interface_enterprise.php'); ?>
	<script language="javascript" type="text/javascript">
		<!--
		function updateCommentsOption(){
			if(document.getElementById("config_comments_feature_disqus").checked){
				document.getElementById('config_disqus_shortname_label').style.display = 'inline-block';
				document.getElementById('config_disqus_shortname').style.display = '';
			}else{
				document.getElementById('config_disqus_shortname_label').style.display = 'none';
				document.getElementById('config_disqus_shortname').style.display = 'none';
			}
		}
		//-->
	</script>
</div>
