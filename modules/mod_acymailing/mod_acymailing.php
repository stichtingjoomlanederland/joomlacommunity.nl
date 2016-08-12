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

if(!include_once(rtrim(JPATH_ADMINISTRATOR, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_acymailing'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php')){
	echo 'This module can not work without the AcyMailing Component';
	return;
};

$doc = JFactory::getDocument();
$config = acymailing_config();
$overridedesign = preg_replace('#[^a-z0-9_]#i', '', JRequest::getCmd('design'));
if(!empty($overridedesign)){
	if($overridedesign == 'popup') $overridedesign = '';
	$params->set('effect', 'mootools-box');
}

switch($params->get('redirectmode', '0')){
	case 1 :
		$redirectUrl = acymailing_completeLink('lists', false, true);
		$redirectUrlUnsub = $redirectUrl;
		break;
	case 2 :
		$redirectUrl = $params->get('redirectlink');
		$redirectUrlUnsub = $params->get('redirectlinkunsub');
		break;
	default :
		if(isset($_SERVER["REQUEST_URI"])){
			$requestUri = $_SERVER["REQUEST_URI"];
		}else{
			$requestUri = $_SERVER['PHP_SELF'];
			if(!empty($_SERVER['QUERY_STRING'])) $requestUri = rtrim($requestUri, '/').'?'.$_SERVER['QUERY_STRING'];
		}
		$redirectUrl = (((!empty($_SERVER['HTTPS']) AND strtolower($_SERVER['HTTPS']) == "on") || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://').$_SERVER["HTTP_HOST"].$requestUri;
		$redirectUrlUnsub = $redirectUrl;
		if($params->get('effect', 'normal') == 'mootools-box') $redirectUrlUnsub = $redirectUrl = '';
}

$regex = trim(preg_replace('#[^a-z0-9\|\.]#i', '', $config->get('module_redirect')), '|');
if($regex != 'all'){
	preg_match('#^(https?://)?(www.)?([^/]*)#i', $redirectUrl, $resultsurl);
	$domainredirect = preg_replace('#[^a-z0-9\.]#i', '', @$resultsurl[3]);
	preg_match('#^(https?://)?(www.)?([^/]*)#i', $redirectUrlUnsub, $resultsurl);
	$domainredirectunsub = preg_replace('#[^a-z0-9\.]#i', '', @$resultsurl[3]);
	$saveRedir = false;
	if(!empty($domainredirect) && !preg_match('#^'.$regex.'$#i', $domainredirect)){
		$regex .= '|'.$domainredirect;
		$saveRedir = true;
	}
	if(!empty($domainredirectunsub) && !preg_match('#^'.$regex.'$#i', $domainredirectunsub)){
		$regex .= '|'.$domainredirectunsub;
		$saveRedir = true;
	}
	if($saveRedir){
		$newConfig = new stdClass();
		$newConfig->module_redirect = $regex;
		$config->save($newConfig);
	}
}

$formName = acymailing_getModuleFormName();
if(!empty($overridedesign)){
	$params->set('includejs', 'module');
}

$introText = $params->get('introtext');
$postText = $params->get('finaltext');
$mootoolsIntro = $params->get('mootoolsintro', '');
if(!empty($introText) && preg_match('#^[A-Z_]*$#', $introText)){
	$introText = JText::_($introText);
}
if(!empty($postText) && preg_match('#^[A-Z_]*$#', $postText)){
	$postText = JText::_($postText);
}
if(!empty($mootoolsIntro) && preg_match('#^[A-Z_]*$#', $mootoolsIntro)){
	$mootoolsIntro = JText::_($mootoolsIntro);
}


if($params->get('effect') == 'mootools-box' AND JRequest::getString('tmpl') != 'component'){
	$mootoolsButton = $params->get('mootoolsbutton', '');
	if(empty($mootoolsButton)){
		$mootoolsButton = JText::_('SUBSCRIBE');
	}else{
		if(!empty($mootoolsButton) && preg_match('#^[A-Z_]*$#', $mootoolsButton)){
			$mootoolsButton = JText::_($mootoolsButton);
		}
	}

	$moduleCSS = $config->get('css_module', 'default');
	if(!empty($moduleCSS)){
		$doc->addStyleSheet(ACYMAILING_CSS.'module_'.$moduleCSS.'.css?v='.filemtime(ACYMAILING_MEDIA.'css'.DS.'module_'.$moduleCSS.'.css'));
	}
	JHTML::_('behavior.modal', 'a.modal');
	require(JModuleHelper::getLayoutPath('mod_acymailing', 'popup'));
	return;
}
acymailing_initModule($params->get('includejs', 'header'), $params);

$userClass = acymailing_get('class.subscriber');
$identifiedUser = null;
$connectedUser = JFactory::getUser();
if($params->get('loggedin', 1) && !empty($connectedUser->email)){
	$identifiedUser = $userClass->get($connectedUser->email);
}

$visibleLists = trim($params->get('lists', 'None'));
$hiddenLists = trim($params->get('hiddenlists', 'All'));
$visibleListsArray = array();
$hiddenListsArray = array();
$listsClass = acymailing_get('class.list');
if(empty($identifiedUser->subid)){
	$allLists = $listsClass->getLists('listid');
}else{
	$allLists = $userClass->getSubscription($identifiedUser->subid, 'listid');
}

if(acymailing_level(1)){
	$allLists = $listsClass->onlyCurrentLanguage($allLists);
}

if(acymailing_level(3)){
	$allLists = $listsClass->onlyAllowedLists($allLists);
}

if(strpos($visibleLists, ',') OR is_numeric($visibleLists)){
	$allvisiblelists = explode(',', $visibleLists);
	foreach($allLists as $oneList){
		if($oneList->published AND in_array($oneList->listid, $allvisiblelists)) $visibleListsArray[] = $oneList->listid;
	}
}elseif(strtolower($visibleLists) == 'all'){
	foreach($allLists as $oneList){
		if($oneList->published){
			$visibleListsArray[] = $oneList->listid;
		}
	}
}

if(strpos($hiddenLists, ',') OR is_numeric($hiddenLists)){
	$allhiddenlists = explode(',', $hiddenLists);
	foreach($allLists as $oneList){
		if($oneList->published AND in_array($oneList->listid, $allhiddenlists)) $hiddenListsArray[] = $oneList->listid;
	}
}elseif(strtolower($hiddenLists) == 'all'){
	$visibleListsArray = array();
	foreach($allLists as $oneList){
		if(!empty($oneList->published)){
			$hiddenListsArray[] = $oneList->listid;
		}
	}
}

if(!empty($visibleListsArray) AND !empty($hiddenListsArray)){
	$visibleListsArray = array_diff($visibleListsArray, $hiddenListsArray);
}

$visibleLists = $params->get('dropdown', 0) ? '' : implode(',', $visibleListsArray);
$hiddenLists = implode(',', $hiddenListsArray);

if(!$params->get('dropdown', 0) && empty($hiddenLists) && empty($visibleLists)){
	echo '<p style="color:red">Error : Please select some lists in your AcyMailing module configuration for the field "'.JText::_('AUTO_SUBSCRIBE_TO').'" and make sure the selected lists are enabled </p>';
}

if(!empty($identifiedUser->subid)){
	$countSub = 0;
	$countUnsub = 0;
	foreach($visibleListsArray as $idOneList){
		if($allLists[$idOneList]->status == -1){
			$countSub++;
		}elseif($allLists[$idOneList]->status == 1) $countUnsub++;
	}
	foreach($hiddenListsArray as $idOneList){
		if($allLists[$idOneList]->status == -1){
			$countSub++;
		}elseif($allLists[$idOneList]->status == 1) $countUnsub++;
	}
}

$checkedLists = $params->get('listschecked', 'All');
if(strtolower($checkedLists) == 'all'){
	$checkedListsArray = $visibleListsArray;
}elseif(strpos($checkedLists, ',') OR is_numeric($checkedLists)){
	$checkedListsArray = explode(',', $checkedLists);
}else{
	$checkedListsArray = array();
}

$listPosition = $params->get('listposition', 'before');


$nameCaption = $params->get('nametext', JText::_('NAMECAPTION'));
$emailCaption = $params->get('emailtext', JText::_('EMAILCAPTION'));
$displayOutside = $params->get('displayfields', 0);
$displayInline = ($params->get('displaymode', 'vertical') == 'vertical') ? false : true;

$displayedFields = $params->get('customfields', 'name,email');
$fieldsToDisplay = explode(',', $displayedFields);
$extraFields = array();

$fieldsize = $params->get('fieldsize', '80%');
if(is_numeric($fieldsize)) $fieldsize .= 'px';

if(acymailing_level(3)){
	$fieldsClass = acymailing_get('class.fields');
	$fieldsClass->origin = 'module';
	$fieldsClass->prefix = 'user_';
	$fieldsClass->suffix = '_'.$formName;
	if(!empty($connectedUser->email)) $fieldsClass->currentUser = $connectedUser;
	$extraFields = $fieldsClass->getFields('module', $identifiedUser);

	$newOrdering = array();
	$requiredFields = array();
	$validMessages = array();
	$checkFields = array();
	$checkFieldsType = array();
	$checkFieldsRegexp = array();
	$validCheckMsg = array();
	foreach($extraFields as $fieldnamekey => $oneField){
		if(in_array($fieldnamekey, $fieldsToDisplay)) $newOrdering[] = $fieldnamekey;
		if(in_array($oneField->type, array('text', 'date')) AND $params->get('fieldsize', '80%') AND (empty($extraFields[$fieldnamekey]->options['size']) || $params->get('fieldsize', '80%') < $extraFields[$fieldnamekey]->options['size'])){
			$extraFields[$fieldnamekey]->options['size'] = $params->get('fieldsize', '80%');
		}
		if(strlen($params->get($fieldnamekey.'text')) > 1){
			$extraFields[$fieldnamekey]->fieldname = $params->get($fieldnamekey.'text');
		}
		if(in_array($oneField->namekey, array('name', 'email'))) continue;
		if(!empty($oneField->required)){
			$requiredFields[] = $fieldnamekey;
			if(!empty($oneField->options['errormessage'])){
				$validMessages[] = addslashes($fieldsClass->trans($oneField->options['errormessage']));
			}else{
				$validMessages[] = addslashes(JText::sprintf('FIELD_VALID', $fieldsClass->trans($oneField->fieldname)));
			}
		}
		if($oneField->type == 'text' && !empty($oneField->options['checkcontent']) && in_array($fieldnamekey, explode(',', $params->get('customfields')))){
			$checkFields[] = $fieldnamekey;
			$checkFieldsType[] = $oneField->options['checkcontent'];
			if($oneField->options['checkcontent'] == 'regexp') $checkFieldsRegexp[] = $oneField->options['regexp'];
			if(!empty($oneField->options['errormessagecheckcontent'])){
				$validCheckMsg[] = addslashes($fieldsClass->trans($oneField->options['errormessagecheckcontent']));
			}elseif(!empty($oneField->options['errormessage'])){
				$validCheckMsg[] = addslashes($fieldsClass->trans($oneField->options['errormessage']));
			}else{
				$validCheckMsg[] = addslashes(JText::sprintf('FIELD_CONTENT_VALID', $fieldsClass->trans($oneField->fieldname)));
			}
		}
	}
	$fieldsToDisplay = $newOrdering;

	if(!empty($requiredFields)){
		$js = "acymailing['reqFields".$formName."'] = Array('".implode("','", $requiredFields)."');
		acymailing['validFields".$formName."'] = Array('".implode("','", $validMessages)."');";

		if($params->get('includejs', 'header') == 'header'){
			$doc->addScriptDeclaration($js);
		}else{
			echo "<script type=\"text/javascript\">
				<!--
					$js
				//-->
					</script>";
		}
	}
	if(!empty($checkFields)){
		$js = "acymailing['checkFields".$formName."'] = Array('".implode("','", $checkFields)."');
		acymailing['checkFieldsType".$formName."'] = Array('".implode("','", $checkFieldsType)."');
		acymailing['validCheckFields".$formName."'] = Array('".implode("','", $validCheckMsg)."');";
		if(!empty($checkFieldsRegexp)) $js .= "acymailing['checkFieldsRegexp".$formName."'] = Array('".implode("','", $checkFieldsRegexp)."');";

		if($params->get('includejs', 'header') == 'header'){
			$doc->addScriptDeclaration($js);
		}else{
			echo "<script type=\"text/javascript\">
					$js
					</script>";
		}
	}

	$arrayForCondDisplay = array();
	foreach($fieldsToDisplay as $oneField){
		$arrayForCondDisplay[$oneField] = $extraFields[$oneField];
	}
	$js = $fieldsClass->prepareConditionalDisplay($arrayForCondDisplay, 'user', 'mod_'.$params->get('displaymode', 'vertical'), $formName);
	if(!empty($js)) $doc->addScriptDeclaration($js);
}
if(!in_array('email', $fieldsToDisplay) && empty($connectedUser->id)) $fieldsToDisplay[] = 'email';

if($params->get('loadmootools', '1') == 1 && ($params->get('effect') == 'mootools-slide' || $params->get('redirectmode', 0) == '3')){
	acymailing_loadMootools($params->get('effect') == 'mootools-slide');
}

if($params->get('effect') == 'mootools-slide'){
	$mootoolsButton = $params->get('mootoolsbutton', '');
	if(empty($mootoolsButton)) $mootoolsButton = JText::_('SUBSCRIBE');

	$js = 'if (window.jQuery) {
			jQuery(document).ready(function(){
				jQuery("#acymailing_fulldiv_'.$formName.'").hide();
 				jQuery("#acymailing_togglemodule_'.$formName.'").click(function(){
					jQuery("#acymailing_fulldiv_'.$formName.'").slideToggle("fast");
					jQuery("#acymailing_togglemodule_'.$formName.'").toggleClass("acyactive");
					return false;
				});
			});
		} else{
		';
	$js .= "window.addEvent('domready', function(){
				var mySlide = new Fx.Slide('acymailing_fulldiv_$formName');
				mySlide.hide();
				try{
					var acytogglemodule = document.id('acymailing_togglemodule_$formName');
				}catch(err){
					var acytogglemodule = $('acymailing_togglemodule_$formName');
				}

				acytogglemodule.addEvent('click', function(e){
					if(mySlide.wrapper.offsetHeight == 0){
						acytogglemodule.className = 'acymailing_togglemodule acyactive';
					}else{
						acytogglemodule.className = 'acymailing_togglemodule';
					}
					mySlide.toggle();
					try {
						var evt = new Event(e);
						evt.stop();
					} catch(err) {
						e.stop();
					}
				});
			});
		}";

	if($params->get('includejs', 'header') == 'header'){
		$doc->addScriptDeclaration($js);
	}else{
		echo "<script type=\"text/javascript\">
			<!--
				$js
			//-->
				</script>";
	}
}

