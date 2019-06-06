<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<form method="post" action="index.php?option=com_rsform" name="adminForm" id="adminForm">
	<p>
		<button class="btn btn-success pull-left" type="button" onclick="Joomla.submitbutton('richtext.apply');"><?php echo JText::_('JAPPLY'); ?></button>
		<button class="btn btn-success pull-left" type="button" onclick="Joomla.submitbutton('richtext.save');"><?php echo JText::_('JSAVE'); ?></button>
		<button class="btn pull-left" type="button" onclick="window.close();"><?php echo JText::_('JCANCEL'); ?></button>
	</p>
	<span class="rsform_clear_both"></span>

	<fieldset>
		<h3 class="rsfp-legend"><?php echo JText::_('RSFP_EDITING_TEXT'); ?><?php if (!RSFormProHelper::getConfig('global.disable_multilanguage')) { ?> <small><?php echo JText::sprintf('RSFP_YOU_ARE_EDITING_IN_SHORT', $this->lang); ?></small><?php } ?></h3>

	<?php if ($this->noEditor) { ?>
		<textarea cols="70" rows="10" style="width: 500px; height: 320px;" class="rs_textarea" name="<?php echo $this->editorName; ?>"><?php echo RSFormProHelper::htmlEscape($this->editorText); ?></textarea>
	<?php } else { ?>
		<?php echo $this->editor->display($this->editorName, htmlentities($this->editorText, ENT_COMPAT, 'UTF-8'), 500, 320, 70, 10); ?>
	<?php } ?>
	</fieldset>
	
	<input type="hidden" name="option" value="com_rsform" />
	<input type="hidden" name="opener" value="<?php echo $this->editorName; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="formId" value="<?php echo $this->formId; ?>" />
</form>

<script type="text/javascript">
Joomla.submitbutton = function(task) {
	try {
		<?php echo $this->editor->save($this->editorName); ?>
	} catch (e) {}
	Joomla.submitform(task, document.getElementById('adminForm'));
}
</script>

<style type="text/css">
body {
	padding: 20px !important;
}
</style>

<?php JHtml::_('behavior.keepalive'); ?>