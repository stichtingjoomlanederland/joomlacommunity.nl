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

if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

define('ACYMAILING_COMPONENT', 'com_acymailing');
define('ACYMAILING_ROOT', rtrim(JPATH_ROOT, DS).DS);
define('ACYMAILING_FRONT', rtrim(JPATH_SITE, DS).DS.'components'.DS.ACYMAILING_COMPONENT.DS);
define('ACYMAILING_BACK', rtrim(JPATH_ADMINISTRATOR, DS).DS.'components'.DS.ACYMAILING_COMPONENT.DS);
define('ACYMAILING_HELPER', ACYMAILING_BACK.'helpers'.DS);
define('ACYMAILING_CLASS', ACYMAILING_BACK.'classes'.DS);
define('ACYMAILING_TYPE', ACYMAILING_BACK.'types'.DS);
define('ACYMAILING_CONTROLLER', ACYMAILING_BACK.'controllers'.DS);
define('ACYMAILING_CONTROLLER_FRONT', ACYMAILING_FRONT.'controllers'.DS);
define('ACYMAILING_DBPREFIX', '#__acymailing_');
define('ACYMAILING_NAME', 'AcyMailing');
define('ACYMAILING_MEDIA', ACYMAILING_ROOT.'media'.DS.ACYMAILING_COMPONENT.DS);
define('ACYMAILING_TEMPLATE', ACYMAILING_MEDIA.'templates'.DS);
define('ACYMAILING_UPDATEURL', 'https://www.acyba.com/index.php?option=com_updateme&ctrl=update&task=');
define('ACYMAILING_SPAMURL', 'https://www.acyba.com/index.php?option=com_updateme&ctrl=spamsystem&task=');
define('ACYMAILING_SPECIALCONTENTURL', 'https://www.acyba.com/index.php?option=com_updateme&ctrl=infodisplay&task=');
define('ACYMAILING_HELPURL', 'https://www.acyba.com/index.php?option=com_updateme&ctrl=doc&component='.ACYMAILING_NAME.'&page=');
define('ACYMAILING_REDIRECT', 'https://www.acyba.com/index.php?option=com_updateme&ctrl=redirect&page=');
define('ACYMAILING_INC', ACYMAILING_FRONT.'inc'.DS);

$jversion = preg_replace('#[^0-9\.]#i', '', JVERSION);
define('ACYMAILING_J16', version_compare($jversion, '1.6.0', '>=') ? true : false);
define('ACYMAILING_J25', version_compare($jversion, '2.5.0', '>=') ? true : false);
define('ACYMAILING_J30', version_compare($jversion, '3.0.0', '>=') ? true : false);

$compatPath = ACYMAILING_BACK.'compat'.DS.'compat';
if(file_exists($compatPath.substr(str_replace('.', '', $jversion), 0, 2).'.php')){
	require($compatPath.substr(str_replace('.', '', $jversion), 0, 2).'.php');
}elseif(file_exists($compatPath.substr(str_replace('.', '', $jversion), 0, 1).'.php')) require($compatPath.substr(str_replace('.', '', $jversion), 0, 1).'.php');
else{
	echo 'AcyMailing: Could not load compat file for J'.JVERSION;
	return;
}

if(is_callable("date_default_timezone_set")) date_default_timezone_set(@date_default_timezone_get());

function acymailing_getDate($time = 0, $format = '%d %B %Y %H:%M'){

	if(empty($time)) return '';

	if(is_numeric($format)) $format = JText::_('DATE_FORMAT_LC'.$format);
	if(ACYMAILING_J16){
		$format = str_replace(array('%A', '%d', '%B', '%m', '%Y', '%y', '%H', '%M', '%S', '%a', '%I', '%p', '%w'), array('l', 'd', 'F', 'm', 'Y', 'y', 'H', 'i', 's', 'D', 'h', 'a', 'w'), $format);
		try{
			return JHTML::_('date', $time, $format, false);
		}catch(Exception $e){
			return date($format, $time);
		}
	}else{
		static $timeoffset = null;
		if($timeoffset === null){
			$config = JFactory::getConfig();
			$timeoffset = $config->getValue('config.offset');
		}
		return JHTML::_('date', $time - date('Z'), $format, $timeoffset);
	}
}

function acymailing_isRobot(){
	if(empty($_SERVER)) return false;
	if(!empty($_SERVER['HTTP_USER_AGENT']) && strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'spambayes') !== false) return true;
	if(!empty($_SERVER['REMOTE_ADDR']) && version_compare($_SERVER['REMOTE_ADDR'], '64.235.144.0', '>=') && version_compare($_SERVER['REMOTE_ADDR'], '64.235.159.255', '<=')) return true;

	return false;
}

function acymailing_isAllowed($allowedGroups, $groups = null){
	if($allowedGroups == 'all') return true;
	if($allowedGroups == 'none') return false;
	$my = JFactory::getUser();
	if(!is_array($allowedGroups)) $allowedGroups = explode(',', trim($allowedGroups, ','));

	if(empty($my->id) && empty($groups) && in_array('nonloggedin', $allowedGroups)) return true;

	if(empty($groups) AND empty($my->id)) return false;
	if(empty($groups)){
		if(!ACYMAILING_J16){
			$groups = $my->gid;
		}else{
			jimport('joomla.access.access');
			$groups = JAccess::getGroupsByUser($my->id, false);
		}
	}
	if(is_array($groups)){
		$inter = array_intersect($groups, $allowedGroups);
		if(empty($inter)) return false;
		return true;
	}else{
		return in_array($groups, $allowedGroups);
	}
}

