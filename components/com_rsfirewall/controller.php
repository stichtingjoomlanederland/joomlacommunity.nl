<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

class RSFirewallController extends JControllerLegacy
{
	public function __construct() {
		parent::__construct();
	}

	public function display($cachable = false, $urlparams = false) {
		// View caching logic -- simple... are we logged in?
		$user = JFactory::getUser();
		if ($user->get('id')) {
			parent::display(false, $urlparams);
		} else {
			parent::display(true, $urlparams);
		}
	}
	
	public function cloak() {
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		
		if ($string = $input->get('string', '', 'string')) {
			if (($mail = @base64_decode($string)) && !headers_sent()) {
				if (strpos($mail,'@') !== false) {
					if (function_exists('imagecreate')) {
						$length = strlen($mail);
						$size = 15;
				 
						$imagelength = $length*7;
						$imageheight = $size;
						$image       = imagecreate($imagelength, $imageheight);
						$usebgrcolor = sscanf('#FFFFFF', '#%2x%2x%2x');
						$usestrcolor = sscanf('#000000', '#%2x%2x%2x');

						$bgcolor     = imagecolorallocate($image, $usebgrcolor[0], $usebgrcolor[1], $usebgrcolor[2]);
						$stringcolor = imagecolorallocate($image, $usestrcolor[0], $usestrcolor[1], $usestrcolor[2]);
						
						imagestring ($image, 3, 0, 0,  $mail, $stringcolor); 
						
						header('Content-type: image/png');
						imagepng($image);
						imagedestroy($image);
					} else {
						// disable if image creation doesn't work
						RSFirewallConfig::getInstance()->set('verify_emails', 0);
					}
				}
			}
		}
		
		$app->close();
    }
	
	public function mail() {
		$app 	= JFactory::getApplication();
		$input 	= $app->input;
		
		if ($string = $input->get('string', '', 'string')) {
			if (($mail = @base64_decode($string)) && !headers_sent()) {
				header('Location: mailto:'.$mail);
			}
		}
		
		$app->close();
	}
}