<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<script type="text/javascript">
function rsf_download() {
	<?php if ($this->config->captcha_enabled && !$this->briefcase) { ?>
	document.location = '<?php echo addslashes(JRoute::_('index.php?option=com_rsfiles&layout=validate&tmpl=component&path='.rsfilesHelper::encode($this->path).$this->itemid,false)); ?>';
	<?php } else { ?>
	window.top.location = '<?php echo addslashes(JRoute::_('index.php?option=com_rsfiles&task=rsfiles.download'.($this->briefcase ? '&from=briefcase' : '').'&path='.rsfilesHelper::encode($this->path).($this->hash ? '&hash='.$this->hash : '').$this->itemid,false)); ?>';
	window.top.setTimeout('window.parent.SqueezeBox.close();', 1000);
	<?php } ?>
}
</script>

<div class="rsfiles-layout">
	<div class="rsf_fixed">
		<div class="rsf_fixed_name">
			<i class="rsicon-download"></i> <?php echo JText::sprintf('COM_RSFILES_DOWNLOADING_FILE',rsfilesHelper::getName($this->item->FilePath)); ?>
		</div>
		<div class="btn-group">
			<button type="button" class="btn btn-primary" onclick="rsf_download();"><?php echo JText::_('COM_RSFILES_I_AGREE'); ?></button>
			<button type="button" class="btn" onclick="window.parent.SqueezeBox.close();"><?php echo JText::_('COM_RSFILES_I_DISAGREE'); ?></button>
		</div>
	</div>
	<div class="clearfix"></div>
	<pre class="rsf_pre"><?php echo $this->license->LicenseText; ?></pre>
</div>