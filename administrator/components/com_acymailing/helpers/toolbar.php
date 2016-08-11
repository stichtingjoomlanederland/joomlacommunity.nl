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

class acytoolbarHelper{
	var $buttons = array();
	var $buttonOptions = array();
	var $title = '';
	var $titleLink = '';

	var $topfixed = true;

	var $htmlclass = '';

	function setTitle($name, $link = ''){
		$this->title = $name;
		$this->titleLink = $link;
		acymailing_setPageTitle($name);
	}

	function custom($task, $text, $class, $listSelect = true, $onClick = ''){

		$confirm = !ACYMAILING_J16 ? JText::sprintf('PLEASE MAKE A SELECTION FROM THE LIST TO', strtolower(JText::_('ACY_'.strtoupper($task)))) : JText::_('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST');
		$submit = !ACYMAILING_J16 ? "submitbutton('".$task."')" : "Joomla.submitbutton('".$task."')";
		$js = !empty($listSelect) ? "if(document.adminForm.boxchecked.value==0){alert('".str_replace(array("'", '"'), array("\'", '\"'), $confirm)."');return false;}else{".$submit."}" : $submit;

		$onClick = !empty($onClick) ? $onClick : $js;

		if(empty($this->buttonOptions)){
			$this->buttons[] = '<button id="toolbar-'.$class.'" onclick="'.$onClick.'" class="acytoolbar_'.$class.'" title="'.$text.'"><i class="acyicon-'.$class.'"></i><span>'.$text.'</span></button>';
			return;
		}

		$dropdownOptions = '<ul class="buttonOptions" style="padding: 0px; margin: 0px; text-align: left;">';
		foreach($this->buttonOptions as $oneOption){
			$dropdownOptions .= '<li>'.$oneOption.'</li>';
		}
		$dropdownOptions .= '</ul>';

		$buttonArea = '<button onclick="'.$onClick.'" class="acytoolbar_'.$class.'" title="'.$text.'"><i class="acyicon-'.$class.'"></i><span>'.$text.'</span></button>';


		$this->buttons[] = '<div style="display:inline;" class="subbuttonactions">'.$buttonArea.'<span class="acytoolbar_hover"><span style="vertical-align: top; display:inline-block; padding-top:10px;" class="acyicon-down"></span><span class="acytoolbar_hover_display">'.$dropdownOptions.'</span></span></div>';

		$this->buttonOptions = array();
	}

	function display(){
		$classCtrl = JRequest::getCmd('ctrl', '');
		echo '<div '.(empty($this->topfixed) ? '' : 'id="acymenu_top"').' class="acytoolbarmenu donotprint '.(empty($this->topfixed) ? '' : 'acyaffix-top ').(!empty($classCtrl) ? 'acytopmenu_'.$classCtrl.' ' : '').$this->htmlclass.'" >';
		if(!empty($this->title)){
			$title = htmlspecialchars($this->title, ENT_COMPAT, 'UTF-8');
			if(!empty($this->titleLink)) $title = '<a style="color:white;" href="'.acymailing_completeLink($this->titleLink).'">'.$title.'</a>';
			echo '<span class="acytoolbartitle">'.$title.'</span>';
		}
		echo '<div class="acytoolbarmenu_menu">';
		echo implode(' ', $this->buttons);
		echo '</div></div>';

		$types = array('acymessagesuccess' => 'success', 'acymessageinfo' => 'info', 'acymessagewarning' => 'warning', 'acymessageerror' => 'error', 'acymessagenotice' => 'notice', 'acymessagemessage' => 'message');
		foreach($types as $key => $type){
			if(empty($_SESSION[$key])) continue;
			acymailing_display($_SESSION[$key], $type);
			unset($_SESSION[$key]);
		}
	}

	function add(){
		$this->custom('add', JText::_('ACY_NEW'), 'new', false);
	}

	function edit(){
		$this->custom('edit', JText::_('ACY_EDIT'), 'edit', true);
	}

