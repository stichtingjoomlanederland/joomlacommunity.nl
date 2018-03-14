<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access'); 
$clength	= (int) $this->config->max_comm_len;
$icons		= RSCommentsHelper::showIcons($this->permissions); 
$emoticons	= RSCommentsEmoticons::createEmoticons();
$upload		= (int) $this->config->enable_upload;
$required	= ' *'; ?>

<div class="rscomment-form well<?php if ($this->config->comment_form_position) { ?> rscomment-form-top<?php } ?>">
	<form id="rscommentsForm" name="rscommentsForm" action="javascript:void(0)" method="post">
		<?php if ($this->config->form_accordion) { ?>
		<div class="rscomments-accordion">
			<a class="rscomments-accordion-title<?php echo $this->config->show_form ? ' active' : ''; ?>" href="#rscomments-accordion-content">
				<?php echo $this->config->show_form ? JText::_('COM_RSCOMMENTS_HIDE_FORM') : JText::_('COM_RSCOMMENTS_SHOW_FORM'); ?>
			</a>
			<div class="rscomments-accordion-content<?php echo $this->config->show_form ? ' open' : ''; ?>" id="rscomments-accordion-content"<?php echo !$this->config->show_form ? ' style="display: none;"' : ''; ?>>
		<?php } ?>
		
		<div id="rscomments-form-message" class="alert" style="display: none;"></div>
		
		<div class="row-fluid">
			<div class="control-group span6">
				<?php if ($this->config->show_labels) { ?>
				<label class="control-label <?php echo RSTooltip::tooltipClass(); ?>" for="rsc_name" title="<?php echo RSTooltip::tooltipText(JText::_('COM_RSCOMMENTS_COMMENT_NAME_DESC')); ?>">
					<?php echo JText::_('COM_RSCOMMENTS_COMMENT_NAME').$required; ?>
				</label>
				<?php } ?>
				<div class="controls">
					<input <?php echo $this->disable; ?> type="text" class="span11 required" id="rsc_name" name="jform[name]" value="<?php echo $this->user->get('name'); ?>" size="45" <?php if (!$this->config->show_labels) { ?>placeholder="<?php echo JText::_('COM_RSCOMMENTS_COMMENT_NAME').$required; ?>"<?php } ?> />
				</div>
			</div>
			<div class="control-group span6">
				<?php if ($this->config->show_labels) { ?>
				<label class="control-label <?php echo RSTooltip::tooltipClass(); ?>" for="rsc_email" title="<?php echo RSTooltip::tooltipText(JText::_('COM_RSCOMMENTS_COMMENT_EMAIL_DESC')); ?>">
					<?php echo JText::_('COM_RSCOMMENTS_COMMENT_EMAIL').$required; ?>
				</label>
				<?php } ?>
				<div class="controls">
					<input <?php echo $this->disable; ?> type="text" class="span11 required" id="rsc_email" name="jform[email]" value="<?php echo $this->user->get('email'); ?>" size="45" <?php if (!$this->config->show_labels) { ?>placeholder="<?php echo JText::_('COM_RSCOMMENTS_COMMENT_EMAIL').$required; ?>"<?php } ?> />
				</div>
			</div>
		</div>
		
		<?php if ($this->config->enable_title_field == 1 || $this->config->enable_website_field == 1) { ?>
		<div class="row-fluid">
			<?php if ($this->config->enable_title_field == 1) { ?>
			<div class="control-group span6">
				<?php if ($this->config->show_labels) { ?>
				<label class="control-label <?php echo RSTooltip::tooltipClass(); ?>" for="rsc_subject" title="<?php echo RSTooltip::tooltipText(JText::_('COM_RSCOMMENTS_COMMENT_SUBJECT_DESC')); ?>">
					<?php echo JText::_('COM_RSCOMMENTS_COMMENT_SUBJECT'); ?>
				</label>
				<?php } ?>
				<div class="controls">
					<input type="text" class="span11" id="rsc_subject" name="jform[subject]" value="" size="45" <?php if (!$this->config->show_labels) { ?>placeholder="<?php echo JText::_('COM_RSCOMMENTS_COMMENT_SUBJECT'); ?>"<?php } ?> />
				</div>
			</div>
			<?php } ?>
			
			<?php if ($this->config->enable_website_field == 1) { ?>
			<div class="control-group span6">
				<?php if ($this->config->show_labels) { ?>
				<label class="control-label <?php echo RSTooltip::tooltipClass(); ?>" for="rsc_website" title="<?php echo RSTooltip::tooltipText(JText::_('COM_RSCOMMENTS_COMMENT_WEBSITE_DESC')); ?>">
					<?php echo JText::_('COM_RSCOMMENTS_COMMENT_WEBSITE'); ?>
				</label>
				<?php } ?>
				<div class="controls">
					<input type="text" class="span11" id="rsc_website" name="jform[website]" value="" size="45" <?php if (!$this->config->show_labels) { ?>placeholder="<?php echo JText::_('COM_RSCOMMENTS_COMMENT_WEBSITE'); ?>"<?php } ?> />
				</div>
			</div>
			<?php } ?>
		</div>
		<?php } ?>
		
		<?php if ($this->config->enable_location) { ?>
		<div class="row-fluid">
			<div class="control-group rsc_location_container">
				<?php if ($this->config->show_labels) { ?>
				<label class="control-label <?php echo RSTooltip::tooltipClass(); ?>" for="rsc_location" title="<?php echo RSTooltip::tooltipText(JText::_('COM_RSCOMMENTS_COMMENT_LOCATION_DESC')); ?>">
					<?php echo JText::_('COM_RSCOMMENTS_COMMENT_LOCATION'); ?>
				</label>
				<?php } ?>
				<a id="rsc_detect_btn" href="javascript:void(0)" class="badge badge-info rsc_detect hidden-phone"><?php echo JText::_('COM_RSCOMMENTS_COMMENT_DETECT_LOCATION'); ?></a>
				<input type="text" class="span12" id="rsc_location" name="jform[location]" value="" size="45" <?php if (!$this->config->show_labels) { ?>placeholder="<?php echo JText::_('COM_RSCOMMENTS_COMMENT_LOCATION'); ?>"<?php } ?> />
			</div>
		</div>
		<?php } ?>
		
		<div class="row-fluid">
			<?php if (!empty($icons) || !empty($emoticons)) { ?>
			<div class="control-group span12">
				<div class="rscomm-editor-buttons">
					<div class="btn-toolbar">						
					<?php 
					if (!empty($icons)) {
						if ($iconchunks = array_chunk($icons,4)) {
							foreach ($iconchunks as $iconchunk) {
								echo '<div class="btn-group">';
								foreach ($iconchunk as $i => $icon) {
									echo $icon."\n";
								}
								echo '</div>';
							}
						}
					}
					?>
					</div>
					
					<?php if ($this->config->enable_smiles == 1 && !empty($emoticons)) { ?>
					<div class="btn-toolbar" id="rsc_emoticons" style="display: none;">
					<?php 
					if ($emoticonschuncks = array_chunk($emoticons,4)) {
						foreach ($emoticonschuncks as $emoticonschunck) {
							echo '<div class="btn-group">';
							foreach ($emoticonschunck as $emoticon) { 
								echo $emoticon."\n";
							}
							echo '</div>';
						}
					}
					?>
					</div>
					<?php } ?>
				</div>
			</div>
			<?php } ?>
		</div>
		
		<div class="row-fluid">
			<?php if ($this->config->show_counter == 1) { ?>
			<div class="control-group pull-right">
				<p class="char-left muted"><span id="commentlen"><?php echo $clength; ?></span> <?php echo JText::_('COM_RSCOMMENTS_CHARS_LEFT'); ?></p>
			</div>
			<?php } ?>
			<div class="control-group span12" id="rscomments-comment-area">
				<div class="controls">
					<textarea id="rsc_comment" class="input-block-level required" name="jform[comment]" rows="5" onkeydown="rsc_comment_cnt('rsc_comment','commentlen','<?php echo $clength; ?>');" onkeyup="rsc_comment_cnt('rsc_comment','commentlen','<?php echo $clength; ?>');" maxlength="<?php echo $clength; ?>" placeholder="<?php echo JText::_('COM_RSCOMMENTS_COMMENT_COMMENT'); ?>"></textarea>
				</div>
			</div>
		</div>
		
		<?php if ($this->config->enable_upload) { ?>
		<iframe src="<?php echo RSCommentsHelper::route('index.php?option=com_rscomments&task=upload&tmpl=component'); ?>" name="rsc_frame" id="rsc_frame" frameborder="0" scrolling="no" width="90%" height="40"></iframe>
		<?php } ?>
		
		<hr>
		<?php if (($this->config->show_subcription_checkbox && !$this->permissions['auto_subscribe_thread']) || $this->config->terms) { ?>
		<div class="row-fluid">
			<?php if ($this->config->show_subcription_checkbox && !$this->permissions['auto_subscribe_thread']) { ?>
			<div class="control-group span3">
				<div class="controls">
					<label class="checkbox <?php echo RSTooltip::tooltipClass(); ?>" title="<?php echo RSTooltip::tooltipText(JText::_('COM_RSCOMMENTS_SUBSCRIBE_DESC')); ?>">
						<input type="checkbox" id="rsc_subscribe_thread" class="rsc_chk" name="jform[subscribe_thread]" value="1" /> <?php echo JText::_('COM_RSCOMMENTS_SUBSCRIBE'); ?>
					</label>
				</div>
			</div>
			<?php } ?>
			
			<?php if ($this->config->terms) { ?>
			<div class="control-group span9">
				<div class="controls">
					<label class="checkbox">
						<input type="checkbox" id="rsc_terms" class="rsc_chk required" name="jform[rsc_terms]" value="1" /> 
						<a data-toggle="modal" href="javascript:void(0)" data-target="#rscomments-terms">
							<?php echo JText::_('COM_RSCOMMENTS_TERMS'); ?>
						</a>
					</label>
				</div>
			</div>
			<?php } ?>
		</div>
		<?php } ?>
		
		<?php if (isset($this->permissions['captcha']) && $this->permissions['captcha']) { ?>
		<div class="row-fluid">
			<div class="control-group">
				<div class="controls">
					<?php if ($this->config->captcha == 0) { ?>
					<img src="<?php echo RSCommentsHelper::route('index.php?option=com_rscomments&task=captcha&type=form'); ?>" id="submit_captcha_image" alt="Antispam" height="80" />
						<span class="<?php echo RSTooltip::tooltipClass(); ?>" title="<?php echo RSTooltip::tooltipText(JText::_('COM_RSCOMMENTS_REFRESH_CAPTCHA_DESC')); ?>">
							<a id="rscomments-refresh-captcha" style="border-style: none" href="javascript:void(0)" onclick="rsc_refresh_captcha('<?php echo $this->root; ?>','<?php echo RSCommentsHelper::route('index.php?option=com_rscomments&task=captcha&type=form'); ?>');">
								<i class="fa fa-refresh"></i>
							</a>
						</span> <br />
						<input type="text" name="jform[captcha]" id="submit_captcha" size="40" value="" class="span5 <?php echo RSTooltip::tooltipClass(); ?> required" title="<?php echo RSTooltip::tooltipText($this->config->captcha_cases ? JText::_('COM_RSCOMMENTS_CAPTCHA_CASE_SENSITIVE') : JText::_('COM_RSCOMMENTS_CAPTCHA_CASE_INSENSITIVE')); ?>" />
					<?php } else if ($this->config->captcha == 1) {
						require_once(JPATH_SITE.'/components/com_rscomments/helpers/recaptcha/recaptchalib.php');
						echo RSCommentsReCAPTCHA::loadScript('rscomments-form-recaptcha',$this->config);
					} else { ?>
						<div id="rsc-g-recaptcha"></div>
					<?php } ?>
				</div>
			</div>
		</div>
		
		<?php } ?>
		
		<div class="row-fluid">
			<span id="rsc_loading_form" class="rsloading" style="display:none;">
				<?php echo JHtml::image('com_rscomments/loader.gif', '', array(), true); ?>
			</span>		
			<button type="button" id="rsc_submit" class="btn btn-primary" onclick="rsc_validate('<?php echo $upload; ?>', '<?php echo RSCommentsHelper::route('index.php?option=com_rscomments&task=captcha'); ?>');"><?php echo JText::_('COM_RSCOMMENTS_SEND'); ?></button>
			<button type="button" onclick="rsc_reset_form();" id="rsc_reset" class="btn"><?php echo JText::_('COM_RSCOMMENTS_RESET'); ?></button>
			<button type="button" id="rsc_cancel" class="btn" style="display:none;" onclick="rsc_cancel_reply();"><?php echo JText::_('COM_RSCOMMENTS_CANCEL'); ?></button>
		</div>
		
		<?php if ($this->config->form_accordion) { ?>
			</div>
		</div>
		<?php } ?>
		
		<input type="hidden" id="rsc_id_parent" name="jform[IdParent]" value="0" />
		<input type="hidden" id="rsc_obj_option" name="jform[obj_option]" value="<?php echo $this->theoption; ?>" />
		<input type="hidden" id="rsc_url" name="jform[url]" value="<?php echo RSCommentsHelper::getUrl(); ?>" />
		<input type="hidden" id="rsc_obj_id" name="jform[obj_id]" value="<?php echo $this->id; ?>" />
		<input type="hidden" id="rsc_IdComment" name="jform[IdComment]" value="" />
		<input type="hidden" id="rsc_override" name="jform[override]" value="<?php echo (int) $this->override; ?>" />
		<input type="hidden" id="rsc_coordinates" name="jform[coordinates]" value="" />
	</form>
	
	<?php if ($this->config->captcha == 1) { ?>
	<script type="text/javascript">
	function reload_form_recapthca() {
		Recaptcha.destroy();
		Recaptcha.create("<?php echo $this->config->rec_public; ?>", "rscomments-form-recaptcha", {
			theme: "<?php echo $this->config->rec_themes; ?>"
		});
	}
	</script>
	<?php } ?>
</div>