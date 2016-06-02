<?php
/**
 * @package   AkeebaBackup
 * @copyright Copyright (c)2006-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

/** @var  \Akeeba\Backup\Admin\View\Alice\Html  $this */
?>
<?php if ( ! (empty($this->logs))): ?>
	<form name="adminForm" id="adminForm" action="index.php" method="post" class="form-inline">
		<input name="option" value="com_akeeba" type="hidden"/>
		<input name="view" value="Alice" type="hidden"/>
		<input type="hidden" name="<?php echo \JFactory::getSession()->getFormToken(); ?>" value="1"/>

		<?php if($this->autorun): ?>
			<div class="alert">
				<?php echo \JText::_('ALICE_AUTORUN_NOTICE'); ?>
			</div>
		<?php endif; ?>

		<fieldset>
			<label for="tag">
				<?php echo \JText::_('COM_AKEEBA_LOG_CHOOSE_FILE_TITLE'); ?>
			</label>
			<?php echo \JHtml::_('select.genericlist', $this->logs, 'log', [
				'onchange' => "akeeba.jQuery(this).val() ? akeeba.jQuery('#analyze-log').show() : akeeba.jQuery('#analyze-log').hide()",
			], 'value', 'text', $this->log); ?>

			<button class="btn btn-primary" id="analyze-log" style="display:none">
				<span class="icon-arrow-right-4 icon-white"></span>
				<?php echo \JText::_('COM_AKEEBA_ALICE_ANALYZE'); ?>
			</button>

            <button class="btn btn-inverse" id="download-log" data-url="<?php echo $this->escape(JUri::base()); ?>index.php?option=com_akeeba&view=Log&task=download&tag=[TAG]" style="display: none;">
                <span class="icon-download icon-white"></span>
                <?php echo \JText::_('COM_AKEEBA_LOG_LABEL_DOWNLOAD'); ?>
            </button>

        </fieldset>

		<div id="stepper-holder" style="margin-top: 15px">
			<div id="stepper-loading" style="text-align: center;display: none">
				<img src="<?php echo $this->escape($this->getContainer()->template->parsePath('media://com_akeeba/icons/loading.gif')); ?>"/>
			</div>
			<div id="stepper-progress-pane" style="display: none">
				<div class="alert">
					<span class="icon-warning-sign"></span>
					<?php echo \JText::_('COM_AKEEBA_BACKUP_TEXT_BACKINGUP'); ?>
				</div>
				<fieldset>
					<legend><?php echo \JText::_('COM_AKEEBA_ALICE_ANALYZE_LABEL_PROGRESS'); ?></legend>
					<div id="stepper-progress-content">
						<div id="stepper-steps">
						</div>
						<div id="stepper-status" class="well">
							<div id="stepper-step"></div>
							<div id="stepper-substep"></div>
						</div>
						<div id="stepper-percentage" class="progress">
							<div class="bar" style="width: 0"></div>
						</div>
						<div id="response-timer">
							<div class="color-overlay"></div>
							<div class="text"></div>
						</div>
					</div>
					<span id="ajax-worker"></span>
				</fieldset>
			</div>
			<div id="output-plain" style="display:none; margin-bottom: 20px;">
				<h4><?php echo \JText::_('COM_AKEEBA_ALICE_ANALYZE_RAW_OUTPUT'); ?></h4>
				<textarea style="width:50%; margin:auto; display:block; height: 100px;" readonly="readonly"></textarea>
			</div>
			<div id="stepper-complete" style="display: none">
			</div>
		</div>
	</form>
<?php endif; ?>

<?php if(empty($this->logs)): ?>
<div class="alert alert-error alert-block">
	<?php echo \JText::_('COM_AKEEBA_LOG_NONE_FOUND'); ?>
</div>
<?php endif; ?>