<?php
/**
 * @package   AkeebaBackup
 * @copyright Copyright (c)2006-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

/** @var  $this  \Akeeba\Backup\Admin\View\Backup\Html */

use Akeeba\Engine\Platform;

?>
<?php /* Configuration Wizard pop-up */ ?>
<?php if($this->promptForConfigurationWizard): ?>
	<?php echo $this->loadAnyTemplate('admin:com_akeeba/Configuration/confwiz_modal'); ?>
<?php endif; ?>

<?php /* The Javascript of the page */ ?>
<?php echo $this->loadAnyTemplate('admin:com_akeeba/Backup/script'); ?>

<?php /* Obsolete PHP version warning */ ?>
<?php echo $this->loadAnyTemplate('admin:com_akeeba/ControlPanel/warning_phpversion'); ?>

<?php /* Backup Setup */ ?>
<div id="backup-setup">
	<h3><?php echo \JText::_('COM_AKEEBA_BACKUP_HEADER_STARTNEW'); ?></h3>

	<?php if($this->hasWarnings && !$this->unwriteableOutput): ?>
	<div id="quirks" class="alert <?php echo $this->hasErrors ? 'alert-error' : 'alert-warning'; ?>">
		<h4 class="alert-heading">
			<?php echo \JText::_('COM_AKEEBA_BACKUP_LABEL_DETECTEDQUIRKS'); ?>
		</h4>
		<p>
			<?php echo \JText::_('COM_AKEEBA_BACKUP_LABEL_QUIRKSLIST'); ?>
		</p>
		<?php echo $this->warningsCell; ?>

	</div>
	<?php endif; ?>

	<?php if($this->unwriteableOutput): ?>
	<div id="akeeba-fatal-outputdirectory" class="alert alert-error">
		<p>
			<?php echo \JText::_('COM_AKEEBA_BACKUP_ERROR_UNWRITABLEOUTPUT_' . ($this->autoStart ? 'AUTOBACKUP' : 'NORMALBACKUP')); ?>
		</p>
		<p>
			<?php echo \JText::sprintf(
				'COM_AKEEBA_BACKUP_ERROR_UNWRITABLEOUTPUT_COMMON',
				'index.php?option=com_akeeba&view=Configuration',
				'https://www.akeebabackup.com/warnings/q001.html'
			); ?>
		</p>
	</div>
	<?php endif; ?>

	<form action="index.php" method="post" name="flipForm" id="flipForm"
		  class="well akeeba-formstyle-reset form-inline"
		  autocomplete="off">
		<input type="hidden" name="option" value="com_akeeba"/>
		<input type="hidden" name="view" value="Backup"/>
		<input type="hidden" name="returnurl" value="<?php echo $this->returnURL; ?>"/>
		<input type="hidden" name="description" id="flipDescription" value=""/>
		<input type="hidden" name="comment" id="flipComment" value=""/>
		<input type="hidden" name="<?php echo \JFactory::getSession()->getFormToken(); ?>" value="1"/>

		<label>
			<?php echo \JText::_('COM_AKEEBA_CPANEL_PROFILE_TITLE'); ?>: #<?php echo $this->profileid; ?>

		</label>
		<?php echo \JHtml::_('select.genericlist', $this->profileList, 'profileid', 'onchange="akeeba.Backup.flipProfile();" class="advancedSelect"', 'value', 'text', $this->profileid); ?>
		<button class="btn" onclick="akeeba.Backup.flipProfile(); return false;">
			<span class="icon-refresh"></span>
			<?php echo \JText::_('COM_AKEEBA_CPANEL_PROFILE_BUTTON'); ?>
		</button>
	</form>

	<form id="dummyForm" class="form-horizontal" style="display: <?php echo $this->unwriteableOutput ? 'none' : 'block'; ?>;">
		<div class="control-group">
			<label class="control-label" for="backup-description">
				<?php echo \JText::_('COM_AKEEBA_BACKUP_LABEL_DESCRIPTION'); ?>
			</label>
			<div class="controls">
				<input type="text" name="description" value="<?php echo $this->escape($this->description); ?>"
					maxlength="255" size="80" id="backup-description" class="input-xxlarge" autocomplete="off" />
				<span class="help-block"><?php echo \JText::_('COM_AKEEBA_BACKUP_LABEL_DESCRIPTION_HELP'); ?></span>
			</div>
		</div>

		<?php if($this->showJPSPassword): ?>
		<div class="control-group">
			<label class="control-label" for="jpskey">
				<?php echo \JText::_('COM_AKEEBA_CONFIG_JPS_KEY_TITLE'); ?>
			</label>
			<div class="controls">
				<input type="password" name="jpskey" value="<?php echo $this->escape($this->jpsPassword); ?>" size="50" id="jpskey" autocomplete="off" />
				<span class="help-block"><?php echo \JText::_('COM_AKEEBA_CONFIG_JPS_KEY_DESCRIPTION'); ?></span>
			</div>
		</div>
		<?php endif; ?>

		<?php if(AKEEBA_PRO && $this->showANGIEPassword): ?>
		<div class="control-group">
			<label class="control-label" for="angiekey">
				<?php echo \JText::_('COM_AKEEBA_CONFIG_ANGIE_KEY_TITLE'); ?>
			</label>
			<div class="controls">
				<input type="password" name="angiekey" value="<?php echo $this->escape($this->ANGIEPassword); ?>"  size="50" id="angiekey" autocomplete="off" />
				<span class="help-block"><?php echo \JText::_('COM_AKEEBA_CONFIG_ANGIE_KEY_DESCRIPTION'); ?></span>
			</div>
		</div>
		<?php endif; ?>

		<div class="control-group">
			<label class="control-label" for="comment">
				<?php echo \JText::_('COM_AKEEBA_BACKUP_LABEL_COMMENT'); ?>
			</label>
			<div class="controls">
				<textarea id="comment" rows="5" cols="73" class="input-xxlarge"><?php echo $this->comment; ?></textarea>
				<span class="help-block"><?php echo \JText::_('COM_AKEEBA_BACKUP_LABEL_COMMENT_HELP'); ?></span>
			</div>
		</div>

		<div class="form-actions">
			<button class="btn btn-primary" id="backup-start" onclick="return false;">
				<span class="icon-play icon-white"></span>
				<?php echo \JText::_('COM_AKEEBA_BACKUP_LABEL_START'); ?>
			</button>

            <span class="btn btn-warning" id="backup-default">
                <span class="icon-refresh icon-white"></span>
                <?php echo \JText::_('COM_AKEEBA_BACKUP_LABEL_RESTORE_DEFAULT'); ?>
            </span>
		</div>
	</form>
