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

class acycaptchaClass{
	var $letters = 'abcdefghijkmnpqrstwxyz23456798ABCDEFGHJKLMNPRSTUVWXYZ';
	var $background_color = '';
	var $colors = array();
	var $width;
	var $height;
	var $nb_letters;
	var $rotated = true;
	var $font = '';

	var $image = null;
	var $code = '';
	var $error = '';


	var $_footprint = array();
	var $_angle = array();
	var $_rest = 0;
	var $space = 3;
	var $size = 16;
	var $state;

	public $pluginName = '';

	function __construct(){
		$config = acymailing_config();
		$this->font = ACYMAILING_FRONT.'inc'.DS.'font'.DS.'mgopencosmeticabold.ttf';
		$this->pluginName = $config->get('captcha_plugin');
	}

	function available(){

		if(!function_exists('gd_info')){
			$this->error = 'The GD library is not installed.';
			return false;
		}


		if(!function_exists('imagettftext')){
			$this->error = 'The FreeType library is not installed.';
			return false;
		}

		if(!function_exists('imagecreatetruecolor')){
			$this->error = 'GD library version is too old. Update to GD2';
			return false;
		}

		if(!file_exists($this->font)){
			$this->error = 'Font missing';
			return false;
		}
		return true;
	}

	function getCaptchaPlugins(){
		$results = array();
		$captchaPlugins = JPluginHelper::getPlugin('captcha');
		foreach($captchaPlugins as $captchaPlugin){
			$plugin = new stdClass();
			$plugin->text = $plugin->value = $captchaPlugin->name;
			$results[] = $plugin;
		}

		if($this->available()){
			$acycaptcha = new stdClass();
			$acycaptcha->text = JText::_('CAPTCHA_ACYCAPTCHA');
			$acycaptcha->value = 'acycaptcha';
		}

		$nocaptcha = new stdClass();
		$nocaptcha->text = JText::_('CAPTCHA_NOCAPTCHA');
		$nocaptcha->value = 'no';

		$results = array_merge(array($nocaptcha, $acycaptcha), $results);

		return $results;
	}

