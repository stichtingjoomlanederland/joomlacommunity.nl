<?php
/**
 * @package   AkeebaBackup
 * @copyright Copyright (c)2006-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

/** @var  $this  \Akeeba\Backup\Admin\View\Backup\Html */
?>

<script type="text/javascript" language="javascript">
	akeeba.jQuery(document).ready(function($){
		// Initialization
		akeeba.Backup.defaultDescription = "<?php echo addslashes($this->defaultDescription); ?>";
		akeeba.Backup.config_angiekey    = "<?php echo addslashes($this->ANGIEPassword); ?>";
		akeeba.Backup.jpsKey             = "<?php echo $this->showJPSPassword ? addslashes($this->jpsPassword) : ''; ?>";

		// Auto-resume setup
		akeeba.Backup.resume.enabled = <?php echo (int)$this->autoResume; ?>;
		akeeba.Backup.resume.timeout = <?php echo (int)$this->autoResumeTimeout; ?>;
		akeeba.Backup.resume.maxRetries = <?php echo (int)$this->autoResumeRetries; ?>;

		// The return URL
		akeeba.Backup.returnUrl = '<?php echo $this->returnURL; ?>';

		// Used as parameters to start_timeout_bar()
		akeeba.Backup.maxExecutionTime = <?php echo (int)$this->maxExecutionTime; ?>;
		akeeba.Backup.runtimeBias = <?php echo (int)$this->runtimeBias; ?>;

		// Create a function for saving the editor's contents
		akeeba.Backup.commentEditorSave = function() {
		};

		akeeba.System.notification.iconURL = '<?php echo addslashes(JUri::base()); ?>../media/com_akeeba/icons/logo-48.png';

		//Parse the domain keys
		akeeba.Backup.domains = JSON.parse('<?php echo addcslashes($this->domains, "'\\"); ?>');

		// Setup AJAX proxy URL
		akeeba.System.params.AjaxURL = 'index.php?option=com_akeeba&view=Backup&task=ajax';

		// Setup base View Log URL
		akeeba.Backup.URLs.LogURL = '<?php echo addslashes(JUri::base()); ?>index.php?option=com_akeeba&view=Log';
		akeeba.Backup.URLs.AliceURL = '<?php echo addslashes(JUri::base()); ?>index.php?option=com_akeeba&view=Alice';

		// Setup the IFRAME mode
		akeeba.System.params.useIFrame = <?php echo $this->useIFRAME ? 'true' : 'false'; ?>;

		<?php if($this->desktopNotifications): ?>
		akeeba.System.notification.askPermission();
		<?php endif; ?>

		<?php if(!$this->unwriteableOutput && $this->autoStart): ?>
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
