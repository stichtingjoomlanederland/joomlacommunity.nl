<?php
/**
* @package      Komento
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<div class="row">
	<div class="col-md-6">
		<fieldset class="panel form-horizontal">
			<div class="panel-head"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_ATTACHMENT' ); ?></div>
			<div class="panel-body">
				<!-- Enable Attachment -->
				<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ATTACHMENT_ENABLE', 'upload_enable' ); ?>

				<!-- Custom Path -->
				<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ATTACHMENT_CUSTOM_PATH', 'upload_path', 'input', array( 'size' => 10, 'pretext' => '/media/com_komento/uploads/' ) ); ?>

				<!-- Allowed Extension -->
				<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ATTACHMENT_ALLOWED_EXTENSION', 'upload_allowed_extension', 'input', 50 ); ?>

				<!-- Maximum Files -->
				<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ATTACHMENT_MAX_FILE', 'upload_max_file', 'input', array( 'size' => 2, 'align' => 'center' ) ); ?>

				<!-- Maximum Size -->
				<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ATTACHMENT_MAX_SIZE', 'upload_max_size', 'input', array( 'size' => 2, 'posttext' => 'MegaBytes (MB)', 'align' => 'center' ) ); ?>

				<div class="form-group">
					<div class="col-md-7 col-md-offset-5"><span class="small"><?php echo JText::sprintf( 'COM_KOMENTO_SETTINGS_PHP_MAX_FILESIZE', ini_get( 'upload_max_filesize') ); ?></span></div>
				</div>

				<div class="form-group">
					<div class="col-md-7 col-md-offset-5"><span class="small"><?php echo JText::sprintf( 'COM_KOMENTO_SETTINGS_PHP_MAX_POSTSIZE', ini_get( 'post_max_size') ); ?></span></div>
				</div>

				<!-- Preview for Images -->
				<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ATTACHMENT_SHOW_IMAGE_PREVIEW', 'upload_image_preview' ); ?>

				<!-- Fancybox for Images -->
				<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ATTACHMENT_USE_IMAGE_FANCYBOX', 'upload_image_fancybox' ); ?>

				<!-- Use dark overlay when using Fancybox image -->
				<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ATTACHMENT_USE_FANCYBOX_OVERLAY', 'upload_image_overlay' ); ?>
			</div>
		</fieldset>
	</div>
</div>