	function display($formName = '', $isModule = false){
		$config = acymailing_config();
		$captchaPluginName = $this->pluginName;


		if($captchaPluginName == 'acycaptcha'){
			$js = '
			function refreshCaptchaModule(formName){
				var captchaLink = document.getElementById(\'captcha_picture_\'+formName).src;
				myregexp = new RegExp(\'val[-=]([0-9]+)\');
				valToChange=captchaLink.match(myregexp)[1];
				document.getElementById(\'captcha_picture_\'+formName).src = captchaLink.replace(valToChange,valToChange+\'0\');
			}
			';
			$doc = JFactory::getDocument();
			$doc->addScriptDeclaration($js);

			if($isModule) {
				$configSuffix = '_module';
			} else {
				$configSuffix = '_component';
			}

			if(ACYMAILING_J16){
				$image = '<img id="captcha_picture_'.$formName.'" title="'.JText::_('ERROR_CAPTCHA').'" width="'.$config->get('captcha_width'.$configSuffix).'" height="'.$config->get('captcha_height'.$configSuffix).'" class="captchaimagemodule" src="'.JRoute::_('index.php?option=com_acymailing&ctrl=captcha&acyformname='.$formName.'&val='.rand(0, 10000)).'" alt="captcha" />';
			}else{
				$image = '<img id="captcha_picture_'.$formName.'" title="'.JText::_('ERROR_CAPTCHA').'" width="'.$config->get('captcha_width'.$configSuffix).'" height="'.$config->get('captcha_height'.$configSuffix).'" class="captchaimagemodule" src="'.rtrim(JURI::root(), '/').'/index.php?option=com_acymailing&amp;ctrl=captcha&amp;acyformname='.$formName.'&amp;val='.rand(0, 10000).'" alt="captcha" />';
			}
			$refreshImg = '<span class="refreshCaptchaModule" onclick="refreshCaptchaModule(\''.$formName.'\')">&nbsp;</span>';
			$input = '<input id="user_captcha_'.$formName.'" title="'.JText::_('ERROR_CAPTCHA').'" class="inputbox captchafield" type="text" name="acycaptcha" style="width:50px" />';
			echo $image.$refreshImg.$input;
		}else{
			if(!empty($formName)){
				$paramsInit = array($formName.'-captcha');
				$paramsDisplay = array(null, $formName.'-captcha', 'class=""');
			}else{
				$paramsInit = array('acymailing-captcha');
				$paramsDisplay = array(null, 'acymailing-captcha', 'class=""');
			}
			$dispatcher = JDispatcher::getInstance();
			JPluginHelper::importPlugin('captcha', $captchaPluginName);
			$dispatcher->trigger('onInit', $paramsInit);
			$result = $dispatcher->trigger('onDisplay', $paramsDisplay);
			if(!empty($result) && !empty($result[0])) echo $result[0];
		}
	}

	function get(){
		if(!$this->available()){
			echo $this->error;
			exit;
		}

		if(empty($this->code)){
			$this->_generateCode();
		}

		$this->_initImage();

		$this->_addCode();

		$currentSession = JFactory::getSession();
		if(is_null($currentSession->get('registry'))){
			jimport('joomla.registry.registry');
			$currentSession->set('registry', new JRegistry('session'));
		}
		$currentSession->set($this->state, $this->code);

		ob_start();
		imagepng($this->image);
		$image = ob_get_clean();
		imagedestroy($this->image);
		$this->image = $image;

		return true;
	}

	function check($input, $secKey = ''){
		$config = acymailing_config();
		if($secKey == $config->get('security_key')) return true;

		$captchaPluginName = $this->pluginName;

		if($captchaPluginName == 'acycaptcha'){
			$currentSession = JFactory::getSession();
			$code = $currentSession->get($this->state);
			if(empty($code) || empty($input)) return false;
			if(strtolower($code) == strtolower($input)){
				return true;
			}
			return false;
		}else{
			$dispatcher = JDispatcher::getInstance();
			JPluginHelper::importPlugin('captcha', $captchaPluginName);
			$result = $dispatcher->trigger('onCheckAnswer', array());
			return $result[0];
		}
	}

	function returnError(){
		header("Content-type:text/html; charset=utf-8");
		echo "<script> alert('".JText::_('ERROR_CAPTCHA', true)."'); window.history.go(-1);</script>\n";
		exit;
	}

	function displayImage(){
		@ob_end_clean();
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Cache-Control: post-check=0, pre-check=0', false);
		header('Pragma: no-cache');
		header('Content-type: image/png');

		echo $this->image;
		exit;
	}

	private function _addCode(){
		if($this->rotated){
			$this->_generateRotate();
		}
		$this->_generateFootprint();
		$i = 0;
		$test = 0;
		$old = 'size';
		while(!$this->_fit() && $test < 50){
			if($old == 'size'){
				$old = 'space';
				$this->size--;
			}else{
				$old = 'size';
				$this->space--;
			}
			$this->_generateFootprint();
			$test++;
		}

		$outerspace = round($this->_rest / 2);
		$x = ($outerspace < $this->_vals[$i]['x'] / 2 ? $this->_vals[$i]['x'] / 2 : $outerspace);
		$y = rand($this->_vals[$i]['y'] + 5, $this->height - $this->_vals[$i]['y'] + 5);

		while($i < $this->nb_letters){
			list($r, $g, $b) = $this->_color_hex2dec($this->colors[mt_rand(0, count($this->colors) - 1)]);
			$clr = imagecolorallocate($this->image, $r, $g, $b);

			if($this->rotated){
				$angle = $this->_angle[$i];
			}else{
				$angle = 0;
			}

			imagettftext($this->image, $this->size, $angle, $x, $y, $clr, $this->font, $this->code[$i]);

			$x = $x + $this->_vals[$i]['x'] + $this->space;
			$y = rand($this->_vals[$i]['y'] + 5, $this->height - $this->_vals[$i]['y'] + 5);

			$i++;
		}
	}

	private function _fit(){
		$i = 0;
		$px = 0;
		while($i < $this->nb_letters){
			$maxx = $this->_letter($i, 'max', 'x');
			$minx = $this->_letter($i, 'min', 'x');
			$maxy = $this->_letter($i, 'max', 'y');
			$miny = $this->_letter($i, 'min', 'y');
			$val = $maxx - $minx;
			if(!isset($this->_vals)){
				$this->_vals = array();
			}
			if(!array_key_exists($i, $this->_vals)){
				$this->_vals[$i] = array();
			}
			$this->_vals[$i]['x'] = $val;
			$this->_vals[$i]['y'] = $maxy - $miny;
			$px += $val;
			$i++;
		}
		$spaces = ($this->nb_letters + 1) * $this->space;
		$rest = $this->width - ($spaces + $px + 13);
		if($rest > 0){
			$this->_rest = $rest;
			return true;
		}
		return false;
	}

	private function _letter($i, $type){
		$start = 0;
		$extreme = $this->_footprint[$i][$start];
		$start += 2;
		while(array_key_exists($start, $this->_footprint[$i])){
			switch($type){
				case 'min':
					if($this->_footprint[$i][$start] < $extreme){
						$extreme = $this->_footprint[$i][$start];
					}
					break;
				case 'max':
					if($this->_footprint[$i][$start] > $extreme){
						$extreme = $this->_footprint[$i][$start];
					}
					break;
			}
			$start += 2;
		}

		return $extreme;
	}

	private function _generateCode(){
		$this->code = '';
		$length = strlen($this->letters) - 1;
		$tmp = $this->nb_letters;
		while($tmp > 0){
			$this->code .= $this->letters[mt_rand(0, $length)];
			$tmp--;
		}
		return true;
	}

	private function _generateRotate(){
		$i = 0;
		while($i < $this->nb_letters){
			$this->_angle[$i] = 360 + mt_rand(-15, 15);
			$i++;
		}
	}

	private function _generateFootprint(){
		$i = 0;
		while($i < $this->nb_letters){
			if($this->rotated){
				$angle = $this->_angle[$i];
			}else{
				$angle = 0;
			}
			$this->_imagettfbbox_t($angle, $this->code[$i]);
			$i++;
		}
	}

	private function _imagettfbbox_t($angle, $text){
		$coords = imagettfbbox($this->size, 0, $this->font, $text);
		if($coords == null){
			echo 'error loading the font at '.$this->font;
			exit;
		}
		$a = deg2rad($angle);
		$ca = cos($a);
		$sa = sin($a);
		$ret = array();
		for($i = 0; $i < 7; $i += 2){
			$ret[$i] = round($coords[$i] * $ca + $coords[$i + 1] * $sa);
			$ret[$i + 1] = round($coords[$i + 1] * $ca - $coords[$i] * $sa);
		}
		$this->_footprint[] = $ret;
	}

	private function _initImage(){
		$this->image = imagecreatetruecolor(intval($this->width), intval($this->height));

		if(empty($this->background_color)){
			$ImgWhite = imagecolorallocate($this->image, 255, 255, 255);
			imagefill($this->image, 0, 0, $ImgWhite);
			imagecolortransparent($this->image, $ImgWhite);
		}else{
			list($r, $g, $b) = $this->_color_hex2dec($this->background_color);
			$clr = imagecolorallocate($this->image, $r, $g, $b);
			imagefill($this->image, 0, 0, $clr);
		}
	}

	private function _color_hex2dec($color){
		if($color[0] == '#'){
			$color = substr($color, 1);
		}
		return array(hexdec(substr($color, 0, 2)), hexdec(substr($color, 2, 2)), hexdec(substr($color, 4, 2)));
	}

}
