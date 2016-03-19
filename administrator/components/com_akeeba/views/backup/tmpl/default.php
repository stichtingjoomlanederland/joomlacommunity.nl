<?php
/**
 * @package AkeebaBackup
 * @copyright Copyright (c)2009-2016 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 *
 * @since 1.3
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;

// Apply error container chrome if there are errors detected
$quirks_style = $this->haserrors ? 'alert-error' : "";
$formstyle = '';

$configuration = Factory::getConfiguration();

JHtml::_('formbehavior.chosen');
?>
<?php
// Configuration Wizard prompt
if (!\Akeeba\Engine\Factory::getConfiguration()->get('akeeba.flag.confwiz', 0))
{
	echo $this->loadAnyTemplate('admin:com_akeeba/config/confwiz_modal');
}
?>

<script type="text/javascript" language="javascript">
// Initialization
akeeba.Backup.default_descr = "<?php echo $this->default_descr ?>";
akeeba.Backup.config_angiekey = "<?php echo $this->angiekey ?>";
akeeba.Backup.jpsKey = "<?php echo $this->showjpskey ? $this->jpskey : '' ?>";

// Auto-resume setup
akeeba.Backup.resume.enabled = <?php echo (int)$configuration->get('akeeba.advanced.autoresume', 1); ?>;
akeeba.Backup.resume.timeout = <?php echo (int)$configuration->get('akeeba.advanced.autoresume_timeout', 10); ?>;
akeeba.Backup.resume.maxRetries = <?php echo (int)$configuration->get('akeeba.advanced.autoresume_maxretries', 3); ?>;

akeeba.jQuery(document).ready(function($){
	// The return URL
    akeeba.Backup.returnUrl = '<?php echo AkeebaHelperEscape::escapeJS($this->returnurl) ?>';

	// Used as parameters to start_timeout_bar()
    akeeba.Backup.maxExecutionTime = <?php echo $this->maxexec; ?>;
    akeeba.Backup.runtimeBias = <?php echo $this->bias; ?>;

	// Create a function for saving the editor's contents
    akeeba.Backup.commentEditorSave = function() {
	};

    akeeba.System.notification.iconURL = '<?php echo JUri::base() . '../media/com_akeeba/icons/logo-48.png' ?>';

	// Push some translations
	akeeba.Backup.translations['UI-LASTRESPONSE'] = '<?php echo AkeebaHelperEscape::escapeJS(JText::_('BACKUP_TEXT_LASTRESPONSE')) ?>';
    akeeba.Backup.translations['UI-BACKUPSTARTED'] = '<?php echo AkeebaHelperEscape::escapeJS(JText::_('COM_AKEEBA_BACKUP_TEXT_BACKUPSTARTED')) ?>';
    akeeba.Backup.translations['UI-BACKUPFINISHED'] = '<?php echo AkeebaHelperEscape::escapeJS(JText::_('COM_AKEEBA_BACKUP_TEXT_BACKUPFINISHED')) ?>';
    akeeba.Backup.translations['UI-BACKUPHALT'] = '<?php echo AkeebaHelperEscape::escapeJS(JText::_('COM_AKEEBA_BACKUP_TEXT_BACKUPHALT')) ?>';
    akeeba.Backup.translations['UI-BACKUPRESUME'] = '<?php echo AkeebaHelperEscape::escapeJS(JText::_('COM_AKEEBA_BACKUP_TEXT_BACKUPRESUME')) ?>';
    akeeba.Backup.translations['UI-BACKUPHALT_DESC'] = '<?php echo AkeebaHelperEscape::escapeJS(JText::_('COM_AKEEBA_BACKUP_TEXT_BACKUPHALT_DESC')) ?>';
    akeeba.Backup.translations['UI-BACKUPFAILED'] = '<?php echo AkeebaHelperEscape::escapeJS(JText::_('COM_AKEEBA_BACKUP_TEXT_BACKUPFAILED')) ?>';
    akeeba.Backup.translations['UI-BACKUPWARNING'] = '<?php echo AkeebaHelperEscape::escapeJS(JText::_('COM_AKEEBA_BACKUP_TEXT_BACKUPWARNING')) ?>';

	//Parse the domain keys
    akeeba.Backup.domains = JSON.parse("<?php echo $this->domains ?>");

	// Setup AJAX proxy URL
    akeeba.System.params.AjaxURL = 'index.php?option=com_akeeba&view=backup&task=ajax';

	// Setup base View Log URL
    akeeba.Backup.URLs.LogURL = '<?php echo JUri::base() ?>index.php?option=com_akeeba&view=log';
    akeeba.Backup.URLs.AliceURL = '<?php echo JUri::base() ?>index.php?option=com_akeeba&view=alices';

	// Setup the IFRAME mode
    akeeba.System.params.useIFrame = <?php echo $this->useiframe ?>;

	if (<?php echo $this->desktop_notifications; ?>)
	{
        akeeba.System.notification.askPermission();
	}

	<?php if( !$this->unwritableoutput && $this->autostart ):?>
    akeeba.Backup.start();
	<?php else: ?>
	// Bind start button's click event
	$('#backup-start').bind("click", function(e){
        akeeba.Backup.start();
	});

    $('#backup-default').click(akeeba.Backup.restoreDefaultOptions);

	// Work around Safari which ignores autocomplete=off (FOR CRYING OUT LOUD!)
	setTimeout('akeeba.Backup.restoreDefaultOptions();', 500);
	<?php endif; ?>
});
</script>

<?php if(!version_compare(PHP_VERSION, '5.4.0', 'ge') && \Akeeba\Engine\Util\Comconfig::getValue('displayphpwarning', 1)): ?>
<div class="alert">
	<a class="close" data-dismiss="alert" href="#">×</a>
	<p><strong><?php echo JText::_('COM_AKEEBA_CONFIG_LBL_OUTDATEDPHP_HEADER') ?></strong><br/>
	<?php echo JText::sprintf('COM_AKEEBA_CONFIG_LBL_OUTDATEDPHP_BODY', PHP_VERSION) ?>
	</p>

	<?php
	if(function_exists('base64_encode')) {
		$returnurl = '&returnurl=' . base64_encode(JUri::getInstance()->toString());
	} else {
		$returnurl = '';
	}
	?>
	<p>
		<a class="btn btn-small btn-primary" href="index.php?option=com_akeeba&view=cpanel&task=disablephpwarning&<?php echo JFactory::getSession()->getFormToken() ?>=1<?php echo $returnurl ?>">
			<?php echo JText::_('COM_AKEEBA_CONFIG_LBL_OUTDATEDPHP_BUTTON'); ?>
		</a>
	</p>
</div>
<?php endif; ?>

<div id="backup-setup">
	<h3><?php echo JText::_('BACKUP_HEADER_STARTNEW') ?></h3>

	<script type="text/javascript" language="javascript">
	function flipProfile()
	{
		(function($) {
			// Save the description and comments
			$('#flipDescription').val(  $('#backup-description').val() );
			$('#flipComment').val( $('#comment').val() );
			document.forms.flipForm.submit();
		})(akeeba.jQuery);
	}
	</script>

	<?php if ($this->hasquirks && !$this->unwritableoutput): ?>
	<div id="quirks" class="alert <?php echo $quirks_style ?>">
		<h4 class="alert-heading"><?php echo JText::_('BACKUP_LABEL_DETECTEDQUIRKS') ?></h4>
		<p><?php echo JText::_('BACKUP_LABEL_QUIRKSLIST') ?></p>
		<?php echo $this->quirks; ?>
	</div>
	<?php endif; ?>

	<?php if($this->unwritableoutput): $formstyle="style=\"display: none;\"" ?>
	<div id="akeeba-fatal-outputdirectory" class="alert alert-error">

	<?php if($this->autostart): ?>
	<p>
		<?php echo JText::_('BACKUP_ERROR_UNWRITABLEOUTPUT_AUTOBACKUP') ?>
	</p>
	<?php else: ?>
	<p>
		<?php echo JText::_('BACKUP_ERROR_UNWRITABLEOUTPUT_NORMALBACKUP') ?>
	</p>
	<?php endif; ?>
	<p>
		<?php echo JText::sprintf(
			'BACKUP_ERROR_UNWRITABLEOUTPUT_COMMON',
			'index.php?option=com_akeeba&view=config',
			'https://www.akeebabackup.com/warnings/q001.html'
		) ?>
	</p>
	</div>
	<?php endif; ?>

	<?php $row = 1 ?>

	<?php if(!$this->unwritableoutput):?>

	<form action="index.php" method="post" name="flipForm" id="flipForm" class="well akeeba-formstyle-reset form-inline" autocomplete="off">
		<input type="hidden" name="option" value="com_akeeba" />
		<input type="hidden" name="view" value="backup" />
		<input type="hidden" name="returnurl" value="<?php htmlentities($this->returnurl, ENT_COMPAT, 'UTF-8', false) ?>" />
		<input type="hidden" name="description" id="flipDescription" value="" />
		<input type="hidden" name="comment" id="flipComment" value="" />
		<input type="hidden" name="<?php echo JFactory::getSession()->getFormToken()?>" value="1" />

		<label>
			<?php echo JText::_('CPANEL_PROFILE_TITLE'); ?>: #<?php echo $this->profileid; ?>
		</label>
		<?php echo JHTML::_('select.genericlist', $this->profilelist, 'profileid', 'onchange="flipProfile();" class="advancedSelect"', 'value', 'text', $this->profileid); ?>
		<button class="btn" onclick="flipProfile(); return false;">
			<i class="icon-retweet"></i>
			<?php echo JText::_('CPANEL_PROFILE_BUTTON'); ?>
		</button>
	</form>

	<?php endif; ?>

	<form id="dummyForm" <?php echo $formstyle ?> class="form-horizontal">
		<div class="control-group">
			<label class="control-label" for="description">
				<?php echo JText::_('BACKUP_LABEL_DESCRIPTION'); ?>
			</label>
			<div class="controls">
				<input type="text" name="description" value="<?php echo $this->description; ?>"
					maxlength="255" size="80" id="backup-description" class="input-xxlarge" autocomplete="off" />
				<span class="help-block"><?php echo JText::_('BACKUP_LABEL_DESCRIPTION_HELP'); ?></span>
			</div>
		</div>
		<?php if($this->showjpskey): ?>
		<div class="control-group">
			<label class="control-label" for="jpskey">
				<?php echo JText::_('CONFIG_JPS_KEY_TITLE'); ?>
			</label>
			<div class="controls">
				<input type="password" name="jpskey" value="<?php echo htmlentities($this->jpskey, ENT_COMPAT, 'UTF-8', false) ?>"
				size="50" id="jpskey" autocomplete="off" />
				<span class="help-block"><?php echo JText::_('CONFIG_JPS_KEY_DESCRIPTION'); ?></span>
			</div>
		</div>
		<?php endif; ?>
		<?php if(AKEEBA_PRO && $this->showangiekey): ?>
		<div class="control-group">
			<label class="control-label" for="angiekey">
				<?php echo JText::_('CONFIG_ANGIE_KEY_TITLE'); ?>
			</label>
			<div class="controls">
				<input type="password" name="angiekey" value="<?php echo htmlentities($this->angiekey, ENT_COMPAT, 'UTF-8', false) ?>"
				size="50" id="angiekey" autocomplete="off" />
				<span class="help-block"><?php echo JText::_('CONFIG_ANGIE_KEY_DESCRIPTION'); ?></span>
			</div>
		</div>
		<?php endif; ?>
		<div class="control-group">
			<label class="control-label" for="comment">
				<?php echo JText::_('BACKUP_LABEL_COMMENT'); ?>
			</label>
			<div class="controls">
				<textarea id="comment" rows="5" cols="73" class="input-xxlarge" autocomplete="off"><?php echo $this->comment ?></textarea>
				<span class="help-block"><?php echo JText::_('BACKUP_LABEL_COMMENT_HELP'); ?></span>
			</div>
		</div>
		<div class="form-actions">
			<button class="btn btn-primary" id="backup-start" onclick="return false;">
				<i class="icon-road icon-white"></i>
				<?php echo JText::_('BACKUP_LABEL_START') ?>
			</button>

            <span class="btn btn-warning" id="backup-default">
                <i class="icon-refresh icon-white"></i>
                <?php echo JText::_('BACKUP_LABEL_RESTORE_DEFAULT')?>
            </span>
		</div>
	</form>
</div>

<div id="angie-password-warning" class="alert alert-danger alert-error" style="display: none">
    <h1><?php echo JText::_('BACKUP_ANGIE_PASSWORD_WARNING_HEADER')?></h1>

    <p><?php echo JText::_('BACKUP_ANGIE_PASSWORD_WARNING_1')?></p>
    <p><?php echo JText::_('BACKUP_ANGIE_PASSWORD_WARNING_2')?></p>
    <p><?php echo JText::_('BACKUP_ANGIE_PASSWORD_WARNING_3')?></p>
</div>

<div id="backup-progress-pane" style="display: none">
	<div class="alert">
		<i class="icon-warning-sign"></i>
		<?php echo JText::_('BACKUP_TEXT_BACKINGUP'); ?>
	</div>
	<fieldset>
		<legend><?php echo JText::_('BACKUP_LABEL_PROGRESS') ?></legend>
		<div id="backup-progress-content">
			<div id="backup-steps">
			</div>
			<div id="backup-status" class="well">
				<div id="backup-step"></div>
				<div id="backup-substep"></div>
			</div>
			<div id="backup-percentage" class="progress">
				<div class="bar" style="width: 0%"></div>
			</div>
			<div id="response-timer">
				<div class="color-overlay"></div>
				<div class="text"></div>
			</div>
		</div>
		<span id="ajax-worker"></span>
	</fieldset>
</div>

<div id="backup-complete" style="display: none">
	<div class="alert alert-success alert-block">
		<h2 class="alert-heading"><?php echo JText::_(empty($this->returnurl) ? 'BACKUP_HEADER_BACKUPFINISHED' : 'BACKUP_HEADER_BACKUPWITHRETURNURLFINISHED'); ?></h2>
		<div id="finishedframe">
			<p>
				<?php if(empty($this->returnurl)): ?>
				<?php echo JText::_('BACKUP_TEXT_CONGRATS') ?>
				<?php else: ?>
				<?php echo JText::_('BACKUP_TEXT_PLEASEWAITFORREDIRECTION') ?>
				<?php endif; ?>
			</p>

			<?php if(empty($this->returnurl)): ?>
			<a class="btn btn-primary btn-large" href="<?php echo JUri::base() ?>index.php?option=com_akeeba&view=buadmin">
				<i class="icon-inbox icon-white"></i>
				<?php echo JText::_('BUADMIN'); ?>
			</a>
			<a class="btn" id="ab-viewlog-success" href="<?php echo JUri::base() ?>index.php?option=com_akeeba&view=log">
				<i class="icon-list-alt"></i>
				<?php echo JText::_('VIEWLOG'); ?>
			</a>
			<?php endif; ?>
		</div>
	</div>


</div>

<div id="backup-warnings-panel" style="display:none">
	<div class="alert">
		<h3 class="alert-heading"><?php echo JText::_('BACKUP_LABEL_WARNINGS') ?></h3>
		<div id="warnings-list">
		</div>
	</div>
</div>

<div id="retry-panel" style="display: none">
	<div class="alert alert-warning">
		<h3 class="alert-heading"><?php echo JText::_('BACKUP_HEADER_BACKUPRETRY'); ?></h3>
		<div id="retryframe">
			<p><?php echo JText::_('BACKUP_TEXT_BACKUPFAILEDRETRY') ?></p>
			<p>
				<strong>
					<?php echo JText::_('BACKUP_TEXT_WILLRETRY') ?>
					<span id="akeeba-retry-timeout">0</span>
					<?php echo JText::_('BACKUP_TEXT_WILLRETRYSECONDS') ?>
				</strong>
				<br/>
				<button class="btn btn-danger btn-small" onclick="akeeba.Backup.cancelResume(); return false;">
					<span class="icon-cancel"></span>
					<?php echo JText::_('UI-MULTIDB-CANCEL'); ?>
				</button>
				<button class="btn btn-success btn-small" onclick="akeeba.Backup.resumeBackup(); return false;">
					<span class="icon-ok-circle"></span>
					<?php echo JText::_('BACKUP_TEXT_BTNRESUME'); ?>
				</button>
			</p>

			<p><?php echo JText::_('BACKUP_TEXT_LASTERRORMESSAGEWAS') ?></p>
			<p id="backup-error-message-retry">
			</p>
		</div>
	</div>

</div>

<div id="error-panel" style="display: none">
	<div class="alert alert-error">
		<h3 class="alert-heading"><?php echo JText::_('BACKUP_HEADER_BACKUPFAILED'); ?></h3>
		<div id="errorframe">
			<p><?php echo JText::_('BACKUP_TEXT_BACKUPFAILED') ?></p>
			<p id="backup-error-message">
			</p>

			<?php if(AKEEBA_PRO):?>
			<p>
				<?php echo JText::_('BACKUP_TEXT_READLOGFAILPRO') ?>
			</p>
			<?php else: ?>
			<p>
				<?php echo JText::_('BACKUP_TEXT_READLOGFAIL') ?>
			</p>
			<?php endif; ?>

			<div class="alert alert-block alert-info">
				<p>
					<?php if(AKEEBA_PRO):?>
					<?php echo JText::_('BACKUP_TEXT_RTFMTOSOLVEPRO') ?>
					<?php endif; ?>
					<?php echo JText::sprintf('BACKUP_TEXT_RTFMTOSOLVE', 'https://www.akeebabackup.com/documentation/troubleshooter/abbackup.html?utm_source=akeeba_backup&utm_campaign=backuperrorlink') ?>
				</p>
				<p>
					<?php if(AKEEBA_PRO):?>
					<?php echo JText::sprintf('BACKUP_TEXT_SOLVEISSUE_PRO', 'https://www.akeebabackup.com/support.html?utm_source=akeeba_backup&utm_campaign=backuperrorpro') ?>
					<?php else: ?>
					<?php echo JText::sprintf('BACKUP_TEXT_SOLVEISSUE_CORE', 'https://www.akeebabackup.com/subscribe.html?utm_source=akeeba_backup&utm_campaign=backuperrorcore','https://www.akeebabackup.com/support.html?utm_source=akeeba_backup&utm_campaign=backuperrorcore') ?>
					<?php endif; ?>
					<?php echo JText::sprintf('BACKUP_TEXT_SOLVEISSUE_LOG', 'index.php?option=com_akeeba&view=log') ?>
				</p>
			</div>

			<?php if(AKEEBA_PRO):?>
			<a class="btn btn-large btn-success" id="ab-alice-error" href="index.php?option=com_akeeba&view=alices">
				<i class="icon-list-alt icon-white"></i>
				<?php echo JText::_('BACKUP_ANALYSELOG') ?>
			</a>
			<?php endif; ?>

			<button class="btn btn-large btn-primary" onclick="window.location='https://www.akeebabackup.com/documentation/troubleshooter/abbackup.html?utm_source=akeeba_backup&utm_campaign=backuperrorbutton'; return false;">
				<i class="icon-share-alt icon-white"></i>
				<?php echo JText::_('BACKUP_TROUBLESHOOTINGDOCS') ?>
			</button>
			<a class="btn" id="ab-viewlog-error" href="<?php echo JUri::base() ?>index.php?option=com_akeeba&view=log">
				<i class="icon-list-alt"></i>
				<?php echo JText::_('VIEWLOG'); ?>
			</a>
		</div>
	</div>

</div>