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
		<div class="panel <?php echo $this->config->get('storage_attachments') != 'amazon' ? 't-hidden' : '';?>" data-storage-amazon>
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_STORAGE_AMAZON'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<?php echo $this->html('settings.toggle', 'amazon_enabled', 'COM_EASYDISCUSS_STORAGE_AMAZON_ENABLE'); ?>
					<?php echo $this->html('settings.toggle', 'amazon_ssl', 'COM_EASYDISCUSS_STORAGE_AMAZON_SSL'); ?>

					<?php echo $this->html('settings.textbox', 'amazon_access_key', 'COM_EASYDISCUSS_STORAGE_AMAZON_ACCESS_KEY'); ?>
					<?php echo $this->html('settings.textbox', 'amazon_access_secret', 'COM_EASYDISCUSS_STORAGE_AMAZON_ACCESS_SECRET'); ?>
					<?php echo $this->html('settings.textbox', 'amazon_bucket', 'COM_EASYDISCUSS_STORAGE_AMAZON_BUCKET_PATH'); ?>

					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_STORAGE_AMAZON_REGION'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.dropdown', 'amazon_region', array(
													'us' => 'US East (Northern Virginia)', 
													'us-east-2' => 'US East (Ohio)', 
													'us-west-2' => 'US West (Oregon)', 
													'us-west-1' => 'US West (Northern California)',
													'eu-central-1' => 'EU Frankfurt', 
													'eu-west-1' => 'EU Ireland',
													'eu-west-2' => 'EU London',
													'ap-southeast-1' => 'Asia Pacific (Singapore)', 
													'ap-southeast-2' => 'Asia Pacific (Sydney)', 
													'ap-southeast-3' => 'Asia Pacific (Tokyo)',
													'ap-south-1' => 'Asia Pacific (Mumbai)',
													'sa-east-1' => 'South America (Sau Paulo)'
												), $this->config->get('amazon_region')
							);?>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>
