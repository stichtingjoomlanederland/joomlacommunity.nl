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
					echo "selectedContents[".$oneRow->listid."] = 'content';";
				}
			}
		?>
		function applyContent(contentid, rowClass) {
			if (selectedContents[contentid]) {
				window.document.getElementById('content' + contentid).className = rowClass;
				delete selectedContents[contentid];
			} else {
				window.document.getElementById('content' + contentid).className = 'selectedrow';
				selectedContents[contentid] = 'content';
			}
		}

		function insertTag() {
			var tag = '';
			for (var i in selectedContents) {
				if (selectedContents[i] == 'content') {
					allElements--;
					if (tag != '') tag += ',';
					tag = tag + i;
				}
			}
			if (allElements == 0) tag = 'All';
			if (allElements == <?php echo count($this->rows);?>) tag = 'None';

			<?php if(empty($this->popup)){ ?>
			window.parent.document.getElementById('<?php echo $this->controlName.$this->fieldName; ?>').value = tag;
			window.parent.displayLists();
			<?php }else{ ?>
			window.top.document.getElementById('<?php echo $this->controlName.$this->fieldName; ?>').value = tag;
			window.top.document.getElementById('link<?php echo $this->controlName.$this->fieldName; ?>').href = 'index.php?option=com_acymailing&tmpl=component&ctrl=chooselist&task=<?php echo $this->fieldName; ?>&control=<?php echo $this->controlName; ?>&values=' + tag;
			acymailing_js.closeBox(true);
			<?php } ?>
		}
		//-->
	</script>
	<style type="text/css">
		table.acymailing_table tr.selectedrow td{
			background-color: #f3f7fc;
		}
	</style>
	<form action="index.php?option=<?php echo ACYMAILING_COMPONENT ?>" method="post" name="adminForm" id="adminForm">
		<div style="float:right;margin-bottom : 10px">
			<button class="acymailing_button_grey" id="insertButton" onclick="insertTag(); return false;"><?php echo JText::_('ACY_APPLY'); ?></button>
		</div>
		<div style="clear:both"/>
		<table class="acymailing_table" cellpadding="1">
			<thead>
			<tr>
				<th class="title">

				</th>
				<th class="title titlecolor">

				</th>
				<th class="title">
					<?php echo JText::_('LIST_NAME'); ?>
				</th>
				<th class="title titleid">
					<?php echo JText::_('ACY_ID'); ?>
				</th>
			</tr>
			</thead>
			<tbody>
			<?php
			$k = 0;

			for($i = 0, $a = count($this->rows); $i < $a; $i++){
				$row =& $this->rows[$i];
				?>
				<tr class="<?php echo empty($row->selected) ? "row$k" : 'selectedrow'; ?>" id="content<?php echo $row->listid ?>" onclick="applyContent(<?php echo $row->listid.",'row$k'" ?>);" style="cursor:pointer;">
					<td class="acytdcheckbox"></td>
					<td>
						<?php echo '<div class="roundsubscrib rounddisp" style="background-color:'.$row->color.'"></div>'; ?>
					</td>
					<td>
						<?php
						echo acymailing_tooltip($row->description, $row->name, 'tooltip.png', $row->name);
						?>
					</td>
					<td align="center" style="text-align:center">
						<?php echo $row->listid; ?>
					</td>
				</tr>
				<?php
				$k = 1 - $k;
			}
			?>
			</tbody>
		</table>
	</form>
</div>
