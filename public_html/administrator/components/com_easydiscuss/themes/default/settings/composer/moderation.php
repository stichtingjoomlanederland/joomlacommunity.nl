<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
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
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_MAIN_MODERATION'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<?php echo $this->html('settings.toggle', 'main_moderatepost', 'COM_EASYDISCUSS_MODERATE_NEW_POST', '','data-moderation-threshold'); ?>
				</div>
			</div>

		</div>
	</div>

	<div class="col-md-6">
		<div class="panel <?php echo $this->config->get('main_moderatepost') ? '' : 't-hidden';?>" data-moderation-threshold-wrapper>
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_MODERATION_THRESHOLD'); ?>

			<div class="panel-body">
				<?php echo $this->html('panel.info', 'COM_EASYDISCUSS_SETTINGS_MODERATION_THRESHOLD_INFO'); ?>
				
				<div class="form-horizontal">
					<?php echo $this->html('settings.toggle', 'main_moderation_automated', 'COM_EASYDISCUSS_ENABLE_MODERATION_THRESHOLD'); ?>
					<?php echo $this->html('settings.textbox', 'moderation_threshold', 'COM_EASYDISCUSS_MODERATION_THRESHOLD', '', array('size' => 7, 'postfix' => 'Posts'), '', 'form-control-sm text-center'); ?>
				</div>
			</div>
		</div>
	</div>
</div>