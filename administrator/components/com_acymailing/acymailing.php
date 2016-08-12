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
if(version_compare(PHP_VERSION, '5.0.0', '<')){
	echo '<p style="color:red">This version of AcyMailing does not support PHP4, it is time to upgrade your server to PHP5!</p>';
	exit;
}

if(!include_once(rtrim(JPATH_ADMINISTRATOR, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_acymailing'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php')){
	echo "Could not load Acy helper file";
	return;
}

if(defined('JDEBUG') AND JDEBUG) acymailing_displayErrors();

$taskGroup = JRequest::getCmd('ctrl', JRequest::getCmd('gtask', 'dashboard'));
if($taskGroup == 'config') $taskGroup = 'cpanel';

$config =& acymailing_config();
$doc = JFactory::getDocument();
$app = JFactory::getApplication();

$doc->addStyleSheet(ACYMAILING_CSS.'backend_default.css?v='.filemtime(ACYMAILING_MEDIA.'css'.DS.'backend_default.css'));
$cssBackend = $config->get('css_backend');
if($cssBackend == 'backend_custom') $doc->addStyleSheet(ACYMAILING_CSS.'backend_custom.css?v='.filemtime(ACYMAILING_MEDIA.'css'.DS.'backend_custom.css'));


$doc->addScript(ACYMAILING_JS.'acymailing_compat.js?v='.filemtime(ACYMAILING_MEDIA.'js'.DS.'acymailing_compat.js'));

JHTML::_('behavior.tooltip');

if($taskGroup != 'update' && !$config->get('installcomplete')){
	$url = acymailing_completeLink('update&task=install', false, true);
	echo "<script>document.location.href='".$url."';</script>\n";
	echo 'Install not finished... You will be redirected to the second part of the install screen<br />';
	echo '<a href="'.$url.'">Please click here if you are not automatically redirected within 3 seconds</a>';
	return;
}


$action = JRequest::getCmd('task', 'listing');
if(empty($action)){
	$action = JRequest::getCmd('defaulttask', 'listing');
	JRequest::setVar('task', $action);
}

$menuDisplayed = false;
if(!($taskGroup == 'send' && $action == 'send') && $taskGroup !== 'toggle' && JRequest::getString('tmpl') !== 'component' && !in_array($action, array('doexport', 'continuesend', 'load')) && !in_array($taskGroup, array('editor'))){
	$menuHelper = acymailing_get('helper.acymenu');
	echo '<div id="acyallcontent" class="acyallcontent">';
	echo $menuHelper->display($taskGroup);

	echo '<div id="acymainarea" class="acymaincontent_'.$taskGroup.'">';
	$menuDisplayed = true;
}

$currentuser = JFactory::getUser();
if($taskGroup != 'update' && ACYMAILING_J16 && !$currentuser->authorise('core.manage', 'com_acymailing')){
	acymailing_display(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
	return;
}
if(($taskGroup == 'cpanel' || ($taskGroup == 'update' && $action == 'listing')) && ACYMAILING_J16 && !$currentuser->authorise('core.admin', 'com_acymailing')){
	acymailing_display(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
	return;
}

if(!include_once(ACYMAILING_CONTROLLER.$taskGroup.'.php')){
	$app->redirect('index.php?option=com_acymailing');
	return;
}
$className = ucfirst($taskGroup).'Controller';
$classGroup = new $className();

JRequest::setVar('view', $classGroup->getName());
$classGroup->execute($action);

$classGroup->redirect();

if($menuDisplayed){
	echo '</div></div>';
}
