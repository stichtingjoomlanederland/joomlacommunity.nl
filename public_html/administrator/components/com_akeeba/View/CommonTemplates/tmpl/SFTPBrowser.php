<?php
/**
 * @package   AkeebaBackup
 * @copyright Copyright (c)2006-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();
?>
<?php /* SFTP browser */ ?>
<div class="modal fade" id="sftpdialog" tabindex="-1" role="dialog" aria-labelledby="sftpdialogLabel" aria-hidden="true" style="display: none;">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="sftpdialogLabel">
					<?php echo \JText::_('COM_AKEEBA_CONFIG_UI_SFTPBROWSER_TITLE'); ?>
				</h4>
			</div>
			<div class="modal-body">
				<p class="instructions alert alert-info">
					<?php echo \JText::_('COM_AKEEBA_SFTPBROWSER_LBL_INSTRUCTIONS'); ?>
				</p>
				<div class="error alert alert-danger" id="sftpBrowserErrorContainer">
					<h2><?php echo \JText::_('COM_AKEEBA_SFTPBROWSER_LBL_ERROR'); ?></h2>

					<p id="sftpBrowserError"></p>
				</div>
				<ul id="ak_scrumbs" class="breadcrumb"></ul>
				<div class="folderBrowserWrapper" id="sftpBrowserWrapper">
					<table id="sftpBrowserFolderList" class="table table-striped">
					</table>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" id="sftpdialogCancelButton" class="btn btn-default" data-dismiss="modal">
					<?php echo \JText::_('JTOOLBAR_CANCEL'); ?>
				</button>
				<button type="button" id="sftpdialogOkButton" class="btn btn-primary">
					<?php echo \JText::_('COM_AKEEBA_BROWSER_LBL_USE'); ?>
				</button>
			</div>
		</div>
	</div>
</div>