</div>

<?php /* Warning for having set an ANGIE password */ ?>
<div id="angie-password-warning" class="alert alert-danger alert-error" style="display: none">
    <h1><?php echo \JText::_('COM_AKEEBA_BACKUP_ANGIE_PASSWORD_WARNING_HEADER'); ?></h1>
    <p><?php echo \JText::_('COM_AKEEBA_BACKUP_ANGIE_PASSWORD_WARNING_1'); ?></p>
    <p><?php echo \JText::_('COM_AKEEBA_BACKUP_ANGIE_PASSWORD_WARNING_2'); ?></p>
    <p><?php echo \JText::_('COM_AKEEBA_BACKUP_ANGIE_PASSWORD_WARNING_3'); ?></p>
</div>

<?php /* Backup in progress */ ?>
<div id="backup-progress-pane" style="display: none">
	<div class="alert">
		<span class="icon-warning-circle"></span>
		<?php echo \JText::_('COM_AKEEBA_BACKUP_TEXT_BACKINGUP'); ?>
	</div>

	<h3><?php echo \JText::_('COM_AKEEBA_BACKUP_LABEL_PROGRESS'); ?></h3>

	<div id="backup-progress-content">
		<div id="backup-steps"></div>
		<div id="backup-status" class="well">
			<div id="backup-step"></div>
			<div id="backup-substep"></div>
		</div>
		<div id="backup-percentage" class="progress">
			<div class="bar" style="width: 0"></div>
		</div>
		<div id="response-timer">
			<div class="color-overlay"></div>
			<div class="text"></div>
		</div>
	</div>
	<span id="ajax-worker"></span>
</div>

<?php /* Backup complete */ ?>
<div id="backup-complete" style="display: none">
	<div class="alert alert-success alert-block">
		<h2 class="alert-heading">
			<?php if(empty($this->returnURL)): ?>
			<?php echo \JText::_('COM_AKEEBA_BACKUP_HEADER_BACKUPFINISHED'); ?>
			<?php else: ?>
			<?php echo \JText::_('COM_AKEEBA_BACKUP_HEADER_BACKUPWITHRETURNURLFINISHED'); ?>
			<?php endif; ?>
		</h2>
		<div id="finishedframe">
			<p>
				<?php if(empty($this->returnURL)): ?>
					<?php echo \JText::_('COM_AKEEBA_BACKUP_TEXT_CONGRATS'); ?>
				<?php else: ?>
					<?php echo \JText::_('COM_AKEEBA_BACKUP_TEXT_PLEASEWAITFORREDIRECTION'); ?>
				<?php endif; ?>
			</p>

			<?php if(empty($this->returnURL)): ?>
			<a class="btn btn-primary btn-large" href="<?php echo JUri::base() ?>index.php?option=com_akeeba&view=Manage">
				<span class="icon-stack icon-white"></span>
				<?php echo \JText::_('COM_AKEEBA_BUADMIN'); ?>
			</a>
			<a class="btn" id="ab-viewlog-success" href="<?php echo JUri::base() ?>index.php?option=com_akeeba&view=Log">
				<span class="icon-list"></span>
				<?php echo \JText::_('COM_AKEEBA_LOG'); ?>
			</a>
			<?php endif; ?>
		</div>
	</div>
</div>

<?php /* Backup warnings */ ?>
<div id="backup-warnings-panel" style="display:none">
	<div class="alert">
		<h3 class="alert-heading">
			<?php echo \JText::_('COM_AKEEBA_BACKUP_LABEL_WARNINGS'); ?>
		</h3>
		<div id="warnings-list">
		</div>
	</div>
