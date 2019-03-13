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
			<?php echo $this->html('panel.head', 'COM_ED_SETTINGS_ATTACHMENTS_GENERAL'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<?php echo $this->html('settings.toggle', 'attachment_questions', 'COM_EASYDISCUSS_ENABLE_FILE_ATTACHMENTS_QUESTIONS'); ?>
					<?php echo $this->html('settings.toggle', 'enable_attachment_limit', 'COM_EASYDISCUSS_FILE_ENABLE_ATTACHMENTS_LIMIT'); ?>

					<div class="form-group <?php echo !$this->config->get('enable_attachment_limit') ? 't-hidden' : ''; ?>" data-ed-attachment-limit>
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_FILE_ATTACHMENTS_LIMIT'); ?>
						</div>
						<div class="col-md-7">
							<input type="text" name="attachment_limit" class="form-control form-control-sm text-center" value="<?php echo $this->config->get('attachment_limit', 0 );?>" />&nbsp;<?php echo JText::_( 'COM_EASYDISCUSS_FILE_ATTACHMENTS_FILES' );?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_FILE_ATTACHMENTS_MAXSIZE'); ?>
						</div>
						<div class="col-md-7">
							<input type="text" name="attachment_maxsize" class="form-control form-control-sm text-center" value="<?php echo $this->config->get('attachment_maxsize' );?>" />&nbsp;<?php echo JText::_( 'COM_EASYDISCUSS_FILE_ATTACHMENTS_MAXSIZE_MEGABYTES' );?>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_STORAGE_PATH'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.textbox', 'storage_path', $this->config->get('storage_path', '/media/com_easydiscuss/' . $this->config->get('attachment_path')));?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_FILE_ATTACHMENTS_ALLOWED_EXTENSION'); ?>
						</div>
						<div class="col-md-7">
							<textarea name="main_attachment_extension" class="form-control" cols="65" rows="5"><?php echo $this->config->get( 'main_attachment_extension' ); ?></textarea>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_IMAGE_ATTACHMENTS'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<?php echo $this->html('settings.toggle', 'attachment_image_title', 'COM_EASYDISCUSS_IMAGE_ATTACHMENTS_TITLE'); ?>
				</div>
			</div>
		</div>
	</div>
</div>