if($params->get('overlay', 0)){
	JHTML::_('behavior.tooltip');
}

if($params->get('showterms', false)){
	require_once JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php';
	$termsIdContent = $params->get('termscontent', 0);
	if(empty($termsIdContent)){
		$termslink = JText::_('JOOMEXT_TERMS');
	}else{
		if(is_numeric($termsIdContent)){
			$db = JFactory::getDBO();
			if(!ACYMAILING_J16){
				$query = 'SELECT a.id,a.alias,a.catid,a.sectionid, c.alias as catalias, s.alias as secalias FROM #__content as a ';
				$query .= ' LEFT JOIN #__categories AS c ON c.id = a.catid ';
				$query .= ' LEFT JOIN #__sections AS s ON s.id = a.sectionid ';
				$query .= 'WHERE a.id = '.$termsIdContent.' LIMIT 1';
				$db->setQuery($query);
				$article = $db->loadObject();

				$section = $article->sectionid.(!empty($article->secalias) ? ':'.$article->secalias : '');
				$category = $article->catid.(!empty($article->catalias) ? ':'.$article->catalias : '');
				$articleid = $article->id.(!empty($article->alias) ? ':'.$article->alias : '');
				$url = ContentHelperRoute::getArticleRoute($articleid, $category, $section);
			}else{
				$query = 'SELECT a.id,a.alias,a.catid, c.alias as catalias FROM #__content as a ';
				$query .= ' LEFT JOIN #__categories AS c ON c.id = a.catid ';
				$query .= 'WHERE a.id = '.$termsIdContent.' LIMIT 1';
				$db->setQuery($query);
				$article = $db->loadObject();

				$category = $article->catid.(!empty($article->catalias) ? ':'.$article->catalias : '');
				$articleid = $article->id.(!empty($article->alias) ? ':'.$article->alias : '');

				$url = ContentHelperRoute::getArticleRoute($articleid, $category);
			}
			$url .= (strpos($url, '?') ? '&' : '?').'tmpl=component';
		}else{
			$url = $termsIdContent;
		}

		if($params->get('showtermspopup', 1) == 1){
			$acypop = acymailing_get('helper.acypopup');
			$acypop->useMootools = ($params->get('loadmootools', '1') == 1) ? true : false;
			$termslink = $acypop->display(JText::_('JOOMEXT_TERMS'), JText::_('JOOMEXT_TERMS', true), $url, $articleid, 650, 375, '', '', 'text');
		}else{
			$termslink = '<a title="'.JText::_('JOOMEXT_TERMS', true).'"  href="'.$url.'" target="_blank">'.JText::_('JOOMEXT_TERMS').'</a>';
		}
	}
}

if(!empty($overridedesign)){
	ob_start();
}

if($params->get('displaymode') == 'tableless'){
	require(JModuleHelper::getLayoutPath('mod_acymailing', 'tableless'));
}else{
	require(JModuleHelper::getLayoutPath('mod_acymailing'));
}

if(!empty($connectedUser->email)){
	echo '<span style="display:none">{emailcloak=off}</span>';
}

if(!empty($overridedesign)){
	$moduleDisplay = ob_get_clean();
	$file = ACYMAILING_MEDIA.'plugins'.DS.'squeezepage'.DS.$overridedesign.'.php';
	if(file_exists($file)){
		ob_start();
		require($file);
		$squeezePage = ob_get_clean();
		$squeezePage = str_replace('{module}', $moduleDisplay, $squeezePage);
		echo $squeezePage;
	}else{
		echo $moduleDisplay;
	}
}