	function delete(){
		$selectMessage = ACYMAILING_J16 ? JText::_('JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST') : JText::sprintf('PLEASE MAKE A SELECTION FROM THE LIST TO', strtolower(JText::_('ACY_DELETE')));
		$onClick = 'if(document.adminForm.boxchecked.value==0){
						alert(\''.str_replace("'", "\\'", $selectMessage).'\');
					}else{
						if(confirm(\''.str_replace("'", "\\'", JText::_('ACY_VALIDDELETEITEMS', true)).'\')){
							'.(ACYMAILING_J16 ? 'Joomla.' : '').'submitbutton(\'remove\');
						}
					}';
		$this->custom('remove', JText::_('ACY_DELETE'), 'delete', true, $onClick);
	}

	function copy(){
		$this->custom('copy', JTEXT::_('ACY_COPY'), 'copy', true);
	}

	function link($link, $text, $class){
		$onClick = "location.href='".$link."';return false;";
		$this->custom('link', $text, $class, false, $onClick);
	}

	function help($helpname, $anchor = ''){
		$doc = JFactory::getDocument();
		$config =& acymailing_config();
		$level = $config->get('level');

		$url = ACYMAILING_HELPURL.$helpname.'&level='.$level.(!empty($anchor) ? '#'.$anchor : '');
		$iFrame = "'<iframe frameborder=\"0\" src=\'$url\' width=\'100%\' height=\'100%\' scrolling=\'auto\'></iframe>'";

		$js = "var openHelp = true;
				function displayDoc(){
					var box=document.getElementById('iframedoc');
					if(openHelp){
						box.innerHTML = ".$iFrame.";
						box.style.display = 'block';
						box.className = 'help_open';
					}else{
						box.style.display = 'none';
						box.className = 'help_close';
					}
					openHelp = !openHelp;
				}";
		$doc->addScriptDeclaration($js);

		$onClick = 'displayDoc();return false;';

		$this->custom('help', JText::_('ACY_HELP'), 'help', false, $onClick);
	}

	function divider(){
		$this->buttons[] = '<span class="acytoolbar_divider"></span>';
	}

	function cancel(){
		$this->custom('cancel', JText::_('ACY_CANCEL'), 'cancel', false);
	}

	function save(){
		$this->custom('save', JText::_('ACY_SAVE'), 'save', false);
	}

	function apply(){
		$this->custom('apply', JText::_('ACY_APPLY'), 'apply', false);
	}

	function popup($name = '', $text = '', $url = '', $width = 640, $height = 480){
		$this->buttons[] = $this->_popup($name, $text, $url, $width, $height);
	}

	function directPrint(){
		$this->buttons[] = $this->_directPrint();
	}

	private function _popup($name = '', $text = '', $url = '', $width = 640, $height = 480){
		$params = array();
		$onClick = '';
		if(in_array($name, array('ABtesting', 'action'))){
			$doc = JFactory::getDocument();
			if(empty($doc->_script['text/javascript']) || strpos($doc->_script['text/javascript'], 'getAcyPopupUrl') === false){
				$js = "
				function getAcyPopupUrl(mylink){
					i = 0;
					mymailids = '';
					while(window.document.getElementById('cb'+i)){
						if(window.document.getElementById('cb'+i).checked) mymailids += window.document.getElementById('cb'+i).value+',';
						i++;
					}
					mylink += mymailids.slice(0,-1);
					return mylink;
				}";
				$doc->addScriptDeclaration($js);
			}

			if($name == 'ABtesting'){
				$mylink = 'index.php?option=com_acymailing&ctrl=newsletter&task=abtesting&tmpl=component&mailid=';
				$url = JURI::base()."index.php?option=com_acymailing&ctrl=newsletter&task=abtesting&tmpl=component";
			}elseif($name == 'action'){
				$mylink = 'index.php?option=com_acymailing&ctrl=filter&tmpl=component&subid=';
				$url = JURI::base()."index.php?option=com_acymailing&ctrl=filter&tmpl=component";
			}

			$onClick = ' onclick="this.href=getAcyPopupUrl(\''.$mylink.'\');"';
			$params['url'] = '\'+getAcyPopupUrl(\''.$mylink.'\')+\'';
		}else{
			$params['url'] = $url;
		}

		if(!ACYMAILING_J30){
			JHTML::_('behavior.modal', 'a.modal');
			$html = '<a'.$onClick.' id="a_'.$name.'" class="modal" href="'.$url.'" rel="{handler: \'iframe\', size: {x: '.$width.', y: '.$height.'}}">';
			$html .= '<button class="acytoolbar_'.$name.'" title="'.$text.'"><i class="acyicon-'.$name.'"></i><span>'.$text.'</span></button></a>';
			return $html;
		}

		$html = '<button id="toolbar-'.$name.'" class="acytoolbar_'.$name.'" data-toggle="modal" data-target="#modal-'.$name.'" title="'.$text.'"><i class="acyicon-'.$name.'"></i><span>'.$text.'</span></button>';

		$params['height'] = $height;
		$params['width'] = $width;
		$params['title'] = $text;

		$modalHtml = JHtml::_('bootstrap.renderModal', 'modal-'.$name, $params);

		$html .= str_replace(array('id="modal-'.$name.'"', 'class="modal-body"', 'id="modal-'.$name.'-container"', 'class="iframe"'), array('id="modal-'.$name.'" style="width:82%;height:84%;margin-left:9%;left:0;top:0px;margin-top:50px;"', 'class="modal-body" style="height:82%;max-height:none;"', 'id="modal-'.$name.'-container" style="height:100%"', 'class="iframe" style="width:100%"'), $modalHtml);
		$html .= '<script>'."\r\n".'jQuery(document).ready(function(){jQuery("#modal-'.$name.'").appendTo(jQuery(document.body));});'."\r\n".'</script>';
		$html .= '<style type="text/css">#modal-'.$name.' iframe.iframe{ height: 100%; }</style>';

		return $html;
	}

	private function _directPrint(){

		$doc = JFactory::getDocument();
		$doc->addStyleSheet(ACYMAILING_CSS.'acyprint.css?v='.filemtime(ACYMAILING_MEDIA.'css'.DS.'acyprint.css'), 'text/css', 'print');

		$function = "if(document.getElementById('iframepreview')){document.getElementById('iframepreview').contentWindow.focus();document.getElementById('iframepreview').contentWindow.print();}else{window.print();}return false;";

		return '<button class="acytoolbar_print" onclick="'.$function.'" title="'.JText::_('ACY_PRINT', true).'"><i class="acyicon-print"></i><span>'.JText::_('ACY_PRINT', true).'</span></button>';
	}

	function addButtonOption($task, $text, $class, $listSelect){

		$confirm = !ACYMAILING_J16 ? 'PLEASE MAKE A SELECTION FROM THE LIST TO' : 'JLIB_HTML_PLEASE_MAKE_A_SELECTION_FROM_THE_LIST';
		$submit = !ACYMAILING_J16 ? "submitbutton('".$task."')" : "Joomla.submitbutton('".$task."')";
		$js = !empty($listSelect) ? "if(document.adminForm.boxchecked.value==0){alert('".str_replace(array("'", '"'), array("\'", '\"'), JText::_($confirm))."');return false;}else{".$submit."}" : $submit;

		$onClick = !empty($onClick) ? $onClick : $js;

		$this->buttonOptions[] = '<button onclick="'.$onClick.'" class="acytoolbar_'.$class.'" title="'.$text.'"><span class="acyicon-'.$class.'"></span><span>'.$text.'</span></button>';
	}
}
