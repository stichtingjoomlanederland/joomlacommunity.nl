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

class unsubType{
	function __construct(){

		$query = 'SELECT `subject`, `mailid` FROM '.acymailing_table('mail').' WHERE `type`= \'unsub\'';
		$db = JFactory::getDBO();
		$db->setQuery($query);
		$messages = $db->loadObjectList();

		$this->values = array();
		$this->values[] = JHTML::_('select.option', '0', JText::_('NO_UNSUB_MESSAGE') );
		foreach($messages as $oneMessage){
			$this->values[] = JHTML::_('select.option', $oneMessage->mailid, '['.JText::_('ACY_ID').' '.$oneMessage->mailid.'] '.$oneMessage->subject);
		}

		$js = "function changeMessage(idField,value){
			linkEdit = idField+'_edit';
			if(value>0){
				window.document.getElementById(linkEdit).href = 'index.php?option=com_acymailing&tmpl=component&ctrl=email&task=edit&mailid='+value;
				window.document.getElementById(linkEdit).style.display = 'inline';
			}else{
				window.document.getElementById(linkEdit).style.display = 'none';
			}
		}";
		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration( $js );

	}

	function display($value){
		JHTML::_('behavior.modal','a.modal');
		$linkEdit = 'index.php?option=com_acymailing&amp;tmpl=component&amp;ctrl=email&amp;task=edit&amp;type=unsub&amp;mailid='.$value;
		$linkAdd = 'index.php?option=com_acymailing&amp;tmpl=component&amp;ctrl=email&amp;task=add&amp;type=unsub';
		$style = empty($value) ? 'style="display:none"' : '';
		$text = ' <a '.$style.' class="modal" id="unsub_edit" title="'.JText::_('EDIT_EMAIL',true).'"  href="'.$linkEdit.'" rel="{handler: \'iframe\', size:{x:800, y:500}}"><img class="icon16" src="'.ACYMAILING_IMAGES.'icons/icon-16-edit.png" alt="'.JText::_('EDIT_EMAIL',true).'"/></a>';
		$text .= ' <a class="modal" id="unsub_add" title="'.JText::_('CREATE_EMAIL',true).'"  href="'.$linkAdd.'" rel="{handler: \'iframe\', size:{x:800, y:500}}"><img class="icon16" src="'.ACYMAILING_IMAGES.'icons/icon-16-add.png" alt="'.JText::_('CREATE_EMAIL',true).'"/></a>';

		return JHTML::_('select.genericlist',   $this->values, 'data[list][unsubmailid]', 'class="inputbox" size="1" onchange="changeMessage(\'unsub\',this.value);"', 'value', 'text', (int) $value ).$text;
	}
}
