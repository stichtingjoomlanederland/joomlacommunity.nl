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

/** @var  AkeebaViewUpdates  $this */

$class = 'success';
$updateInfoKey = 'UPDATE_LABEL_NOUPGRADESFOUND';

if ($this->updateInfo['hasUpdate'])
{
	$class = 'warning';
	$updateInfoKey = 'An upgrade was found!';
}

?>

<?php if ($this->needsDownloadID): ?>
<div class="alert alert-success">
	<h3>
		<?php echo JText::_('COM_AKEEBA_CPANEL_MSG_MUSTENTERDLID') ?>
	</h3>
	<p>
		<?php echo JText::sprintf('COM_AKEEBA_LBL_CPANEL_NEEDSDLID','https://www.akeebabackup.com/instructions/1435-akeeba-backup-download-id.html'); ?>
	</p>
	<form name="dlidform" action="index.php" method="post" class="form-inline">
		<input type="hidden" name="option" value="com_akeeba" />
		<input type="hidden" name="view" value="cpanel" />
		<input type="hidden" name="task" value="applydlid" />
		<input type="hidden" name="returnurl" value="<?php echo base64_encode(JUri::current()) ?>" />
		<input type="hidden" name="<?php echo JFactory::getSession()->getFormToken()?>" value="1" />
		<span>
			<?php echo JText::_('COM_AKEEBA_CPANEL_MSG_PASTEDLID') ?>
		</span>
		<input type="text" name="dlid" placeholder="<?php echo JText::_('CONFIG_DOWNLOADID_LABEL')?>" class="input-xlarge">
		<button type="submit" class="btn btn-success">
			<span class="icon icon-<?php echo version_compare(JVERSION, '3.0.0', 'ge') ? 'checkbox' : 'ok icon-white' ?>"></span>
			<?php echo JText::_('COM_AKEEBA_CPANEL_MSG_APPLYDLID') ?>
		</button>
	</form>
</div>
<?php return; endif; ?>

<div class="alert alert-<?php echo $class?>">
	<h4>
		<?php echo JText::_($updateInfoKey); ?>
	</h4>
</div>

<form name="adminForm" action="index.php" method="post" class="form form-horizontal">

<input type="hidden" name="option" value="com_akeeba" />
<input type="hidden" name="view" value="update" />
<input type="hidden" name="task" value="startupdate" />
	<input type="hidden" name="<?php echo JFactory::getSession()->getFormToken()?>" value="1" />

<table class="table table-striped" width="100%">
	<thead style="height: 0">
	<tr>
		<th width="20%"></th>
		<th></th>
	</tr>
	</thead>
	<tbody>
	<tr>
		<td>
			<?php echo JText::_('UPDATE_LABEL_YOURVERSION'); ?>
		</td>
		<td>
			<?php echo $this->componentTitle ?>
			<span class="badge badge-<?php echo $class?>">
			<?php echo $this->currentVersion ?>
			</span>
		</td>
	</tr>
	<?php if ($this->updateInfo['hasUpdate']): ?>
	<tr>
		<td>
			<?php echo JText::_('UPDATE_LABEL_LATESTVERSION'); ?>
		</td>
		<td>
			<?php echo $this->componentTitle ?>
			<span class="badge badge-success">
				<?php echo $this->updateInfo['version'] ?>
			</span>

			&nbsp;&nbsp;
			<a href="<?php echo $this->updateInfo['infoURL'] ?>" class="btn btn-info btn-small" target="_blank">
				<span class="icon icon-white icon-info-sign"></span>
				<?php echo JText::_('COM_AKEEBA_CPANEL_MSG_MOREINFO'); ?>
			</a>
			&nbsp;
			<a href="<?php echo $this->updateInfo['downloadURL'] ?>" class="btn btn-small">
				<span class="icon icon-download-alt"></span>
				<?php echo JText::_('STATS_LOG_DOWNLOAD') ?>
			</a>
		</td>
	</tr>
	<?php endif; ?>
	<tr>
		<td>
		</td>
		<td>
			<?php if ($this->updateInfo['hasUpdate'] && $this->needsFTPCredentials): ?>
			<div class="well">
				<p>
					<strong><?php echo JText::_('RESTORE_LABEL_FTPOPTIONS'); ?></strong>
				</p>
				<p>
					<?php echo JText::_('COM_AKEEBA_UPDATE_ERR_FTPINFOMISSING'); ?>
				</p>
				<div class="control-group">
					<label class="control-label" for="username">
						<?php echo JText::_('COM_AKEEBA_TRANSFER_LBL_FTP_USERNAME'); ?>
					</label>
					<div class="controls">
						<input type="text" id="username" placeholder="<?php echo JText::_('COM_AKEEBA_TRANSFER_LBL_FTP_USERNAME'); ?>">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="password">
						<?php echo JText::_('COM_AKEEBA_TRANSFER_LBL_FTP_PASSWORD'); ?>
					</label>
					<div class="controls">
						<input type="passsword" id="passsword" placeholder="<?php echo JText::_('COM_AKEEBA_TRANSFER_LBL_FTP_PASSWORD'); ?>">
					</div>
				</div>
			</div>
			<?php endif; ?>

			<p>
				<button type="submit" class="btn btn-success btn-large">
					<span class="icon icon-white icon-chevron-right"></span>
					<?php echo JText::_('UPDATE_LABEL_UPDATENOW'); ?>
				</button>
			</p>

			<p>
				<a href="index.php?option=com_akeeba&view=update&task=force&update=1" class="btn btn-inverse btn-mini">
					<span class="icon icon-white icon-retweet"></span>
					<?php echo JText::_('COM_AKEEBA_CPANEL_MSG_RELOADUPDATE'); ?>
				</a>
			</p>
		</td>
	</tr>
	</tbody>
</table>

</form>