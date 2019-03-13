<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
?>
<form action="<?php echo JRoute::_('index.php?option=com_rsform'); ?>" method="post" name="adminForm" id="adminForm" autocomplete="off">
    <!-- this workaround is needed because browsers no longer honor autocomplete="off" -->
    <input type="text" style="display:none">
    <input type="password" style="display:none">
<div id="tablers">
<table class="admintable">
	<tr>
		<td width="160" style="width: 160px;" align="right" class="key"><?php echo JText::_('RSFP_FORM_MAPPINGS_CONNECTION') ?></td>
		<td>
			<span id="mpConnectionOn">
				<?php
					echo $this->lists['MappingConnection']; 
				?>
			</span>
			<span id="mpConnectionOff" style="display:none;"></span>
		</td>
	</tr>
	<tr>
		<td width="160" style="width: 160px;" align="right" class="key"><?php echo JText::_('RSFP_FORM_MAPPINGS_METHOD') ?></td>
		<td>
			<span id="mpMethodOn">
				<?php
					echo $this->lists['MappingMethod'];
				?>
			</span>
			<span id="mpMethodOff" style="display:none;"></span>
		</td>
	</tr>
	<tbody id="mappingsid">
	<tr>
		<td width="160" style="width: 160px;" align="right" class="key"><?php echo JText::_('RSFP_FORM_MAPPINGS_HOST') ?></td>
		<td>
			<span id="mpHostOn">
				<input type="text" class="rs_inp rs_50" name="host" id="MappingHost" value="<?php echo $this->escape($this->mapping->host); ?>" size="50" />
			</span>
			<span id="mpHostOff" style="display:none;"></span>
			<span id="mpPortOn">
				<?php echo JText::_('RSFP_FORM_MAPPINGS_PORT'); ?> : <input type="text" class="rs_inp rs_10" name="port" id="MappingPort" value="<?php echo $this->escape($this->mapping->port); ?>" size="5" />
			</span>
		</td>
	</tr>
	<tr>
		<td width="160" style="width: 160px;" align="right" class="key"><?php echo JText::_('RSFP_FORM_MAPPINGS_DRIVER') ?></td>
		<td>
			<span id="mpDriverOn">
				<?php echo $this->lists['MappingDriver']; ?>
			</span>
			<span id="mpDriverOff" style="display:none;"></span>
		</td>
	</tr>
	<tr>
		<td width="160" style="width: 160px;" align="right" class="key"><?php echo JText::_('RSFP_FORM_MAPPINGS_USERNAME') ?></td>
		<td>
			<span id="mpUsernameOn">
				<input type="text" autocomplete="off" class="rs_inp rs_50" name="username" id="MappingUsername" value="<?php echo $this->escape($this->mapping->username); ?>" size="50" />
			</span>
			<span id="mpUsernameOff" style="display:none;"></span>
		</td>
	</tr>
	<tr>
		<td width="160" style="width: 160px;" align="right" class="key"><?php echo JText::_('RSFP_FORM_MAPPINGS_PASSWORD') ?></td>
		<td>
			<span id="mpPasswordOn">
				<input type="password" autocomplete="new-password" class="rs_inp rs_50" name="password" id="MappingPassword" value="<?php echo $this->escape($this->mapping->password); ?>" size="50" />
			</span>
			<span id="mpPasswordOff" style="display:none;"></span>
		</td>
	</tr>
	<tr>
		<td width="160" style="width: 160px;" align="right" class="key"><?php echo JText::_('RSFP_FORM_MAPPINGS_DATABASE') ?></td>
		<td>
			<span id="mpDatabaseOn">
				<input type="text" class="rs_inp rs_50" name="database" id="MappingDatabase" value="<?php echo $this->escape($this->mapping->database); ?>" size="50" />
			</span>
			<span id="mpDatabaseOff" style="display:none;"></span>
		</td>
	</tr>
	</tbody>
	<tr>
		<td width="160" style="width: 160px;" align="right">&nbsp;</td>
		<td>
			<button class="btn" type="button" id="connectBtn" onclick="mpConnect();"><?php echo JText::_('RSFP_FORM_MAPPINGS_CONNECT'); ?></button>
            <?php echo JHtml::image('com_rsform/admin/loading.gif', '', 'id="mappingloader" style="vertical-align: middle; display: none;"',true); ?>
		</td>
	</tr>
</table>
</div>
<div id="rsfpmappingContent">
<?php if (!empty($this->mapping->id)) { ?>
	<table class="admintable">
		<tr>
			<td width="160" style="width: 160px;" align="right" class="key"><?php echo JText::_('RSFP_FORM_MAPPINGS_TABLE'); ?></td>
			<td>
				<?php echo $this->lists['tables']; ?>
                <?php echo JHtml::image('com_rsform/admin/loading.gif', '', 'id="mappingloader2" style="vertical-align: middle; display: none;"',true); ?>
            </td>
		</tr>
	</table>
<?php } ?>
</div>
<br /><br />
<div id="rsfpmappingColumns">
<?php if (!empty($this->mapping->id) && ($this->mapping->method == 0 || $this->mapping->method == 1 || $this->mapping->method == 3)) { ?>
	<?php
	try {
		echo RSFormProHelper::mappingsColumns($this->config, 'set', $this->mapping);
	} catch (Exception $e) {
		echo $this->escape(JText::sprintf('RSFP_DB_ERROR', $e->getMessage()));
	}
	?>
<?php } ?>
</div>
<br /><br />
<div id="rsfpmappingWhere">
<?php if (!empty($this->mapping->id) && ($this->mapping->method == 1 || $this->mapping->method == 2)) { ?>
	<?php
	try {
		echo RSFormProHelper::mappingsColumns($this->config, 'where', $this->mapping);
	} catch (Exception $e) {
		echo $this->escape(JText::sprintf('RSFP_DB_ERROR', $e->getMessage()));
	}
	?>
<?php } ?>
</div>
	<input type="hidden" name="option" value="com_rsform" />
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="controller" value="mappings" />
	<input type="hidden" name="id" id="mappingid" value="<?php echo $this->mapping->id; ?>" />
	<input type="hidden" name="formId" value="<?php echo $this->formId; ?>" />
</form>

<script type="text/javascript">
	function enableDbDetails(value) {
		document.getElementById('mappingsid').style.display = value == 1 ? '' : 'none';
	}

	enableDbDetails(<?php echo $this->mapping->connection; ?>);
</script>

<style type="text/css">
body {
	padding: 20px !important;
}
</style>