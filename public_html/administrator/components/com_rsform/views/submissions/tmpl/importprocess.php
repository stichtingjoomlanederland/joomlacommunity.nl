<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

JText::script('COM_RSFORM_IMPORT_HAS_FINISHED');
JText::script('MESSAGE');
?>
<div class="progressWrapper"><div class="progressBar" id="progressBar">0%</div></div>

<form action="<?php echo JRoute::_('index.php?option=com_rsform&view=submissions'); ?>" method="post" name="adminForm" id="adminForm">
	<input type="hidden" name="formId" value="<?php echo $this->formId; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="option" value="com_rsform" />
</form>

<script type="text/javascript">
jQuery(document).ready(function(){
	importProcess(0, <?php echo $this->limit; ?>, <?php echo $this->total; ?>, <?php echo $this->formId; ?>);
});
</script>