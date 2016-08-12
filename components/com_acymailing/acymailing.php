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
jimport('joomla.application.component.controller');
jimport('joomla.application.component.view');

include_once(rtrim(JPATH_ADMINISTRATOR, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_acymailing'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php');

if(defined('JDEBUG') AND JDEBUG) acymailing_displayErrors();

$view = JRequest::getCmd('view');
if(!empty($view) AND !JRequest::getCmd('ctrl')){
	JRequest::setVar('ctrl', $view);
	$layout = JRequest::getCmd('layout');
	if(!empty($layout)){
		JRequest::setVar('task', $layout);
	}
}
$taskGroup = JRequest::getCmd('ctrl', JRequest::getCmd('gtask', 'lists'));

global $Itemid;
if(empty($Itemid)){
	$urlItemid = JRequest::getInt('Itemid');
	if(!empty($urlItemid)) $Itemid = $urlItemid;
}


$config =& acymailing_config();

$doc = JFactory::getDocument();
$doc->addScript(ACYMAILING_JS.'acymailing.js?v='.str_replace('.', '', $config->get('version')));
$doc->addScript(ACYMAILING_JS.'acymailing_compat.js?v='.str_replace('.', '', $config->get('version')));


$cssFrontend = $config->get('css_frontend', 'default');
if(!empty($cssFrontend)){
	$doc->addStyleSheet(ACYMAILING_CSS.'component_'.$cssFrontend.'.css?v='.filemtime(ACYMAILING_MEDIA.'css'.DS.'component_'.$cssFrontend.'.css'));
}

if($taskGroup == 'newsletter') $taskGroup = 'frontnewsletter';

if(!file_exists(ACYMAILING_CONTROLLER_FRONT.$taskGroup.'.php') || !include(ACYMAILING_CONTROLLER_FRONT.$taskGroup.'.php')){
	return JError::raiseError(404, 'Page not found : '.$taskGroup);
}

$className = ucfirst($taskGroup).'Controller';
$classGroup = new $className();
JRequest::setVar('view', $classGroup->getName());

$action = JRequest::getCmd('task');
if(empty($action)){
	$action = JRequest::getCmd('defaulttask');
	JRequest::setVar('task', $action);
}

$classGroup->execute($action);
$classGroup->redirect();
if(JRequest::getString('tmpl') !== 'component' && !in_array(JRequest::getCmd('task'), array('unsub', 'saveunsub', 'optout', 'out', 'view'))){
	echo acymailing_footer();
}
