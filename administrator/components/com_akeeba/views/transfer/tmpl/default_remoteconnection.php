<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2009-2016 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 *
 * @since 3.3
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

/** @var  $this  AkeebaViewTransfer */

$js = <<< JS
akeeba.jQuery(document).ready(function(){
	akeeba.jQuery('#akeeba-transfer-ftp-method').change(akeeba.Transfer.onTransferMethodChange);
	akeeba.jQuery('#akeeba-transfer-ftp-directory-browse').click(akeeba.Transfer.initFtpSftpBrowser);
	akeeba.jQuery('#akeeba-transfer-btn-apply').click(akeeba.Transfer.applyConnection);
	akeeba.jQuery('#akeeba-transfer-err-url-notexists-btn-ignore').click(akeeba.Transfer.showConnectionDetails);
});
JS;

JFactory::getDocument()->addScriptDeclaration($js);

?>

<fieldset>
	<legend>
		<?php echo JText::_('COM_AKEEBA_TRANSFER_HEAD_REMOTECONNECTION'); ?>
	</legend>

	<div class="form form-horizontal">
		<div class="control-group">
			<label class="control-label" for="akeeba-transfer-url">
				<?php echo JText::_('COM_AKEEBA_TRANSFER_LBL_NEWURL'); ?>
			</label>

			<div class="controls" id="akeeba-transfer-row-url">
				<input type="text" class="input-large" id="akeeba-transfer-url" placeholder="http://www.example.com"
					   value="<?php echo htmlentities($this->newSiteUrl) ?>">
				<button onclick="akeeba.Transfer.onUrlChange(true);" class="btn btn-inverse" id="akeeba-transfer-btn-url">
					<?php echo JText::_('COM_AKEEBA_TRANSFER_ERR_NEWURL_BTN') ?>
				</button>

				<img src="<?php echo JUri::base() ?>../media/com_akeeba/icons/loading.gif" id="akeeba-transfer-loading" style="display: none;" />

				<br/>

				<div id="akeeba-transfer-lbl-url">
					<small>
						<?php echo JText::_('COM_AKEEBA_TRANSFER_LBL_NEWURL_TIP'); ?>
					</small>
				</div>
				<div id="akeeba-transfer-err-url-same" class="alert alert-info" style="display: none;">
					<?php echo JText::_('COM_AKEEBA_TRANSFER_ERR_NEWURL_SAME'); ?>
					<p style="text-align: center">
						<iframe width="560" height="315" src="https://www.youtube.com/embed/vo_r0r6cZNQ" frameborder="0" allowfullscreen></iframe>
					</p>
				</div>
				<div id="akeeba-transfer-err-url-invalid" class="alert alert-danger" style="display: none;">
					<?php echo JText::_('COM_AKEEBA_TRANSFER_ERR_NEWURL_INVALID'); ?>
				</div>
				<div id="akeeba-transfer-err-url-notexists" class="alert alert-danger" style="display: none;">
					<p>
						<?php echo JText::_('COM_AKEEBA_TRANSFER_ERR_NEWURL_NOTEXISTS'); ?>
					</p>
					<p>
						<button type="button" class="btn btn-danger" id="akeeba-transfer-err-url-notexists-btn-ignore">
							&#9888;
							<?php echo JText::_('COM_AKEEBA_TRANSFER_ERR_NEWURL_BTN_IGNOREERROR') ?>
						</button>
					</p>
				</div>
			</div>
		</div>
	</div>

	<div class="form form-horizontal" id="akeeba-transfer-ftp-container" style="display: none">
		<div class="control-group">
			<label for="akeeba-transfer-ftp-method" class="control-label">
				<?php echo JText::_('COM_AKEEBA_TRANSFER_LBL_TRANSFERMETHOD'); ?>
			</label>
			<div class="controls">
				<?php echo JHtml::_('select.genericlist', $this->transferOptions, 'akeeba-transfer-ftp-method', array(), 'value', 'text', $this->transferOption, 'akeeba-transfer-ftp-method') ?>
				<?php if ($this->hasFirewalledMethods): ?>
					<div class="help-block">
						<div class="alert alert-warning">
							<h4>
								<?php echo JText::_('COM_AKEEBA_TRANSFER_WARN_FIREWALLED_HEAD'); ?>
							</h4>
							<p>
								<?php echo JText::_('COM_AKEEBA_TRANSFER_WARN_FIREWALLED_BODY'); ?>
							</p>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<div class="control-group">
			<label for="akeeba-transfer-ftp-host" class="control-label">
				<?php echo JText::_('COM_AKEEBA_TRANSFER_LBL_FTP_HOST'); ?>
			</label>
			<div class="controls">
				<input type="text" class="input-large" value="<?php echo $this->ftpHost ?>" id="akeeba-transfer-ftp-host"
					   placeholder="ftp.example.com"/>
			</div>
		</div>

		<div class="control-group">
			<label for="akeeba-transfer-ftp-port" class="control-label">
				<?php echo JText::_('COM_AKEEBA_TRANSFER_LBL_FTP_PORT'); ?>
			</label>
			<div class="controls">
				<input type="text" class="input-large" value="<?php echo $this->ftpPort ?>" id="akeeba-transfer-ftp-port"
					   placeholder="21"/>
			</div>
		</div>

		<div class="control-group">
			<label for="akeeba-transfer-ftp-username" class="control-label">
				<?php echo JText::_('COM_AKEEBA_TRANSFER_LBL_FTP_USERNAME'); ?>
			</label>
			<div class="controls">
				<input type="text" class="input-large" value="<?php echo $this->ftpUsername ?>" id="akeeba-transfer-ftp-username"
					   placeholder="myUserName"/>
			</div>
		</div>

		<div class="control-group">
			<label for="akeeba-transfer-ftp-password" class="control-label">
				<?php echo JText::_('COM_AKEEBA_TRANSFER_LBL_FTP_PASSWORD'); ?>
			</label>
			<div class="controls">
				<input type="password" class="input-large" value="<?php echo $this->ftpPassword ?>" id="akeeba-transfer-ftp-password"
					   placeholder="myPassword"/>
			</div>
		</div>

		<div class="control-group">
			<label for="akeeba-transfer-ftp-pubkey" class="control-label">
				<?php echo JText::_('COM_AKEEBA_TRANSFER_LBL_FTP_PUBKEY'); ?>
			</label>
			<div class="controls">
				<input type="text" class="input-xlarge" value="<?php echo $this->ftpPubKey ?>" id="akeeba-transfer-ftp-pubkey"
					   placeholder="<?php echo JPATH_SITE . DIRECTORY_SEPARATOR ?>id_rsa.pub"/>
			</div>
		</div>

		<div class="control-group">
			<label for="akeeba-transfer-ftp-privatekey" class="control-label">
				<?php echo JText::_('COM_AKEEBA_TRANSFER_LBL_FTP_PRIVATEKEY'); ?>
			</label>
			<div class="controls">
				<input type="text" class="input-xlarge" value="<?php echo $this->ftpPrivateKey ?>" id="akeeba-transfer-ftp-privatekey"
					   placeholder="<?php echo JPATH_SITE . DIRECTORY_SEPARATOR ?>id_rsa"/>
			</div>
		</div>

		<div class="control-group">
			<label for="akeeba-transfer-ftp-directory" class="control-label">
				<?php echo JText::_('COM_AKEEBA_TRANSFER_LBL_FTP_DIRECTORY'); ?>
			</label>
			<div class="controls">
				<div class="input-append">
					<input type="text" class="input-large" value="<?php echo $this->ftpDirectory ?>" id="akeeba-transfer-ftp-directory"
						   placeholder="public_html"/>
					<!--
					<button class="btn" type="button" id="akeeba-transfer-ftp-directory-browse">
						<?php echo JText::_('CONFIG_UI_BROWSE'); ?>
					</button>
					<button class="btn" type="button" id="akeeba-transfer-ftp-directory-detect">
						<?php echo JText::_('COM_AKEEBA_TRANSFER_BTN_FTP_DETECT'); ?>
					</button>
					-->
				</div>
			</div>
		</div>

		<div class="control-group" id="akeeba-transfer-ftp-passive-container">
			<label for="akeeba-transfer-ftp-passive" class="control-label">
				<?php echo JText::_('COM_AKEEBA_TRANSFER_LBL_FTP_PASSIVE'); ?>
			</label>
			<div class="controls">
				<?php echo JHtml::_('select.booleanlist', 'akeeba-transfer-ftp-passive', array(), $this->ftpPassive ? 1 : 0, 'JYES', 'JNO', 'akeeba-transfer-ftp-passive') ?>
			</div>
		</div>

		<div class="alert alert-error" id="akeeba-transfer-ftp-error" style="display:none;">
			<h3 id="akeeba-transfer-ftp-error-title">TITLE</h3>
			<p id="akeeba-transfer-ftp-error-body">MESSAGE</p>
		</div>

		<div class="form-actions">
			<button type="button" class="btn btn-primary" id="akeeba-transfer-btn-apply">
				<?php echo JText::_('COM_AKEEBA_TRANSFER_BTN_FTP_PROCEED'); ?>
			</button>

			<span id="akeeba-transfer-apply-loading" style="display: none;">
				&nbsp;
				<span class="label label-info">
					<?php echo JText::_('COM_AKEEBA_TRANSFER_LBL_VALIDATING'); ?>
				</span>
				&nbsp;
				<img src="<?php echo JUri::base() ?>../media/com_akeeba/icons/loading.gif" />
			</span>
		</div>

	</div>

</fieldset>