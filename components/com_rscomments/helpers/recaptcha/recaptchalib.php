<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access'); 

define("RSC_RECAPTCHA_API_SERVER", "http://www.google.com/recaptcha/api");
define("RSC_RECAPTCHA_API_SECURE_SERVER", "https://www.google.com/recaptcha/api");
define("RSC_RECAPTCHA_VERIFY_SERVER", "www.google.com");

class RSCommentsReCAPTCHA
{
	/**
	 * Encodes the given data into a query string format
	 * @param $data - array of string elements to be encoded
	 * @return string - encoded request
	 */
	protected static function qsencode ($data) {
		$req = "";
		foreach ( $data as $key => $value )
			$req .= $key . '=' . urlencode( stripslashes($value) ) . '&';

		// Cut the last '&'
		$req=substr($req,0,strlen($req)-1);
		return $req;
	}

	/**
	 * Submits an HTTP POST to a reCAPTCHA server
	 * @param string $host
	 * @param string $path
	 * @param array $data
	 * @param int port
	 * @return array response
	 */
	protected static function post($host, $path, $data, $port = 80) {
		$req = RSCommentsReCAPTCHA::qsencode ($data);

		$http_request  = "POST $path HTTP/1.0\r\n";
		$http_request .= "Host: $host\r\n";
		$http_request .= "Content-Type: application/x-www-form-urlencoded;\r\n";
		$http_request .= "Content-Length: " . strlen($req) . "\r\n";
		$http_request .= "User-Agent: reCAPTCHA/PHP\r\n";
		$http_request .= "\r\n";
		$http_request .= $req;

		$response = '';
		if( false == ( $fs = @fsockopen($host, $port, $errno, $errstr, 10) ) ) {
			JError::raiseWarning(500, 'RSComments! ReCAPTCHA: Could not open socket. Please check that your server can connect to '.$host);
			return false;
		}

		fwrite($fs, $http_request);
		while ( !feof($fs) )
			$response .= fgets($fs, 1160); // One TCP-IP packet
		fclose($fs);
		$response = explode("\r\n\r\n", $response, 2);

		return $response;
	}

	/**
	 * Gets the challenge HTML (javascript and non-javascript version).
	 * This is called from the browser, and the resulting reCAPTCHA HTML widget
	 * is embedded within the HTML form it was called from.
	 * @param string $pubkey A public key for reCAPTCHA
	 * @param string $error The error given by reCAPTCHA (optional, default is null)
	 * @param boolean $use_ssl Should the request be made over ssl? (optional, default is false)

	 * @return string - The HTML to be embedded in the user's form.
	 */
	public static function getHTML ($error = null, $use_ssl = false) {
		$pubkey = RSCommentsHelper::getConfig('rec_public');
		if ($pubkey == null || $pubkey == '') {
			return "To use reCAPTCHA you must get an API key from <a target='_blank' href='http://www.google.com/recaptcha'>http://www.google.com/recaptcha</a>";
		}
		
		$return = ' <script type="text/javascript">var RecaptchaOptions = { theme : \''.RSCommentsHelper::getConfig('rec_themes').'\' };</script>';
		$jconfig = new JConfig();
		$use_ssl = $jconfig->force_ssl == 2;
		$server = $use_ssl ? RSC_RECAPTCHA_API_SECURE_SERVER : RSC_RECAPTCHA_API_SERVER;

		$errorpart = "";
		if ($error) {
		   $errorpart = "&amp;error=" . $error;
		}
		$return .= '<script type="text/javascript" src="'. $server . '/challenge?k=' . $pubkey . $errorpart . '"></script>

		<noscript>
			<iframe src="'. $server . '/noscript?k=' . $pubkey . $errorpart . '" height="300" width="500" frameborder="0"></iframe><br/>
			<textarea name="recaptcha_challenge_field" rows="3" cols="40"></textarea>
			<input type="hidden" name="recaptcha_response_field" value="manual_challenge"/>
		</noscript>';
		
		return $return;
	}
	
