<?php
/**
 * @package   AkeebaBackup
 * @copyright Copyright (c)2006-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

/** @var  \Akeeba\Backup\Admin\View\Log\Html  $this */

?>
<?php if(isset($this->logs) && count($this->logs)): ?>
<form name="adminForm" id="adminForm" action="index.php" method="post" class="form-inline">
	<input name="option" value="com_akeeba" type="hidden" />
	<input name="view" value="Log" type="hidden" />
	<input type="hidden" name="<?php echo $this->container->platform->getToken(true); ?>" value="1" />
	<fieldset>
		<label for="tag"><?php echo \JText::_('COM_AKEEBA_LOG_CHOOSE_FILE_TITLE'); ?></label>
		<?php echo \JHtml::_('select.genericlist', $this->logs, 'tag', 'onchange="submitform();" class="advancedSelect"', 'value', 'text', $this->tag); ?>

		<?php if (!empty($this->tag)): ?>
			<a class="btn btn-primary" href="<?php echo $this->escape(JUri::base()); ?>index.php?option=com_akeeba&view=Log&task=download&tag=<?php echo $this->escape($this->tag); ?>">
				<span class="icon-download icon-white"></span>
				<?php echo \JText::_('COM_AKEEBA_LOG_LABEL_DOWNLOAD'); ?>
			</a>

			<br/>
			<hr/>
			<div id="iframe-holder">
			<?php if ($this->logTooBig):?>
				<p class="alert alert-info">
					<?php echo JText::sprintf('COM_AKEEBA_LOG_SIZE_WARNING', number_format($this->logSize / (1024 * 1024), 2))?>
				</p>
				<span class="btn btn-inverse" id="showlog">
					<?php echo JText::_('COM_AKEEBA_LOG_SHOW_LOG')?>
				</span>
			<?php else:?>
				<iframe
					src="index.php?option=com_akeeba&view=Log&task=iframe&format=raw&tag=<?php echo urlencode($this->tag); ?>"
					width="99%" height="400px">
				</iframe>
			<?php endif;?>
			</div>
		<?php endif; ?>
	</fieldset>
</form>
<?php endif; ?>

<?php if ( ! (isset($this->logs) && count($this->logs))): ?>
<div class="alert alert-error alert-block">
	<?php echo JText::_('COM_AKEEBA_LOG_NONE_FOUND') ?>
</div>
<?php endif; ?>