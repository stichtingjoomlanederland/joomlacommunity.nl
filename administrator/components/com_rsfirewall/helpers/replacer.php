<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class RSFirewallReplacer
{	
	public static function addCaptcha(&$buffer) {
		$find = '<div class="button-holder">';
		
		if (strpos($buffer, $find) !== false) {
			$with  = '<div style="clear: both;">'."\n"
					 .'<table cellspacing="0" cellpadding="2" border="0" width="100%">'."\n"
					 .'<tr>'."\n"
					 .'<td width="50%"><p>'.JText::_('COM_RSFIREWALL_PLEASE_ENTER_THE_IMAGE_CODE').'</p></td>'."\n"
					 .'<td align="center"><img src="index.php?option=com_rsfirewall&amp;task=captcha" alt="Captcha" /></td>'."\n"
					 .'</tr>'."\n"
					 .'<tr>'."\n"
					 .'<td width="50%"></td>'."\n"
					 .'<td align="center"><input type="text" size="15" class="inputbox" id="mod-login-captcha" name="rsf_backend_captcha" /></td>'."\n"
					 .'</tr>'."\n"
					 .'</table>'."\n"
					 .'</div>'."\n";
			$with .= $find;
			
			$buffer = str_replace($find, $with, $buffer);
			
			return true;
		}

		$find = '<div class="btn-group pull-left">';
		
		if (strpos($buffer, $find) !== false) {
			$with = '
			<div class="control-group">
				<div class="controls">
					<div class="center">
						<img src="index.php?option=com_rsfirewall&amp;task=captcha" alt="Captcha" />
					</div>
				</div>
			</div>
			<div class="control-group">
				<div class="controls">
				  <div class="center">
					<label for="mod-login-captcha" class="element-invisible">'.JText::_('COM_RSFIREWALL_PLEASE_ENTER_THE_IMAGE_CODE').'</label>
					<input name="rsf_backend_captcha" id="mod-login-captcha" type="text" class="input-medium"  placeholder="'.JText::_('COM_RSFIREWALL_PLEASE_ENTER_THE_IMAGE_CODE').'" size="15" /></a>
				  </div>
				 </div>
			</div>
			<div class="control-group">
				<div class="controls">
					<div class="btn-group pull-left">';
			
			$buffer = str_replace($find, $with, $buffer);
			
			return true;
		}
		
		$find = '<div class="btn-group">';
		if (strpos($buffer, $find) !== false) {
			$with = '
			<div class="control-group">
				<div class="controls">
					<div class="center">
						<img src="index.php?option=com_rsfirewall&amp;task=captcha" alt="Captcha" />
					</div>
				</div>
			</div>
			<div class="control-group">
				<div class="controls">
				  <div class="center">
					<label for="mod-login-captcha" class="element-invisible">'.JText::_('COM_RSFIREWALL_PLEASE_ENTER_THE_IMAGE_CODE').'</label>
					<input name="rsf_backend_captcha" id="mod-login-captcha" type="text" class="input-medium"  placeholder="'.JText::_('COM_RSFIREWALL_PLEASE_ENTER_THE_IMAGE_CODE').'" size="15" /></a>
				  </div>
				 </div>
			</div>
			<div class="control-group">
				<div class="controls">
					<div class="btn-group">';
			
			$buffer = str_replace($find, $with, $buffer);
			
			return true;
		}
		
		return false;
	}
	
	public static function replaceEmails(&$text) {
		if (strpos($text, '{emailcloak=off}') !== false) {
			$text = str_ireplace('{emailcloak=off}', '', $text);
			return true;
		}
		
		// performance check
		if (strpos($text, '@') === false) {
			return false;
		}
		
		// Example: any@example.org
		$searchEmail = '([\w\.\-\+]+\@(?:[a-z0-9\.\-]+\.)+(?:[a-zA-Z0-9\-]{2,10}))';
		
		// Example: any@example.org?subject=anyText
		$searchEmailLink = $searchEmail . '([?&][\x20-\x7f][^"<>]+)';
		
		// Any Text
		$searchText = '((?:[\x20-\x7f]|[\xA1-\xFF]|[\xC2-\xDF][\x80-\xBF]|[\xE0-\xEF][\x80-\xBF]{2}|[\xF0-\xF4][\x80-\xBF]{3})[^<>]+)';
		
		$emails = array();
		
		/*
		 * Search for derivatives of link code <a href="mailto:email@amail.com"
		 * >email@amail.com</a>
		 */
		$pattern = self::_searchPattern($searchEmail, $searchEmail);
		while (preg_match($pattern, $text, $regs, PREG_OFFSET_CAPTURE))
		{
			$mail = $regs[1][0];
			$mailText = $regs[2][0];
			
			$params = array('mail' => $mail, 'mailText' => $mailText);
			
			$replacement = self::_getReplacement($params, $emails);
			$text = substr_replace($text, $replacement, $regs[0][1], strlen($regs[0][0]));
		}

		/*
		 * Search for derivatives of link code <a href="mailto:email@amail.com">
		 * anytext</a>
		 */
		$pattern = self::_searchPattern($searchEmail, $searchText);
		while (preg_match($pattern, $text, $regs, PREG_OFFSET_CAPTURE))
		{
			$mail = $regs[1][0];
			$mailText = $regs[2][0];
			$params = array('mail' => $mail, 'mailText' => $mailText);
			
			$replacement = self::_getReplacement($params, $emails);
			$text = substr_replace($text, $replacement, $regs[0][1], strlen($regs[0][0]));
		}

		/*
		 * Search for derivatives of link code <a href="mailto:email@amail.com?
		 * subject=Text">email@amail.com</a>
		 */
		$pattern = self::_searchPattern($searchEmailLink, $searchEmail);
		while (preg_match($pattern, $text, $regs, PREG_OFFSET_CAPTURE))
		{
			$mail = $regs[1][0] . $regs[2][0];
			$mail = str_replace( '&amp;', '&', $mail );
			$mailText = $regs[3][0];
			$params = array('mail' => $mail, 'mailText' => $mailText);

			$replacement = self::_getReplacement($params, $emails);
			$text = substr_replace($text, $replacement, $regs[0][1], strlen($regs[0][0]));
		}

		/*
		 * Search for derivatives of link code <a href="mailto:email@amail.com?
		 * subject=Text">anytext</a>
		 */
		$pattern = self::_searchPattern($searchEmailLink, $searchText);
		while (preg_match($pattern, $text, $regs, PREG_OFFSET_CAPTURE))
		{
			$mail = $regs[1][0] . $regs[2][0];
			$mail = str_replace('&amp;', '&', $mail);
			$mailText = $regs[3][0];
			$params = array('mail' => $mail, 'mailText' => $mailText);

			$replacement = self::_getReplacement($params, $emails);
			$text = substr_replace($text, $replacement, $regs[0][1], strlen($regs[0][0]));
		}

		// Search for plain text email@amail.com
		$pattern = '~' . $searchEmail . '([^a-z0-9]|$)~i';
		while (preg_match($pattern, $text, $regs, PREG_OFFSET_CAPTURE))
		{
			$mail = $regs[1][0];
			$params = array('mail' => $mail);
			
			$replacement = self::_getReplacement($params, $emails);
			$text = substr_replace($text, $replacement, $regs[1][1], strlen($mail));
		}
		
		if ($emails) {
			$text = str_replace('</body>', '<script type="text/javascript" src="'.JUri::root(true).'/components/com_rsfirewall/assets/js/rsfirewall.js"></script></body>', $text);
		}
		
		return !empty($emails);
	}
	
	protected static function _searchPattern($link, $text) {
		$pattern = '~(?:<a [\w "\'=\@\.\-]*href\s*=\s*"mailto:'
		. $link . '"[\w "\'=\@\.\-]*)>' . $text . '</a>~i';

		return $pattern;
	}
	
	protected static function _getReplacement($params=array(), &$emails) {
		$_mail 		 = array();
		$id 		 = uniqid('');
		$_mail['id'] = $id;
		if (!empty($params['mail'])) {
			$_mail['encoded_mail'] 	= base64_encode($params['mail']);
			$_mail['mail'] 			= $params['mail'];
			
			$replacement = '<a href="javascript:void(0);" onclick="RSFirewallMail(\''.$_mail['encoded_mail'].'\')" class="rsfirewall_emails_a"><img src="'.htmlentities(self::getImageString($params['mail']), ENT_COMPAT, 'utf-8').'" style="vertical-align: middle" class="rsfirewall_emails_img" alt="." /></a>';
		}
		
		if (!empty($params['mailText'])) {
			$mailText = $params['mailText'];
			$_mail['mailText'] = $mailText;
			$replacement = $mailText;
		}
		
		$emails[] = $_mail;
		
		return $replacement;
	}
	
	protected static function getImageString($mail) {
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
			
			imagestring($image, 3, 0, 0,  $mail, $stringcolor); 
			
			ob_start();
			imagegif($image);
			imagedestroy($image);
			$data = ob_get_contents();
			ob_end_clean();
			
			return 'data:image/gif;base64,'.base64_encode($data);
		} else {
			// disable if image creation doesn't work
			RSFirewallConfig::getInstance()->set('verify_emails', 0);
		}
		
		return '';
	}
}