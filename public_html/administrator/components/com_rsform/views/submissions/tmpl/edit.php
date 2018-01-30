<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<form action="index.php?option=com_rsform" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
	<?php if ($this->submission->Lang) { ?>
	<p><?php echo JText::sprintf('RSFP_SUBMISSION_SENT_IN', $this->submission->Lang); ?></p>
	<?php } ?>
	<table class="admintable">
		<?php foreach ($this->staticHeaders as $header) { ?>
		<tr>
			<td width="200" style="width: 200px;" align="right" class="key"><?php echo JText::_('RSFP_'.$header); ?></td>
			<td>
				<?php if ($header == 'confirmed') { ?>
				<?php echo JHtml::_('select.booleanlist','formStatic['.$header.']','class="inputbox"',$this->staticFields->$header); ?>
				<?php } else { ?>
				<input class="rs_inp rs_80" type="text" name="formStatic[<?php echo $header; ?>]" value="<?php echo $this->escape($this->staticFields->$header); ?>" size="105" />
				<?php } ?>
			</td>
		</tr>
		<?php } ?>
		<?php foreach ($this->fields as $field) { ?>
		<tr>
			<td width="200" style="width: 200px;" align="right" class="key">
				<?php echo $field[0]; ?>
			</td>
			<td>
				<?php echo $field[1]; ?>
			</td>
		</tr>
		<?php } ?>
	</table>
	
	<input type="hidden" name="option" value="com_rsform">
	<input type="hidden" name="task" value="">
	<input type="hidden" name="cid" value="<?php echo $this->submissionId; ?>">
	<input type="hidden" name="formId" value="<?php echo $this->formId; ?>">
</form>
<?php JHtml::_('behavior.keepalive'); ?>