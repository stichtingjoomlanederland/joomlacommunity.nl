<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

/** @var \Akeeba\AdminTools\Admin\View\Scans\Form $this */

defined('_JEXEC') or die;
?>

<div class="form-inline">
	<a class="btn btn-primary" href="index.php?option=com_admintools&view=Scanner">
		<i class="icon icon-white icon-cog"></i>
		<?php echo JText::_('COM_ADMINTOOLS_LBL_SCAN_CONFIGURE'); ?>
	</a>
<span class="help-inline">
	<i class="icon-info-sign"></i>
	<?php echo JText::_('COM_ADMINTOOLS_MSG_SCAN_CONFIGUREHELP'); ?>
</span>
</div>
<hr/>

<?php
	echo $this->getRenderedForm();
?>
<div id="admintools-scan-dim" style="display: none">
	<div id="admintools-scan-container">
		<p>
			<?php echo JText::_('COM_ADMINTOOLS_MSG_SCAN_PLEASEWAIT') ?><br/>
			<?php echo JText::_('COM_ADMINTOOLS_MSG_SCAN_SCANINPROGRESS') ?>
		</p>

		<p>
			<progress></progress>
		</p>
		<p>
			<span id="admintools-lastupdate-text" class="lastupdate"></span>
		</p>
	</div>
</div>
