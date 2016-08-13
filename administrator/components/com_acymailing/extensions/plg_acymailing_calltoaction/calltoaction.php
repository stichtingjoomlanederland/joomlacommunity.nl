<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2016 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
defined('_JEXEC') or die('Restricted access');

class plgAcymailingCalltoaction extends JPlugin{

	function __construct(&$subject, $config){
		parent::__construct($subject, $config);
		$this->name = 'calltoaction';
		if(!isset($this->params)){
			$plugin = JPluginHelper::getPlugin('acymailing', $this->name);
			$this->params = new acyParameter($plugin->params);
		}

		$this->db = JFactory::getDBO();
		$this->acypluginsHelper = acymailing_get('helper.acyplugins');
	}

	function acymailing_getPluginType(){
		$onePlugin = new stdClass();
		$onePlugin->name = 'Call To Action';
		$onePlugin->function = 'acymailing_'.$this->name.'_show';
		$onePlugin->help = 'plugin-'.$this->name;
		return $onePlugin;
	}

	function acymailing_calltoaction_show(){
		$app = JFactory::getApplication();
		$pageInfo = new stdClass();
		$paramBase = ACYMAILING_COMPONENT.'.'.$this->name;

		$pageInfo->filter_type = $app->getUserStateFromRequest($paramBase.".filter_type", 'filter_type', '', 'int');
		$pageInfo->readmore = $app->getUserStateFromRequest($paramBase.".readmore", 'readmore', '1', 'string');
		$pageInfo->wrap = $app->getUserStateFromRequest($paramBase.".wrap", 'wrap', '0', 'string');
		$pageInfo->clickable = $app->getUserStateFromRequest($paramBase.".clickable", 'clickable', '1', 'string');
		?>
		<script language="javascript" type="text/javascript">
			<!--
			function previewBtn(){
				var header = '';
				var body = '';
				var footer = '';
				var alignment;
				var tdAndBackColor = '<td style="background-color:' + document.getElementById('color1').value + ';';
				var leftIcon;
				var rightIcon;
				var size = document.getElementById('textsize').value;
				if(isNaN(size)) size = 18;
				var link = document.getElementById('btnlink').value;
				var text = document.getElementById('btntext').value;
				var width = document.getElementById('btnwidth').value;
				if(isNaN(width)) width = 200;
				width = ' width:' + width + 'px;" ';
				var border = document.getElementById('borderradius').value;
				if(isNaN(border)) border = 0;


				if(link.length === 0){
					document.getElementById('btnlink').style.border = '1px solid red';
				}else{
					document.getElementById('btnlink').style.border = '';
				}

				if(link.substr(0, 8).indexOf(':') == -1) link = 'http://' + link;
				link = '<a href="' + link + '" style="cursor:pointer;text-decoration:none;color:' + document.getElementById('color0').value + '; font-size:' + size + 'px;" target="_blank">';

				if(text.length === 0){
					document.getElementById('btntext').style.border = '1px solid red';
				}else{
					document.getElementById('btntext').style.border = '';
				}

				for(var i = 0; i < document.adminForm.alignment.length; i++){
					if(document.adminForm.alignment[i].checked){
						alignment = document.adminForm.alignment[i].value;
					}
				}

				for(var i = 0; i < document.adminForm.leftIcon.length; i++){
					if(document.adminForm.leftIcon[i].checked){
						leftIcon = document.adminForm.leftIcon[i].value;
					}
				}

				for(var i = 0; i < document.adminForm.rightIcon.length; i++){
					if(document.adminForm.rightIcon[i].checked){
						rightIcon = document.adminForm.rightIcon[i].value;
					}
				}

				if(alignment == 'center'){
					alignment = 'align="center" style="margin:auto;"';
				}else{
					alignment = 'align="' + alignment + '"';
				}

				var tag = '<table class="actionbutton" cellspacing="0" ' + alignment + '>';

				var emptyTd = tdAndBackColor + '" height="5" width="30"></td>';
				var image = '<img alt="" src="<?php echo JURI::root(); ?>media/com_acymailing/calltoaction/';

				header += '<tr class="toprow">' + tdAndBackColor + ' border-radius:' + border + 'px 0px 0px 0px" height="5" width="10"></td>';
				body += '<tr class="contentrow">' + tdAndBackColor + '" height="30" width="10"></td>';
				footer += '<tr class="bottomrow">' + tdAndBackColor + ' border-radius:0px 0px 0px ' + border + 'px" height="5" width="10"></td>';

				if(leftIcon != 'none'){
					header += emptyTd;
					body += tdAndBackColor + ' text-align:center; line-height:0px" width="30">' + link + image + leftIcon + '"></a></td>';
					footer += emptyTd;
				}

				header += tdAndBackColor + width + 'height="5"></td>';
				body += tdAndBackColor + ' text-align:center;' + width + 'height="30">' + link + text + '</a></td>';
				footer += tdAndBackColor + width + 'height="5"></td>';

				if(rightIcon != 'none'){
					header += emptyTd;
					body += tdAndBackColor + ' text-align:center; line-height:0px" width="30">' + link + image + rightIcon + '"></a></td>';
					footer += emptyTd;
				}

				header += tdAndBackColor + ' border-radius:0px ' + border + 'px 0px 0px" height="5" width="10"></td></tr>';
				body += tdAndBackColor + '" height="30" width="10"></td></tr>';
				footer += tdAndBackColor + ' border-radius:0px 0px ' + border + 'px 0px" height="5" width="10"></td></tr>';

				tag += header + body + footer + '</table>';

				document.getElementById('preview').innerHTML = tag;
				setTag(tag);
			}
			//-->
		</script>
		<?php $colorBox = acymailing_get('type.color'); ?>
		<div class="onelineblockoptions">
			<table width="100%" class="acymailing_table">
				<tr>
					<td><?php echo JText::_('ACY_BUTTON_TEXT'); ?></td>
					<td><input type="text" id="btntext" style="width:150px;" value="" onkeyup="previewBtn();"/></td>
					<td><?php echo JText::_('CAPTCHA_COLOR'); ?></td>
					<td><?php echo $colorBox->displayAll('0', 'textcolor', '#ffffff'); ?></td>
				</tr>
				<tr>
					<td><?php echo JText::_('ACY_LINK'); ?></td>
					<td><input type="text" id="btnlink" style="width:150px;" value="" placeholder="http://..." onchange="previewBtn();"/></td>
					<td><?php echo JText::_('BACKGROUND_COLOUR'); ?></td>
					<td><?php echo $colorBox->displayAll('1', 'backcolor', '#33bcf0'); ?></td>
				</tr>
				<tr>
					<td><?php echo JText::_('CAPTCHA_WIDTH'); ?></td>
					<td><input type="text" id="btnwidth" style="width:35px;" value="200" onkeyup="previewBtn();"/>px</td>
					<td><?php echo JText::_('ALIGNMENT'); ?></td>
					<td>
						<?php
						$alignment = array(JHTML::_('select.option', "left", JText::_('ACY_LEFT')), JHTML::_('select.option', "center", JText::_('ACY_CENTER')), JHTML::_('select.option', "right", JText::_('ACY_RIGHT')));
						echo JHTML::_('acyselect.radiolist', $alignment, 'alignment', 'size="1" onclick="previewBtn();"', 'value', 'text', 'center');
						?>
					</td>
				</tr>
				<tr>
					<td><?php echo JText::_('FIELD_SIZE'); ?></td>
					<td><input type="text" id="textsize" style="width:35px;" value="18" onkeyup="previewBtn();"/>px</td>
					<td><?php echo JText::_('ACY_BORDER'); ?></td>
					<td><input type="text" id="borderradius" style="width:35px;" value="6" onkeyup="previewBtn();"/>px</td>
				</tr>
				<?php
				jimport('joomla.filesystem.folder');
				$allIcons = JFolder::files(ACYMAILING_MEDIA.'calltoaction', '\.png$');
				?>
				<tr style="background-color: #ABBFDE;">
					<td nowrap="nowrap" valign="top" style="color:white;"><?php echo JText::_('ACY_LEFT'); ?></td>
					<td colspan="3" style="color:white;">
						<?php
						foreach($allIcons as $oneIcon){
							echo '<label style="margin-left:10px" for="leftIcon'.$oneIcon.'"><input style="display:none" type="radio" name="leftIcon" value="'.$oneIcon.'" id="leftIcon'.$oneIcon.'" onclick="previewBtn();"/><img onclick="document.getElementById(\'leftIcon'.$oneIcon.'\').click();" style="width:15px;" alt="" src="'.JURI::root().'media/com_acymailing/calltoaction/'.$oneIcon.'" /></label>';
						}
						?>
						<label style="margin-left:10px" for="leftIconnone"><input style="display:none" type="radio" name="leftIcon" value="none" id="leftIconnone" onclick="previewBtn();" checked="checked"/><?php echo JText::_('ACY_NONE'); ?></label>
					</td>
				</tr>
				<tr style="background-color: #ABBFDE;">
					<td nowrap="nowrap" valign="top" style="color:white;"><?php echo JText::_('ACY_RIGHT'); ?></td>
					<td colspan="3" style="color:white;">
						<?php
						foreach($allIcons as $oneIcon){
							echo '<label style="margin-left:10px" for="rightIcon'.$oneIcon.'"><input style="display:none" type="radio" name="rightIcon" value="'.$oneIcon.'" id="rightIcon'.$oneIcon.'" onclick="previewBtn();"/><img onclick="document.getElementById(\'rightIcon'.$oneIcon.'\').click();" style="width:15px;" alt="" src="'.JURI::root().'media/com_acymailing/calltoaction/'.$oneIcon.'" /></label>';
						}
						?>
						<label style="margin-left:10px" for="rightIconnone"><input style="display:none" type="radio" name="rightIcon" value="none" id="rightIconnone" onclick="previewBtn();" checked="checked"/><?php echo JText::_('ACY_NONE'); ?></label>
					</td>
				</tr>
			</table>
			<table width="100%">
				<tr>
					<td colspan="4" style="padding:15px;" id="preview"></td>
				</tr>
			</table>
		</div>
		<script>
			document.getElementById("colordiv0").addEventListener("click", previewBtn);
			document.getElementById("colordiv1").addEventListener("click", previewBtn);
		</script>
		<?php
	}
}
