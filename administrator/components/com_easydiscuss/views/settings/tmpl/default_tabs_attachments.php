<?php
/**
* @package		EasyDiscuss
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
<div class="row-fluid">
	<div class="span12 panel-title">
		<h2><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_ATTACHMENTS_TITLE' );?></h2>
		<p style="margin: 0 0 15px;">
			<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_ATTACHMENTS_DESC' );?>
		</p>
	</div>
</div>

<div class="row-fluid ">
	<div class="span6">
		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option01">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_ATTACHMENTS' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>

			<div id="option01" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_FILE_ATTACHMENTS_QUESTIONS' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_FILE_ATTACHMENTS_QUESTIONS' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ENABLE_FILE_ATTACHMENTS_QUESTIONS_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'attachment_questions' , $this->config->get( 'attachment_questions' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_FILE_ENABLE_ATTACHMENTS_LIMIT' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_FILE_ENABLE_ATTACHMENTS_LIMIT' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_FILE_ENABLE_ATTACHMENTS_LIMIT_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'enable_attachment_limit' , $this->config->get( 'enable_attachment_limit', 0 ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_FILE_ATTACHMENTS_LIMIT' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_FILE_ATTACHMENTS_LIMIT' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_FILE_ATTACHMENTS_LIMIT_DESC'); ?>"
						>
							<input type="text" name="attachment_limit" class="text-center input-small" value="<?php echo $this->config->get('attachment_limit', 0 );?>" />&nbsp;<?php echo JText::_( 'COM_EASYDISCUSS_FILE_ATTACHMENTS_FILES' );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_FILE_ATTACHMENTS_MAXSIZE' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_FILE_ATTACHMENTS_MAXSIZE' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_FILE_ATTACHMENTS_MAXSIZE_DESC'); ?>"
						>
							<input type="text" name="attachment_maxsize"  class="input-small text-center" value="<?php echo $this->config->get('attachment_maxsize' );?>" />&nbsp;<?php echo JText::_( 'COM_EASYDISCUSS_FILE_ATTACHMENTS_MAXSIZE_MEGABYTES' );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_FILE_ATTACHMENTS_PATH' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_FILE_ATTACHMENTS_PATH' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_FILE_ATTACHMENTS_PATH_DESC'); ?>"
						>
							<?php echo JText::_( 'COM_EASYDISCUSS_FILE_ATTACHMENTS_PATH_INFO' );?><input type="text" name="attachment_path"  style="width: 100px;" value="<?php echo $this->config->get('attachment_path' );?>" />
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_FILE_ATTACHMENTS_ALLOWED_EXTENSION' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_FILE_ATTACHMENTS_ALLOWED_EXTENSION' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_FILE_ATTACHMENTS_ALLOWED_EXTENSION_DESC'); ?>"
						>
							<textarea name="main_attachment_extension" class="input-full" cols="65" rows="5"><?php echo $this->config->get( 'main_attachment_extension' ); ?></textarea>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="span6">
		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option01">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_IMAGE_ATTACHMENTS' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>

			<div id="option01" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_IMAGE_ATTACHMENTS_TITLE' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_IMAGE_ATTACHMENTS_TITLE' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_IMAGE_ATTACHMENTS_TITLE_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'attachment_image_title' , $this->config->get( 'attachment_image_title' ) );?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