function acymailing_getFunctionsEmailCheck($controllButtons = array(), $bounce = false){
	$return = '<script language="javascript" type="text/javascript">
				<!--
				function validateEmail(emailAddress, fieldName){
					if(emailAddress.length > 0 && emailAddress.indexOf("{") == -1 && !emailAddress.match(/^([a-z0-9_\'&\.\-\+=])+\@(([a-z0-9\-])+\.)+([a-z0-9]{2,10})+((,|;)([a-z0-9_\'&\.\-\+=])+\@(([a-z0-9\-])+\.)+([a-z0-9]{2,10})+)*$/i)){
						alert(\'Wrong email address supplied for the \' + fieldName + \' field: \' + emailAddress);
						return false;
					}
					return true;
				}';

	if(!empty($controllButtons)){
		foreach($controllButtons as &$oneField){
			$oneField = 'pressbutton == \''.$oneField.'\'';
		}

		$return .= (ACYMAILING_J16 ? 'Joomla.submitbutton = function(pressbutton){' : 'function submitbutton(pressbutton){').'
						if('.implode(' || ', $controllButtons).'){
							var emailVars = ["fromemail","replyemail"'.($bounce ? ',"bounceemail"' : '').'];
							var val = "";
							for(var key in emailVars){
								if(isNaN(key)) continue;
								val = document.getElementById(emailVars[key]).value;
								if(!validateEmail(val, emailVars[key])){
									return;
								}
							}
						}
'.(ACYMAILING_J16 ? 'Joomla.submitform(pressbutton,document.adminForm);' : 'submitform(pressbutton);').'
					}';
	}

	$return .= '
				-->
				</script>';

	return $return;
}

function acymailing_getTime($date){
	static $timeoffset = null;
	if($timeoffset === null){
		$config = JFactory::getConfig();
		if(ACYMAILING_J30){
			$timeoffset = $config->get('offset');
		}else{
			$timeoffset = $config->getValue('config.offset');
		}

		if(ACYMAILING_J16){
			$dateC = JFactory::getDate($date, $timeoffset);
			$timeoffset = $dateC->getOffsetFromGMT(true);
		}
	}

	return strtotime($date) - $timeoffset * 60 * 60 + date('Z');
}

function acymailing_loadLanguage(){
	$lang = JFactory::getLanguage();
	$lang->load(ACYMAILING_COMPONENT, JPATH_SITE);
	$lang->load(ACYMAILING_COMPONENT.'_custom', JPATH_SITE);
}

function acymailing_createDir($dir, $report = true, $secured = false){
	if(is_dir($dir)) return true;

	jimport('joomla.filesystem.folder');
	jimport('joomla.filesystem.file');

	$indexhtml = '<html><body bgcolor="#FFFFFF"></body></html>';

	try{
		$status = JFolder::create($dir);
	}catch(Exception $e){
		$status = false;
	}

	if(!$status){
		if($report) acymailing_display('Could not create the directory '.$dir, 'error');
		return false;
	}

	try{
		$status = JFile::write($dir.DS.'index.html', $indexhtml);
	}catch(Exception $e){
		$status = false;
	}

	if(!$status){
		if($report) acymailing_display('Could not create the file '.$dir.DS.'index.html', 'error');
	}

	if($secured){
		try{
			$htaccess = 'Order deny,allow'."\r\n".'Deny from all';
			$status = JFile::write($dir.DS.'.htaccess', $htaccess);
		}catch(Exception $e){
			$status = false;
		}

		if(!$status){
			if($report) acymailing_display('Could not create the file '.$dir.DS.'.htaccess', 'error');
		}
	}

	return $status;
}

function acymailing_getUpgradeLink($tolevel){
	$config =& acymailing_config();
	return ' <a class="acyupgradelink" href="'.ACYMAILING_REDIRECT.'upgrade-acymailing-'.$config->get('level').'-to-'.$tolevel.'" target="_blank">'.JText::_('ONLY_FROM_'.strtoupper($tolevel)).'</a>';
}

function acymailing_replaceDate($mydate){

	if(strpos($mydate, '{time}') === false) return $mydate;

	$mydate = str_replace('{time}', time(), $mydate);
	$operators = array('+', '-');
	foreach($operators as $oneOperator){
		if(!strpos($mydate, $oneOperator)) continue;
		list($part1, $part2) = explode($oneOperator, $mydate);
		if($oneOperator == '+'){
			$mydate = trim($part1) + trim($part2);
		}elseif($oneOperator == '-'){
			$mydate = trim($part1) - trim($part2);
		}
	}

	return $mydate;
}

function acymailing_initJSStrings($includejs = 'header', $params = null){
	static $alreadyThere = false;
	if($alreadyThere && $includejs == 'header') return;

	$alreadyThere = true;

	$doc = JFactory::getDocument();
	if(method_exists($params, 'get')){
		$nameCaption = $params->get('nametext');
		$emailCaption = $params->get('emailtext');
	}
	if(empty($nameCaption)) $nameCaption = JText::_('NAMECAPTION');
	if(empty($emailCaption)) $emailCaption = JText::_('EMAILCAPTION');
	$js = "	if(typeof acymailing == 'undefined'){
					var acymailing = Array();
				}
				acymailing['NAMECAPTION'] = '".str_replace("'", "\'", $nameCaption)."';
				acymailing['NAME_MISSING'] = '".str_replace("'", "\'", JText::_('NAME_MISSING'))."';
				acymailing['EMAILCAPTION'] = '".str_replace("'", "\'", $emailCaption)."';
				acymailing['VALID_EMAIL'] = '".str_replace("'", "\'", JText::_('VALID_EMAIL'))."';
				acymailing['ACCEPT_TERMS'] = '".str_replace("'", "\'", JText::_('ACCEPT_TERMS'))."';
				acymailing['CAPTCHA_MISSING'] = '".str_replace("'", "\'", JText::_('ERROR_CAPTCHA'))."';
				acymailing['NO_LIST_SELECTED'] = '".str_replace("'", "\'", JText::_('NO_LIST_SELECTED'))."';
		";
	if($includejs == 'header'){
		$doc->addScriptDeclaration($js);
	}else{
		echo "<script type=\"text/javascript\">
					<!--
					$js
					//-->
				</script>";
	}
}

function acymailing_generateKey($length){
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$randstring = '';
	$max = strlen($characters) - 1;
	for($i = 0; $i < $length; $i++){
		$randstring .= $characters[mt_rand(0, $max)];
	}
	return $randstring;
}

function acymailing_absoluteURL($text){
	static $mainurl = '';
	if(empty($mainurl)){
		$urls = parse_url(ACYMAILING_LIVE);
		if(!empty($urls['path'])){
			$mainurl = substr(ACYMAILING_LIVE, 0, strrpos(ACYMAILING_LIVE, $urls['path'])).'/';
		}else{
			$mainurl = ACYMAILING_LIVE;
		}
	}

	$text = str_replace(array('href="../undefined/', 'href="../../undefined/', 'href="../../../undefined//', 'href="undefined/', ACYMAILING_LIVE.'http://', ACYMAILING_LIVE.'https://'), array('href="'.$mainurl, 'href="'.$mainurl, 'href="'.$mainurl, 'href="'.ACYMAILING_LIVE, 'http://', 'https://'), $text);
	$text = preg_replace('#href="(/?administrator)?/({|%7B)#Ui', 'href="$2', $text);

	$text = preg_replace('#href="http:/([^/])#Ui', 'href="http://$1', $text);

	$text = preg_replace('#href="'.preg_quote(str_replace(array('http://', 'https://'), '', $mainurl), '#').'#Ui', 'href="'.$mainurl, $text);

	$replace = array();
	$replaceBy = array();
	if($mainurl !== ACYMAILING_LIVE){

		$replace[] = '#(href|src|action|background)[ ]*=[ ]*\"(?!(\{|%7B|\[|\#|\\\\|[a-z]{3,15}:|/))(?:\.\./)#i';
		$replaceBy[] = '$1="'.substr(ACYMAILING_LIVE, 0, strrpos(rtrim(ACYMAILING_LIVE, '/'), '/') + 1);


		$subfolder = substr(ACYMAILING_LIVE, strrpos(rtrim(ACYMAILING_LIVE, '/'), '/'));
		$replace[] = '#(href|src|action|background)[ ]*=[ ]*\"'.preg_quote($subfolder, '#').'(\{|%7B)#i';
		$replaceBy[] = '$1="$2';
	}
	$replace[] = '#(href|src|action|background)[ ]*=[ ]*\"(?!(\{|%7B|\[|\#|\\\\|[a-z]{3,15}:|/))(?:\.\./|\./)?#i';
	$replaceBy[] = '$1="'.ACYMAILING_LIVE;
	$replace[] = '#(href|src|action|background)[ ]*=[ ]*\"(?!(\{|%7B|\[|\#|\\\\|[a-z]{3,15}:))/#i';
	$replaceBy[] = '$1="'.$mainurl;

	$replace[] = '#((background-image|background)[ ]*:[ ]*url\(\'?"?(?!(\\\\|[a-z]{3,15}:|/|\'|"))(?:\.\./|\./)?)#i';
	$replaceBy[] = '$1'.ACYMAILING_LIVE;

	return preg_replace($replace, $replaceBy, $text);
}

function acymailing_setPageTitle($title){
	$app = JFactory::getApplication();
	if(empty($title)){
		$title = $app->getCfg('sitename');
	}elseif($app->getCfg('sitename_pagetitles', 0) == 1){
		$title = JText::sprintf('ACY_JPAGETITLE', $app->getCfg('sitename'), $title);
	}elseif($app->getCfg('sitename_pagetitles', 0) == 2){
		$title = JText::sprintf('ACY_JPAGETITLE', $title, $app->getCfg('sitename'));
	}
	$document = JFactory::getDocument();
	$document->setTitle($title);
}

function acymailing_frontendLink($link, $popup = false){
	if($popup) $link .= '&tmpl=component';

	$config = acymailing_config();
	$app = JFactory::getApplication();
	if($config->get('use_sef', 0) && strpos($link, '&ctrl=url') === false){
		if($app->isAdmin()){
			$adminLink = JApplication::getInstance('site')->getRouter()->build($link)->toString();
			$link = substr($adminLink, strlen(JUri::base(true)) + 1);
		}else{
			$link = ltrim(JRoute::_($link, false), '/');
		}
	}

	static $mainurl = '';
	static $otherarguments = false;
	if(empty($mainurl)){
		$urls = parse_url(ACYMAILING_LIVE);
		if(isset($urls['path']) AND strlen($urls['path']) > 0){
			$mainurl = substr(ACYMAILING_LIVE, 0, strrpos(ACYMAILING_LIVE, $urls['path'])).'/';
			$otherarguments = trim(str_replace($mainurl, '', ACYMAILING_LIVE), '/');
			if(strlen($otherarguments) > 0) $otherarguments .= '/';
		}else{
			$mainurl = ACYMAILING_LIVE;
		}
	}

	if($otherarguments AND strpos($link, $otherarguments) === false){
		$link = $otherarguments.$link;
	}

	return $mainurl.$link;
}

function acymailing_bytes($val){
	$val = trim($val);
	if(empty($val)){
		return 0;
	}
	$last = strtolower($val[strlen($val) - 1]);
	switch($last){
		case 'g':
			$val *= 1024;
		case 'm':
			$val *= 1024;
		case 'k':
			$val *= 1024;
	}

	return (int)$val;
}

function acymailing_display($messages, $type = 'success', $close = false){
	if(empty($messages)) return;
	if(!is_array($messages)) $messages = array($messages);
	$app = JFactory::getApplication();
	if(ACYMAILING_J30 || $app->isAdmin()){
		$tmpl = JRequest::getString('tmpl', '');
		if($app->isAdmin() && empty($tmpl)) echo '<div style="padding:1px;">';
		echo '<div id="acymailing_messages_'.$type.'" class="alert alert-'.$type.' alert-block">';
		if($close && ACYMAILING_J30) echo '<button type="button" class="close" data-dismiss="alert">×</button>';
		echo '<p>'.implode('</p><p>', $messages).'</p></div>';
		if($app->isAdmin() && empty($tmpl)) echo '</div>';
	}else{
		echo '<div id="acymailing_messages_'.$type.'" class="acymailing_messages acymailing_'.$type.'"><ul><li>'.implode('</li><li>', $messages).'</li></ul></div>';
	}
}

function acymailing_enqueueMessage($message, $type = 'success'){
	$result = is_array($message) ? implode('<br/>', $message) : $message;

	$app = JFactory::getApplication();
	if($app->isAdmin()){
		if(ACYMAILING_J30){
			$type = str_replace(array('notice', 'message'), array('info', 'success'), $type);
		}else{
			$type = str_replace(array('message', 'notice', 'warning'), array('info', 'warning', 'error'), $type);
		}
		$_SESSION['acymessage'.$type][] = $result;
	}else{
		if(ACYMAILING_J30){
			$type = str_replace(array('success', 'info'), array('message', 'notice'), $type);
		}else{
			$type = str_replace(array('success', 'error', 'warning', 'info'), array('message', 'warning', 'notice', 'message'), $type);
		}
		$app->enqueueMessage($result, $type);
	}
}

function acymailing_completeLink($link, $popup = false, $redirect = false){
	if($popup) $link .= '&tmpl=component';
	return JRoute::_('index.php?option='.ACYMAILING_COMPONENT.'&ctrl='.$link, !$redirect);
}

function acymailing_table($name, $component = true){
	$prefix = $component ? ACYMAILING_DBPREFIX : '#__';
	return $prefix.$name;
}

function acymailing_secureField($fieldName){
	if(!is_string($fieldName) OR preg_match('|[^a-z0-9#_.-]|i', $fieldName) !== 0){
		die('field "'.htmlspecialchars($fieldName, ENT_COMPAT, 'UTF-8').'" not secured');
	}
	return $fieldName;
}

function acymailing_displayErrors(){
	error_reporting(E_ALL);
	@ini_set("display_errors", 1);
}

function acymailing_increasePerf(){
	@ini_set('max_execution_time', 600);
	@ini_set('pcre.backtrack_limit', 1000000);
}

function &acymailing_config($reload = false){
	static $configClass = null;
	if($configClass === null || $reload){
		$configClass = acymailing_get('class.config');
		$configClass->load();
	}
	return $configClass;
}

function acymailing_listingsearch($search){
	$app = JFactory::getApplication();
	if($app->isAdmin()){ ?>
		<div class="filter-search">
			<input type="text" name="search" id="search" value="<?php echo htmlspecialchars($search, ENT_COMPAT, 'UTF-8'); ?>" class="text_area" placeholder="<?php echo JText::_('ACY_SEARCH'); ?>" title="<?php echo JText::_('ACY_SEARCH'); ?>"/>
			<button style="float:none;" onclick="document.adminForm.limitstart.value=0;this.form.submit();" class="btn tip hasTooltip" type="submit" title="<?php echo JText::_('ACY_SEARCH'); ?>"><i class="acyicon-search"></i></button>
			<button style="float:none;" onclick="document.adminForm.limitstart.value=0;document.getElementById('search').value='';this.form.submit();" class="btn tip hasTooltip" type="button" title="<?php echo JText::_('JOOMEXT_RESET'); ?>"><i class="acyicon-cancel"></i></button>
		</div>
	<?php }else{ ?>
		<input placeholder="<?php echo JText::_('ACY_SEARCH'); ?>" type="text" name="search" id="search" value="<?php echo htmlspecialchars($search, ENT_COMPAT, 'UTF-8'); ?>" class="text_area" style="margin-bottom:0;"/>
		<button class="btn" onclick="document.adminForm.limitstart.value=0;this.form.submit();" title="<?php echo JText::_('JOOMEXT_GO'); ?>"><?php echo JText::_('JOOMEXT_GO'); ?></button>
		<button class="btn" onclick="document.adminForm.limitstart.value=0;document.getElementById('search').value='';this.form.submit();" title="<?php echo JText::_('JOOMEXT_RESET'); ?>"><?php echo JText::_('JOOMEXT_RESET'); ?></button>
		<?php
	}
}

function acymailing_level($level){
	$config =& acymailing_config();
	if($config->get($config->get('level'), 0) >= $level) return true;
	return false;
}

function acymailing_getModuleFormName(){
	static $i = 1;
	return 'formAcymailing'.rand(1000, 9999).$i++;
}

function acymailing_initModule($includejs, $params){

	static $alreadyThere = false;
	if($alreadyThere && $includejs == 'header') return;

	$alreadyThere = true;

	acymailing_initJSStrings($includejs, $params);
	$doc = JFactory::getDocument();
	$config = acymailing_config();
	if($includejs == 'header'){
		if(ACYMAILING_J16){
			$doc->addScript(ACYMAILING_JS.'acymailing_module.js?v='.str_replace('.', '', $config->get('version')), 'text/javascript', false, true);
		}else{
			$doc->addScript(ACYMAILING_JS.'acymailing_module.js?v='.str_replace('.', '', $config->get('version')));
		}
	}else{
		echo "\n".'<script type="text/javascript" src="'.ACYMAILING_JS.'acymailing_module.js?v='.str_replace('.', '', $config->get('version')).'" ></script>'."\n";
	}

	$moduleCSS = $config->get('css_module', 'default');
	if(!empty($moduleCSS)){
		if($includejs == 'header'){
			$doc->addStyleSheet(ACYMAILING_CSS.'module_'.$moduleCSS.'.css?v='.filemtime(ACYMAILING_MEDIA.'css'.DS.'module_'.$moduleCSS.'.css'));
		}else{
			echo "\n".'<link rel="stylesheet" property="stylesheet" href="'.ACYMAILING_CSS.'module_'.$moduleCSS.'.css?v='.filemtime(ACYMAILING_MEDIA.'css'.DS.'module_'.$moduleCSS.'.css').'" type="text/css" />'."\n";
		}
	}
}

function acymailing_footer(){
	$config = acymailing_config();
	$description = $config->get('description_'.strtolower($config->get('level')), 'Joomla!® Mailing System');
	$text = '<!--  AcyMailing Component powered by http://www.acyba.com -->
		<!-- version '.$config->get('level').' : '.$config->get('version').' -->';
	if(acymailing_level(1) && !acymailing_level(4)) return $text;
	$level = $config->get('level');
	$text .= '<div class="acymailing_footer" align="center" style="text-align:center"><a href="https://www.acyba.com/?utm_source=acymailing-'.$level.'&utm_medium=front-end&utm_content=txt&utm_campaign=powered-by" target="_blank" title="'.ACYMAILING_NAME.' : '.str_replace('TM ', ' ', strip_tags($description)).'">'.ACYMAILING_NAME;
	$text .= ' - '.$description.'</a></div>';
	return $text;
}

function acymailing_dispSearch($string, $searchString){
	$secString = htmlspecialchars($string, ENT_COMPAT, 'UTF-8');
	if(strlen($searchString) == 0) return $secString;
	return preg_replace('#('.preg_quote($searchString, '#').')#i', '<span class="searchtext">$1</span>', $secString);
}

function acymailing_perf($name){
	static $previoustime = 0;
	static $previousmemory = 0;
	static $file = '';

	if(empty($file)){
		$file = ACYMAILING_ROOT.'acydebug_'.rand().'.txt';
		$previoustime = microtime(true);
		$previousmemory = memory_get_usage();
		file_put_contents($file, "\r\n\r\n-- new test : ".$name." -- ".date('d M H:i:s')." from ".@$_SERVER['REMOTE_ADDR'], FILE_APPEND);
		return;
	}

	$nowtime = microtime(true);
	$totaltime = $nowtime - $previoustime;
	$previoustime = $nowtime;

	$nowmemory = memory_get_usage();
	$totalmemory = $nowmemory - $previousmemory;
	$previousmemory = $nowmemory;

	file_put_contents($file, "\r\n".$name.' : '.number_format($totaltime, 2).'s - '.$totalmemory.' / '.memory_get_usage(), FILE_APPEND);
}

function acymailing_search($searchString, $object){

	if(empty($object) || is_numeric($object)) return $object;

	if(is_string($object)){
		return preg_replace('#('.str_replace('#', '\#', $searchString).')#i', '<span class="searchtext">$1</span>', $object);
	}

	if(is_array($object)){
		foreach($object as $key => $element){
			$object[$key] = acymailing_search($searchString, $element);
		}
	}elseif(is_object($object)){
		foreach($object as $key => $element){
			$object->$key = acymailing_search($searchString, $element);
		}
	}

	return $object;
}

function acymailing_get($path){
	list($group, $class) = explode('.', $path);
	if($group == 'helper' && $class == 'user') $class = 'acyuser';
	if($group == 'helper' && $class == 'mailer') $class = 'acymailer';
	if($class == 'config') $class = 'cpanel';

	$className = $class.ucfirst($group);
	if($group == 'helper' && strpos($className, 'acy') !== 0) $className = 'acy'.$className;
	if(!class_exists($className)) include(constant(strtoupper('ACYMAILING_'.$group)).$class.'.php');

	if(!class_exists($className)) return null;
	return new $className();
}

function acymailing_getCID($field = ''){
	$oneResult = JRequest::getVar('cid', array(), '', 'array');
	$oneResult = intval(reset($oneResult));
	if(!empty($oneResult) OR empty($field)) return $oneResult;

	$oneResult = JRequest::getVar($field, 0, '', 'int');
	return intval($oneResult);
}

function acymailing_tooltip($desc, $title = ' ', $image = 'tooltip.png', $name = '', $href = '', $link = 1){
	$app = JFactory::getApplication();
	$config = acymailing_config();
	$bootstrap = $config->get('bootstrap_frontend');
	if(ACYMAILING_J30 && ($app->isAdmin() || !empty($bootstrap))){
		$class = 'hasTooltip';
		JHtml::_('bootstrap.tooltip');
	}else{
		$class = 'hasTip';
	}
	return JHTML::_('tooltip', str_replace(array("'", "::"), array("&#039;", ": : "), $desc.' '), str_replace(array("'", '::'), array("&#039;", ': : '), $title), $image, str_replace(array("'", '::'), array("&#039;", ': : '), $name.' '), $href, $link, $class);
}

function acymailing_checkRobots(){
	if(preg_match('#(libwww-perl|python|googlebot)#i', @$_SERVER['HTTP_USER_AGENT'])) die('Not allowed for robots. Please contact us if you are not a robot');
}

function acymailing_removeChzn($eltsToClean){
	if(!ACYMAILING_J30) return;

	$js = ' function removeChosen(){';
	foreach($eltsToClean as $elt){
		$js .= 'jQuery("#'.$elt.' .chzn-container").remove();
					jQuery("#'.$elt.' .chzn-done").removeClass("chzn-done").show();
					';
	}
	$js .= '}
		window.addEvent("domready", function(){removeChosen();
			setTimeout(function(){
				removeChosen();
		}, 100);});';
	$doc = JFactory::getDocument();
	$doc->addScriptDeclaration($js);
}

function acymailing_checkPluginsFolders(){
	$folders = array(JPATH_ROOT.DS.'plugins' => '', JPATH_ROOT.DS.'plugins'.DS.'user' => '', JPATH_ROOT.DS.'plugins'.DS.'system' => '');
	$results = array('', '', '');
	foreach($folders as $oneFolderToCheck => &$result){
		if(!is_writable($oneFolderToCheck)){
			$writableIssue = true;
			break;
		}
	}
	if(!empty($writableIssue)){
		$results = array();
		foreach($folders as $oneFolderToCheck => &$result){
			$results[] = ' : <span style="color:'.(is_writable($oneFolderToCheck) ? 'green;">OK' : 'red;">Not writable').'</span>';
		}
	}
	$errorPluginTxt = 'Some required AcyMailing plugins have not been installed.<br />Please make sure your plugins folders are writables by checking the user/group permissions:<br />* Joomla / Plugins'.$results[0].'<br />* Joomla / Plugins / User'.$results[1].'<br />* Joomla / Plugins / System'.$results[0].'<br />';
	if(empty($writableIssue)) $errorPluginTxt .= 'Please also empty your plugins cache: System => Clear cache => com_plugins => Delete<br />';
	acymailing_display($errorPluginTxt.'<a href="index.php?option=com_acymailing&amp;ctrl=update&amp;task=install">'.JText::_('ACY_ERROR_INSTALLAGAIN').'</a>', 'warning');
}

function acymailing_fileGetContent($url, $timeout = 0){
	$data = '';
	if(function_exists('file_get_contents')){
		if(!empty($timeout)){
			ini_set('default_socket_timeout', $timeout);
		}
		$data = file_get_contents($url);
	}

	if(!$data && function_exists('curl_exec')){
		$conn = curl_init($url);
		curl_setopt($conn, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($conn, CURLOPT_FRESH_CONNECT, true);
		curl_setopt($conn, CURLOPT_RETURNTRANSFER, 1);
		if(!empty($timeout)){
			curl_setopt($conn, CURLOPT_TIMEOUT, $timeout);
			curl_setopt($conn, CURLOPT_CONNECTTIMEOUT, $timeout);
		}

		$data = curl_exec($conn);
		curl_close($conn);
	}

	if(!$data && function_exists('fopen') && function_exists('stream_get_contents')){
		$handle = fopen($url, "r");
		if(!empty($timeout)){
			stream_set_timeout($handle, $timeout);
		}
		$data = stream_get_contents($handle);
	}

	return $data;
}

function acymailing_importFile($file, $uploadPath, $onlyPict){
	JRequest::checkToken() or die('Invalid Token');

	$config =& acymailing_config();
	$additionalMsg = '';

	if($file["error"] > 0){
		acymailing_display("Error Uploading code: ".htmlspecialchars($file["error"], ENT_COMPAT, 'UTF-8'), 'error');
		return false;
	}

	acymailing_createDir($uploadPath, true);

	if(!is_writable($uploadPath)){
		@chmod($uploadPath, '0755');
		if(!is_writable($uploadPath)){
			acymailing_display(JText::sprintf('WRITABLE_FOLDER', $uploadPath), 'error');
			return false;
		}
	}

	if($onlyPict){
		$allowedExtensions = array('png', 'jpeg', 'jpg', 'gif', 'ico', 'bmp');
	}else{
		$allowedExtensions = explode(',', $config->get('allowedfiles'));
	}

	if(!preg_match('#\.('.implode('|', $allowedExtensions).')$#Ui', $file["name"], $extension)){
		$ext = substr($file["name"], strrpos($file["name"], '.') + 1);
		acymailing_display(JText::sprintf('ACCEPTED_TYPE', htmlspecialchars($ext, ENT_COMPAT, 'UTF-8'), implode(', ', $allowedExtensions)), 'error');
		return false;
	}

	if(preg_match('#\.(php.?|.?htm.?|pl|py|jsp|asp|sh|cgi)#Ui', $file["name"])){
		acymailing_display('This extension name is blocked by the system regardless your configuration for security reasons', 'error');
		return false;
	}

	$file["name"] = preg_replace('#[^a-z0-9]#i', '_', strtolower(substr($file["name"], 0, strrpos($file["name"], '.')))).'.'.$extension[1];

	if($onlyPict){
		$imageSize = getimagesize($file['tmp_name']);
		if(empty($imageSize)){
			acymailing_display('Invalid image', 'error');
			return false;
		}
	}

	if(file_exists($uploadPath.DS.$file["name"])){
		$i = 1;
		$nameFile = preg_replace("/\\.[^.\\s]{3,4}$/", "", $file["name"]);
		$ext = substr($file["name"], strrpos($file["name"], '.') + 1);
		while(file_exists($uploadPath.DS.$nameFile.'_'.$i.'.'.$ext)){
			$i++;
		}

		$file["name"] = $nameFile.'_'.$i.'.'.$ext;
		$additionalMsg = '<br />'.JText::sprintf('FILE_RENAMED', $file["name"]);
		$additionalMsg .= '<br /><a style="color: blue; cursor: pointer;" onclick="confirmBox(\'rename\', \''.$file['name'].'\', \''.$nameFile.'.'.$ext.'\')">'.JText::_('ACY_RENAME_OR_REPLACE').'</a>';
	}

	if(!JFile::upload($file["tmp_name"], rtrim($uploadPath, DS).DS.$file["name"])){
		if(!move_uploaded_file($file["tmp_name"], rtrim($uploadPath, DS).DS.$file["name"])){
			acymailing_display(JText::sprintf('FAIL_UPLOAD', '<b><i>'.htmlspecialchars($file["tmp_name"], ENT_COMPAT, 'UTF-8').'</i></b>', '<b><i>'.htmlspecialchars(rtrim($uploadPath, DS).DS.$file["name"], ENT_COMPAT, 'UTF-8').'</i></b>'), 'error');
			return false;
		}
	}

	if($onlyPict && $imageSize[0] > 1000){
		$pictureHelper = acymailing_get('helper.acypict');
		if($pictureHelper->available()){
			$pictureHelper->maxHeight = 9999;
			$pictureHelper->maxWidth = 700;
			$pictureHelper->destination = $uploadPath;
			$thumb = $pictureHelper->generateThumbnail(rtrim($uploadPath, DS).DS.$file["name"], $file["name"]);
			$resize = JFile::move($thumb['file'], $uploadPath.DS.$file["name"]);
			if($thumb) $additionalMsg .= '<br />'.JText::_('IMAGE_RESIZED');
		}
	}
	acymailing_display('<strong>'.JText::_('SUCCESS_FILE_UPLOAD').'</strong>'.$additionalMsg, 'success');
	return $file["name"];
}

function acymailing_getFilesFolder($folder = 'upload', $multipleFolders = false){
	$my = JFactory::getUser();
	$db = JFactory::getDBO();
	$app = JFactory::getApplication();
	$listClass = acymailing_get('class.list');
	if($app->isAdmin()){
		$allLists = $listClass->getLists('listid');
	}else{
		$allLists = $listClass->getFrontendLists('listid');
		if(acymailing_level(1)) $allLists = $listClass->onlyCurrentLanguage($allLists);
		if(acymailing_level(3)) $allLists = $listClass->onlyAllowedLists($allLists);
	}
	$newFolders = array();

	$config =& acymailing_config();
	if($folder == 'upload'){
		$uploadFolder = $config->get('uploadfolder', 'media/com_acymailing/upload');
	}else{
		$uploadFolder = $config->get('mediafolder', 'media/com_acymailing/upload');
	}

	$folders = explode(',', $uploadFolder);

	foreach($folders as $k => $folder){
		$folders[$k] = trim($folder, '/');
		if(strpos($folder, '{userid}') !== false) $folders[$k] = str_replace('{userid}', $my->id, $folders[$k]);

		if(strpos($folder, '{listalias}') !== false){
			if(empty($allLists)){
				$noList = new stdClass();
				$noList->alias = 'none';
				$allLists = array($noList);
			}

			foreach($allLists as $oneList){
				$newFolders[] = str_replace('{listalias}', strtolower(str_replace(array(' ', '-'), '_', $oneList->alias)), $folders[$k]);
			}

			$folders[$k] = '';
			continue;
		}

		if(strpos($folder, '{groupid}') !== false || strpos($folder, '{groupname}') !== false){
			if(ACYMAILING_J16){
				jimport('joomla.access.access');
				$groups = JAccess::getGroupsByUser($my->id, false);
			}else{
				$groups = array($my->gid);
			}

			JArrayHelper::toInteger($groups);

			if(ACYMAILING_J16){
				$db->setQuery('SELECT id, title FROM #__usergroups WHERE id IN ('.implode(',', $groups).')');
				$completeGroups = $db->loadObjectList();
			}else{
				$groupObject = new stdClass();
				$groupObject->id = $my->gid;
				$groupObject->title = $my->usertype;
				$completeGroups = array($groupObject);
			}

			foreach($completeGroups as $group){
				$newFolders[] = str_replace(array('{groupid}', '{groupname}'), array($group->id, strtolower(str_replace(' ', '_', $group->title))), $folders[$k]);
			}

			$folders[$k] = '';
		}
	}

	$folders = array_merge($folders, $newFolders);
	$folders = array_filter($folders);
	sort($folders);
	if($multipleFolders){
		return $folders;
	}else{
		return array_shift($folders);
	}
}

function acymailing_generateArborescence($folders){
	$folderList = array();
	foreach($folders as $folder){
		$folderPath = JPath::clean(ACYMAILING_ROOT.trim(str_replace('/', DS, trim($folder)), DS));
		if(!file_exists($folderPath)) acymailing_createDir($folderPath);
		$subFolders = JFolder::listFolderTree($folderPath, '', 15);
		$folderList[$folder] = array();
		foreach($subFolders as $oneFolder){
			$subFolder = str_replace(ACYMAILING_ROOT, '', $oneFolder['relname']);
			$subFolder = str_replace(DS, '/', $subFolder);
			$folderList[$folder][$subFolder] = ltrim($subFolder, '/');
		}
		$folderList[$folder] = array_unique($folderList[$folder]);
	}
	return $folderList;
}

function acymailing_checkToken(){
	JRequest::checkToken() || JRequest::checkToken('get') || JSession::checkToken('get') || die('Invalid Token');
}

function getSpecialContent(){
	$config = acymailing_config();
	$level = strtolower($config->get('level', 'starter'));
	$version = $config->get('version', '0.0.0');
	$urlSite = trim(base64_encode(preg_replace('#https?://(www\.)?#i', '', ACYMAILING_LIVE)), '=/');

	ob_start();
	$url = ACYMAILING_SPECIALCONTENTURL.'getSpecialContent&component=acymailing&level='.$level.'&version='.$version.'&urlsite='.$urlSite;
	$dataDisplay = acymailing_fileGetContent($url, 30);
	$warnings = ob_get_clean();
	if(empty($dataDisplay) || $dataDisplay === false || !empty($warnings)){
		acymailing_display('Could not load your information from our server'.((!empty($warnings) && defined('JDEBUG') && JDEBUG) ? $warnings : ''), 'error');
		return;
	}

	$decodedInformation = json_decode($dataDisplay, true);
	if(!empty($decodedInformation['messages']) || !empty($decodedInformation['error'])){
		$msgError = (!empty($decodedInformation['messages'])) ? $decodedInformation['messages'].'<br />' : '';
		$msgError .= (!empty($decodedInformation['error'])) ? $decodedInformation['error'] : '';
		acymailing_display($msgError, 'error');
		return;
	}
	if(empty($decodedInformation['data']['content']) || empty($decodedInformation['data']['displayType'])){
		return;
	}
	return $decodedInformation['data'];
}

class acymailing{

	static function initModule($includejs, $params){
		return acymailing_initModule($includejs, $params);
	}

	static function initJSStrings($includejs = 'header', $params = null){
		return acymailing_initJSStrings($includejs, $params);
	}

	static function getModuleFormName(){
		return acymailing_getModuleFormName();
	}

	static function absoluteURL($text){
		return acymailing_absoluteURL($text);
	}

	static function getDate($time = 0, $format = '%d %B %Y %H:%M'){
		return acymailing_getDate($time, $format);
	}

	static function isAllowed($allowedGroups, $groups = null){
		return acymailing_isAllowed($allowedGroups, $groups);
	}

	static function getTime($date){
		return acymailing_getTime($date);
	}

	static function loadLanguage(){
		return acymailing_loadLanguage();
	}

	static function level($level){
		return acymailing_level($level);
	}

	static function createDir($dir, $report = true){
		return acymailing_createDir($dir, $report);
	}

	static function replaceDate($mydate){
		return acymailing_replaceDate($mydate);
	}

	static function frontendLink($link, $popup = false){
		return acymailing_frontendLink($link, $popup);
	}

	static function display($messages, $type = 'success'){
		return acymailing_display($messages, $type);
	}

	static function completeLink($link, $popup = false, $redirect = false){
		return acymailing_completeLink($link, $popup, $redirect);
	}

	static function table($name, $component = true){
		return acymailing_table($name, $component);
	}

	static function secureField($fieldName){
		return acymailing_secureField($fieldName);
	}

	static function &config($reload = false){
		return acymailing_config($reload);
	}

	static function search($searchString, $object){
		return acymailing_search($searchString, $object);
	}

	static function get($path){
		return acymailing_get($path);
	}

	static function getCID($field = ''){
		return acymailing_getCID($field);
	}

	static function tooltip($desc, $title = ' ', $image = 'tooltip.png', $name = '', $href = '', $link = 1){
		return acymailing_tooltip($desc, $title, $image, $name, $href, $link);
	}
}


class acymailingController extends acymailingControllerCompat{

	var $pkey = '';
	var $table = '';
	var $groupMap = '';
	var $groupVal = '';
	var $aclCat = '';

	function __construct($config = array()){
		parent::__construct($config);

		$this->registerDefaultTask('listing');
	}

	function getModel($name = '', $prefix = '', $config = array()){
		return false;
	}

	function listing(){
		if(!empty($this->aclCat) AND !$this->isAllowed($this->aclCat, 'manage')) return;
		JRequest::setVar('layout', 'listing');
		return parent::display();
	}

	function isAllowed($cat, $action){
		if(acymailing_level(3)){
			$config = acymailing_config();
			if(!acymailing_isAllowed($config->get('acl_'.$cat.'_'.$action, 'all'))){
				acymailing_display(JText::_('ACY_NOTALLOWED'), 'error');
				return false;
			}
		}
		return true;
	}

	function edit(){
		if(!empty($this->aclCat) AND !$this->isAllowed($this->aclCat, 'manage')) return;
		JRequest::setVar('hidemainmenu', 1);
		JRequest::setVar('layout', 'form');
		return parent::display();
	}


	function add(){
		if(!empty($this->aclCat) AND !$this->isAllowed($this->aclCat, 'manage')) return;
		JRequest::setVar('cid', array());
		JRequest::setVar('hidemainmenu', 1);
		JRequest::setVar('layout', 'form');
		return parent::display();
	}

	function apply(){
		$this->store();
		return $this->edit();
	}

	function save(){
		$this->store();
		return $this->listing();
	}

	function save2new(){
		$this->store();
		JRequest::setVar('cid', array());
		JRequest::setVar('hidemainmenu', 1);
		JRequest::setVar('layout', 'form');
		JRequest::setVar($this->pkey, '');
		return parent::display();
	}

	function orderdown(){
		if(!empty($this->aclCat) AND !$this->isAllowed($this->aclCat, 'manage')) return;
		JRequest::checkToken() or jexit('Invalid Token');

		$orderClass = acymailing_get('helper.order');
		$orderClass->pkey = $this->pkey;
		$orderClass->table = $this->table;
		$orderClass->groupMap = $this->groupMap;
		$orderClass->groupVal = $this->groupVal;
		$orderClass->order(true);

		return $this->listing();
	}

	function orderup(){
		if(!empty($this->aclCat) AND !$this->isAllowed($this->aclCat, 'manage')) return;
		JRequest::checkToken() or jexit('Invalid Token');

		$orderClass = acymailing_get('helper.order');
		$orderClass->pkey = $this->pkey;
		$orderClass->table = $this->table;
		$orderClass->groupMap = $this->groupMap;
		$orderClass->groupVal = $this->groupVal;
		$orderClass->order(false);

		return $this->listing();
	}

	function saveorder(){
		if(!empty($this->aclCat) AND !$this->isAllowed($this->aclCat, 'manage')) return;
		JRequest::checkToken() or jexit('Invalid Token');

		$orderClass = acymailing_get('helper.order');
		$orderClass->pkey = $this->pkey;
		$orderClass->table = $this->table;
		$orderClass->groupMap = $this->groupMap;
		$orderClass->groupVal = $this->groupVal;
		$orderClass->save();

		return $this->listing();
	}
}


class acymailingClass extends JObject{

	var $tables = array();

	var $pkey = '';

	var $namekey = '';

	var $errors = array();

	function __construct($config = array()){
		$this->database = JFactory::getDBO();

		return parent::__construct($config);
	}


	function save($element){
		$pkey = $this->pkey;
		if(empty($element->$pkey)){
			$status = $this->database->insertObject(acymailing_table(end($this->tables)), $element);
		}else{
			if(count((array)$element) > 1){
				$status = $this->database->updateObject(acymailing_table(end($this->tables)), $element, $pkey);
			}else{
				$status = true;
			}
		}
		if(!$status){
			$this->errors[] = substr(strip_tags($this->database->getErrorMsg()), 0, 200).'...';
		}

		if($status) return empty($element->$pkey) ? $this->database->insertid() : $element->$pkey;
		return false;
	}

	function delete($elements){
		if(!is_array($elements)){
			$elements = array($elements);
		}

		if(empty($elements)) return 0;

		$column = is_numeric(reset($elements)) ? $this->pkey : $this->namekey;

		foreach($elements as $key => $val){
			$elements[$key] = $this->database->Quote($val);
		}

		if(empty($column) || empty($this->pkey) || empty($this->tables) || empty($elements)) return false;

		$whereIn = ' WHERE '.acymailing_secureField($column).' IN ('.implode(',', $elements).')';
		$result = true;

		JPluginHelper::importPlugin('acymailing');
		$dispatcher = JDispatcher::getInstance();

		foreach($this->tables as $oneTable){
			$dispatcher->trigger('onAcyBefore'.ucfirst($oneTable).'Delete', array(&$elements));
			$query = 'DELETE FROM '.acymailing_table($oneTable).$whereIn;
			$this->database->setQuery($query);
			$result = $this->database->query() && $result;
		}


		if(!$result) return false;

		return $this->database->getAffectedRows();
	}
}

acymailing_loadLanguage();
$app = JFactory::getApplication();
$config = acymailing_config();
if($app->isAdmin()){
	define('ACYMAILING_IMAGES', '../media/'.ACYMAILING_COMPONENT.'/images/');
	define('ACYMAILING_CSS', '../media/'.ACYMAILING_COMPONENT.'/css/');
	define('ACYMAILING_JS', '../media/'.ACYMAILING_COMPONENT.'/js/');
}else{
	define('ACYMAILING_IMAGES', JURI::base(true).'/media/'.ACYMAILING_COMPONENT.'/images/');
	define('ACYMAILING_CSS', JURI::base(true).'/media/'.ACYMAILING_COMPONENT.'/css/');
	define('ACYMAILING_JS', JURI::base(true).'/media/'.ACYMAILING_COMPONENT.'/js/');
}

if(!$config->get('ssl_links', 0)){
	define('ACYMAILING_LIVE', rtrim(str_replace('https:', 'http:', JURI::root()), '/').'/');
}else{
	define('ACYMAILING_LIVE', rtrim(str_replace('http:', 'https:', JURI::root()), '/').'/');
}

JHTML::_('select.booleanlist', 'acymailing');
if(ACYMAILING_J30 && ($app->isAdmin() || $config->get('bootstrap_frontend', 0))){
	require(ACYMAILING_BACK.'compat'.DS.'bootstrap.php');
}else{
	class JHtmlAcyselect extends JHTMLSelect{
	}
}
