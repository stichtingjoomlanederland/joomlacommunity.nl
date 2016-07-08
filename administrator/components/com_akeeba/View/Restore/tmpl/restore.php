<?php
/**
 * @package   AkeebaBackup
 * @copyright Copyright (c)2006-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

$this->loadHelper('escape');

?>
<div class="alert">
	<span class="icon-warning-sign"></span>
	<?php echo \JText::_('COM_AKEEBA_RESTORE_LABEL_DONOTCLOSE'); ?>
</div>


<div id="restoration-progress">
	<h3><?php echo \JText::_('COM_AKEEBA_RESTORE_LABEL_INPROGRESS'); ?></h3>

	<table class="table table-striped">
		<tr>
			<td width="25%">
				<?php echo \JText::_('COM_AKEEBA_RESTORE_LABEL_BYTESREAD'); ?>
			</td>
			<td>
				<span id="extbytesin"></span>
			</td>
		</tr>
		<tr>
			<td width="25%">
				<?php echo \JText::_('COM_AKEEBA_RESTORE_LABEL_BYTESEXTRACTED'); ?>
			</td>
			<td>
				<span id="extbytesout"></span>
			</td>
		</tr>
		<tr>
			<td width="25%">
				<?php echo \JText::_('COM_AKEEBA_RESTORE_LABEL_FILESEXTRACTED'); ?>
			</td>
			<td>
				<span id="extfiles"></span>
			</td>
		</tr>
	</table>

	<div id="response-timer">
		<div class="color-overlay"></div>
		<div class="text"></div>
	</div>
</div>

<div id="restoration-error" style="display:none">
	<div class="alert alert-error">
		<h3 class="alert-heading"><?php echo \JText::_('COM_AKEEBA_RESTORE_LABEL_FAILED'); ?></h3>
		<div id="errorframe">
			<p><?php echo \JText::_('COM_AKEEBA_RESTORE_LABEL_FAILED_INFO'); ?></p>
			<p id="backup-error-message">
			</p>
		</div>
	</div>
</div>

<div id="restoration-extract-ok" style="display:none">
	<div class="alert alert-success">
		<h3 class="alert-heading"><?php echo \JText::_('COM_AKEEBA_RESTORE_LABEL_SUCCESS'); ?></h3>
		<p>
			<?php echo \JText::_('COM_AKEEBA_RESTORE_LABEL_SUCCESS_INFO2'); ?>
		</p>
		<p>
			<?php echo \JText::_('COM_AKEEBA_RESTORE_LABEL_SUCCESS_INFO2B'); ?>
		</p>
	</div>
	<p>
		<button class="btn btn-large btn-success" id="restoration-runinstaller" onclick="return false;">
			<span class="icon-share-alt icon-white"></span>
			<?php echo \JText::_('COM_AKEEBA_RESTORE_LABEL_RUNINSTALLER'); ?>
		</button>
	</p>
	<p>
		<button class="btn btn-large btn-success" id="restoration-finalize" style="display: none" onclick="return false;">
			<span class="icon-exit icon-white"></span>
			<?php echo \JText::_('COM_AKEEBA_RESTORE_LABEL_FINALIZE'); ?>
		</button>
	</p>
</div>

<script type="text/javascript" language="javascript">
    akeeba.Restore.password = '<?php echo addslashes($this->password); ?>';
	akeeba.Restore.ajaxURL = '<?php echo addslashes(JUri::base()); ?>/components/com_akeeba/restore.php';
    akeeba.Restore.mainURL = '<?php echo addslashes(JUri::base()); ?>/index.php';

	(function($){
		$(document).ready(function(){
            akeeba.Restore.pingRestoration();
		});

		$('#restoration-runinstaller').click(akeeba.Restore.runInstaller);
		$('#restoration-finalize').click(akeeba.Restore.finalize);
	})(akeeba.jQuery);
</script>