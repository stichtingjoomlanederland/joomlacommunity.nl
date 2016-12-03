<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.plugin.plugin');

class plgSystemRSComments extends JPlugin
{
	public function __construct(&$subject, $config) {
		parent::__construct($subject, $config);
	}
	
	protected function canRun() {
		$app	= JFactory::getApplication();
		$input	= $app->input;
		
		if ($app->getName() != 'site' || $input->get('view') == 'frontpage' || $input->get('format') == 'feed') {
			return false;
		}
		
		if (file_exists(JPATH_SITE.'/components/com_rscomments/helpers/rscomments.php')) {
			require_once JPATH_SITE.'/components/com_rscomments/helpers/tooltip.php';
			require_once JPATH_SITE.'/components/com_rscomments/helpers/rscomments.php';
			JFactory::getLanguage()->load('com_rscomments',JPATH_SITE);
			return true;
		}
		
		return false;
	}

	public function onAfterDispatch() {
		if (!$this->canRun()) return;
		
		if (RSCommentsHelper::isJ3()) {
			JHtml::_('behavior.core');
		} else {
			JHtml::_('script', 'system/core.js', false, true);
		}
		
		JText::script('COM_RSCOMMENTS_NO_SUBSCRIBER_NAME');
		JText::script('COM_RSCOMMENTS_NO_SUBSCRIBER_EMAIL');
		JText::script('COM_RSCOMMENTS_INVALID_SUBSCRIBER_EMAIL');
		JText::script('COM_RSCOMMENTS_REPORT_NO_REASON');
		JText::script('COM_RSCOMMENTS_REPORT_INVALID_CAPTCHA');
		JText::script('COM_RSCOMMENTS_HIDE_FORM');
		JText::script('COM_RSCOMMENTS_SHOW_FORM');
	}
	
	public function onAfterRender() {
		if (!$this->canRun()) return;
		
		$pattern 	= '#{rscomments option="(.*?)" id="(.*?)"}#is';
		$body 		= JResponse::getBody();
		
		// Get all ocurrences for : {rscomments option="com_test" id="1"}
		preg_match_all($pattern, $body, $matches);
		
		if (!empty($matches[0])) {
			$placeholder 	= end($matches[0]);
			$option 		= end($matches[1]);
			$id 			= end($matches[2]);
			
			RSCommentsHelper::clearCache();
			
			$this->_template = RSCommentsHelper::getTemplate();
			$html = RSCommentsHelper::showRSComments($option,$id,$this->_template);
			$body = str_replace($placeholder,$html,$body);
			
			foreach($matches[0] as $text) {
				$body = str_replace($text,'',$body);
			}
			
			$this->setScripts($body);
		}
		
		$pattern = '#{rscomments_no option="(.*?)" id="(.*?)"}#is';
		preg_match_all($pattern, $body, $matches);
		
		if (!empty($matches[0])) {
			foreach ($matches[0] as $index => $fullmatch) {
				$placeholder 	= $fullmatch;
				$option 		= $matches[1][$index];
				$id 			= $matches[2][$index];
				
				$comments = RSCommentsHelper::getCommentsNumber($id, false, $option);
				$text = empty($comments) ? JText::_('COM_RSCOMMENTS_NO_COMMENTS') : JText::sprintf('COM_RSCOMMENTS_COMMENTS_NUMBER',$comments);
				
				RSCommentsHelper::clearCache();
				
				$body = str_replace($fullmatch, $text, $body);
			}
		}
		
		JResponse::setBody($body);
	}
	
