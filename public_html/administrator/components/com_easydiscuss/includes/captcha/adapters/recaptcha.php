<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

/**
 * This is a PHP library that handles calling reCAPTCHA.
 *    - Documentation and latest version
 *          https://developers.google.com/recaptcha/docs/php
 *    - Get a reCAPTCHA API Key
 *          https://www.google.com/recaptcha/admin/create
 *    - Discussion group
 *          http://groups.google.com/group/recaptcha
 *
 * @copyright Copyright (c) 2014, Google Inc.
 * @link      http://www.google.com/recaptcha
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

require_once(__DIR__ . '/abstract.php');

class EasyDiscussCaptchaRecaptcha extends EasyDiscussCaptchaAbstract
{
	private static $_signupUrl = "https://www.google.com/recaptcha/admin";
	private static $_siteVerifyUrl = "https://www.google.com/recaptcha/api/siteverify?";

	private static $_version = "php_1.0";
	private $options = array();

	private $public = '';
	private $secret = '';
	private $colorScheme = null;
	private $language = null;

	public function __construct($options = array())
	{
		parent::__construct();

		$this->public = $this->config->get('antispam_recaptcha_public');
		$this->secret = $this->config->get('antispam_recaptcha_private');
		$this->colorScheme = $this->config->get('antispam_recaptcha_theme');
		$this->language = $this->config->get('antispam_recaptcha_lang');
	}

	/**
	 * Validates the captcha image
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function validate($data = array())
	{
		$response = $this->input->get('g-recaptcha-response');
		$ip = @$_SERVER['REMOTE_ADDR'];

		$response = $this->verifyResponse($ip, $response);

		if ($response === true) {
			return true;
		}

		$this->setError($response->errorCodes);

		return false;
	}

	/**
	 * Generates the recaptcha image
	 *
	 * @since   4.0
	 * @access  public
	 */
	public function html($isModule = false)
	{
		$uid = uniqid();
		$invisible = $this->config->get('antispam_recaptcha_invisible');
		$key = $this->config->get('antispam_recaptcha_public');

		$theme = ED::themes();
		$theme->set('recaptchaUid', $uid);
		$theme->set('public', $this->public);
		$theme->set('colorScheme', $this->colorScheme);
		$theme->set('language', $this->language);
		$theme->set('invisible', $invisible);
		$theme->set('key', $key);

		$output = '';
		$namespace = 'site/captcha/recaptcha';

		if ($isModule) {
			$namespace = 'site/captcha/recaptcha.module';
		}

		$output = $theme->output($namespace);

		return $output;
	}

	public function reload($previousCaptchaId = null)
	{

	}

	public function getImageSource()
	{

	}

	/**
	 * Encodes the given data into a query string format.
	 *
	 * @param array $data array of string elements to be encoded.
	 *
	 * @return string - encoded request.
	 */
	private function _encodeQS($data)
	{
		$req = "";
		foreach ($data as $key => $value) {
			$req .= $key . '=' . urlencode(stripslashes($value)) . '&';
		}

		// Cut the last '&'
		$req=substr($req, 0, strlen($req)-1);
		return $req;
	}

	/**
	 * Submits an HTTP GET to a reCAPTCHA server.
	 *
	 * @param string $path url path to recaptcha server.
	 * @param array  $data array of parameters to be sent.
	 *
	 * @return array response
	 */
	private function _submitHTTPGet($path, $data)
	{
		$req = $this->_encodeQS($data);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $path . $req);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CAINFO, JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/connector/adapters/cacert.pem');

		$response = curl_exec($ch);

		curl_close($ch);

		return $response;
	}

	/**
	 * Calls the reCAPTCHA siteverify API to verify whether the user passes
	 * CAPTCHA test.
	 *
	 * @param string $remoteIp   IP address of end user.
	 * @param string $response   response string from recaptcha verification.
	 *
	 * @return EasyDiscussRecaptchaResponse
	 */
	public function verifyResponse($remoteIp, $response)
	{
		$recaptchaResponse = new EasyDiscussRecaptchaResponse();
		$recaptchaResponse->success = true;
		$recaptchaResponse->errorCodes = '';

		// Discard empty solution submissions
		if ($response == null || strlen($response) == 0) {
			$recaptchaResponse->success = false;
			$recaptchaResponse->errorCodes = JText::_('COM_EASYDISCUSS_RECAPTCHA_MISSING_INPUT');
			return $recaptchaResponse;
		}

		$getResponse = $this->_submitHttpGet(
			self::$_siteVerifyUrl,
			array (
				'secret' => $this->secret,
				'remoteip' => $remoteIp,
				'v' => self::$_version,
				'response' => $response
			)
		);

		$answers = json_decode($getResponse, true);

		if (trim($answers['success']) == false) {

			$recaptchaResponse->success = false;

			return $recaptchaResponse;
		}

		return true;
	}

	public function isInvisible()
	{
		if ($this->config->get('antispam_recaptcha_invisible')) {
			return true;
		}
		
		return false;
	}
}

class EasyDiscussRecaptchaResponse
{
	public $success;
	public $errorCodes;
}
