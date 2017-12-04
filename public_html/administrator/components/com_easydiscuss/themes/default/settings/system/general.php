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
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="row">
	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_ENVIRONMENT'); ?>

			<div class="panel-body">
				<div class="form-group">
					<div class="col-md-5 control-label">
						<?php echo $this->html('form.label', 'COM_EASYDISCUSS_SYSTEM_ENVIRONMENT'); ?>
					</div>

					<div class="col-md-7">
						<?php echo $this->html('form.dropdown', 'system_environment', array('production' => 'COM_EASYDISCUSS_ENVIRONMENT_PRODUCTION', 'development' => 'COM_EASYDISCUSS_ENVIRONMENT_DEVELOPMENT'), $this->config->get('system_environment')); ?>
					</div>
				</div>

				<div class="form-group">
					<div class="col-md-5 control-label">
						<?php echo $this->html('form.label', 'COM_EASYDISCUSS_LOAD_JQUERY'); ?>
					</div>

					<div class="col-md-7">
						<?php echo $this->html('form.boolean', 'system_jquery', $this->config->get('system_jquery')); ?>
					</div>
				</div>

				<div class="form-group">
					<div class="col-md-5 control-label">
						<?php echo $this->html('form.label', 'COM_EASYDISCUSS_DB_PROFILING'); ?>
					</div>

					<div class="col-md-7">
						<?php echo $this->html('form.boolean', 'system_db_profiling', $this->config->get('system_db_profiling')); ?>
					</div>
				</div>

				<div class="form-group">
					<div class="col-md-5 control-label">
						<?php echo $this->html('form.label', 'COM_EASYDISCUSS_USE_INDEX_FOR_AJAX_URLS'); ?>
					</div>

					<div class="col-md-7">
						<?php echo $this->html('form.boolean', 'system_ajax_index', $this->config->get('system_ajax_index')); ?>
					</div>
				</div>
				<div class="form-group">
					<div class="col-md-5 control-label">
						<?php echo $this->html('form.label', 'COM_EASYDISCUSS_SETTINGS_MAIN_ERROR_REDIRECTION'); ?>
					</div>
					<div class="col-md-7">
						<?php echo $this->html('form.boolean', 'system_error_redirection', $this->config->get('system_error_redirection')); ?>
					</div>
				</div>
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
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_CDN'); ?>

			<div class="panel-body">
				<div class="form-group">
					<div class="col-md-5 control-label">
						<?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_CDN'); ?>
					</div>
					<div class="col-md-7">
						<?php echo $this->html('form.boolean', 'system_cdn', $this->config->get('system_cdn')); ?>
					</div>
				</div>

				<div class="form-group">
					<div class="col-md-5 control-label">
						<?php echo $this->html('form.label', 'COM_EASYDISCUSS_CDN_URL'); ?>
					</div>
					<div class="col-md-7">
						<?php echo $this->html('form.textbox', 'system_cdn_url', $this->config->get('system_cdn_url'), 'cdn.yourdomain.com'); ?>
					</div>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_CLEANUP'); ?>

			<div class="panel-body">

				<div class="form-group">
					<div class="col-md-5 control-label">
						<?php echo $this->html('form.label', 'COM_EASYDISCUSS_AUTO_PRUNE_NOTIFICATIONS_ON_CRON'); ?>
					</div>

					<div class="col-md-7">
						<?php echo $this->html('form.boolean', 'prune_notifications_cron', $this->config->get('prune_notifications_cron')); ?>
					</div>
				</div>

				<div class="form-group">
					<div class="col-md-5 control-label">
						<?php echo $this->html('form.label', 'COM_EASYDISCUSS_AUTO_PRUNE_NOTIFICATIONS_ON_PAGE_LOAD'); ?>
					</div>

					<div class="col-md-7">
						<?php echo $this->html('form.boolean', 'prune_notifications_onload', $this->config->get('prune_notifications_onload')); ?>
					</div>
				</div>

				<div class="form-group">
					<div class="col-md-5 control-label">
						<?php echo $this->html('form.label', 'COM_EASYDISCUSS_AUTO_PRUNE_NOTIFICATIONS'); ?>
					</div>

					<div class="col-md-7">
						<input type="text" class="form-control form-control-sm text-center" name="notifications_history" value="<?php echo $this->config->get('notifications_history');?>" />
							<span class="form-help-inline"><?php echo JText::_('COM_EASYDISCUSS_DAYS'); ?></span>
					</div>
				</div>

				<div class="form-group">
					<div class="col-md-5 control-label">
						<?php echo $this->html('form.label', 'COM_EASYDISCUSS_OWNER_FOR_ORPHANED_ITEMS'); ?>
					</div>

					<div class="col-md-7">
						<input type="text" class="form-control form-control-sm text-center" name="main_orphanitem_ownership" value="<?php echo $this->config->get('main_orphanitem_ownership');?>" />
					</div>
				</div>
			</div>
		</div>
	</div>

</div>
