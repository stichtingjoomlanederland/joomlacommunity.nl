<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2016 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div id="acy_content">
<script language="javascript" type="text/javascript">
<!--
	var selectedContents = new Array();
	var allElements = <?php echo count($this->rows);?>;
	<?php
		foreach($this->rows as $oneRow){
			if(!empty($oneRow->selected)){
				echo "selectedContents['".$oneRow->namekey."'] = 'content';";
			}
		}
	?>
	function applyContent(contentid,rowClass){
		if(selectedContents[contentid]){
			window.document.getElementById('content'+contentid).className = rowClass;
			delete selectedContents[contentid];
		}else{
			window.document.getElementById('content'+contentid).className = 'selectedrow';
			selectedContents[contentid] = 'content';
		}
	}

	function insertTag(){
		var tag = '';
		for(var i in selectedContents){
			if(selectedContents[i] == 'content'){
				allElements--;
				if(tag != '') tag += ',';
				tag = tag + i;
			}
		}

		window.top.document.getElementById('<?php echo $this->controlName; ?>customfields').value = tag;
		window.top.document.getElementById('link<?php echo $this->controlName; ?>customfields').href = 'index.php?option=com_acymailing&tmpl=component&ctrl=chooselist&task=customfields&control=<?php echo $this->controlName; ?>&values='+tag;
		acymailing_js.closeBox(true);
	}
//-->
</script>
<style type="text/css">
	table.acymailing_table tr.selectedrow td{
		background-color:#FDE2BA;
	}
</style>
<form action="index.php?option=<?php echo ACYMAILING_COMPONENT ?>" method="post" name="adminForm" id="adminForm" >
<div style="float:right;margin-bottom : 10px">
	<button class="acymailing_button_grey" id="insertButton" onclick="insertTag(); return false;"><?php echo JText::_('ACY_APPLY'); ?></button>
</div>
<div style="clear:both"/>
	<table class="acymailing_table" cellpadding="1">
		<thead>
			<tr>
				<th class="title">

				</th>
				<th class="title">
					<?php echo JText::_('FIELD_COLUMN'); ?>
				</th>
				<th class="title">
					<?php echo JText::_('FIELD_LABEL'); ?>
				</th>
				<th class="title titleid">
					<?php echo JText::_('ACY_ID'); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php
				$k = 0;

				foreach($this->rows as $row){
			?>
				<tr class="<?php echo empty($row->selected) ? "row$k" : 'selectedrow'; ?>" id="content<?php echo $row->namekey; ?>" onclick="applyContent('<?php echo $row->namekey."','row$k'"?>);" style="cursor:pointer;">
					<td class="acytdcheckbox"></td>
					<td>
					<?php echo $row->namekey; ?>
					</td>
					<td>
					<?php echo $this->fieldsClass->trans($row->fieldname); ?>
					</td>
					<td align="center" style="text-align:center" >
						<?php echo $row->fieldid; ?>
					</td>
				</tr>
			<?php
					$k = 1-$k;
				}
			?>
		</tbody>
	</table>
</form>
</div>
