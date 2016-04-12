<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class RSFirewallCaptcha
{
	protected $font = null;
	protected $code = null;
	
	protected function getFont() {
		$this->font = JPATH_ADMINISTRATOR.'/components/com_rsfirewall/assets/fonts/monofont.ttf';
		return $this->font;
	}
	
	protected function generateCode($chars=4) {
		$possible = 'bBcCdDfFgGhHjJkKmMnNpPqQrRsStTvVwWxXyYzZ23456789';
		$count = strlen($possible) - 1;
		$this->code = '';
		for ($i=0;$i<$chars;$i++)
			$this->code .= substr($possible, mt_rand(0, $count), 1);
		
		$session = JFactory::getSession();
		$session->set('com_rsfirewall.backend_captcha', $this->code);
		return $this->code;
	}
	
	protected function close($err='') {
		$app = JFactory::getApplication();
		$config = RSFirewallConfig::getInstance();
		$config->set('enable_backend_captcha', 0);
		$app->close($err);
	}
	
	public function showImage($width=80,$height=45,$chars=4,$dots=1,$lines=1) {
		if (!function_exists('imagecreate')) {
			$this->close('imagecreate() not available.');
		}
		if (!function_exists('imagettfbbox')) {
			$this->close('imagettfbbox() not available.');
		}
		
		$code 		= $this->generateCode($chars);
		$font 		= $this->getFont();
		$font_size  = $height * 0.80;
		
		$image = imagecreate($width, $height);
		if (!$image) {
			$this->close('imagecreate() error.');
		}
		
		$background_color 	= imagecolorallocate($image, 255, 255, 255);
		$text_color 		= imagecolorallocate($image, 0, 50, 50);
		$noise_color 		= imagecolorallocate($image, 0, 10, 38);
		
		if ($dots == 1) {
			for ($i=0; $i<($width*$height)/3; $i++) {
				imagefilledellipse($image, mt_rand(0,$width), mt_rand(0,$height), 1, 1, $noise_color);
			}
		}
		
		if ($lines == 1) {
			for ($i=0; $i<($width*$height)/150; $i++) {
				imageline($image, mt_rand(0,$width), mt_rand(0,$height), mt_rand(0,$width), mt_rand(0,$height), $noise_color);
			}
		}
		
		$textbox = imagettfbbox($font_size, 0, $font, $code);
		if (!$textbox) {
			$this->close('imagettfbbox() error.');
		}
		
		$x = ($width - $textbox[4])/2;
		$y = ($height - $textbox[5])/2;
		if (!imagettftext($image, $font_size, 0, $x, $y, $text_color, $font, $code)) {
			$this->close('imagettftext() error.');
		}
		
		header('Content-Type: image/jpeg');
		imagejpeg($image);
		imagedestroy($image);
	}
}