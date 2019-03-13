<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
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
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_EASYBLOG_INTEGRATIONS'); ?>

			<div class="panel-body">
				<div>
					<img width="64" align="left" src="<?php echo JURI::root();?>administrator/components/com_easydiscuss/themes/default/images/integrations/easyblog.png" style="margin-left: 20px;margin-right:25px; float: left;">
					
					<div class="small" style="overflow:hidden;">
						<?php echo JText::_('COM_EASYDISCUSS_EASYBLOG_INFO');?><br /><br />
						<a target="_blank" class="btn btn-primary btn-sm t-lg-mb--lg" href="https://stackideas.com/easyblog"><?php echo JText::_('COM_EASYDISCUSS_LEARN_MORE_ABOUT_EASYBLOG'); ?> &rarr;</a>
					</div>
				</div>
				<div class="form-horizontal">
					<?php echo $this->html('settings.toggle', 'integrations_easyblog_toolbar', 'COM_ED_SETTINGS_INTEGRATIONS_EASYBLOG_TOOLBAR'); ?>
					<?php echo $this->html('settings.toggle', 'integrations_easyblog_profile', 'COM_EASYDISCUSS_EASYBLOG_DISPLAY_BLOGS_IN_PROFILE'); ?>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">
	</div>
</div>
