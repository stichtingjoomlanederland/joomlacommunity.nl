<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<div class="progressWrapper"><div class="progressBar" id="progressBar">0%</div></div>

<input type="hidden" value="<?php echo $this->file; ?>" id="ExportFile" />
<input type="hidden" value="<?php echo $this->exportType; ?>" id="exportType" />

<div style="display: none" id="backButtonContainer">
<button type="button" class="btn" onclick="document.location.href='<?php echo JRoute::_('index.php?option=com_rsform&view=forms&layout=edit&formId='.$this->formId); ?>'"><?php echo JText::_('RSFP_BACK_TO_FORM'); ?></button>
</div>

<script type="text/javascript">
RSFormPro.$(document).ready(function(){
	exportProcess(0,<?php echo $this->limit; ?>,<?php echo $this->total;?>);
});
</script>