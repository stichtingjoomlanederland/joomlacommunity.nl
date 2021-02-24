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
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_EASYSOCIAL_INTEGRATIONS', '', '/docs/easydiscuss/administrators/integrations/integrations#easysocial'); ?>

			<div class="panel-body">
				<div class="panel-info">
					<img width="64" align="left" src="<?php echo JURI::root();?>administrator/components/com_easydiscuss/themes/default/images/integrations/easysocial.png" style="margin-right:25px; float: left;">
					
					<div style="overflow:hidden;">
						<?php echo JText::_('COM_EASYDISCUSS_EASYSOCIAL_INFO');?><br /><br />
						<a target="_blank" class="o-btn o-btn--default-o" href="https://stackideas.com/easysocial"><?php echo JText::_('COM_EASYDISCUSS_LEARN_MORE_EASYSOCIAL'); ?></a>
					</div>
				</div>

				<div class="o-form-horizontal">
					<?php echo $this->html('settings.toggle', 'integration_easysocial_toolbar', 'COM_ED_SETTINGS_INTEGRATIONS_EASYSOCIAL_TOOLBAR'); ?>
					<?php echo $this->html('settings.toggle', 'integration_easysocial_toolbar_profile', 'COM_EASYDISCUSS_LINK_TO_EASYSOCIAL_PROFILE'); ?>
					<?php echo $this->html('settings.toggle', 'integration_easysocial_popbox', 'COM_EASYDISCUSS_EASYSOCIAL_POPBOX_AVATAR'); ?>
					<?php echo $this->html('settings.toggle', 'integration_easysocial_points', 'COM_EASYDISCUSS_EASYSOCIAL_USE_POINTS_INTEGRATIONS'); ?>
					<?php echo $this->html('settings.toggle', 'integration_easysocial_members', 'COM_EASYDISCUSS_LINK_TO_EASYSOCIAL_MEMBERS'); ?>
					<?php echo $this->html('settings.toggle', 'integration_easysocial_messaging', 'COM_EASYDISCUSS_LINK_TO_EASYSOCIAL_MESSAGING'); ?>
					<?php echo $this->html('settings.toggle', 'integration_easysocial_notifications', 'COM_ED_INTEGRATE_EASYSOCIAL_NOTIFICATIONS', '', '', 'COM_ED_INTEGRATE_EASYSOCIAL_NOTIFICATIONS_INSTRUCTION'); ?>
					<?php echo $this->html('settings.toggle', 'integration_easysocial_stream', 'COM_ED_INTEGRATE_EASYSOCIAL_STREAM'); ?>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">
	</div>
</div>
