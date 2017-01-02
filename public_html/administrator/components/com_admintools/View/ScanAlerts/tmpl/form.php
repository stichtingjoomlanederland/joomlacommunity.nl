<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

/** @var $this Akeeba\AdminTools\Admin\View\ScanAlerts\Html  */

defined('_JEXEC') or die;

$subtitle = JText::sprintf('COM_ADMINTOOLS_TITLE_SCANALERT_EDIT', $this->item->scan_id);
JToolbarHelper::title(JText::_('COM_ADMINTOOLS') . ' &ndash; <small>' . $subtitle . '</small>', 'admintools');

?>
<form name="adminForm" id="adminForm" action="index.php" method="post" class="form form-horizontal">
	<input type="hidden" name="option" value="com_admintools"/>
	<input type="hidden" name="view" value="ScanAlerts"/>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="admintools_scanalert_id" value="<?php echo $this->item->admintools_scanalert_id ?>"/>
	<input type="hidden" name="<?php echo JFactory::getSession()->getFormToken(); ?>" value="1"/>

	<fieldset>
		<legend><?php echo JText::_('COM_ADMINTOOLS_LBL_SCANALERT_FILEINFO'); ?></legend>

		<table class="table table-striped">
			<tr>
				<td>
					<?php echo JText::_('COM_ADMINTOOLS_LBL_SCANALERTS_PATH'); ?>
				</td>
				<td>
					<?php echo $this->item->path ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('COM_ADMINTOOLS_LBL_SCANALERT_SCANDATE'); ?>
				</td>
				<td>
					<?php echo $this->scanDate->format(JText::_('DATE_FORMAT_LC2'), true) ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('COM_ADMINTOOLS_LBL_SCANALERTS_STATUS'); ?>
				</td>
				<td>
					<span
						class="admintools-scanfile-<?php echo $this->fstatus ?> <?php if (!$this->item->threat_score): ?>admintools-scanfile-nothreat<?php endif ?>">
						<?php echo JText::_('COM_ADMINTOOLS_LBL_SCANALERTS_STATUS_' . $this->fstatus) ?>
					</span>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('COM_ADMINTOOLS_LBL_SCANALERTS_THREAT_SCORE'); ?>
				</td>
				<td>
					<span class="admintools-scanfile-threat-<?php echo $this->threatindex ?>">
						<?php echo $this->item->threat_score ?>
					</span>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('COM_ADMINTOOLS_LBL_SCANALERTS_ACKNOWLEDGED'); ?>
				</td>
				<td class="decrapbooleanlist">
					<?php echo JHtml::_('select.booleanlist', 'acknowledged', null, $this->item->acknowledged); ?>
				</td>
			</tr>

		</table>

	</fieldset>

	<?php if ($this->generateDiff && ($this->fstatus == 'modified')):
		echo JHtml::_('sliders.start', 'ScanAlertPanes');
		echo JHtml::_('sliders.panel', JText::_('COM_ADMINTOOLS_LBL_SCANALERT_DIFF'), 'diff');
	?>

	<pre><code class="<?php echo $this->suspiciousFile ? 'php' : 'diff' ?>"><?php echo htmlentities($this->item->diff); ?></code></pre>

	<?php
		echo JHtml::_('sliders.panel', JText::_('COM_ADMINTOOLS_LBL_SCANALERT_SOURCE'), 'source');
	else: ?>
	<fieldset>
		<legend><?php echo JText::_('COM_ADMINTOOLS_LBL_SCANALERT_SOURCE') ?></legend>
		<?php endif; ?>

		<div class="control-group">
			<label class="control-label"><?php echo JText::_('COM_ADMINTOOLS_LBL_SCANALERTS_MD5'); ?></label>

			<div class="controls">
				<span class="help-block"><?php echo @md5_file(JPATH_SITE . '/' . $this->item->path) ?></span>
			</div>
		</div>
		<div style="clear:left"></div>

		<pre><?php echo $this->item->getFileSourceForDisplay(true); ?></pre>

		<?php if ($this->generateDiff && ($this->fstatus == 'modified')):
			echo JHtml::_('sliders.end');
			?>
		<?php else: ?>
	</fieldset>
<?php endif; ?>
</form>