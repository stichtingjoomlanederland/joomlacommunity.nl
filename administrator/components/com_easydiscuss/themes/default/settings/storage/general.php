<?php
/**
* @package      EasyDiscuss
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<div class="app-content-body">
	<div class="row">
		<div class="col-md-6">
			<div class="panel">
				<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_STORAGE_GENERAL'); ?>

				<div class="panel-body">
					<div class="form-horizontal">
						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_STORAGE_ATTACHMENTS'); ?>
							</div>
							<div class="col-md-7">
								<?php echo $this->html('form.dropdown', 'storage_attachments', array('joomla' => 'Local', 'amazon' => 'Amazon S3'), $this->config->get('storage_attachments'));?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-md-6">
			<div class="panel">
				<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_STORAGE_AMAZON'); ?>

				<div class="panel-body">
					<div class="form-horizontal">
						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_STORAGE_AMAZON_ENABLE'); ?>
							</div>
							<div class="col-md-7">
								<?php echo $this->html('form.boolean', 'amazon_enabled', $this->config->get('amazon_enabled'));?>
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_STORAGE_AMAZON_ACCESS_KEY'); ?>
							</div>
							<div class="col-md-7">
								<?php echo $this->html('form.textbox', 'amazon_access_key', $this->config->get('amazon_access_key'));?>
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_STORAGE_AMAZON_ACCESS_SECRET'); ?>
							</div>
							<div class="col-md-7">
								<?php echo $this->html('form.textbox', 'amazon_access_secret', $this->config->get('amazon_access_secret'));?>
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_STORAGE_AMAZON_BUCKET_PATH'); ?>
							</div>
							<div class="col-md-7">
								<?php echo $this->html('form.textbox', 'amazon_bucket', $this->config->get('amazon_bucket'));?>
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_STORAGE_AMAZON_SSL'); ?>
							</div>
							<div class="col-md-7">
								<?php echo $this->html('form.boolean', 'amazon_ssl', $this->config->get('amazon_ssl'));?>
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_STORAGE_AMAZON_REGION'); ?>
							</div>
							<div class="col-md-7">
								<?php echo $this->html('form.dropdown', 'amazon_region', array('us' => 'US Standard', 'us-west-2' => 'US West (Oregon)', 'us-west-1' => 'US West (Northern California)',
														'eu-central-1' => 'EU Frankfurt', 'eu-west-1' => 'EU Ireland', 
														'ap-southeast-1' => 'Asia Pacific (Singapore)', 'ap-southeast-2' => 'Asia Pacific (Sydney)', 'ap-southeast-3' => 'Asia Pacific (Tokyo)',
														'sa-east-1' => 'South America (Sau Paulo)'), $this->config->get('amazon_region')
								);?>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>
	</div>
</div>
