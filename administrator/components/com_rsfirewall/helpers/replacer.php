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
			$string  = '';
			$string .= "\r\n".'<script type="text/javascript">function rsfirewall_mail(what){';
			foreach ($emails as $mail)
				$string .= "\nif (what == 'rsfirewall_".$mail['id']."')"."\ndocument.getElementById(what).src = '".JRoute::_('index.php?option=com_rsfirewall&task=mail&string='.$mail['encoded_mail'])."';\r\n";
			$string .= '}</script>';
			$text = str_replace('</body>', $string.'</body>', $text);
			
			return true;
		}
		
		return false;
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
			$mail = $params['mail'];
			$encoded_mail = base64_encode($mail);
			$_mail['encoded_mail'] = $encoded_mail;
			$_mail['mail'] = $mail;
			$replacement = '<img src="'.JRoute::_('index.php?option=com_rsfirewall&task=cloak&string='.$encoded_mail).'" style="cursor: pointer; vertical-align: middle" alt="" onclick="rsfirewall_mail(\'rsfirewall_'.$id.'\')" />';
		}
		
		if (!empty($params['mailText'])) {
			$mailText = $params['mailText'];
			$_mail['mailText'] = $mailText;
			$replacement = '<a href="javascript: void(0)" onclick="rsfirewall_mail(\'rsfirewall_'.$id.'\')">'.$mailText.'</a>';
		}
		
		$emails[] = $_mail;
		
		$replacement .= '<iframe src="" style="display: none; position: absolute; left: -1000px; top: -1000px;" width="0%" height="0%" id="rsfirewall_'.$id.'"></iframe>';
		return $replacement;
	}
}