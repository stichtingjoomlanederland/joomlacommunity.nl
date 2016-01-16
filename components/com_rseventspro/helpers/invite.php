<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

// Yahoo! Contacts
class RSYahoo {
	
	public static $emails = array();
	
	public static function auth($callback) {
		require_once JPATH_SITE.'/components/com_rseventspro/helpers/yahoo/yahoo.php';
		
		$config	= rseventsproHelper::getConfig();
		$key	= $config->yahoo_key;
		$secret = $config->yahoo_secret;
		$appid	= $config->yahoo_appid;
		
		// No credentials for the Yahoo! integration
		if (empty($key) && empty($secret) && empty($appid)) {
			return;
		}
		
		$hasSession = YahooSession::hasSession($key, $secret, $appid);
		
		if ($hasSession == FALSE) {
			$auth_url = YahooSession::createAuthorizationUrl($key, $secret, $callback);
		} else {
			$session = YahooSession::requireSession($key, $secret, $appid);
			
			if ($session) {
				$user = $session->getSessionedUser();
				$contacts = $user->getContacts(0,9999999);
				$contacts = @$contacts->contacts->contact;
				
				if (isset($contacts) && !empty($contacts)) {
					foreach ($contacts as $contact) {
						foreach ($contact->fields as $field) {
							if ($field->type == 'email') {
								self::$emails[] = $field->value;
							}
						}
					}
				}
			
				YahooSession::clearSession();
				$auth_url = YahooSession::createAuthorizationUrl($key, $secret, $callback);
			}
		}
		
		return $auth_url;
	}
	
	public static function getContacts() {
		if (!empty(self::$emails)) {
			return implode("\n", self::$emails);
		}
		
		return;
	}
}