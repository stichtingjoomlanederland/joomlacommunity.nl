<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="row">
	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_INTEGRATIONS_GOOGLE_ADSENSE', '', '/docs/easydiscuss/administrators/integrations/integrations#google'); ?>

			<div class="panel-body">
				<div class="o-form-horizontal">
					<?php echo $this->html('settings.toggle', 'integration_google_adsense_enable', 'COM_EASYDISCUSS_INTEGRATIONS_GOOGLE_ADSENSE_ENABLE'); ?>
					<?php echo $this->html('settings.toggle', 'integration_google_adsense_responsive', 'COM_EASYDISCUSS_INTEGRATIONS_GOOGLE_ADSENSE_RESPONSIVE_CODE'); ?>
					<?php echo $this->html('settings.toggle', 'integration_google_adsense_script', 'COM_EASYDISCUSS_INTEGRATIONS_GOOGLE_ADSENSE_SCRIPT'); ?>

					<?php echo $this->html('settings.textarea', 'integration_google_adsense_code', 'COM_EASYDISCUSS_INTEGRATIONS_GOOGLE_ADSENSE_CODE', '', '', array(), 'COM_EASYDISCUSS_INTEGRATIONS_GOOGLE_ADSENSE_CODE_EXAMPLE'); ?>


					<div class="o-form-group" data-responsive-form>
						<div class="col-md-5 o-form-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_INTEGRATIONS_GOOGLE_ADSENSE_CODE_RESPONSIVE'); ?>
						</div>
						<div class="col-md-7">
							<textarea name="integration_google_adsense_responsive_code" class="o-form-control" rows="5"><?php echo $this->html('string.escape', $this->config->get('integration_google_adsense_responsive_code'));?></textarea>

							<div class="t-mt--sm t-mb--sm">
								<?php echo JText::_('COM_EASYDISCUSS_INTEGRATIOSN_GOOGLE_ADSENSE_ONLY_CODES_BELOW');?>
							</div>

							<pre><?php echo $this->html('string.escape', '<ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-XXXXXXXXXXXX" data-ad-slot="xxxx" data-ad-format="auto"></ins>');?></pre>
						</div>
					</div>

					<div class="o-form-group">
						<div class="col-md-5 o-form-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_INTEGRATIONS_GOOGLE_ADSENSE_DISPLAY'); ?>
						</div>
						<?php 
						$storedDisplay = $this->config->get('integration_google_adsense_display', array());
						if ($storedDisplay) {
							$storedDisplay = explode(',', $storedDisplay);
						} 
						?>
						<div class="col-md-7">						
							<select name="integration_google_adsense_display[]" class="o-form-select" multiple="multiple" size="4">
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
							<p class="t-mt--sm"><?php echo JText::_('COM_EASYDISCUSS_GOOGLE_ADSENSE_SELECT_MULTIPLE'); ?></p>
						</div>
					</div>

					<?php echo $this->html('settings.dropdown', 'integration_google_adsense_display_access', 'COM_EASYDISCUSS_INTEGRATIONS_ADSENSE_DISPLAY_ACCESS', '', 
						array(
							'both' => 'COM_EASYDISCUSS_INTEGRATIONS_ADSENSE_DISPLAY_ALL',
							'members' => 'COM_EASYDISCUSS_INTEGRATIONS_ADSENSE_DISPLAY_MEMBERS',
							'guests' => 'COM_EASYDISCUSS_INTEGRATIONS_ADSENSE_DISPLAY_GUESTS'
						)
					);?>
	
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-6">
	</div>	
</div>
