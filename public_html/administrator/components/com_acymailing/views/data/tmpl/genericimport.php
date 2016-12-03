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
	<div id="iframedoc"></div>
	<form action="<?php echo JRoute::_('index.php?option='.ACYMAILING_COMPONENT); ?>" method="post" name="adminForm" enctype="multipart/form-data" id="adminForm">
		<input type="hidden" name="option" value="<?php echo ACYMAILING_COMPONENT; ?>"/>
		<input type="hidden" name="task" value=""/>
		<input type="hidden" name="ctrl" value="<?php echo JRequest::getCmd('ctrl'); ?>"/>
		<input type="hidden" name="import_type" id="import_type" value="<?php echo $this->type; ?>"/>
		<input type="hidden" name="filename" id="filename" value="<?php echo JRequest::getCmd('filename'); ?>"/>
		<input type="hidden" name="import_columns" id="import_columns" value=""/>
		<input type="hidden" name="createlist" id="createlist" value="<?php echo JRequest::getString('createlist'); ?>"/>
		<?php
		$app = JFactory::getApplication();
		$checkedLists = JRequest::getVar('importlists', array(), '', 'array');
		foreach($checkedLists as $key => $oneList){
			echo '<input type="hidden" name="importlists['.intval($key).']" id="importlists'.intval($key).'-'.intval($oneList).'" value="'.intval($oneList).'"/>';
		}

		if(!empty($this->Itemid)) echo '<input type="hidden" name="Itemid" value="'.$this->Itemid.'" />';
		echo JHTML::_('form.token'); ?>

		<div class="onelineblockoptions" id="matchdata">
			<?php include_once(ACYMAILING_BACK.'views'.DS.'data'.DS.'tmpl'.DS.'ajaxencoding.php'); ?>
			<div class="loading" align="center"><?php echo JText::sprintf('ACY_FIRST_LINES', ($nbLines < 11 - $noHeader ? ($nbLines - 1 + $noHeader) : 10)); ?></div>
		</div>

		<div class="onelineblockoptions">
			<span class="acyblocktitle">Parameters</span>
			<table class="acymailing_table" cellspacing="1">
				<tr id="trfilecharset">
					<td class="acykey">
						<?php echo JText::_('CHARSET_FILE'); ?>
					</td>
					<td>
						<?php
						$charsetType = acymailing_get('type.charset');
						$charsetType->addinfo = 'onchange="changeCharset();"';
						$this->type = empty($this->type) ? '' : $this->type;
						if($this->type == 'textarea'){
							$default = 'UTF-8';
						}elseif($this->type == 'file'){
							$default = $encodingHelper->detectEncoding($this->content);
						}
						echo $charsetType->display('charsetconvert', $default);
						?>
						<span id="loadingEncoding"></span>
					</td>
				</tr>
				<?php if($this->config->get('require_confirmation')){ ?>
					<tr id="trfileconfirm">
						<td class="acykey">
							<?php echo JText::_('IMPORT_CONFIRMED'); ?>
						</td>
						<td>
							<?php echo JHTML::_('acyselect.booleanlist', "import_confirmed", '', in_array('import_confirmed', $this->selectedParams) ? 1 : 0, JText::_('JOOMEXT_YES'), JTEXT::_('JOOMEXT_NO')); ?>
						</td>
					</tr>
				<?php } ?>
				<tr id="trfilegenerate">
					<td class="acykey">
						<?php echo JText::_('GENERATE_NAME'); ?>
					</td>
					<td>
						<?php echo JHTML::_('acyselect.booleanlist', "generatename", '', in_array('generatename', $this->selectedParams) ? 1 : 0, JText::_('JOOMEXT_YES'), JTEXT::_('JOOMEXT_NO')); ?>
					</td>
				</tr>
				<tr id="trfileblock">
					<td class="acykey">
						<?php echo JText::_('IMPORT_BLOCKED'); ?>
					</td>
					<td>
						<?php echo JHTML::_('acyselect.booleanlist', "importblocked", '', in_array('importblocked', $this->selectedParams) ? 1 : 0, JText::_('JOOMEXT_YES'), JTEXT::_('JOOMEXT_NO')); ?>
					</td>
				</tr>
				<tr id="trfileoverwrite">
					<td class="acykey">
						<?php echo JText::_('OVERWRITE_EXISTING'); ?>
					</td>
					<td>
						<?php echo JHTML::_('acyselect.booleanlist', "overwriteexisting", '', in_array('overwriteexisting', $this->selectedParams) ? 1 : 0, JText::_('JOOMEXT_YES'), JTEXT::_('JOOMEXT_NO')); ?>
					</td>
				</tr>
			</table>
		</div>
		<div class="onelineblockoptions">
			<span class="acyblocktitle"><?php echo JText::_('SUBSCRIPTION'); ?></span>
			<table class="acymailing_table" cellspacing="1">
				<tr id="trsumup">
					<td>
						<?php
						echo JText::_('ACY_IMPORT_LISTS').' : '.(empty($this->lists) ? JText::_('ACY_NONE') : htmlspecialchars($this->lists, ENT_COMPAT, 'UTF-8'));
						echo '<br />'.JText::_('ACY_IMPORT_UNSUB_LISTS').' : '.(empty($this->unsublists) ? JText::_('ACY_NONE') : htmlspecialchars($this->unsublists, ENT_COMPAT, 'UTF-8'));
						?>
					</td>
				</tr>
			</table>
		</div>
	</form>
	<script language="javascript" type="text/javascript">
		<!--
		<?php if(ACYMAILING_J16){ echo 'Joomla.submitbutton = function(pressbutton){'; }else{ echo 'function submitbutton(pressbutton){'; } ?>
		if(pressbutton == 'finalizeimport'){
			var subval = true;
			var errors = "";
			var string = "";
			var emailField = false;
			var columns = "";
			var selectedFields = Array();
			var fieldNb = <?php echo $nbColumns; ?>;
			if(isNaN(fieldNb)) fieldNb = 1;

			for(var i = 0; i < fieldNb; i++){
				if(document.getElementById("newcustom" + i).required){
					string = document.getElementById("newcustom" + i).value;
					if(string == ""){
						subval = false;
						errors += "\nNew custom field's name (column " + (i + 1) + ")";
					}else{
						if(!string.match(/^[A-Za-z][A-Za-z0-9_]+$/)){
							subval = false;
							errors += "\nPlease enter a valid field name for the column n°" + (i + 1) + ": spaces, uppercase and special characters are not allowed";
						}else{
							if(string != 1 && selectedFields.indexOf(string) != -1){
								subval = false;
								errors += "\nDuplicate field \"" + string + "\" for the column n°" + (i + 1);
							}else{
								if(string != 0){
									selectedFields.push(string);
								}
							}
							columns += "," + string;
						}
					}
				}else{
					string = document.getElementById("fieldAssignment" + i).value;
					if(string == 0){
						subval = false;
						errors += "\nAssign the column " + (i + 1) + " to a field";
					}

					if(string == 'email'){
						emailField = true;
					}

					if(string != 1 && selectedFields.indexOf(string) != -1){
						subval = false;
						errors += "\nDuplicate field \"" + string + "\" for the column " + (i + 1);
					}else{
						selectedFields.push(string);
					}

					columns += "," + string;
				}
			}

			if(!emailField){
				subval = false;
				errors += "\nPlease assign a column for the e-mail field";
			}

			if(subval == false){
				alert("<?php echo JText::_('FILL_ALL'); ?>:\n" + errors);
				return false;
			}

			if(columns.substr(0, 1) == ","){
				columns = columns.substring(1);
			}

			document.getElementById("import_columns").value = columns;
		}

		<?php if(ACYMAILING_J16){ echo 'Joomla.submitform(pressbutton,document.adminForm);'; }else{ echo 'submitform(pressbutton);'; } ?>
		}

		function checkNewCustom(key){
			if(document.getElementById("fieldAssignment" + key).value == 2){
				document.getElementById("newcustom" + key).style.display = "";
				document.getElementById("newcustom" + key).required = true;
			}else{
				document.getElementById("newcustom" + key).style.display = "none";
				document.getElementById("newcustom" + key).required = false;
			}
		}

		function changeCharset(){
			var URL = "index.php?option=com_acymailing&ctrl=<?php if(!$app->isAdmin()){ echo 'front'; } ?>data&encoding=" + document.getElementById("charsetconvert").value + "&tmpl=component&task=ajaxencoding&filename=<?php echo urlencode($filename); ?>";
			var selectedDropdowns = "";
			var fieldNb = <?php echo $nbColumns; ?>;
			if(isNaN(fieldNb)) fieldNb = 1;

			for(var i = 0; i < fieldNb; i++){
				selectedDropdowns += "&fieldAssignment" + i + "=" + document.getElementById("fieldAssignment" + i).value;
				if(document.getElementById("newcustom" + i).required){
					selectedDropdowns += "&newcustom" + i + "=" + document.getElementById("newcustom" + i).value;
				}
			}

			URL += selectedDropdowns;


			document.getElementById("loadingEncoding").innerHTML = '<span class=\"onload\"></span>';
			document.getElementById("importdata").style.opacity = "0.5";
			document.getElementById("importdata").style.filter = 'alpha(opacity=50)';

			try{
				var ajaxCall = new Ajax(URL, {
					method: "POST", update: document.getElementById("matchdata"), onComplete: function(){
						document.getElementById("loadingEncoding").innerHTML = '';
					}
				}).request();
			}catch(err){
				new Request({
								url: URL, method: "POST", onSuccess: function(responseText, responseXML){
						document.getElementById("matchdata").innerHTML = responseText;
					}, onComplete: function(){
						document.getElementById("loadingEncoding").innerHTML = '';
					}
							}).send();
			}
		}

		function ignoreAllOthers(){
			var fieldNb = document.adminForm.newcustom.length;
			if(isNaN(fieldNb)) fieldNb = 1;

			for(var i = 0; i < fieldNb; i++){
				if(document.getElementById("fieldAssignment" + i).value == 0){
					document.getElementById("fieldAssignment" + i).value = 1;
				}
			}
		}
		-->
	</script>
</div>
