<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2019 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/field.php';

class RSFormProFieldHcaptcha extends RSFormProField
{
	// backend preview
	public function getPreviewInput()
	{
		$size	= strtolower($this->getProperty('SIZE', 'normal'));
		$image  = $size == 'invisible' ? 'hcaptcha-invisible.png' : 'hcaptcha.png';

		return JHtml::_('image', 'plg_system_rsfphcaptcha/' . $image, 'hCaptcha', null, true);
	}
	
	// functions used for rendering in front view
	public function getFormInput()
	{
		$formId			= $this->formId;
		$componentId	= $this->componentId;

		// If no site key has been setup, just show a warning
		$siteKey = RSFormProHelper::getConfig('hcaptcha.sitekey');
		if (!$siteKey)
		{
			return '<div>'.JText::_('PLG_SYSTEM_HCAPTCHA_NO_SITE_KEY').'</div>';
		}

		// Need to load scripts one-time.
		$this->loadScripts();

		$theme	= strtolower($this->getProperty('THEME'));
		$type	= strtolower($this->getProperty('TYPE'));
		$size	= strtolower($this->getProperty('SIZE', 'normal'));
		$params = array(
			'sitekey' => $siteKey,
			'theme'	  => $theme,
			'type'	  => $type,
			'size'	  => $size
		);
		$onsubmit = '';

		// If it's an invisible CAPTCHA we need to add some callbacks
		if ($size == 'invisible')
		{
			$params['badge'] = strtolower($this->getProperty('BADGE', 'inline'));
			$params['callback'] = 'RSFormProInvisiblehCaptchaCallback' . $formId;

			$form = RSFormProHelper::getForm($formId);

			// Need to trigger hCaptcha
			if (!$form->DisableSubmitButton)
			{
				$onsubmit = "RSFormProUtils.addEvent(RSFormPro.getForm({$formId}), 'submit', function(evt){ evt.preventDefault(); 
	RSFormPro.submitForm(RSFormPro.getForm({$formId})); });";
			}

			$onsubmit .= "RSFormPro.addFormEvent({$formId}, function(){ hcaptcha.execute(id); });";
		}

		// JSON-Encode parameters
		$params = json_encode($params);

		$script = '';

		if ($size == 'invisible')
		{
			// Create the script
			$script .= <<<EOS
function RSFormProInvisiblehCaptchaCallback{$formId}()
{
	var form = RSFormPro.getForm({$formId});
	RSFormPro.submitForm(form);
}
EOS;
		}

		// Create the script
		$script .= <<<EOS
RSFormProhCaptcha.loaders.push(function(){
	if (typeof RSFormProhCaptcha.forms[{$formId}] === 'undefined') {
		var id = hcaptcha.render('h-captcha-{$componentId}', {$params});
		RSFormProhCaptcha.forms[{$formId}] = id;
		{$onsubmit}
	}
});
EOS;
		RSFormProAssets::addScriptDeclaration($script);

		$out = '<div id="h-captcha-'.$componentId.'"></div>';

		// Clear the token on page refresh
		JFactory::getSession()->clear('com_rsform.hCaptchaToken'.$formId);

		return $out;
	}

	public function processValidation($validationType = 'form', $submissionId = 0)
	{
		// Skip directory editing since it makes no sense
		if ($validationType == 'directory')
		{
			return true;
		}

		$formId 	 = $this->formId;
		$form       = RSFormProHelper::getForm($formId);
		$logged		= $form->RemoveCaptchaLogged ? JFactory::getUser()->id : false;
		$secretKey 	= RSFormProHelper::getConfig('hcaptcha.secret');

		// validation:
		// if there's no session token
		// validate based on challenge & response codes
		// if valid, set the session token

		// session token gets cleared after form processes
		// session token gets cleared on page refresh as well

		if (!$secretKey)
		{
			JFactory::getApplication()->enqueueMessage(JText::_('PLG_SYSTEM_HCAPTCHA_MISSING_INPUT_SECRET'), 'error');
			return false;
		}

		if (!$logged)
		{
			$input 	  = JFactory::getApplication()->input;
			$session  = JFactory::getSession();
			$response = $input->post->get('h-captcha-response', '', 'raw');
			$ip		  = $input->server->getString('REMOTE_ADDR');
			$task	  = strtolower($input->get('task'));
			$option	  = strtolower($input->get('option'));
			$isAjax	  = $option == 'com_rsform' && $task == 'ajaxvalidate';
			$isPage   = $input->getInt('page');

			// Already validated, move on
			if ($session->get('com_rsform.hCaptchaToken'.$formId))
			{
				return true;
			}

			// Ajax requests don't validate hCaptcha on page change
			if ($isAjax && $isPage)
			{
				return true;
			}

			try
			{
				jimport('joomla.http.factory');
				$http = JHttpFactory::getHttp();
				if ($request = $http->post('https://hcaptcha.com/siteverify', array('secret' => $secretKey, 'response' => $response, 'remoteip' => $ip)))
				{
					$json = json_decode($request->body);
				}
			}
			catch (Exception $e)
			{
				JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
				return false;
			}

			if (empty($json->success) || !$json->success)
			{
				if (!empty($json) && isset($json->{'error-codes'}) && is_array($json->{'error-codes'}))
				{
					foreach ($json->{'error-codes'} as $code)
					{
						JFactory::getApplication()->enqueueMessage(JText::_('PLG_SYSTEM_HCAPTCHA_'.str_replace('-', '_', $code)), 'error');
					}
				}

				return false;
			}
			elseif ($isAjax)
			{
				$session->set('com_rsform.hCaptchaToken'.$formId, md5(uniqid($response)));
			}
		}

		return true;
	}

	protected function loadScripts()
	{
		static $loaded;

		if (!$loaded)
		{
			$loaded = true;
			$hl = RSFormProHelper::getConfig('hcaptcha.language') != 'auto' ? '&amp;hl='.JFactory::getLanguage()->getTag() : '';
			RSFormProAssets::addCustomTag('<script src="https://hcaptcha.com/1/api.js?render=explicit' . $hl. '" async defer></script>');

			RSFormProAssets::addScript(JHtml::script('plg_system_rsfphcaptcha/hcaptcha.js', array('pathOnly' => true, 'relative' => true)));
		}
	}
}