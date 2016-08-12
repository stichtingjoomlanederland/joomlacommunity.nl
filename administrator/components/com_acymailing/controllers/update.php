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

class UpdateController extends acymailingController{

	function __construct($config = array()){
		parent::__construct($config);
		$this->registerDefaultTask('update');
	}

	function listing(){
		return $this->update();
	}

	function install(){
		acymailing_increasePerf();

		$newConfig = new stdClass();
		$newConfig->installcomplete = 1;
		$config = acymailing_config();

		$updateHelper = acymailing_get('helper.update');

		if(!$config->save($newConfig)){
			$updateHelper->installTables();
			return;
		}

		jimport('joomla.filesystem.folder');
		$frontLanguages = JFolder::folders(JPATH_ROOT.DS.'language', '-');
		$backLanguages = JFolder::folders(JPATH_ADMINISTRATOR.DS.'language', '-');
		$installedLanguages = array_unique(array_merge($frontLanguages, $backLanguages));
		if(($key = array_search('en-GB', $installedLanguages)) !== false) unset($installedLanguages[$key]);

		if(!empty($installedLanguages)){
			$js = 'try{
				var ajaxCall = new Ajax("index.php?option=com_acymailing&ctrl=file&task=installLanguages&tmpl=component&languages='.implode(',', $installedLanguages).'",{
					method: "get",
					onComplete: function(responseText, responseXML) {
						container = document.getElementById("acymailing_div");
						container.innerHTML = responseText+container.innerHTML;
					}
				}).request();
			}catch(err){
				new Request({
					url:"index.php?option=com_acymailing&ctrl=file&task=installLanguages&tmpl=component&languages='.implode(',', $installedLanguages).'",
					method: "get",
					onSuccess: function(responseText, responseXML) {
						container = document.getElementById("acymailing_div");
						container.innerHTML = responseText+container.innerHTML;
					}
				}).send();
			}';
			$doc = JFactory::getDocument();
			$doc->addScriptDeclaration($js);
		}

		$updateHelper->initList();
		$updateHelper->installTemplates();
		$updateHelper->installNotifications();
		$updateHelper->installMenu();
		$updateHelper->installExtensions();
		$updateHelper->installBounceRules();
		$updateHelper->fixDoubleExtension();
		$updateHelper->addUpdateSite();
		$updateHelper->fixMenu();

		if(ACYMAILING_J30) JFile::move(ACYMAILING_BACK.'acymailing_j3.xml', ACYMAILING_BACK.'acymailing.xml');

		$acyToolbar = acymailing::get('helper.toolbar');
		$acyToolbar->setTitle('AcyMailing', 'dashboard');
		$acyToolbar->display();

		$this->_iframe(ACYMAILING_UPDATEURL.'install&fromversion='.JRequest::getCmd('fromversion').'&fromlevel='.JRequest::getCmd('fromlevel'));
	}

	function update(){

		$config = acymailing_config();
		if(!acymailing_isAllowed($config->get('acl_config_manage', 'all'))){
			acymailing_display(JText::_('ACY_NOTALLOWED'), 'error');
			return false;
		}

		$acyToolbar = acymailing::get('helper.toolbar');
		$acyToolbar->setTitle(JText::_('UPDATE_ABOUT'), 'update');
		$acyToolbar->link(acymailing_completeLink('dashboard'), JText::_('ACY_CLOSE'), 'cancel');
		$acyToolbar->display();

		return $this->_iframe(ACYMAILING_UPDATEURL.'update');
	}

	function _iframe($url){

		$config = acymailing_config();
		$url .= '&version='.$config->get('version').'&level='.$config->get('level').'&component=acymailing';
		?>
		<div id="acymailing_div">
			<iframe allowtransparency="true" scrolling="auto" height="700px" frameborder="0" width="100%" name="acymailing_frame" id="acymailing_frame" src="<?php echo $url; ?>">
			</iframe>
		</div>
	<?php
	}

	function checkForNewVersion(){

		$config = acymailing_config();
		ob_start();
		$url = ACYMAILING_UPDATEURL.'loadUserInformation&component=acymailing&level='.strtolower($config->get('level', 'starter'));
		if(acymailing_level(1)) $url .= '&domain='.urlencode(rtrim(ACYMAILING_LIVE, '/'));
		$userInformation = acymailing_fileGetContent($url, 30);
		$warnings = ob_get_clean();
		$result = (!empty($warnings) && defined('JDEBUG') && JDEBUG) ? $warnings : '';

		if(empty($userInformation) || $userInformation === false){
			echo json_encode(array('content' => '<br/><span style="color:#C10000;">Could not load your information from our server</span><br/>'.$result));
			exit;
		}

		$decodedInformation = json_decode($userInformation, true);

		$newConfig = new stdClass();
		$newConfig->latestversion = $decodedInformation['latestversion'];
		$newConfig->expirationdate = $decodedInformation['expiration'];
		$newConfig->lastlicensecheck = time();
		$config->save($newConfig);

		$menuHelper = acymailing_get('helper.acymenu');
		$myAcyArea = $menuHelper->myacymailingarea();

		echo json_encode(array('content' => $myAcyArea));
		exit;
	}
}
