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
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_ENVIRONMENT'); ?>

			<div class="panel-body">
				<?php echo $this->html('settings.toggle', 'system_cdn', 'COM_EASYDISCUSS_ENABLE_CDN'); ?>
				<?php echo $this->html('settings.textbox', 'system_cdn_url', 'COM_EASYDISCUSS_CDN_URL'); ?>
				<?php echo $this->html('settings.toggle', 'system_jquery', 'COM_EASYDISCUSS_LOAD_JQUERY'); ?>
				<?php echo $this->html('settings.toggle', 'system_ajax_index', 'COM_EASYDISCUSS_USE_INDEX_FOR_AJAX_URLS'); ?>
				<?php echo $this->html('settings.toggle', 'system_error_redirection', 'COM_EASYDISCUSS_SETTINGS_MAIN_ERROR_REDIRECTION'); ?>
			</div>
		</div>


		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_DATABASE'); ?>

			<div class="panel-body">
				<div class="form-group">
					<div class="col-md-5 control-label">
						<?php echo $this->html('form.label', 'COM_EASYDISCUSS_PAGINATION_BEHAVIOR'); ?>
					</div>
					<div class="col-md-7">
						<?php echo $this->html('form.dropdown', 'system_query', array('default' => 'COM_EASYDISCUSS_PAGINATION_BEHAVIOR_DEFAULT', 'count' => 'COM_EASYDISCUSS_PAGINATION_BEHAVIOR_COUNT'), $this->config->get('system_query')); ?>
					</div>
				</div>

			</div>
		</div>

	</div>

	<div class="col-md-6">

		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_CLEANUP'); ?>

			<div class="panel-body">
				<?php echo $this->html('settings.toggle', 'prune_notifications_cron', 'COM_EASYDISCUSS_AUTO_PRUNE_NOTIFICATIONS_ON_CRON'); ?>
				<?php echo $this->html('settings.toggle', 'prune_notifications_onload', 'COM_EASYDISCUSS_AUTO_PRUNE_NOTIFICATIONS_ON_PAGE_LOAD'); ?>
				<?php echo $this->html('settings.textbox', 'notifications_history', 'COM_EASYDISCUSS_AUTO_PRUNE_NOTIFICATIONS', '', array('size' => 7, 'postfix' => 'COM_EASYDISCUSS_DAYS'), '', 'form-control-sm text-center'); ?>
				<?php echo $this->html('settings.textbox', 'main_orphanitem_ownership', 'COM_EASYDISCUSS_OWNER_FOR_ORPHANED_ITEMS', '', array(), '', 'form-control-sm text-center'); ?>
			</div>
		</div>
	</div>

</div>
