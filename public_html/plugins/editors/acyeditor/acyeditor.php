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

class plgEditorAcyEditor extends JPlugin
{

	function __construct(&$subject, $config){
		parent::__construct($subject, $config);

		include_once(rtrim(JPATH_ADMINISTRATOR,DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_acymailing'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php');

		if(!isset($this->params)){
			$plugin = JPluginHelper::getPlugin('acymailing', 'acyeditor');
			$this->params = new acyParameter( $plugin->params );
		}
	}


	public function onInit()
	{
		$config =& acymailing_config();
		$doc = JFactory::getDocument();
		$doc->addScript(ACYMAILING_JS.'acyeditor.js?v='.@filemtime(ACYMAILING_MEDIA.'js'.DS.'acyeditor.js'));

		$websiteurl = rtrim(JURI::root(),'/').'/';

		if (ACYMAILING_J16){
			$doc->addScript($websiteurl.'plugins/editors/acyeditor/acyeditor/ckeditor/ckeditor.js?v='.@filemtime(ACYMAILING_ROOT.'plugins'.DS.'editors'.DS.'acyeditor'.DS.'acyeditor'.DS.'ckeditor'.DS.'ckeditor.js'));
		} else{
			$doc->addScript($websiteurl.'plugins/editors/acyeditor/ckeditor/ckeditor.js?v='.@filemtime(ACYMAILING_ROOT.'plugins'.DS.'editors'.DS.'acyeditor'.DS.'ckeditor'.DS.'ckeditor.js'));
		}
		$doc->addScript($websiteurl.'media/com_acymailing/js/jquery/jquery-1.9.1.min.js?v='.@filemtime(ACYMAILING_ROOT.'media'.DS.'com_acymailing'.DS.'js'.DS.'jquery'.DS.'jquery-1.9.1.min.js'));
		$doc->addStyleSheet($websiteurl.'media/com_acymailing/js/colorpicker/css/colorpicker.css?v='.@filemtime(ACYMAILING_ROOT.'media'.DS.'com_acymailing'.DS.'js'.DS.'colorpicker'.DS.'css'.DS.'colorpicker.css'));
		$doc->addScript($websiteurl.'media/com_acymailing/js/colorpicker/js/colorpicker.js?v='.@filemtime(ACYMAILING_ROOT.'media'.DS.'com_acymailing'.DS.'js'.DS.'colorpicker'.DS.'js'.DS.'colorpicker.js'));
		$doc->addScript($websiteurl.'media/com_acymailing/js/jquery/jquery-ui.min.js?v='.@filemtime(ACYMAILING_ROOT.'media'.DS.'com_acymailing'.DS.'js'.DS.'jquery'.DS.'jquery-ui.min.js'));
		return '';
	}

	function onSave()
	{
		return;
	}

	function onGetContent($id)
	{
		return "AcyGetData();\n";
	}

	function onSetContent($id, $html)
	{
		$idIframe = "#".$id."_ifr";
		$initialisation = $this->GetInitialisationFunction($id);

		return "document.getElementById('$id').value = $html;$initialisation";
	}

	function onGetInsertMethod($id)
	{
		static $done = false;

		if($done) return true;
		$done = true;

		$doc = JFactory::getDocument();
		$js = "\tfunction jInsertEditorText(text, editor) {
				insertAtCursor(document.getElementById(editor), text);
				}";
		$doc->addScriptDeclaration($js);

		return true;
	}

	function onDisplay($name, $content, $width, $height, $col, $row, $buttons = true, $id = null, $asset = null, $author = null, $params = array())
	{
		if (empty($id)) {
			$id = $name;
		}

		if (is_numeric($width)) {
			$width .= 'px';
		}

		if (is_numeric($height)) {
			$height .= 'px';
		}

		$idIframe = $id."_ifr";
		$initialisation = $this->GetInitialisationFunction($id);

		$contentAvecOnClick = htmlspecialchars_decode($content);
		$editor  = "<textarea name=\"$name\" id=\"$id\" cols=\"$col\" rows=\"$row\" style=\"width:$width; height:$height;display:none\">$content</textarea>\n
					<script type=\"text/javascript\">
						$initialisation
					</script>";

		return $editor;
	}

