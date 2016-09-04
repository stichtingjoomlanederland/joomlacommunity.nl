<?php
/**
* @package RSform!Pro
* @copyright (C) 2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die;

class plgSystemRSFPReCaptchav2 extends JPlugin
{
	protected $autoloadLanguage = true;
	
	public function __construct(&$subject, $config = array()) {
		parent::__construct($subject, $config);
		
		$jversion = new JVersion();
		if ($jversion->isCompatible('2.5') && !$jversion->isCompatible('3.0')) {
			$this->loadLanguage();
		}
	}
	
	// Show field in Form Components
	public function rsfp_bk_onAfterShowComponents() {
		$input 		= JFactory::getApplication()->input;
		$formId 	= $input->getInt('formId');
		$exists 	= RSFormProHelper::componentExists($formId, 2424);
		$link		= $exists ? "displayTemplate('2424', '{$exists[0]}')" : "displayTemplate('2424')";
		
		?>
		<li class="rsform_navtitle"><?php echo JText::_('RSFP_RECAPTCHAV2_LABEL'); ?></li>
		<li><a href="javascript: void(0);" onclick="<?php echo $link;?>;return false;" id="recaptchav2"><span class="rsficon rsficon-spinner9"></span><span class="inner-text"><?php echo JText::_('RSFP_RECAPTCHAV2_LABEL'); ?></span></a></li>
		<?php
	}
	
	// Show backend preview of field
	public function rsfp_bk_onAfterCreateComponentPreview($args = array()) {
		if ($args['ComponentTypeName'] == 'recaptchav2') {
			$args['out']  = '<td>'.$args['data']['CAPTION'].'</td>';
			$args['out'] .= '<td><img src="components/com_rsform/assets/images/recaptchav2.gif" style="width: 300px;" /></td>';
		}
	}
	
	// Show the Configuration tab
	public function rsfp_bk_onAfterShowConfigurationTabs($tabs) {		
		$tabs->addTitle(JText::_('RSFP_RECAPTCHAV2_LABEL'), 'form-recaptcha-v2');
		$tabs->addContent($this->showConfigurationScreen());
	}
	
	protected function showConfigurationScreen() {
		ob_start();
		?>
		<div id="page-recaptchav2">
			<p><a href="https://www.google.com/recaptcha/" target="_blank"><?php echo JText::_('RSFP_RECAPTCHAV2_GET_RECAPTCHA_HERE'); ?></a></p>
			<table class="admintable">
				<tr>
					<td width="200" style="width: 200px;" align="right" class="key"><label for="recaptchav2sitekey"><?php echo JText::_('RSFP_RECAPTCHAV2_SITE_KEY'); ?></label></td>
					<td><input type="text" name="rsformConfig[recaptchav2.site.key]" id="recaptchav2sitekey" value="<?php echo RSFormProHelper::htmlEscape(RSFormProHelper::getConfig('recaptchav2.site.key')); ?>" size="100" maxlength="100" /></td>
				</tr>
				<tr>
					<td width="200" style="width: 200px;" align="right" class="key"><label for="recaptchav2secretkey"><?php echo JText::_('RSFP_RECAPTCHAV2_SECRET_KEY'); ?></label></td>
					<td><input type="text" name="rsformConfig[recaptchav2.secret.key]" id="recaptchav2secretkey" value="<?php echo RSFormProHelper::htmlEscape(RSFormProHelper::getConfig('recaptchav2.secret.key')); ?>" size="100" maxlength="100" /></td>
				</tr>
				<tr>
					<td width="200" style="width: 200px;" align="right" class="key"><label for="recaptchav2language"><?php echo JText::_('RSFP_RECAPTCHAV2_LANGUAGE'); ?></label></td>
					<td>
						<select name="rsformConfig[recaptchav2.language]" id="recaptchav2language">
							<?php echo JHtml::_('select.options',
									array(
										JHtml::_('select.option', 'auto', JText::_('RSFP_RECAPTCHAV2_LANGUAGE_AUTO')),
										JHtml::_('select.option', 'site', JText::_('RSFP_RECAPTCHAV2_LANGUAGE_SITE'))
									),
								'value', 'text', RSFormProHelper::getConfig('recaptchav2.language'));
							?>
						</select>
					</td>
				</tr>
			</table>
		</div>
		<?php
		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	}
	
	public function rsfp_bk_onAfterCreateFrontComponentBody($args) {
		$typeId 		= $args['r']['ComponentTypeId'];
		$formId			= $args['formId'];
		$componentId	= $args['componentId'];
		
		if ($typeId == 2424) {			
			static $loaded;
			if (!$loaded) {
				$loaded = true;
				
				$hl = '';
				if (RSFormProHelper::getConfig('recaptchav2.language') != 'auto') {
					$hl = '&amp;hl='.JFactory::getLanguage()->getTag();
				}
				
				RSFormProAssets::addScript('https://www.google.com/recaptcha/api.js?render=explicit'.$hl);
				RSFormProAssets::addScript(JHtml::script('com_rsform/recaptchav2.js', false, true, true));
			}
			
			if ($siteKey = RSFormProHelper::getConfig('recaptchav2.site.key')) {
				$data		= $args['data'];
				$theme		= strtolower($data['THEME']);
				$type		= strtolower($data['TYPE']);
				$size		= !empty($data['SIZE']) ? strtolower($data['SIZE']) : 'normal';
				
				RSFormProAssets::addScriptDeclaration("
					RSFormProReCAPTCHAv2.loaders.push(function(){
						grecaptcha.render('g-recaptcha-$componentId', {
							'sitekey': '".$this->escape($siteKey)."',
							'theme': '".$this->escape($theme)."',
							'type': '".$this->escape($type)."',
							'size': '".$this->escape($size)."'
						});
					});
				");
				
				$args['out'] .= '<div id="g-recaptcha-'.$componentId.'"></div>';
				$args['out'] .= '
					<noscript>
					  <div style="width: 302px; height: 352px;">
						<div style="width: 302px; height: 352px; position: relative;">
						  <div style="width: 302px; height: 352px; position: absolute;">
							<iframe src="https://www.google.com/recaptcha/api/fallback?k='.$this->escape($siteKey).'" frameborder="0" scrolling="no" style="width: 302px; height:352px; border-style: none;"></iframe>
						  </div>
						  <div style="width: 250px; height: 80px; position: absolute; border-style: none; bottom: 21px; left: 25px; margin: 0px; padding: 0px; right: 25px;">
							<textarea id="g-recaptcha-response" name="g-recaptcha-response" class="g-recaptcha-response" style="width: 250px; height: 80px; border: 1px solid #c1c1c1; margin: 0px; padding: 0px; resize: none;" value=""></textarea>
						  </div>
						</div>
					  </div>
					</noscript>';
			} else {
				$args['out'] .= '<div>'.JText::_('RSFP_RECAPTCHAV2_NO_SITE_KEY').'</div>';
			}
			
			// clear the token on page refresh
			JFactory::getSession()->clear('com_rsform.recaptchav2Token'.$formId);
		}
	}
	
	public function rsfp_f_onBeforeFormValidation($args) {
		$formId 	= $args['formId'];
		$invalid 	=& $args['invalid'];
		$post		=& $args['post'];
		$form       = RSFormProHelper::getForm($formId);
		if ($form->RemoveCaptchaLogged) {
			$logged     = JFactory::getUser()->id;
		} else {
			$logged = false;
		}
		
		$secretKey 	= RSFormProHelper::getConfig('recaptchav2.secret.key');
		
		// validation:
		// if there's no session token
		// validate based on challenge & response codes
		// if valid, set the session token
		
		// session token gets cleared after form processes
		// session token gets cleared on page refresh as well
		
		if (($componentId = RSFormProHelper::componentExists($formId, 2424)) && $secretKey && !$logged) {
			$input = JFactory::getApplication()->input;
			
			$response = $input->get('g-recaptcha-response', '', 'raw');
			$ip		  = $input->server->get('REMOTE_ADDR');
			$task	  = strtolower($input->get('task'));
			$option	  = strtolower($input->get('option'));
			
			$session = JFactory::getSession();
			// already validated, move on
			if ($session->get('com_rsform.recaptchav2Token'.$formId)) {
				return true;
			}
			
			try {
				jimport('joomla.http.factory');
				$http = JHttpFactory::getHttp();
				if ($request = $http->get('https://www.google.com/recaptcha/api/siteverify?secret='.urlencode($secretKey).'&response='.urlencode($response).'&remoteip='.urlencode($ip))) {
					$json = json_decode($request->body);
				}
			} catch (Exception $e) {
				JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
				$invalid[] = $componentId[0];
				return false;
			}
			
			if (empty($json->success) || !$json->success) {
				$invalid[] = $componentId[0];
				
				if (!empty($json) && isset($json->{'error-codes'}) && is_array($json->{'error-codes'})) {
					foreach ($json->{'error-codes'} as $code) {
						JFactory::getApplication()->enqueueMessage(JText::_('RSFP_RECAPTCHAV2_'.str_replace('-', '_', $code)), 'error');
					}
				}
				
			} elseif ($option == 'com_rsform' && $task == 'ajaxvalidate') {
				$session->set('com_rsform.recaptchav2Token'.$formId, md5(uniqid($response)));
			}
		}
	}
	
	public function rsfp_f_onAfterFormProcess($args) {
		$formId = $args['formId'];
		
		if (RSFormProHelper::componentExists($formId, 2424)) {
			JFactory::getSession()->clear('com_rsform.recaptchav2Token'.$formId);
		}
	}
	
	protected function escape($string) {
		return htmlentities($string, ENT_QUOTES, 'utf-8');
	}
}