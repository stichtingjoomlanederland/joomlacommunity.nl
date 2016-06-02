<?php
/**
 * @package Akeeba
 * @copyright Copyright (c)2009-2016 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 * @since 3.0
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

?>
<div class="akeeba-bootstrap" id="ftpdialog" title="<?php echo JText::_('COM_AKEEBA_CONFIG_UI_FTPBROWSER_TITLE') ?>" style="display:none;">
	<p class="instructions alert alert-info">
		<?php echo JText::_('COM_AKEEBA_FTPBROWSER_LBL_INSTRUCTIONS'); ?>
	</p>
	<div class="error alert alert-error" id="ftpBrowserErrorContainer">
		<h2><?php echo JText::_('COM_AKEEBA_FTPBROWSER_LBL_ERROR'); ?></h2>
		<p id="ftpBrowserError"></p>
	</div>
	<ul id="ak_crumbs" class="breadcrumb"></ul>
	<div class="row-fluid">
		<div class="span12">
			<table id="ftpBrowserFolderList" class="table table-striped">
			</table>
		</div>
	</div>
</div>

<div class="akeeba-bootstrap" id="sftpdialog" title="<?php echo JText::_('COM_AKEEBA_CONFIG_UI_SFTPBROWSER_TITLE') ?>" style="display:none;">
	<p class="instructions alert alert-info">
		<?php echo JText::_('COM_AKEEBA_SFTPBROWSER_LBL_INSTRUCTIONS'); ?>
	</p>
	<div class="error alert alert-error" id="sftpBrowserErrorContainer">
		<h2><?php echo JText::_('COM_AKEEBA_SFTPBROWSER_LBL_ERROR'); ?></h2>
		<p id="sftpBrowserError"></p>
	</div>
	<ul id="ak_scrumbs" class="breadcrumb"></ul>
	<div class="row-fluid">
		<div class="span12">
			<table id="sftpBrowserFolderList" class="table table-striped">
			</table>
		</div>
	</div>
</div>

<div class="akeeba-bootstrap" id="testFtpDialog" style="display:none;">
	<h4 id="testFtpDialogLabel"></h4>
	<div id="testFtpDialogBody">
		<div class="alert alert-success" id="testFtpDialogBodyOk"></div>
		<div class="alert alert-danger" id="testFtpDialogBodyFail"></div>
	</div>
</div>
