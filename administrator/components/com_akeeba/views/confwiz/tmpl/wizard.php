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

?>

<div id="akeeba-confwiz">

<div id="backup-progress-pane" class="ui-widget" style="x-display: none">
	<div class="alert alert-info">
			<?php echo JText::_('COM_AKEEBA_CONFWIZ_INTROTEXT'); ?>
	</div>

	<fieldset id="backup-progress-header">
		<legend><?php echo JText::_('COM_AKEEBA_CONFWIZ_PROGRESS') ?></legend>
		<div id="backup-progress-content">
			<div id="backup-steps">
				<div id="step-ajax" class="label"><?php echo JText::_('COM_AKEEBA_CONFWIZ_AJAX'); ?></div>
				<div id="step-minexec" class="label"><?php echo JText::_('COM_AKEEBA_CONFWIZ_MINEXEC'); ?></div>
				<div id="step-directory" class="label"><?php echo JText::_('COM_AKEEBA_CONFWIZ_DIRECTORY'); ?></div>
				<div id="step-dbopt" class="label"><?php echo JText::_('COM_AKEEBA_CONFWIZ_DBOPT'); ?></div>
				<div id="step-maxexec" class="label"><?php echo JText::_('COM_AKEEBA_CONFWIZ_MAXEXEC'); ?></div>
				<div id="step-splitsize" class="label"><?php echo JText::_('COM_AKEEBA_CONFWIZ_SPLITSIZE'); ?></div>
			</div>
			<div class="well">
				<div id="backup-substep"></div>
			</div>
		</div>
		<span id="ajax-worker"></span>
	</fieldset>

</div>

<div id="error-panel" class="alert alert-error alert-block" style="display:none">
	<h2 class="alert-heading"><?php echo JText::_('COM_AKEEBA_CONFWIZ_HEADER_FAILED'); ?></h2>
	<div id="errorframe">
		<p id="backup-error-message">
		TEST ERROR MESSAGE
		</p>
	</div>
</div>

<div id="backup-complete" style="display: none">
	<div class="alert alert-success alert-block">
		<h2 class="alert-heading"><?php echo JText::_('COM_AKEEBA_CONFWIZ_HEADER_FINISHED'); ?></h2>
		<div id="finishedframe">
			<p>
				<?php echo JText::_('COM_AKEEBA_CONFWIZ_CONGRATS') ?>
			</p>
		</div>
		<button class="btn btn-primary btn-large" onclick="window.location='<?php echo JUri::base() ?>index.php?option=com_akeeba&view=backup'; return false;">
			<i class="icon-road icon-white"></i>
			<?php echo JText::_('COM_AKEEBA_BACKUP'); ?>
		</button>
		<button class="btn" onclick="window.location='<?php echo JUri::base() ?>index.php?option=com_akeeba&view=config'; return false;">
			<i class="icon-wrench"></i>
			<?php echo JText::_('COM_AKEEBA_CONFIG'); ?>
		</button>
	</div>

</div>

</div>

<script type="text/javascript" language="javascript">
    akeeba.System.params.AjaxURL = 'index.php?option=com_akeeba&view=confwiz&task=ajax';
    akeeba.Backup.translations['UI-LASTRESPONSE'] = '<?php echo AkeebaHelperEscape::escapeJS(JText::_('COM_AKEEBA_BACKUP_TEXT_LASTRESPONSE')) ?>';
<?php
	$keys = array('tryajax','tryiframe','cantuseajax','minexectry','cantsaveminexec','saveminexec','cantdetermineminexec',
		'cantfixdirectories','cantdbopt','exectoolow','savingmaxexec','cantsavemaxexec','cantdeterminepartsize','partsize');
	foreach($keys as $key):
?>
akeeba.Wizard.translation['UI-<?php echo strtoupper($key)?>']="<?php echo JText::_('COM_AKEEBA_CONFWIZ_UI_'.strtoupper($key)) ?>";
<?php endforeach; ?>
akeeba.Wizard.boot();
</script>