	public static function loadScript($id, $config) {
		$pubkey = RSCommentsHelper::getConfig('rec_public');
		if ($pubkey == null || $pubkey == '') {
			return "To use reCAPTCHA you must get an API key from <a target='_blank' href='http://www.google.com/recaptcha'>http://www.google.com/recaptcha</a>";
		}
		
		$jconfig = JFactory::getConfig();
		$use_ssl = $jconfig->get('force_ssl') == 2;
		$server = $use_ssl ? RSC_RECAPTCHA_API_SECURE_SERVER : RSC_RECAPTCHA_API_SERVER;
		
		$html = '<script type="text/javascript" src="'.$server.'/challenge?k='.$pubkey.'"></script>'."\n";
		$html .= '<div id="'.$id.'"></div>'."\n";
		$html .= '<script type="text/javascript">'."\n";
		$html .= "\t".'Recaptcha.destroy();'."\n";
		$html .= "\t".'Recaptcha.create("'.$config->rec_public.'", "'.$id.'", {'."\n";
		$html .= "\t\t".'theme: "'.$config->rec_themes.'"'."\n";
		$html .= "\t".'});'."\n";
		$html .= '</script>'."\n";
		
		return $html;
	}

	/**
	  * Calls an HTTP POST function to verify if the user's guess was correct
	  * @param string $privkey
	  * @param string $remoteip
	  * @param string $challenge
	  * @param string $response
	  * @param array $extra_params an array of extra variables to post to the server
	  * @return ReCaptchaResponse
	  */
	public static function checkAnswer ($privkey, $remoteip, $challenge, $response, $extra_params = array()) {
		if ($privkey == null || $privkey == '') {
			JError::raiseWarning(500, 'To use reCAPTCHA you must get an API key from <a href="http://recaptcha.net/api/getkey">http://recaptcha.net/api/getkey</a>');
			return false;
		}

		if ($remoteip == null || $remoteip == '') {
			JError::raiseWarning(500, 'For security reasons, you must pass the remote IP to reCAPTCHA. We could not detect your IP.');
			return false;
		}
		
		//discard spam submissions
		if ($challenge == null || strlen($challenge) == 0 || $response == null || strlen($response) == 0) {
				$recaptcha_response = new RSCommentsReCAPTCHAResponse();
				$recaptcha_response->is_valid = false;
				$recaptcha_response->error = 'incorrect-captcha-sol';
				return $recaptcha_response;
		}

		$response = RSCommentsReCAPTCHA::post (RSC_RECAPTCHA_VERIFY_SERVER, "/recaptcha/api/verify",
										  array (
												 'privatekey' => $privkey,
												 'remoteip' => $remoteip,
												 'challenge' => $challenge,
												 'response' => $response
												 ) + $extra_params
										  );

		$answers = explode ("\n", $response [1]);
		$recaptcha_response = new RSCommentsReCAPTCHAResponse();

		if (trim ($answers [0]) == 'true') {
			$recaptcha_response->is_valid = true;
		} else {
			$recaptcha_response->is_valid = false;
			$recaptcha_response->error = @$answers[1];
		}
		return $recaptcha_response;
	}

	/**
	 * gets a URL where the user can sign up for reCAPTCHA. If your application
	 * has a configuration page where you enter a key, you should provide a link
	 * using this function.
	 * @param string $domain The domain where the page is hosted
	 * @param string $appname The name of your application
	 */
	public function get_signup_url ($domain = null, $appname = null) {
		return "http://recaptcha.net/api/getkey?" .  RSCommentsReCAPTCHA::qsencode (array ('domain' => $domain, 'app' => $appname));
	}
}

/**
 * A RSCommentsReCAPTCHAResponse is returned from recaptcha_check_answer()
 */
class RSCommentsReCAPTCHAResponse {
	public $is_valid;
	public $error;
}