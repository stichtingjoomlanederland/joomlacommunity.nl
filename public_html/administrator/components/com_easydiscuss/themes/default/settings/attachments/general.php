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
			<?php echo $this->html('panel.head', 'COM_ED_SETTINGS_ATTACHMENTS_GENERAL'); ?>

			<div class="panel-body">
				<div class="o-form-horizontal">
					<?php echo $this->html('settings.toggle', 'attachment_questions', 'COM_EASYDISCUSS_ENABLE_FILE_ATTACHMENTS_QUESTIONS'); ?>
					<?php echo $this->html('settings.toggle', 'enable_attachment_limit', 'COM_EASYDISCUSS_FILE_ENABLE_ATTACHMENTS_LIMIT'); ?>
					<?php echo $this->html('settings.textbox', 'attachment_limit', 'COM_EASYDISCUSS_FILE_ATTACHMENTS_LIMIT', '', array('size' => 7, 'postfix' => 'COM_EASYDISCUSS_FILE_ATTACHMENTS_FILES'), '', '', 'text-center'); ?>
					<?php echo $this->html('settings.textbox', 'attachment_maxsize', 'COM_EASYDISCUSS_FILE_ATTACHMENTS_MAXSIZE', '', array('size' => 7, 'postfix' => 'COM_EASYDISCUSS_MB'), '', '', 'text-center'); ?>

					<div class="o-form-group">
						<div class="col-md-5 o-form-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_STORAGE_PATH'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.textbox', 'storage_path', $this->config->get('storage_path', '/media/com_easydiscuss/' . $this->config->get('attachment_path')));?>
						</div>
					</div>

					<?php echo $this->html('settings.textarea', 'main_attachment_extension', 'COM_EASYDISCUSS_FILE_ATTACHMENTS_ALLOWED_EXTENSION', '', array(), ''); ?>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_ED_IMAGE_OPTIMIZER', '', '/dashboard/optimizer'); ?>

			<div class="panel-body">
				<?php echo $this->html('panel.info', 'COM_ED_IMAGE_OPTIMIZER_INFO'); ?>

				<div class="o-form-horizontal">
					<?php echo $this->html('settings.toggle', 'optimize_image', 'COM_ED_ENABLE_IMAGE_OPTIMIZATION_FOR_ATTACHMENTS'); ?>
					<?php echo $this->html('settings.toggle', 'optimize_cron', 'COM_ED_ENABLE_IMAGE_OPTIMIZATION_DURING_CRON'); ?>
					<?php echo $this->html('settings.textbox', 'optimize_key', 'COM_ED_IMAGE_COMPRESSION_SERVICE_KEY'); ?>
				</div>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_EASYDISCUSS_SETTINGS_IMAGE_ATTACHMENTS'); ?>

			<div class="panel-body">
				<div class="o-form-horizontal">
					<?php echo $this->html('settings.toggle', 'attachment_image_title', 'COM_EASYDISCUSS_IMAGE_ATTACHMENTS_TITLE'); ?>
				</div>
			</div>
		</div>
	</div>
</div>