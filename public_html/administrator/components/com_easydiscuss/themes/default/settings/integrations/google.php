<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<div class="row">
	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_INTEGRATIONS_GOOGLE_ADSENSE'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_INTEGRATIONS_GOOGLE_ADSENSE_ENABLE'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'integration_google_adsense_enable', $this->config->get('integration_google_adsense_enable')); ?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_INTEGRATIONS_GOOGLE_ADSENSE_RESPONSIVE_CODE'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'integration_google_adsense_responsive', $this->config->get('integration_google_adsense_responsive'), '', 'data-adsense-responsive'); ?>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_INTEGRATIONS_GOOGLE_ADSENSE_SCRIPT'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'integration_google_adsense_script', $this->config->get('integration_google_adsense_script')); ?>
						</div>
					</div>

					<div class="form-group <?php echo $this->config->get('integration_google_adsense_responsive') ? ' hide' : '';?>" data-code-form>
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_INTEGRATIONS_GOOGLE_ADSENSE_CODE'); ?>
						</div>
						<div class="col-md-7">
							<textarea name="integration_google_adsense_code" class="form-control" style="margin-bottom: 10px;height: 75px;"><?php echo $this->config->get('integration_google_adsense_code');?></textarea>
						</div>
						<div class="col-md-5 control-label"></div>
						<div class="col-md-7">
							<?php echo JText::_('COM_EASYDISCUSS_INTEGRATIONS_GOOGLE_ADSENSE_CODE_EXAMPLE');?>
						</div>
					</div>
					<div class="form-group <?php echo !$this->config->get('integration_google_adsense_responsive') ? ' hide' : '';?>" data-responsive-form>
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_INTEGRATIONS_GOOGLE_ADSENSE_CODE_RESPONSIVE'); ?>
						</div>
						<div class="col-md-7">
							<textarea name="integration_google_adsense_responsive_code" class="form-control" style="margin-bottom: 10px;height: 75px;"><?php echo $this->html('string.escape', $this->config->get('integration_google_adsense_responsive_code'));?></textarea>
						</div>
						<div class="col-md-5 control-label"></div>
						<div class="col-md-7">
							<?php echo JText::_('COM_EASYDISCUSS_INTEGRATIOSN_GOOGLE_ADSENSE_ONLY_CODES_BELOW');?><br />

							<pre><?php echo $this->html('string.escape', '<ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-XXXXXXXXXXXX" data-ad-slot="xxxx" data-ad-format="auto"></ins>');?></pre>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_INTEGRATIONS_GOOGLE_ADSENSE_DISPLAY'); ?>
						</div>
						<?php 
						$storedDisplay = $this->config->get('integration_google_adsense_display', array());
						if ($storedDisplay) {
							$storedDisplay = explode(',', $storedDisplay);
						} 
						?>
						<div class="col-md-7">						
							<select name="integration_google_adsense_display[]" class="form-control" multiple="multiple" size="4">
								<option value="header" <?php echo in_array('header', $storedDisplay) ? ' selected="selected"' : '';?>>
									<?php echo JText::_('COM_EASYDISCUSS_INTEGRATIONS_GOOGLE_ADSENSE_HEADER');?>
								</option>
								<option value="footer" <?php echo in_array('footer', $storedDisplay) ? ' selected="selected"' : '';?>>
									<?php echo JText::_('COM_EASYDISCUSS_INTEGRATIONS_GOOGLE_ADSENSE_FOOTER');?>
								</option>
								<option value="beforereplies" <?php echo in_array('beforereplies', $storedDisplay) ? ' selected="selected"' : '';?>>
									<?php echo JText::_('COM_EASYDISCUSS_INTEGRATIONS_GOOGLE_ADSENSE_BEFORE_REPLIES');?>
								</option>                                                                                                                
							</select>
							<p class="mt-5 small"><?php echo JText::_('COM_EASYDISCUSS_GOOGLE_ADSENSE_SELECT_MULTIPLE'); ?></p>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_INTEGRATIONS_ADSENSE_DISPLAY_ACCESS'); ?>
						</div>
						<div class="col-md-7">
							<?php
							$display = array();
							$display[] = JHTML::_('select.option', 'both', JText::_('COM_EASYDISCUSS_INTEGRATIONS_ADSENSE_DISPLAY_ALL'));
							$display[] = JHTML::_('select.option', 'members', JText::_('COM_EASYDISCUSS_INTEGRATIONS_ADSENSE_DISPLAY_MEMBERS'));
							$display[] = JHTML::_('select.option', 'guests', JText::_('COM_EASYDISCUSS_INTEGRATIONS_ADSENSE_DISPLAY_GUESTS'));
							$showOption = JHTML::_('select.genericlist', $display, 'integration_google_adsense_display_access', 'class="form-control"  ', 'value', 'text', $this->config->get('integration_google_adsense_display_access', 'both'));
							echo $showOption;
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-6">
	</div>	
</div>
