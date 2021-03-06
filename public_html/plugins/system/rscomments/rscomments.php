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
		
		JHtml::_('behavior.core');
		
		JText::script('COM_RSCOMMENTS_HIDE_FORM');
		JText::script('COM_RSCOMMENTS_SHOW_FORM');
	}
	
	public function onAfterRender() {
		if (!$this->canRun()) return;
		
		$pattern 	= '#{rscomments option="(.*?)" id="(.*?)"}#is';
		$html		= JFactory::getApplication()->getBody();
		$hasContent	= false;
		$additional = '';
		
		if (strpos($html, '</head>') !== false) {
			list($head, $content) = explode('</head>', $html, 2);
		} else {
			$content = $html;
		}
		
        if (empty($content)) {
            return false;
        }
		
		if (strpos($content, '{rscomments') === false) {
            return false;
        }
		
		// Get all ocurrences for : {rscomments option="com_test" id="1"}
		if (preg_match_all($pattern, $content, $matches)) {
			
			if (count($matches) == 3) {
				RSCommentsHelper::clearCache();
				
				foreach ($matches[0] as $i => $match) {
					$option = $matches[1][$i];
					$id = (int) $matches[2][$i];
					
					$content = str_replace($match, RSCommentsHelper::showRSComments($option,$id), $content);
					$additional .= $this->loadRecaptcha($html, md5($option.$id));
					
					$hasContent = true;
				}
			}
		}
		
		$pattern = '#{rscomments_no option="(.*?)" id="(.*?)"}#is';
		preg_match_all($pattern, $content, $matches);
		
		if (!empty($matches[0])) {
			foreach ($matches[0] as $index => $fullmatch) {
				$placeholder 	= $fullmatch;
				$option 		= $matches[1][$index];
				$id 			= $matches[2][$index];
				
				$comments = RSCommentsHelper::getCommentsNumber($id, false, $option);
				$text = empty($comments) ? JText::_('COM_RSCOMMENTS_NO_COMMENTS') : JText::sprintf('COM_RSCOMMENTS_COMMENTS_NUMBER',$comments);
				
				RSCommentsHelper::clearCache();
				
				$content = str_replace($fullmatch, $text, $content);
			}
		}
		
		$html = isset($head) ? ($head . '</head>' . $content) : $content;
		
		if ($hasContent) {
			$this->setScripts($html, $additional);
		}
		
		JFactory::getApplication()->setBody($html);
	}
	
	protected function setScripts(&$body, $additional) {
		$config			= RSCommentsHelper::getConfig();
		$permissions	= RSCommentsHelper::getPermissions();
		$version		= JFactory::getDocument()->getMediaVersion();
		
		$css	 = array();
		$scripts = array();
		
		$scripts[] = '<script type="text/javascript">';
		$scripts[] = 'if (typeof rsc_root == "undefined") var rsc_root = "'.addslashes(JURI::root()).'";';
		$scripts[] = '</script>';
		
		if (RSCommentsHelper::getConfig('frontend_jquery') || RSCommentsHelper::getConfig('load_bootstrap')) {
			if (strpos($body,'<head>') !== false && strpos($body,'</head>') !== false) {
				$string = substr($body, strpos($body,'<head>'), strpos($body,'</head>'));
			} else {
				$string = $body;
			}
			
			if (RSCommentsHelper::getConfig('frontend_jquery')) {
				$loadJquery		= true;
				
				if (RSCommentsHelper::isJ4()) {
					$wa = JFactory::getApplication()->getDocument()->getWebAssetManager();
					$jQueryScript = $wa->getAsset('script','jquery')->getUri();
					$jQueryNoCScript = $wa->getAsset('script','jquery-noconflict')->getUri();
				} else {
					$jQueryScript = JURI::root(true).'/media/jui/js/jquery.min.js';
					$jQueryNoCScript = JURI::root(true).'/media/jui/js/jquery-noconflict.js';
				}
				
				if (strpos($string, $jQueryScript) !== false) {
					$loadJquery = false;
				}
				
				if ($loadJquery) {
					$scripts[] = '<script src="'.$jQueryScript.'" type="text/javascript"></script>';
					$scripts[] = '<script src="'.$jQueryNoCScript.'" type="text/javascript"></script>';
				}
			}
			
			if (RSCommentsHelper::getConfig('load_bootstrap')) {
				$loadBootstrap	= true;
				
				if (RSCommentsHelper::isJ4()) {
					$wa = JFactory::getApplication()->getDocument()->getWebAssetManager();
					$bootstrapScript = $wa->getAsset('script','bootstrap.es5')->getUri();
				} else {
					$bootstrapScript = JURI::root(true).'/media/jui/js/bootstrap.min.js';
				}
				
				if (strpos($string, $bootstrapScript) !== false) {
					$loadBootstrap = false;
				}
				
				if ($loadBootstrap) {
					$scripts[] = '<script src="'.$bootstrapScript.'" type="text/javascript"></script>';
					
					if (RSCommentsHelper::isJ4()) {
						$wa = JFactory::getApplication()->getDocument()->getWebAssetManager();
						$bootstrapCSS = $wa->getAsset('style','bootstrap.css')->getUri();
						$css[] = '<link rel="stylesheet" href="'.$bootstrapCSS.'" type="text/css" />';
					} else {					
						$css[] = '<link rel="stylesheet" href="'.JURI::root(true).'/media/jui/bootstrap.min.css" type="text/css" />';
						$css[] = '<link rel="stylesheet" href="'.JURI::root(true).'/media/jui/bootstrap-responsive.min.css" type="text/css" />';
						$css[] = '<link rel="stylesheet" href="'.JURI::root(true).'/media/jui/bootstrap-extended.css" type="text/css" />';
					}
				}
			}
		}
		
		if (RSCommentsHelper::isJ4()) {
			$wa = JFactory::getApplication()->getDocument()->getWebAssetManager();
			$bootstrapModalJs = $wa->getAsset('script','bootstrap.modal')->getUri();
			
			if (strpos($body, $bootstrapModalJs) === false) {
				$scripts[] = '<script src="'.$bootstrapModalJs.'" type="module"></script>';
			}
			
			$bmodaljs = JHtml::script('com_rscomments/modal.js', array('relative' => true, 'version' => 'auto', 'pathOnly' => true));
		
			if (strpos($body, $bmodaljs) === false) {
				$scripts[] = '<script src="'.$bmodaljs.'?'.$version.'" type="text/javascript"></script>';
			}
		}
		
		$sitejs = JHtml::script('com_rscomments/site.js', array('relative' => true, 'version' => 'auto', 'pathOnly' => true));
		
		if (strpos($body, $sitejs) === false) {
			$scripts[] = '<script src="'.$sitejs.'?'.$version.'" type="text/javascript"></script>';
		}
		
		if ($config->modal == 2) {
			$popjs = JHtml::script('com_rscomments/jquery.magnific-popup.min.js', array('relative' => true, 'version' => 'auto', 'pathOnly' => true));	
			
			if (strpos($body, $popjs) === false) {
				$scripts[] = '<script src="'.$popjs.'?'.$version.'" type="text/javascript"></script>';
			}
		} else {
			$modaljs = JHtml::script('com_rscomments/modals.js', array('relative' => true, 'version' => 'auto', 'pathOnly' => true));
			
			if (strpos($body, $modaljs) === false) {
				$scripts[] = '<script src="'.$modaljs.'?'.$version.'" type="text/javascript"></script>';
			}
		}
		
		$sitecss = JHtml::stylesheet('com_rscomments/site.css', array('relative' => true, 'version' => 'auto', 'pathOnly' => true));
		
		if (strpos($body, $sitecss) === false) {
			$css[] = '<link rel="stylesheet" href="'.$sitecss.'?'.$version.'" type="text/css" />';
		}
		
		if ($config->fontawesome == 1) {
			$facss = JHtml::stylesheet('com_rscomments/font-awesome.min.css', array('relative' => true, 'version' => 'auto', 'pathOnly' => true));
			
			if (strpos($body, $facss) === false) {
				$css[] = '<link rel="stylesheet" href="'.$facss.'?'.$version.'" type="text/css" />';
			}
		}
		
		if ($config->enable_location) {
			$mapsjs = 'https://maps.google.com/maps/api/js'.(isset($config->map_key) ? '?key='.$config->map_key : '');
			
			if (strpos($body, $mapsjs) === false) {
				$scripts[] = '<script src="'.$mapsjs.'" type="text/javascript"></script>';
			}
		}
		
		if ($config->modal == 2) {
			$popcss = JHtml::stylesheet('com_rscomments/magnific-popup.css', array('relative' => true, 'version' => 'auto', 'pathOnly' => true));
			
			if (strpos($body, $popcss) === false) {
				$css[] = '<link rel="stylesheet" href="'.$popcss.'?'.$version.'" type="text/css" />';
			}
		}
		
		$html = implode("\n",$css)."\n";
		$html .= implode("\n",$scripts);
		
		if (!empty($additional)) {
			$recaptchaAPI = 'https://www.google.com/recaptcha/api.js?render=explicit&amp;hl='.JFactory::getLanguage()->getTag();
			
			if (strpos($body, $recaptchaAPI) === false) {
				$html .= "\n".'<script src="'.$recaptchaAPI.'" '.($config->recaptcha_new_size == 'invisible' ? 'async defer' : '').'></script>';
			}
			
			$html .= "\n".$additional;
		}
		
		$body = str_replace('</head>', $html."\n".'</head>', $body);
	}
	
	protected static function loadRecaptcha($content, $hash) {
		$config = RSCommentsHelper::getConfig();
		$permissions = RSCommentsHelper::getPermissions();
		$scripts = array();
		
		if ($config->captcha == 2 && isset($permissions['captcha']) && $permissions['captcha']) {
			$scripts[] = '<script type="text/javascript">';
			$scripts[] = "RSCommentsReCAPTCHAv2.loaders.push(function(){";
			
			if ($config->recaptcha_new_size == 'invisible') {
				$scripts[] = "\tdocument.getElementById('rscomments-".$hash."').onclick = function() {";
				$scripts[] = "\tvar id = grecaptcha.render('rsc-g-recaptcha-".$hash."', {";
				$scripts[] = "\t\t'sitekey': '".htmlentities($config->recaptcha_new_site_key, ENT_QUOTES, 'UTF-8')."',";
				$scripts[] = "\t\t'theme': '".htmlentities($config->recaptcha_new_theme, ENT_QUOTES, 'UTF-8')."',";
				$scripts[] = "\t\t'type': '".htmlentities($config->recaptcha_new_type, ENT_QUOTES, 'UTF-8')."',";
				$scripts[] = "\t\t'badge': '".htmlentities($config->recaptcha_new_badge, ENT_QUOTES, 'UTF-8')."',";
				$scripts[] = "\t\t'size': '".htmlentities($config->recaptcha_new_size, ENT_QUOTES, 'UTF-8')."'";
				$scripts[] = "\t});";
				$scripts[] = "\tRSCommentsReCAPTCHAv2.ids['$hash'] = id;";
				$scripts[] = "\tRSCommentsReCAPTCHAv2.type['$hash'] = '".htmlentities($config->recaptcha_new_size, ENT_QUOTES, 'UTF-8')."';";
				$scripts[] = "\t\tgrecaptcha.execute(id);";
				$scripts[] = "\t\tdocument.getElementById('rscomments-".$hash."').onclick = null;";
				$scripts[] = "\t}";
			} else {
				$scripts[] = "\tvar id = grecaptcha.render('rsc-g-recaptcha-".$hash."', {";
				$scripts[] = "\t\t'sitekey': '".htmlentities($config->recaptcha_new_site_key, ENT_QUOTES, 'UTF-8')."',";
				$scripts[] = "\t\t'theme': '".htmlentities($config->recaptcha_new_theme, ENT_QUOTES, 'UTF-8')."',";
				$scripts[] = "\t\t'type': '".htmlentities($config->recaptcha_new_type, ENT_QUOTES, 'UTF-8')."',";
				$scripts[] = "\t\t'size': '".htmlentities($config->recaptcha_new_size, ENT_QUOTES, 'UTF-8')."'";
				$scripts[] = "\t});";
				$scripts[] = "\tRSCommentsReCAPTCHAv2.ids['$hash'] = id;";
				$scripts[] = "\tRSCommentsReCAPTCHAv2.type['$hash'] = '".htmlentities($config->recaptcha_new_size, ENT_QUOTES, 'UTF-8')."';";
			}
			
			$scripts[] = "});";
			$scripts[] = '</script>';
			
			return implode("\n",$scripts)."\n";
		}
		
		return '';
	}
}