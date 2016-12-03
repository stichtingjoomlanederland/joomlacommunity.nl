<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );?>
<style type="text/css">
	body, #main { background:#F8F8F8 !important; }
</style>
<script type="text/javascript">
	function validate_form() {
		var ret = true;
		
		if (document.getElementById('name').value == '') {
			document.getElementById('name').style.border = '1px solid red';
			ret = false;
		}
		return ret;
	}
</script>

<form action="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=file'); ?>" method="post" name="adminForm" id="adminForm">
	<table cellpadding="0" cellspacing="0" border="0" width="100%" class="rs_table">
		<tr>
			<td width="80"><?php echo JText::_('COM_RSEVENTSPRO_FILE_NAME'); ?></td>
			<td><input type="text" id="name" name="name" value="<?php echo $this->row->name; ?>" size="40" /></td>
		</tr>
		<tr>
			<td width="80"><?php echo JText::_('COM_RSEVENTSPRO_FILE_PERMISSIONS'); ?></td>
			<td>
				<b><?php echo JText::_('COM_RSEVENTSPRO_FILE_VISIBLE_ALL'); ?></b> <br />
				<input type="checkbox" id="fp0" name="fp0" value="1" <?php echo isset($this->row->permissions[0]) && $this->row->permissions[0] == 1 ? 'checked="checked"' : ''; ?> /> 
				<label for="fp0"><?php echo JText::_('COM_RSEVENTSPRO_FILE_VISIBLE_BEFORE'); ?></label> <br />
				<input type="checkbox" id="fp1" name="fp1" value="1" <?php echo isset($this->row->permissions[1]) && $this->row->permissions[1] == 1 ? 'checked="checked"' : ''; ?> /> 
				<label for="fp1"><?php echo JText::_('COM_RSEVENTSPRO_FILE_VISIBLE_DURING'); ?></label> <br />
				<input type="checkbox" id="fp2" name="fp2" value="1" <?php echo isset($this->row->permissions[2]) && $this->row->permissions[2] == 1 ? 'checked="checked"' : ''; ?> /> 
				<label for="fp2"><?php echo JText::_('COM_RSEVENTSPRO_FILE_VISIBLE_AFTER'); ?></label> <br /> <br />
				
				<b><?php echo JText::_('COM_RSEVENTSPRO_FILE_VISIBLE_REGISTERED'); ?></b> <br />
				<input type="checkbox" id="fp3" name="fp3" value="1" <?php echo isset($this->row->permissions[3]) && $this->row->permissions[3] == 1 ? 'checked="checked"' : ''; ?> /> 
				<label for="fp3"><?php echo JText::_('COM_RSEVENTSPRO_FILE_VISIBLE_BEFORE'); ?></label> <br />
				<input type="checkbox" id="fp4" name="fp4" value="1" <?php echo isset($this->row->permissions[4]) && $this->row->permissions[4] == 1 ? 'checked="checked"' : ''; ?> /> 
				<label for="fp4"><?php echo JText::_('COM_RSEVENTSPRO_FILE_VISIBLE_DURING'); ?></label> <br />
				<input type="checkbox" id="fp5" name="fp5" value="1" <?php echo isset($this->row->permissions[5]) && $this->row->permissions[5] == 1 ? 'checked="checked"' : ''; ?> /> 
				<label for="fp5"><?php echo JText::_('COM_RSEVENTSPRO_FILE_VISIBLE_AFTER'); ?></label>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:right;"><button type="submit" class="button" onclick="return validate_form();"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_SAVE'); ?></button></td>
		</tr>
	</table>

	<?php echo JHTML::_('form.token')."\n"; ?>
	<input type="hidden" name="option" value="com_rseventspro" />
	<input type="hidden" name="task" value="rseventspro.savefile" />
	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
</form>