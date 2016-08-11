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

class SimpleMailViewSimpleMail extends acymailingView{
	public function display($tpl = null){
		$function = $this->getLayout();
		if(method_exists($this, $function)) $this->$function();

		parent::display($tpl);
	}

	public function edit(){
		$toolbar = acymailing_get('helper.toolbar');
		$toolbar->setTitle(JText::_('SIMPLE_SENDING'), 'simplemail&task=edit');
		$toolbar->popup('template', JText::_('ACY_TEMPLATE'), "index.php?option=com_acymailing&ctrl=template&task=theme&tmpl=component", 750, 550);
		$toolbar->popup('tag', JText::_('TAGS'), JURI::base()."index.php?option=com_acymailing&ctrl=tag&task=tag&tmpl=component&type=news", 750, 550);
		$toolbar->divider();
		$toolbar->custom('send', JText::_('SEND'), 'send', false);
		$toolbar->divider();
		$toolbar->help('simplesending#edit');
		$toolbar->display();

		$this->testreceiverType = acymailing_get('type.testreceiver');
		$infos = new stdClass();
		$infos->test_selection = $infos->test_group = $infos->test_emails = '';
		$infos->test_html = '';
		$this->infos = $infos;

		$session = JFactory::getSession();
		$this->tempid = $session->get('simplesending_tempid', 0);
		$session->clear('simplesending_tempid');
		$this->subject = $session->get('simplesending_subject', '');
		$session->clear('simplesending_subject');
		$content = $session->get('simplesending_body', '');
		$session->clear('simplesending_body');


		$editor = acymailing_get('helper.editor');
		$editor->name = 'editor_body';
		$editor->content = $content;
		if($this->tempid > 0){
			$editor->setTemplate($this->tempid);
			$editor->setEditorStylesheet($this->tempid);
		}
		$this->editor = $editor;

		$this->insertScript($editor);
		$this->insertCSS();
	}

	private function insertScript($editor){
		$script = '';
		$script .= "function changeTemplate(newhtml,newtext,newsubject,stylesheet,fromname,fromemail,replyname,replyemail,tempid){
			if(newhtml.length>2){".$editor->setContent('newhtml')."}
			var vartextarea =$('altbody'); if(newtext.length>2) vartextarea.innerHTML = newtext;
			document.getElementById('tempid').value = tempid;
			if(fromname.length>1){
				fromname = fromname.replace('&amp;', '&');
				document.getElementById('fromname').value = fromname;
			}
			if(fromemail.length>1){document.getElementById('fromemail').value = fromemail;}
			if(replyname.length>1){
				replyname = replyname.replace('&amp;', '&');
				document.getElementById('replyname').value = replyname;
			}
			if(replyemail.length>1){document.getElementById('replyemail').value = replyemail;}
			if(newsubject.length>1){
				newsubject = newsubject.replace('&amp;', '&');
				document.getElementById('subject').value = newsubject;
			}
			".$editor->setEditorStylesheet('tempid')."
		}
		";


		$script .= "var zoneEditor = 'editor_body';";

		$script .= "var previousSelection = false;
			function insertTag(tag){
				if(zoneEditor == 'editor_body'){
					try{
						jInsertEditorText(tag,'editor_body',previousSelection);
						return true;
					} catch(err){
						alert('Your editor does not enable AcyMailing to automatically insert the tag, please copy/paste it manually in your Newsletter');
						return false;
					}
				} else{
					try{
						simpleInsert(document.getElementById(zoneToTag), tag);
						return true;
					} catch(err){
						alert('Error inserting the tag in the '+ zoneToTag + 'zone. Please copy/paste it manually in your Newsletter.');
						return false;
					}
				}
			}
			";

		$script .= "function simpleInsert(myField, myValue) {
				if (document.selection) {
					myField.focus();
					sel = document.selection.createRange();
					sel.text = myValue;
				} else if (myField.selectionStart || myField.selectionStart == '0') {
					var startPos = myField.selectionStart;
					var endPos = myField.selectionEnd;
					myField.value = myField.value.substring(0, startPos)
						+ myValue
						+ myField.value.substring(endPos, myField.value.length);
				} else {
					myField.value += myValue;
				}
			}";

		$script .= '
		window.addEventListener("load", function(event) {
			var sendButton = document.getElementById("toolbar-send");
			var onClick = sendButton.onclick;
			sendButton.onclick = "";
			sendButton.addEventListener("click", function() { var val = document.getElementById("message_receivers").value; if(val != ""){ setUser(val); } onClick()});

			var emailField = document.getElementById("message_receivers");
			emailField.addEventListener("keypress", function(event) {var char = event.which || event.keyCode; if(this.value != "" && char == 13) {setUser(this.value)}});
		});
		';


		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration($script);
	}

	private function insertCSS(){
		$css = '
			#test_selection_chzn, #test_selection {
				display: none !important;
			}

			.mail-part input[type="text"] {
				width: 99%;
			}

			#usersSelected {
				display: inline !important;
			}

			#message_receivers {
				margin: 0 25px 0 0 !important;
			}

			.mail-part {
				width: 100%;
				margin: 15px auto;
				background: white;
				padding: 15px;
			}

			#subject {
				margin: 15px 0 35px 0!important;
			}

			#message_receivers {
				width: 40% !important;
			}

			.mail-information {
				width: 80%;
				display: block;
				margin: 0 auto;
			}

			.mail-information:after{
				content: "";
				display: block;
				clear: both;
			}

		';

		$doc = JFactory::getDocument();
		$doc->addStyleDeclaration($css);
	}

}