</div>

<?php /* Backup retry after error */ ?>
<div id="retry-panel" style="display: none">
	<div class="alert alert-warning">
		<h3 class="alert-heading">
			<?php echo \JText::_('COM_AKEEBA_BACKUP_HEADER_BACKUPRETRY'); ?>
		</h3>
		<div id="retryframe">
			<p><?php echo \JText::_('COM_AKEEBA_BACKUP_TEXT_BACKUPFAILEDRETRY'); ?></p>
			<p>
				<strong>
					<?php echo \JText::_('COM_AKEEBA_BACKUP_TEXT_WILLRETRY'); ?>
					<span id="akeeba-retry-timeout">0</span>
					<?php echo \JText::_('COM_AKEEBA_BACKUP_TEXT_WILLRETRYSECONDS'); ?>
				</strong>
				<br/>
				<button class="btn btn-danger btn-small" onclick="akeeba.Backup.cancelResume(); return false;">
					<span class="icon-cancel"></span>
					<?php echo \JText::_('COM_AKEEBA_MULTIDB_GUI_LBL_CANCEL'); ?>
				</button>
				<button class="btn btn-success btn-small" onclick="akeeba.Backup.resumeBackup(); return false;">
					<span class="icon-redo"></span>
					<?php echo \JText::_('COM_AKEEBA_BACKUP_TEXT_BTNRESUME'); ?>
				</button>
			</p>

			<p><?php echo \JText::_('COM_AKEEBA_BACKUP_TEXT_LASTERRORMESSAGEWAS'); ?></p>
			<p id="backup-error-message-retry"></p>
		</div>
	</div>
</div>

<?php /* Backup error (halt) */ ?>
<div id="error-panel" style="display: none">
	<div class="alert alert-error">
		<h3 class="alert-heading">
			<?php echo \JText::_('COM_AKEEBA_BACKUP_HEADER_BACKUPFAILED'); ?>
		</h3>
		<div id="errorframe">
			<p>
				<?php echo \JText::_('COM_AKEEBA_BACKUP_TEXT_BACKUPFAILED'); ?>
			</p>
			<p id="backup-error-message"></p>

			<p>
				<?php echo \JText::_('COM_AKEEBA_BACKUP_TEXT_READLOGFAIL' . (AKEEBA_PRO ? 'PRO' : '')); ?>
			</p>

			<div class="alert alert-block alert-info" id="error-panel-troubleshooting">
				<p>
					<?php if(AKEEBA_PRO): ?>
					<?php echo \JText::_('COM_AKEEBA_BACKUP_TEXT_RTFMTOSOLVEPRO'); ?>
					<?php endif; ?>

					<?php echo \JText::sprintf('COM_AKEEBA_BACKUP_TEXT_RTFMTOSOLVE', 'https://www.akeebabackup.com/documentation/troubleshooter/abbackup.html?utm_source=akeeba_backup&utm_campaign=backuperrorlink'); ?>
				</p>
				<p>
					<?php if(AKEEBA_PRO): ?>
					<?php echo \JText::sprintf('COM_AKEEBA_BACKUP_TEXT_SOLVEISSUE_PRO', 'https://www.akeebabackup.com/support.html?utm_source=akeeba_backup&utm_campaign=backuperrorpro'); ?>
					<?php else: ?>
					<?php echo \JText::sprintf('COM_AKEEBA_BACKUP_TEXT_SOLVEISSUE_CORE', 'https://www.akeebabackup.com/subscribe.html?utm_source=akeeba_backup&utm_campaign=backuperrorcore','https://www.akeebabackup.com/support.html?utm_source=akeeba_backup&utm_campaign=backuperrorcore'); ?>
					<?php endif; ?>

					<?php echo \JText::sprintf('COM_AKEEBA_BACKUP_TEXT_SOLVEISSUE_LOG', 'index.php?option=com_akeeba&view=Log'); ?>
				</p>
			</div>

			<?php if(AKEEBA_PRO): ?>
			<a class="btn btn-large btn-success" id="ab-alice-error" href="index.php?option=com_akeeba&view=Alice">
				<span class="icon-wand icon-white"></span>
				<?php echo \JText::_('COM_AKEEBA_BACKUP_ANALYSELOG'); ?>
			</a>
			<?php endif; ?>

			<button class="btn btn-large btn-primary" onclick="window.location='https://www.akeebabackup.com/documentation/troubleshooter/abbackup.html?utm_source=akeeba_backup&utm_campaign=backuperrorbutton'; return false;">
				<span class="icon-book icon-white"></span>
				<?php echo \JText::_('COM_AKEEBA_BACKUP_TROUBLESHOOTINGDOCS'); ?>
			</button>
			<a class="btn" id="ab-viewlog-error" href="index.php?option=com_akeeba&view=Log">
				<span class="icon-list"></span>
				<?php echo \JText::_('COM_AKEEBA_LOG'); ?>
			</a>
		</div>
	</div>
</div>