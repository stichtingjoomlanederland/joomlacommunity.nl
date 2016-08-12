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

class listslanguagesType{
	var $multipleLang = false;

	function __construct(){
		jimport('joomla.filesystem.folder');

		$path = JLanguage::getLanguagePath(JPATH_ROOT);
		$dirs = JFolder::folders($path);

		$this->languages = array();
		foreach($dirs as $dir){
			if(strlen($dir) != 5 || $dir == "xx-XX") continue;

			$xmlFiles = JFolder::files($path.DS.$dir, '^([-_A-Za-z]*)\.xml$');
			$xmlFile = reset($xmlFiles);
			if(empty($xmlFile)) continue;

			$data = JApplicationHelper::parseXMLLangMetaFile($path.DS.$dir.DS.$xmlFile);

			$oneLanguage = new stdClass();
			$oneLanguage->language = strtolower($dir);
			$oneLanguage->name = empty($data['name']) ? $dir : $data['name'];
			$this->languages[] = $oneLanguage;
		}

		if(count($this->languages) < 2) return;

		$this->multipleLang = true;

		$this->choice = array();
		$this->choice[] = JHTML::_('select.option', 'all', JText::_('ACY_ALL'));
		$this->choice[] = JHTML::_('select.option', 'special', JText::_('ACY_CUSTOM'));

		$js = "function updateLanguages(){
			choice = eval('document.adminForm.choice_languages');
			choiceValue = 'special';
			for (var i=0; i < choice.length; i++){
				 if (choice[i].checked){
					 choiceValue = choice[i].value;
				}
			}

			hiddenVar = document.getElementById('hidden_languages');
			if(choiceValue != 'special'){
				hiddenVar.value = choiceValue;
				document.getElementById('div_languages').style.display = 'none';
			}else{
				document.getElementById('div_languages').style.display = 'block';
				specialVar = eval('document.adminForm.special_languages');
				finalValue = '';
				for (var i=0; i < specialVar.length; i++){
					if (specialVar[i].checked){
							 finalValue += specialVar[i].value+',';
					}
				}
				hiddenVar.value = finalValue;
			}

		}";

		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration($js);
	}

	function display($map, $values){
		$js = 'window.addEvent(\'domready\', function(){ updateLanguages(); });';
		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration($js);


		$choiceValue = ($values == 'all') ? $values : 'special';
		$return = JHTML::_('acyselect.radiolist', $this->choice, "choice_languages", 'onclick="updateLanguages();"', 'value', 'text', $choiceValue);
		$return .= '<input type="hidden" name="data[list][languages]" id="hidden_languages" value="'.$values.'"/>';
		$valuesArray = explode(',', $values);
		$listLang = '<div style="display:none" id="div_languages"><table class="acymailing_smalltable">';
		foreach($this->languages as $oneLanguage){
			$listLang .= '<tr><td>';
			$listLang .= '<input type="checkbox" onclick="updateLanguages();" value="'.$oneLanguage->language.'" '.(in_array($oneLanguage->language, $valuesArray) ? 'checked' : '').' name="special_languages" id="special_languages_'.$oneLanguage->language.'"/>';
			$listLang .= '</td><td><label for="special_languages_'.$oneLanguage->language.'">'.$oneLanguage->name.'</label></td></tr>';
		}
		$listLang .= '</table></div>';
		$return .= $listLang;
		return $return;
	}
}
