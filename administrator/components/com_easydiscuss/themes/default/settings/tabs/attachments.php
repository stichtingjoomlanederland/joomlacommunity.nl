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
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_ATTACHMENTS'); ?>

			<div id="option01" class="panel-body">
				<div class="form-horizontal">
					<div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_FILE_ATTACHMENTS_QUESTIONS'); ?>
                        </div>
                        <div class="col-md-7">
							<?php echo $this->html('form.boolean', ' attachment_questions', $this->config->get('attachment_questions')); ?>
						</div>
					</div>
					<div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_FILE_ENABLE_ATTACHMENTS_LIMIT'); ?>
                        </div>
                        <div class="col-md-7">
							<?php echo $this->html('form.boolean', 'enable_attachment_limit', $this->config->get('enable_attachment_limit'));?>
						</div>
					</div>
					<div class="form-group">
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
							<input type="text" name="attachment_maxsize"  class="form-control form-control-sm text-center" value="<?php echo $this->config->get('attachment_maxsize' );?>" />&nbsp;<?php echo JText::_( 'COM_EASYDISCUSS_FILE_ATTACHMENTS_MAXSIZE_MEGABYTES' );?>
						</div>
					</div>
					<div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_FILE_ATTACHMENTS_PATH'); ?>
                        </div>
                        <div class="col-md-7">
							<?php echo JText::_( 'COM_EASYDISCUSS_FILE_ATTACHMENTS_PATH_INFO' );?><input type="text" name="attachment_path" class="form-control"  style="width: 100px; display: inline; margin: 0 5px;" value="<?php echo $this->config->get('attachment_path' );?>" />
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

			<div id="option01" class="panel-body">
				<div class="form-horizontal">
					<div class="form-group">
                        <div class="col-md-5 control-label">
                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_IMAGE_ATTACHMENTS_TITLE'); ?>
                        </div>
                        <div class="col-md-7">
							<?php echo $this->html('form.boolean', 'attachment_image_title', $this->config->get('attachment_image_title')); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>