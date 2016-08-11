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

class CpanelViewCpanel extends acymailingView{
	function display($tpl = null){
		JHTML::_('behavior.modal', 'a.modal');

		$toggleClass = acymailing_get('helper.toggle');
		$config = acymailing_config();
		$db = JFactory::getDBO();
		$doc = JFactory::getDocument();
		$app = JFactory::getApplication();
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');

		$lg = JFactory::getLanguage();
		$language = $lg->getTag();
		$styleRemind = 'float:right;margin-right:30px;position:relative;';
		$loadLink = '<a onclick="window.document.getElementById(\'acymailing_messages_warning\').style.display = \'none\';return true;" class="modal" rel="{handler: \'iframe\', size:{x:800, y:500}}" href="index.php?option=com_acymailing&amp;tmpl=component&amp;ctrl=file&amp;task=latest&amp;code='.$language.'">'.JText::_('LOAD_LATEST_LANGUAGE').'</a>';
		if(!file_exists(ACYMAILING_ROOT.'language'.DS.$language.DS.$language.'.com_acymailing.ini')){
			if($config->get('errorlanguagemissing', 1)){
				$notremind = '<small style="'.$styleRemind.'">'.$toggleClass->delete('acymailing_messages_warning', 'errorlanguagemissing_0', 'config', false, JText::_('DONT_REMIND')).'</small>';
				acymailing_enqueueMessage(JText::_('MISSING_LANGUAGE').' '.$loadLink.' '.$notremind, 'warning');
			}
		}elseif(version_compare(JText::_('ACY_LANG_VERSION'), $config->get('version'), '<')){
			if($config->get('errorlanguageupdate', 1)){
				$notremind = '<small style="'.$styleRemind.'">'.$toggleClass->delete('acymailing_messages_warning', 'errorlanguageupdate_0', 'config', false, JText::_('DONT_REMIND')).'</small>';
				acymailing_enqueueMessage(JText::_('UPDATE_LANGUAGE').' '.$loadLink.' '.$notremind, 'warning');
			}
		}

		if(ACYMAILING_J30 && $app->getTemplate() == 'hathor'){
			if($config->get('errortemplatenotisis', 1)){
				$message = ' You can change the default Back-end template <a href="index.php?option=com_templates&view=styles">Here</a> or change your personnal default Back-end template by editing your user profile <a href="index.php?option=com_users&view=users">Here</a>, tab "Basic Settings".';
				$personalTemplate = JFactory::getUser()->getParam('admin_style');
				if(!empty($personalTemplate)){
					$message = ' You can change your personnal default Back-end template by editing your user profile <a href="index.php?option=com_users&view=users">Here</a>, tab "Basic Settings".';
				}
				$notremind = '<small style="'.$styleRemind.'">'.$toggleClass->delete('acymailing_messages_warning', 'errortemplatenotisis_0', 'config', false, JText::_('DONT_REMIND')).'</small>';
				acymailing_enqueueMessage('You should rather use the isis template in the Back-End which suits more AcyMailing.'.$message.$notremind, 'warning');
			}
		}

		if($config->get('migratefromj2', 1) && ACYMAILING_J16 && !ACYMAILING_J30){
			$notremind = '<small style="'.$styleRemind.'">'.$toggleClass->delete('acymailing_messages_error', 'migratefromj2_0', 'config', false, JText::_('DONT_REMIND')).'</small>';
			acymailing_enqueueMessage(JText::sprintf('ACY_MIGRATE', '<a target="_blank" href="http://www.acyba.com/index.php?option=com_updateme&ctrl=redirect&page=j25migration">'.JText::_('ACY_MIGRATE_HELP').'</a>').$notremind, 'warning');
		}

		$indexes = array('listsub', 'stats', 'list', 'mail', 'userstats', 'urlclick', 'history', 'template', 'queue', 'subscriber');
		$addIndexes = array('We recenty optimized our database...');
		foreach($indexes as $oneTable){
			if($config->get('optimize_'.$oneTable, 1)) continue;
			$addIndexes[] = 'Please '.$toggleClass->toggleText('addindex', $oneTable, 'config', 'click here').' to add indexes on the '.$oneTable.' table';
		}
		if(count($addIndexes) > 1) acymailing_enqueueMessage($addIndexes, 'warning');

		if(acymailing_level(1)){
			if($config->get('website') != ACYMAILING_LIVE){
				$updateHelper = acymailing_get('helper.update');
				$updateHelper->addUpdateSite();
			}

			$cronHelper = acymailing_get('helper.cron');
			$cronHelper->cleanStats();
		}


		$acyToolbar = acymailing::get('helper.toolbar');
		$acyToolbar->custom('test', JText::_('SEND_TEST'), 'send', false);
		$acyToolbar->divider();
		$acyToolbar->addButtonOption('apply', JText::_('ACY_APPLY'), 'apply', false);
		$acyToolbar->save();
		$acyToolbar->cancel();
		$acyToolbar->divider();
		$acyToolbar->help('config');
		$acyToolbar->setTitle(JText::_('ACY_CONFIGURATION'), 'cpanel');
		$acyToolbar->display();

		$elements = new stdClass();
		$elements->add_names = JHTML::_('acyselect.booleanlist', "config[add_names]", '', $config->get('add_names', true));
		$elements->embed_images = JHTML::_('acyselect.booleanlist', "config[embed_images]", '', $config->get('embed_images', 0));
		$elements->embed_files = JHTML::_('acyselect.booleanlist', "config[embed_files]", '', $config->get('embed_files', 1));
		$elements->multiple_part = JHTML::_('acyselect.booleanlist', "config[multiple_part]", '', $config->get('multiple_part', 0));

		$mailerMethods = array('elasticemail', 'smtp', 'sendmail');
		$js = "function updateMailer(mailermethod){"."\n";
		foreach($mailerMethods as $oneMethod){
			$js .= " window.document.getElementById('".$oneMethod."_config').style.display = 'none'; "."\n";
		}
		$js .= "if(window.document.getElementById(mailermethod+'_config')) {window.document.getElementById(mailermethod+'_config').style.display = 'block';} }";
		$js .= 'window.addEvent(\'domready\', function(){ updateMailer(\''.$config->get('mailer_method', 'phpmail').'\'); });';
		$doc->addScriptDeclaration($js);

		$encodingval = array();
		$encodingval[] = JHTML::_('select.option', 'binary', 'Binary');
		$encodingval[] = JHTML::_('select.option', 'quoted-printable', 'Quoted-printable');
		$encodingval[] = JHTML::_('select.option', '7bit', '7 Bit');
		$encodingval[] = JHTML::_('select.option', '8bit', '8 Bit');
		$encodingval[] = JHTML::_('select.option', 'base64', 'Base 64');
		$elements->encoding_format = JHTML::_('select.genericlist', $encodingval, "config[encoding_format]", 'size="1" style="width:150px;"', 'value', 'text', $config->get('encoding_format', 'base64'));

		$charset = acymailing_get('type.charset');
		$elements->charset = $charset->display("config[charset]", $config->get('charset', 'UTF-8'));

		$securedVals = array();
		$securedVals[] = JHTML::_('select.option', '', '- - -');
		$securedVals[] = JHTML::_('select.option', 'ssl', 'SSL');
		$securedVals[] = JHTML::_('select.option', 'tls', 'TLS');
		$elements->smtp_secured = JHTML::_('select.genericlist', $securedVals, "config[smtp_secured]", 'size="1" style="width:100px;"', 'value', 'text', $config->get('smtp_secured'));

		$elements->smtp_auth = JHTML::_('acyselect.booleanlist', "config[smtp_auth]", '', $config->get('smtp_auth', 0));
		$elements->smtp_keepalive = JHTML::_('acyselect.booleanlist', "config[smtp_keepalive]", '', $config->get('smtp_keepalive', 1));

		$elements->allow_visitor = JHTML::_('acyselect.booleanlist', "config[allow_visitor]", '', $config->get('allow_visitor', 1));

		$editorType = acymailing_get('type.editor');
		$elements->editor = $editorType->display('config[editor]', $config->get('editor'));

		$elements->subscription_message = JHTML::_('acyselect.booleanlist', "config[subscription_message]", '', $config->get('subscription_message', 1));
		$elements->confirmation_message = JHTML::_('acyselect.booleanlist', "config[confirmation_message]", '', $config->get('confirmation_message', 1));
		$elements->unsubscription_message = JHTML::_('acyselect.booleanlist', "config[unsubscription_message]", '', $config->get('unsubscription_message', 1));
		$elements->welcome_message = JHTML::_('acyselect.booleanlist', "config[welcome_message]", '', $config->get('welcome_message', 1));
		$elements->unsub_message = JHTML::_('acyselect.booleanlist', "config[unsub_message]", '', $config->get('unsub_message', 1));
		$elements->confirm_message = JHTML::_('acyselect.booleanlist', "config[confirm_message]", '', $config->get('confirm_message', 0));


		if(acymailing_level(1)){
			$js = 'var selectedForward = '.$config->get('forward', 0).'
					function confirmForward(clickedForward){
						if(clickedForward == selectedForward || clickedForward != 1) return true;

						var cnfrm = confirm(\''.str_replace("'", "\'", JText::_('ACY_FORWARDCHOICE_CONFIRMATION')).'\');
						if(!cnfrm) return true;';

				if(ACYMAILING_J30){
					$js .= '
					var labels = document.getElementById("config_forwardfieldset").getElementsByTagName("label");
					for(oneLabel in labels){
						if(isNaN(oneLabel)) continue;
						if(labels[oneLabel].getAttribute("for") == "config_forward2"){
							labels[oneLabel].click();
						}
					}';
				}else{
					$js .= 'document.getElementById("config[forward]2").checked = true;';
				}

				$js .= '}';
			$doc->addScriptDeclaration($js);

			$forwardValues = array();
			$forwardValues[] = JHTML::_('select.option', 0, JTEXT::_('JOOMEXT_NO'));
			$forwardValues[] = JHTML::_('select.option', 1, JTEXT::_('JOOMEXT_YES'));
			$forwardValues[] = JHTML::_('select.option', 2, JTEXT::_('JOOMEXT_YES_FORWARD'));
			$elements->forward = JHTML::_('acyselect.radiolist', $forwardValues, "config[forward]", 'onclick="confirmForward(this.value);"', 'value', 'text', $config->get('forward', 0));
		}else{
			$elements->forward = acymailing_getUpgradeLink('essential');
		}

		if(acymailing_level(1)){
			$js = "function updateDKIM(dkimval){
						if(dkimval == 1){document.getElementById('dkim_config').style.display = 'block';}
						else{document.getElementById('dkim_config').style.display = 'none';}
						};";
			$doc->addScriptDeclaration($js);
			if(function_exists('openssl_sign')){
				$elements->dkim = JHTML::_('acyselect.booleanlist', "config[dkim]", 'onclick="updateDKIM(this.value)"', $config->get('dkim', 0));
			}else{
				$elements->dkim = '<input type="hidden" name="config[dkim]" value="0" />PHP Extension openssl not enabled';
			}

			$js = "function updateQueueProcess(newvalue){";
			$js .= "if(newvalue == 'onlyauto') {window.document.getElementById('method_auto').style.display = ''; window.document.getElementById('method_manual').style.display = 'none';}";
			$js .= "if(newvalue == 'auto') {window.document.getElementById('method_auto').style.display = ''; window.document.getElementById('method_manual').style.display = '';}";
			$js .= "if(newvalue == 'manual') {window.document.getElementById('method_auto').style.display = 'none'; window.document.getElementById('method_manual').style.display = '';}";
			$js .= '};';

			$doc->addScriptDeclaration($js);

			$queueType = array();
			$queueType[] = JHTML::_('select.option', 'onlyauto', JText::_('AUTO_ONLY'));
			$queueType[] = JHTML::_('select.option', 'auto', JText::_('AUTO_MAN'));
			$queueType[] = JHTML::_('select.option', 'manual', JText::_('MANUAL_ONLY'));
			$elements->queue_type = JHTML::_('acyselect.radiolist', $queueType, "config[queue_type]", 'onclick="updateQueueProcess(this.value);"', 'value', 'text', $config->get('queue_type', 'auto'));
		}else{
			$elements->dkim = acymailing_getUpgradeLink('essential');
		}

		$js = 'var selectedHTTPS = '.($config->get('ssl_links', 0) == 0 ? 'false;' : 'true;').'
		function confirmHTTPS(element){
			var clickedHTTPS = (element == 1);
			if(clickedHTTPS == selectedHTTPS) return true;
			if(clickedHTTPS){
				var cnfrm = confirm(\''.str_replace("'", "\'", JText::_('ACY_SSLCHOICE_CONFIRMATION')).'\');
				if(!cnfrm){';
		if(ACYMAILING_J30){
			$js .= 'var labels = document.getElementById(\'config_ssl_linksfieldset\').getElementsByTagName(\'label\');
					if(labels[0].hasClass(\'btn-success\')){
						labels[1].click();
						return true;
					}else{
						labels[0].click();
						return true;
					}';
		}else{
			$js .= 'return false;';
		}
		$js .= '}
			}
			selectedHTTPS = clickedHTTPS;
			return true;
		}';
		$doc->addScriptDeclaration($js);
		$elements->ssl_links = JHTML::_('acyselect.booleanlist', "config[ssl_links]", 'onclick="return confirmHTTPS(this.value);"', $config->get('ssl_links', 0));

		$delayTypeManual = acymailing_get('type.delay');
		$elements->queue_pause = $delayTypeManual->display('config[queue_pause]', $config->get('queue_pause'), 0);
		$delayTypeAuto = acymailing_get('type.delay');
		$delayTypeAuto->onChange = "window.document.getElementById('autoFrequencyWarning').style.display='inline';";
		$onChangeMsg = '<span style="display:none;color:red;" id="autoFrequencyWarning">'.JText::_('ACY_CRON_CHANGE_FREQUENCY_WARNING').'</span>';
		$elements->cron_frequency = $delayTypeAuto->display('config[cron_frequency]', $config->get('cron_frequency'), 2).$onChangeMsg;

		$js = "function detectTimeout(id){
				try{
					window.document.getElementById(id).className = 'onload';
					window.document.getElementById(id).innerHTML = '".str_replace("'", "\'", JText::_('ACY_CLOSE_TIMEOUT'))."';

					try{
						new Ajax('".rtrim(JURI::root(), '/')."/index.php?option=com_acymailing&tmpl=component&ctrl=stats&task=detecttimeout&seckey=".$config->get('security_key')."',{ method: 'get', onComplete: function() { document.id(id).innerHTML = 'Done!'; window.document.getElementById(id).className = 'loading'; }}).request();
					}catch(err){
						new Request({url:'".rtrim(JURI::root(), '/')."/index.php?option=com_acymailing&tmpl=component&ctrl=stats&task=detecttimeout&seckey=".$config->get('security_key')."',method: 'get', onComplete: function(response) { document.id(id).innerHTML = 'Done!'; window.document.getElementById(id).className = 'loading'; }}).send();
					}
				}catch(err){alert('Could not load the max execution time value : '+err);}
				return;
		}";
		$maxexecutiontime = $config->get('max_execution_time');
		if(empty($maxexecutiontime) && (intval($config->get('last_maxexec_check')) < (time() - 60))){
			$js .= 'window.addEventListener("load", function() {detectTimeout(\'timeoutcheck\')});';
		}
		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration($js);

		$cssval = array('css_frontend' => 'component', 'css_module' => 'module', 'css_backend' => 'backend');
		foreach($cssval as $configval => $type){
			$myvals = array();
			$myvals[] = JHTML::_('select.option', '', JText::_('ACY_NONE'));

			if($configval == 'css_backend'){
				$myvals[] = JHTML::_('select.option', 'backend_custom', JText::_('ACY_CUSTOM'));
				$editFileName = $config->get('css_backend', 'default');
			}else{

				$regex = '^'.$type.'_([-_a-z0-9]*)\.css$';
				$allCSSFiles = JFolder::files(ACYMAILING_MEDIA.'css', $regex);

				$family = '';
				foreach($allCSSFiles as $oneFile){
					preg_match('#'.$regex.'#i', $oneFile, $results);
					$fileName = str_replace('default_', '', $results[1]);
					$fileNameArray = explode('_', $fileName);
					if(count($fileNameArray) == 2){
						if($fileNameArray[0] != $family){
							if(!empty($family)) $myvals[] = JHTML::_('select.option', '</OPTGROUP>');
							$family = $fileNameArray[0];
							$myvals[] = JHTML::_('select.option', '<OPTGROUP>', ucfirst($family));
						}
						unset($fileNameArray[0]);
						$fileName = implode('_', $fileNameArray);
					}

					$fileName = ucwords(str_replace('_', ' ', $fileName));
					$myvals[] = JHTML::_('select.option', $results[1], $fileName);
				}
				if(!empty($family)) $myvals[] = JHTML::_('select.option', '</OPTGROUP>');
				$editFileName = $type.'_'.$config->get($configval, 'default');
			}

			$currentVal = $config->get($configval, 'default');
			$aStyle = empty($currentVal) ? 'style="display:none"' : '';
			$js = 'onchange="updateCSSLink(\''.$configval.'\',\''.$type.'\',this.value);"';

			$elements->$configval = JHTML::_('select.genericlist', $myvals, 'config['.$configval.']', 'class="inputbox" size="1" '.$js, 'value', 'text', $config->get($configval, 'default'), $configval.'_choice');
			$linkEdit = 'index.php?option=com_acymailing&amp;tmpl=component&amp;ctrl=file&amp;task=css&amp;var='.$configval.'&amp;file='.$editFileName;
			$elements->$configval .= ' <a id="'.$configval.'_link" '.$aStyle.' class="modal" title="'.JText::_('ACY_EDIT', true).'"  href="'.$linkEdit.'" rel="{handler: \'iframe\', size:{x:800, y:500}}"><i class="acyicon-edit" style="margin: 5px 5px 0px 5px; display: inline-block;"></i></a>';
		}

		$js = "function updateCSSLink(myid,type,newval){
			if(newval){document.getElementById(myid+'_link').style.display = '';}else{document.getElementById(myid+'_link').style.display = 'none'}
			if(myid == 'css_backend') filename = newval;
			else filename = type+'_'+newval;
			document.getElementById(myid+'_link').href = 'index.php?option=com_acymailing&tmpl=component&ctrl=file&task=css&var='+myid+'&file='+filename;
		}";
		$doc->addScriptDeclaration($js);
		$bootstrapFrontValues = array();
		$bootstrapFrontValues[] = JHTML::_('select.option', 0, JTEXT::_('JOOMEXT_NO'));
		$bootstrapFrontValues[] = JHTML::_('select.option', 1, 'Bootstrap 2');
		$bootstrapFrontValues[] = JHTML::_('select.option', 2, 'Bootstrap 3');
		$elements->bootstrap_frontend = JHTML::_('acyselect.radiolist', $bootstrapFrontValues, "config[bootstrap_frontend]", '', 'value', 'text', $config->get('bootstrap_frontend', 0));

		$elements->colortype = acymailing_get('type.color');

		$elements->use_sef = JHTML::_('acyselect.booleanlist', "config[use_sef]", '', $config->get('use_sef', 0));

		if(acymailing_level(1)){
			$trackingMode = $config->get('trackingsystem', 'acymailing');
			$tracking_system = '<input type="checkbox" name="config[trackingsystem][]" id="trackingsystem[0]" value="acymailing" style="margin-left:10px" '.(stripos($trackingMode, 'acymailing') !== false ? 'checked="checked"' : '').'/> <label for="trackingsystem[0]">Acymailing</label>';
			$tracking_system .= '<input type="checkbox" name="config[trackingsystem][]" id="trackingsystem[1]" value="google" style="margin-left:10px;" '.(stripos($trackingMode, 'google') !== false ? 'checked="checked"' : '').'/> <label for="trackingsystem[1]">Google Analytics</label>';
			$tracking_system .= '<input type="hidden" name="config[trackingsystem][]" value="1"/>';
			$tracking_system_external_website = JHTML::_('acyselect.booleanlist', "config[trackingsystemexternalwebsite]", ' id="trackingsystemexternalwebsite"', $config->get('trackingsystemexternalwebsite', 1));
		}else{
			$tracking_system = acymailing_getUpgradeLink('essential');
			$tracking_system_external_website = acymailing_getUpgradeLink('essential');
		}
		$elements->tracking_system = $tracking_system;
		$elements->tracking_system_external_website = $tracking_system_external_website;

		$indexType = $config->get('indexFollow', '');
		$indexFollow = '<div style="float: left;"><input type="checkbox" name="config[indexFollow][]" id="indexFollow[0]" value="noindex" style="margin-left:10px" '.(stripos($indexType, 'noindex') !== false ? 'checked="checked"' : '').'/> <label for="indexFollow[0]">noindex</label></div>';
		$indexFollow .= '<div style="float: left;"><input type="checkbox" name="config[indexFollow][]" id="indexFollow[1]" value="nofollow" style="margin-left:10px" '.(stripos($indexType, 'nofollow') !== false ? 'checked="checked"' : '').'/> <label for="indexFollow[1]">nofollow</label></div>';
		$indexFollow .= '<input type="hidden" name="config[indexFollow][]" value="1"/>';
		$elements->indexFollow = $indexFollow;

		if(acymailing_level(3)){
			$geolocAvailable = true;
			$geolocation = '<input type="hidden" name="config[geolocation]" value="0"/>';
			$geoloc_api_key = '';
			if(!function_exists('curl_init')){
				$geolocAvailable = false;
				$geolocation .= 'The AcyMailing geolocation plugin needs the CURL library installed but it seems that it is not available on your server. Please contact your web hosting to set it up.';
			}
			if(!function_exists('json_decode')){
				if(!$geolocAvailable) $geolocation .= '<br />';
				$geolocAvailable = false;
				$geolocation .= 'The AcyMailing geolocation plugin can only work with PHP 5.2 at least. Please ask your web hosting to update your PHP version.';
			}

			if($geolocAvailable){
				$geoloc = $config->get('geolocation', '');
				$geolocation = '<span style="white-space:nowrap"><input type="checkbox" name="config[geolocation][]" id="geolocation_0" value="creation" style="margin-left:10px" '.(stripos($geoloc, 'creation') !== false ? 'checked="checked"' : '').'/> <label for="geolocation_0">'.JText::_('ON_USER_CREATE').'</label></span>';
				$geolocation .= ' <span style="white-space:nowrap"><input type="checkbox" name="config[geolocation][]" id="geolocation_1" value="modify" style="margin-left:10px;" '.(stripos($geoloc, 'modify') !== false ? 'checked="checked"' : '').'/> <label for="geolocation_1">'.JText::_('ON_USER_CHANGE').'</label></span>';
				$geolocation .= ' <span style="white-space:nowrap"><input type="checkbox" name="config[geolocation][]" id="geolocation_2" value="confirm" style="margin-left:10px;" '.(stripos($geoloc, 'confirm') !== false ? 'checked="checked"' : '').'/> <label for="geolocation_2">'.JText::_('GEOLOC_CONFIRM_SUB').'</label></span>';
				$geolocation .= ' <span style="white-space:nowrap"><input type="checkbox" name="config[geolocation][]" id="geolocation_3" value="clic" style="margin-left:10px;" '.(stripos($geoloc, 'clic') !== false ? 'checked="checked"' : '').'/> <label for="geolocation_3">'.JText::_('ON_USER_CLICK').'</label></span>';
				$geolocation .= ' <span style="white-space:nowrap"><input type="checkbox" name="config[geolocation][]" id="geolocation_4" value="open" style="margin-left:10px;" '.(stripos($geoloc, 'open') !== false ? 'checked="checked"' : '').'/> <label for="geolocation_4">'.JText::_('ON_OPEN_NEWS').'</label></span>';
				$geolocation .= ' <span style="white-space:nowrap"><input type="checkbox" name="config[geolocation][]" id="geolocation_5" value="unsubscription" style="margin-left:10px;" '.(stripos($geoloc, 'unsubscription') !== false ? 'checked="checked"' : '').'/> <label for="geolocation_5">'.JText::_('GEOLOC_UNSUB').'</label></span>';
				$geolocation .= '<input type="hidden" name="config[geolocation][]" value="1"/>';
				$geoloc_api_key = '<input class="inputbox" type="text" id="geoloc_api_key" name="config[geoloc_api_key]" style="width:450px" value="'.$this->escape($config->get('geoloc_api_key', '')).'">';
			}
		}else{
			$geolocation = acymailing_getUpgradeLink('enterprise');
			$geoloc_api_key = false;
		}
		$elements->geolocation = $geolocation;
		$elements->geoloc_api_key = $geoloc_api_key;

		if(!ACYMAILING_J16){
			$query = 'SELECT a.name, a.id as itemid, b.title  FROM `#__menu` as a JOIN `#__menu_types` as b on a.menutype = b.menutype WHERE a.access = 0 ORDER BY b.title ASC,a.ordering ASC';
		}else{
			$orderby = ACYMAILING_J30 ? 'a.lft' : 'a.ordering';
			$query = 'SELECT a.alias as name, a.id as itemid, b.title  FROM `#__menu` as a JOIN `#__menu_types` as b on a.menutype = b.menutype WHERE a.access = 1 AND a.client_id=0 AND a.parent_id != 0 ORDER BY b.title ASC,'.$orderby.' ASC';
		}

		$db->setQuery($query);
		$joomMenus = $db->loadObjectList();

		$menuvalues = array();
		$menuvalues[] = JHTML::_('select.option', '0', JText::_('ACY_NONE'));
		$lastGroup = '';
		foreach($joomMenus as $oneMenu){
			if($oneMenu->title != $lastGroup){
				if(!empty($lastGroup)) $menuvalues[] = JHTML::_('select.option', '</OPTGROUP>');
				$menuvalues[] = JHTML::_('select.option', '<OPTGROUP>', $oneMenu->title);
				$lastGroup = $oneMenu->title;
			}
			$menuvalues[] = JHTML::_('select.option', $oneMenu->itemid, $oneMenu->name);
		}

		$elements->acymailing_menu = JHTML::_('select.genericlist', $menuvalues, 'config[itemid]', 'size="1"', 'value', 'text', $config->get('itemid'));


		$acyrss_format = array();
		$acyrss_format[] = JHTML::_('select.option', '', JText::_('ACY_NONE'));
		$acyrss_format[] = JHTML::_('select.option', 'rss', 'RSS feed');
		$acyrss_format[] = JHTML::_('select.option', 'atom', 'Atom feed');
		$acyrss_format[] = JHTML::_('select.option', 'both', JText::_('ACY_ALL'));
		$elements->acyrss_format = JHTML::_('select.genericlist', $acyrss_format, "config[acyrss_format]", 'size="1"', 'value', 'text', $config->get('acyrss_format', ''));

		$acyrss_order = array();
		$acyrss_order[] = JHTML::_('select.option', 'senddate', JText::_('SEND_DATE'));
		$acyrss_order[] = JHTML::_('select.option', 'mailid', JText::_('ACY_ID'));
		$acyrss_order[] = JHTML::_('select.option', 'subject', JText::_('ACY_TITLE'));
		$elements->acyrss_order = JHTML::_('select.genericlist', $acyrss_order, "config[acyrss_order]", 'size="1"', 'value', 'text', $config->get('acyrss_order', 'senddate'));


		if(acymailing_level(1)){

			$cronreportval = array();
			$cronreportval[] = JHTML::_('select.option', '0', JText::_('JOOMEXT_NO'));
			$cronreportval[] = JHTML::_('select.option', '1', JText::_('ACY_EACH_TIME'));
			$cronreportval[] = JHTML::_('select.option', '2', JText::_('ACY_ONLY_ACTION'));
			$cronreportval[] = JHTML::_('select.option', '3', JText::_('ONLY_SOMETHING_WRONG'));

			$jscron = "function updateCronReport(){";
			$jscron .= "cronsendreport = window.document.getElementById('cronsendreport').value;";
			$jscron .= "if(cronsendreport != 0) {window.document.getElementById('cronreportdetail').style.display = 'block';}else{window.document.getElementById('cronreportdetail').style.display = 'none';}";
			$jscron .= '}';
			$jscron .= 'window.addEvent(\'domready\', function(){ updateCronReport(); });';

			$elements->cron_sendreport = JHTML::_('select.genericlist', $cronreportval, 'config[cron_sendreport]', 'class="inputbox" size="1" style="width:280px;" onchange="updateCronReport();"', 'value', 'text', (int)$config->get('cron_sendreport', 2), 'cronsendreport');


			$cronsave = array();
			$cronsave[] = JHTML::_('select.option', '0', JText::_('JOOMEXT_NO'));
			$cronsave[] = JHTML::_('select.option', '1', JText::_('SIMPLIFIED_REPORT'));
			$cronsave[] = JHTML::_('select.option', '2', JText::_('DETAILED_REPORT'));

			$jscron .= "function updateCronReportSave(){";
			$jscron .= "cronsavereport = window.document.getElementById('cronsavereport').value;";
			$jscron .= "if(cronsavereport != 0) {window.document.getElementById('cronreportsave').style.display = 'block';}else{window.document.getElementById('cronreportsave').style.display = 'none';}";
			$jscron .= '}';
			$jscron .= 'window.addEvent(\'domready\', function(){ updateCronReportSave(); });';
			$doc = JFactory::getDocument();
			$doc->addScriptDeclaration($jscron);

			$elements->cron_savereport = JHTML::_('select.genericlist', $cronsave, 'config[cron_savereport]', 'class="inputbox" size="1" style="width:200px;" onchange="updateCronReportSave();"', 'value', 'text', (int)$config->get('cron_savereport', 0), 'cronsavereport');

			$link = 'index.php?option=com_acymailing&amp;tmpl=component&amp;ctrl=cpanel&amp;task=cleanreport';
			$elements->deleteReport = '<a class="modal" href="'.$link.'" rel="{handler: \'iframe\', size: {x: 400, y: 100}}"><button class="acymailing_button_grey" onclick="return false">'.JText::_('REPORT_DELETE').'</button></a>';

			$link = 'index.php?option=com_acymailing&amp;tmpl=component&amp;ctrl=cpanel&amp;task=seereport';
			$elements->seeReport = '<a class="modal" href="'.$link.'" rel="{handler: \'iframe\', size: {x: 800, y: 500}}"><button class="acymailing_button_grey" onclick="return false">'.JText::_('REPORT_SEE').'</button></a>';

			$link = 'index.php?option=com_acymailing&amp;tmpl=component&amp;ctrl=email&amp;task=edit&amp;mailid=report';
			$elements->editReportEmail = '<a class="modal" href="'.$link.'" rel="{handler: \'iframe\', size:{x:800, y:500}}"><button class="acymailing_button_grey" onclick="return false">'.JText::_('REPORT_EDIT').'</button></a>';

			$elements->cron_url = ACYMAILING_LIVE.'index.php?option=com_acymailing&ctrl=cron';
			$item = $config->get('itemid');
			if(!empty($item)) $elements->cron_url .= '&Itemid='.$item;

			$delayType2 = acymailing_get('type.delay');
			$delayType3 = acymailing_get('type.delay');
			$elements->delete_stats = $delayType2->display('config[delete_stats]', $config->get('delete_stats', 31104000), 4);
			$elements->delete_history = $delayType3->display('config[delete_history]', $config->get('delete_history', 10368000), 4);

			$urlCron = 'https://www.acyba.com/index.php?option=com_updateme&ctrl=launcher&task=edit&component=acymailing&cronurl='.urlencode($elements->cron_url);
			$elements->cron_edit = '<a class="modal" href="'.$urlCron.'" rel="{handler: \'iframe\', size: {x: 800, y: 500}}"><button class="acymailing_button_grey" onclick="return false">'.JText::_('CREATE_CRON').'</button></a>';
		}

		$link = 'index.php?option=com_acymailing&amp;tmpl=component&amp;ctrl=email&amp;task=edit&amp;mailid=confirmation';
		$elements->editConfEmail = '<a class="modal" id="confirmemail"  href="'.$link.'" rel="{handler: \'iframe\', size:{x:800, y:500}}"><button class="acymailing_button_grey" onclick="return false">'.JText::_('EDIT_CONF_MAIL').'</button></a>';

		$link = 'index.php?option=com_acymailing&amp;tmpl=component&amp;ctrl=email&amp;task=edit&amp;mailid=notification_created';
		$elements->edit_notification_created = '<a class="modal" href="'.$link.'" rel="{handler: \'iframe\', size:{x:800, y:500}}"><button class="acymailing_button_grey" onclick="return false">'.JText::_('EDIT_NOTIFICATION_MAIL').'</button></a>';
		$link = 'index.php?option=com_acymailing&amp;tmpl=component&amp;ctrl=email&amp;task=edit&amp;mailid=notification_refuse';
		$elements->edit_notification_refuse = '<a class="modal" href="'.$link.'" rel="{handler: \'iframe\', size:{x:800, y:500}}"><button class="acymailing_button_grey" onclick="return false">'.JText::_('EDIT_NOTIFICATION_MAIL').'</button></a>';
		$link = 'index.php?option=com_acymailing&amp;tmpl=component&amp;ctrl=email&amp;task=edit&amp;mailid=notification_unsuball';
		$elements->edit_notification_unsuball = '<a class="modal" href="'.$link.'" rel="{handler: \'iframe\', size:{x:800, y:500}}"><button class="acymailing_button_grey" onclick="return false">'.JText::_('EDIT_NOTIFICATION_MAIL').'</button></a>';
		$link = 'index.php?option=com_acymailing&amp;tmpl=component&amp;ctrl=email&amp;task=edit&amp;mailid=notification_unsub';
		$elements->edit_notification_unsub = '<a class="modal" href="'.$link.'" rel="{handler: \'iframe\', size:{x:800, y:500}}"><button class="acymailing_button_grey" onclick="return false">'.JText::_('EDIT_NOTIFICATION_MAIL').'</button></a>';
		$link = 'index.php?option=com_acymailing&amp;tmpl=component&amp;ctrl=email&amp;task=edit&amp;mailid=notification_contact';
		$elements->edit_notification_contact = '<a class="modal" href="'.$link.'" rel="{handler: \'iframe\', size:{x:800, y:500}}"><button class="acymailing_button_grey" onclick="return false">'.JText::_('EDIT_NOTIFICATION_MAIL').'</button></a>';
		$link = 'index.php?option=com_acymailing&amp;tmpl=component&amp;ctrl=email&amp;task=edit&amp;mailid=notification_contact_menu';
		$elements->edit_notification_contact_menu = '<a class="modal" href="'.$link.'" rel="{handler: \'iframe\', size:{x:800, y:500}}"><button class="acymailing_button_grey" onclick="return false">'.JText::_('EDIT_NOTIFICATION_MAIL').'</button></a>';
		$link = 'index.php?option=com_acymailing&amp;tmpl=component&amp;ctrl=email&amp;task=edit&amp;mailid=notification_confirm';
		$elements->edit_notification_confirm = '<a class="modal" href="'.$link.'" rel="{handler: \'iframe\', size:{x:800, y:500}}"><button class="acymailing_button_grey" onclick="return false">'.JText::_('EDIT_NOTIFICATION_MAIL').'</button></a>';

		$link = 'index.php?option=com_acymailing&amp;tmpl=component&amp;ctrl=email&amp;task=edit&amp;mailid=modif';
		$elements->editModifEmail = '<a class="modal" id="modifemail"  href="'.$link.'" rel="{handler: \'iframe\', size:{x:800, y:500}}"><button class="acymailing_button_grey" onclick="return false">'.JText::_('EDIT_NOTIFICATION_MAIL').'</button></a>';

		$link = 'index.php?option=com_acymailing&amp;tmpl=component&amp;ctrl=cpanel&amp;task=checkDB';
		$elements->checkDB = '<a class="modal" href="'.$link.'" rel="{handler: \'iframe\', size:{x:800, y:500}}"><button class="acymailing_button_grey" onclick="return false">'.JText::_('DATABASE_INTEGRITY').'</button></a>';

		$js = "function addUnsubReason(){
			var input = document.createElement('input');
			input.name = 'unsub_reasons[]';
			input.style.width = '300px';
			input.style.margin = '3px 0px';
			input.type = 'text';
			document.getElementById('unsub_reasons').appendChild(input);
			var br = document.createElement('br');
			document.getElementById('unsub_reasons').appendChild(br);
		}
		function displaySurvey(surveyval){
			if(surveyval == 1){
				document.getElementById('unsub_reasons_area').style.display = 'block';
			}else{
				document.getElementById('unsub_reasons_area').style.display = 'none';
			}
		}
		";
		$doc->addScriptDeclaration($js);

		$path = JLanguage::getLanguagePath(JPATH_ROOT);
		$dirs = JFolder::folders($path);
		$languages = array();

		foreach($dirs as $dir){
			if(strlen($dir) != 5 || $dir == "xx-XX") continue;
			$xmlFiles = JFolder::files($path.DS.$dir, '^([-_A-Za-z]*)\.xml$');
			$xmlFile = reset($xmlFiles);
			if(empty($xmlFile)){
				$data = array();
			}else{
				$data = JApplicationHelper::parseXMLLangMetaFile($path.DS.$dir.DS.$xmlFile);
			}

			$oneLanguage = new stdClass();
			$oneLanguage->language = $dir;
			$oneLanguage->name = empty($data['name']) ? $dir : $data['name'];

			$languageFiles = JFolder::files($path.DS.$dir, '^(.*)\.com_acymailing\.ini$');
			$languageFile = reset($languageFiles);
			if(!empty($languageFile)){
				$linkEdit = 'index.php?option=com_acymailing&amp;tmpl=component&amp;ctrl=file&amp;task=language&amp;code='.$oneLanguage->language;
				$oneLanguage->edit = ' <a class="modal" title="'.JText::_('EDIT_LANGUAGE_FILE', true).'"  href="'.$linkEdit.'" rel="{handler: \'iframe\', size:{x:800, y:500}}"><i class="acyicon-edit" id="image'.$oneLanguage->language.'"></i></a>';
			}else{
				$linkEdit = 'index.php?option=com_acymailing&amp;tmpl=component&amp;ctrl=file&amp;task=language&amp;code='.$oneLanguage->language;
				$oneLanguage->edit = ' <a class="modal" title="'.JText::_('ADD_LANGUAGE_FILE', true).'"  href="'.$linkEdit.'" rel="{handler: \'iframe\', size:{x:800, y:500}}"><i class="acyicon-new" id="image'.$oneLanguage->language.'"></i></a>';
			}

			$languages[] = $oneLanguage;
		}

		$js = "function updateConfirmation(newvalue){";
		$js .= "if(newvalue == 0) {window.document.getElementById('confirmemail').style.display = 'none'; window.document.getElementById('confirm_redirect').disabled = true;}else{window.document.getElementById('confirmemail').style.display = 'inline'; window.document.getElementById('confirm_redirect').disabled = false;}";
		$js .= '}';
		$js .= "function updateModification(newvalue){ if(newvalue != 'none') {window.document.getElementById('modifemail').style.display = 'none';}else{window.document.getElementById('modifemail').style.display = 'inline';}} ";
		$js .= 'window.addEvent(\'load\', function(){ updateModification(\''.$config->get('allow_modif', 'data').'\'); updateConfirmation('.$config->get('require_confirmation', 0).'); });';
		$doc->addScriptDeclaration($js);

		$elements->require_confirmation = JHTML::_('acyselect.booleanlist', "config[require_confirmation]", 'onclick="updateConfirmation(this.value)"', $config->get('require_confirmation', 0));

		$allowmodif = array();
		$allowmodif[] = JHTML::_('select.option', "none", JText::_('JOOMEXT_NO'));
		$allowmodif[] = JHTML::_('select.option', "data", JText::_('ONLY_SUBSCRIPTION'));
		$allowmodif[] = JHTML::_('select.option', "all", JText::_('JOOMEXT_YES'));
		$elements->allow_modif = JHTML::_('acyselect.radiolist', $allowmodif, "config[allow_modif]", 'size="1" onclick="updateModification(this.value)"', 'value', 'text', $config->get('allow_modif', 'data'));


		if(!ACYMAILING_J16){
			$db->setQuery("SELECT name,published,id FROM `#__plugins` WHERE `folder` = 'acymailing' AND `element` NOT LIKE 'plg%' ORDER BY published DESC, name ASC");
		}else{
			$db->setQuery("SELECT name,enabled as published,extension_id as id FROM `#__extensions` WHERE `state` <> -1 AND `folder` = 'acymailing' AND `type`= 'plugin' AND `element` NOT LIKE 'plg%' ORDER BY enabled DESC, name ASC");
		}
		$plugins = $db->loadObjectList();

		if(!ACYMAILING_J16){
			$db->setQuery("SELECT name,published,id FROM `#__plugins` WHERE (`folder` != 'acymailing' OR `element` LIKE 'plg%') AND (`name` LIKE '%acymailing%' OR `element` LIKE '%acymailing%') ORDER BY published DESC, name ASC");
		}else{
			$db->setQuery("SELECT name,enabled as published ,extension_id as id FROM `#__extensions` WHERE `state` <> -1 AND (`folder` != 'acymailing' OR `element` LIKE 'plg%') AND `type` = 'plugin' AND (`name` LIKE '%acymailing%' OR `element` LIKE '%acymailing%') ORDER BY enabled DESC, name ASC");
		}

		$integrationplugins = $db->loadObjectList();

		$bounceaction = acymailing_get('type.bounceaction');
		$this->assignRef('bounceaction', $bounceaction);
		$this->assignRef('config', $config);
		$this->assignRef('languages', $languages);
		$this->assignRef('elements', $elements);
		$this->assignRef('plugins', $plugins);
		$this->assignRef('integrationplugins', $integrationplugins);


		$tabs = acymailing_get('helper.acytabs');
		$tabs->setOptions(array('useCookie' => true));

		$this->assignRef('tabs', $tabs);
		$this->assignRef('toggleClass', $toggleClass);

		if((!ACYMAILING_J16 AND !file_exists(rtrim(JPATH_SITE, DS).DS.'plugins'.DS.'acymailing'.DS.'tagsubscriber.php')) OR (ACYMAILING_J16 AND !file_exists(rtrim(JPATH_SITE, DS).DS.'plugins'.DS.'acymailing'.DS.'tagsubscriber'.DS.'tagsubscriber.php'))) acymailing_checkPluginsFolders();

		return parent::display($tpl);
	}
}