	protected function setScripts(&$body) {
		$config			= RSCommentsHelper::getConfig();
		$permissions	= RSCommentsHelper::getPermissions();
		$template		= RSCommentsHelper::getTemplate();
		
		$css	 = array();
		$scripts = array();
		
		$scripts[] = '<script type="text/javascript">';
		$scripts[] = 'var rsc_root = "'.addslashes(JURI::root()).'";';
		$scripts[] = 'var rsc_tooltip = "'.(RSCommentsHelper::isJ3() ? 'hasTooltip' : 'hasTip').'";';
		$scripts[] = '</script>';
		
		if (!RSCommentsHelper::isJ3()) {
			if (RSCommentsHelper::getConfig('frontend_jquery')) {
				$scripts[] = '<script src="'.JURI::root(true).'/components/com_rscomments/assets/js/jquery-1.11.1.min.js" type="text/javascript"></script>';
				$scripts[] = '<script src="'.JURI::root(true).'/components/com_rscomments/assets/js/jquery.noConflict.js" type="text/javascript"></script>';
			}
			
			if (RSCommentsHelper::getConfig('load_bootstrap')) {
				$scripts[] = '<script src="'.JURI::root(true).'/components/com_rscomments/assets/js/bootstrap.min.js" type="text/javascript"></script>';
				$css[] = '<link rel="stylesheet" href="'.JURI::root(true).'/components/com_rscomments/assets/css/bootstrap.min.css" type="text/css" />';
				$css[] = '<link rel="stylesheet" href="'.JURI::root(true).'/components/com_rscomments/assets/css/bootstrap-responsive.min.css" type="text/css" />';
			}
		} else {
			if (RSCommentsHelper::getConfig('frontend_jquery') || RSCommentsHelper::getConfig('load_bootstrap')) {
				if (strpos($body,'<head>') !== false && strpos($body,'</head>') !== false) {
					$string = substr($body, strpos($body,'<head>'), strpos($body,'</head>'));
				} else {
					$string = $body;
				}
				
				if (RSCommentsHelper::getConfig('frontend_jquery')) {
					$loadJquery		= true;
					
					if (strpos($string, 'media/jui/js/jquery.min.js') !== false) {
						$loadJquery = false;
					}
					
					if ($loadJquery) {
						$scripts[] = '<script src="'.JURI::root(true).'/media/jui/js/jquery.min.js" type="text/javascript"></script>';
						$scripts[] = '<script src="'.JURI::root(true).'/media/jui/js/jquery-noconflict.js" type="text/javascript"></script>';
					}
				}
				
				if (RSCommentsHelper::getConfig('load_bootstrap')) {
					$loadBootstrap	= true;
					
					if (strpos($string, 'media/jui/js/bootstrap.min.js') !== false) {
						$loadBootstrap = false;
					}
					
					if ($loadBootstrap) {
						$scripts[] = '<script src="'.JURI::root(true).'/media/jui/js/bootstrap.min.js" type="text/javascript"></script>';
						$css[] = '<link rel="stylesheet" href="'.JURI::root(true).'/media/jui/bootstrap.min.css" type="text/css" />';
						$css[] = '<link rel="stylesheet" href="'.JURI::root(true).'/media/jui/bootstrap-responsive.min.css" type="text/css" />';
						$css[] = '<link rel="stylesheet" href="'.JURI::root(true).'/media/jui/bootstrap-extended.css" type="text/css" />';
					}
				}
			}
		}
		
		$scripts[] = '<script src="'.JURI::root(true).'/components/com_rscomments/assets/js/rscomments.js" type="text/javascript"></script>';
		
		if (isset($permissions['captcha']) && $permissions['captcha']) {
			if ($config->captcha == 2) {
				$scripts[] = '<script src="https://www.google.com/recaptcha/api.js?render=explicit&amp;hl='.JFactory::getLanguage()->getTag().'" type="text/javascript"></script>';
				$scripts[] = "<script type=\"text/javascript\">
					RSCommentsReCAPTCHAv2.loaders.push(function(){
						grecaptcha.render('rsc-g-recaptcha', {
							'sitekey': '".htmlentities($config->recaptcha_new_site_key, ENT_QUOTES, 'UTF-8')."',
							'theme': '".htmlentities($config->recaptcha_new_theme, ENT_QUOTES, 'UTF-8')."',
							'type': '".htmlentities($config->recaptcha_new_type, ENT_QUOTES, 'UTF-8')."'
						});
					});
					</script>";
			}
		}
		
		$css[] = '<link rel="stylesheet" href="'.JURI::root(true).'/components/com_rscomments/assets/css/style.css" type="text/css" />';
		
		if ($config->fontawesome == 1) {
			$css[] = '<link rel="stylesheet" href="'.JURI::root(true).'/components/com_rscomments/assets/css/font-awesome.min.css" type="text/css" />';
		}
		
		if ($config->enable_location) {
			$scripts[] = '<script src="https://maps.google.com/maps/api/js" type="text/javascript"></script>';
			$scripts[] = '<script src="'.JURI::root(true).'/components/com_rscomments/assets/js/jquery.map.js" type="text/javascript"></script>';
		}
		
		if (!RSCommentsHelper::isJ3()) {
			$css[] = '<link rel="stylesheet" href="'.JURI::root(true).'/components/com_rscomments/designs/'.$template.'/'.$template.'.css" type="text/css" />';
		}
		
		$html = implode("\n",$css)."\n";
		$html .= implode("\n",$scripts);
		
		$body = str_replace('</head>', $html."\n".'</head>', $body);
	}
}