	function GetInitialisationFunction($id)
	{

		JHtml::_('behavior.modal', 'a.modal');

		$texteSuppression = JText::_('ACYEDITOR_DELETEAREA');
		$tooltipSuppression = JText::_('ACY_DELETE');
		$tooltipEdition = JText::_('ACY_EDIT');
		$urlBase = JURI::root();
		$urlAdminBase = JURI::base();
		$cssurl = JRequest::getVar('acycssfile');
		$forceComplet = (JRequest::getCmd('option') != 'com_acymailing' || JRequest::getCmd('ctrl') == 'template' || JRequest::getCmd('ctrl') == 'list');
		$modeList = (JRequest::getCmd('option') == 'com_acymailing' && JRequest::getCmd('ctrl') == 'list');
		$modeTemplate = (JRequest::getCmd('option') == 'com_acymailing' && JRequest::getCmd('ctrl') == 'template');
		$modeArticle = (JRequest::getCmd('option') == 'com_content' && JRequest::getCmd('view') == 'article');
		$joomla2_5 = ACYMAILING_J16;
		$joomla3 = ACYMAILING_J30;
		$titleTemplateDelete = JText::_('ACYEDITOR_TEMPLATEDELETE');
		$titleTemplateText = JText::_('ACYEDITOR_TEMPLATETEXT');
		$titleTemplatePicture = JText::_('ACYEDITOR_TEMPLATEPICTURE');
		$titleShowAreas = JText::_('ACYEDITOR_SHOWAREAS');
		$app = JFactory::getApplication();
		$isBack = 0;
		if($app->isAdmin()){
			$isBack = 1;
		};
		$tagAllowed = 0;
		$config = acymailing_config();
		if(JRequest::getCmd('option') == 'com_acymailing'
		&& JRequest::getCmd('ctrl') != 'list'
		&& JRequest::getCmd('ctrl') != 'campaign'
		&& acymailing_isAllowed($config->get('acl_tags_view','all'))
		&& JRequest::getCmd('tmpl') != 'component'){
			$tagAllowed = 1;
		}
		$type = 'news';
		if(JRequest::getCmd('ctrl') == 'autonews' || JRequest::getCmd('ctrl') == 'followup'){
			$type = JRequest::getCmd('ctrl');
		}

		$pasteType = $this->params->get('pasteType', 'plain');
		$enterMode = $this->params->get('enterMode', 'br');
		$inlineSource = $this->params->get('inlineSource', 1);
		$doc = JFactory::getDocument();

		$js = "
		acyEnterMode='".$enterMode."';
		pasteType='".$pasteType."';
		urlSite='".$urlBase."';
		defaultText='".str_replace("'", "\'", JText::_('ACYEDITOR_DEFAULTTEXT'))."';
		titleBtnMore='".str_replace("'", "\'", JText::_('ACYEDITOR_TEMPLATEMORE'))."';
		titleBtnDupliAfter='".str_replace("'", "\'", JText::_('ACYEDITOR_DUPLICATE_AFTER'))."';
		tooltipInitAreas='".str_replace("'", "\'", JText::_('ACYEDITOR_REINIT_ZONE_TOOLTIP'))."';
		confirmInitAreas='".str_replace("'", "\'", JText::_('ACYEDITOR_REINIT_ZONE_CONFIRMATION'))."';
		tooltipTemplateSortable='".str_replace("'", "\'", JText::_('ACYEDITOR_SORTABLE_AREA_TOOLTIP'))."';
		var bgroundColorTxt='".str_replace("'", "\'", JText::_('BACKGROUND_COLOUR'))."';
		var confirmDeleteBtnTxt='".str_replace("'", "\'", JText::_('ACY_DELETE'))."';
		var confirmCancelBtnTxt='".str_replace("'", "\'", JText::_('ACY_CANCEL'))."';
		inlineSource='".$inlineSource."';
		";

		$doc->addScriptDeclaration($js);

		$ckEditorFileVersion = @filemtime(ACYMAILING_ROOT.'plugins'.DS.'editors'.DS.'acyeditor'.DS.'acyeditor'.DS.'ckeditor'.DS.'ckeditor.js');
		return "Initialisation(\"$id\", \"$type\", \"$urlBase\", \"$urlAdminBase\", \"$cssurl\", \"$forceComplet\", \"$modeList\", \"$modeTemplate\", \"$modeArticle\", \"$joomla2_5\", \"$joomla3\", \"$isBack\", \"$tagAllowed\", \"$texteSuppression\", \"$tooltipSuppression\", \"$tooltipEdition\", \"$titleTemplateDelete\", \"$titleTemplateText\", \"$titleTemplatePicture\", \"$titleShowAreas\", \"$ckEditorFileVersion\");\n";
	}
}
