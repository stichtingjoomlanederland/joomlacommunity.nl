<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class RSFirewallGoogleSafeBrowsing
{

	private $api;
	private $url;

	public function __construct($api)
	{
		$this->api = trim($api);
	}

	public function buildUrl()
	{
		$url = 'https://sb-ssl.google.com/safebrowsing/api/lookup?';

		$vars = array(
			'client' => 'rsfirewall',
			'key'    => $this->api,
			'appver' => '1',
			'pver'   => '3.1',
			'url'    => urlencode(JUri::root())
		);

		$url .= http_build_query($vars);

		return $url;
	}

	public function check()
	{
		if (empty($this->api))
		{
			return array(
				'success' => true,
				'result'  => false,
				'message' => JText::_('COM_RSFIREWALL_GOOGLE_SAFE_BROWSER_NO_API_KEY'),
				'details' => JText::_('COM_RSFIREWALL_GOOGLE_SAFE_BROWSER_STEP_SKIPPED')
			);
		}

		$this->url = $this->buildUrl();
		$http    = JHttpFactory::getHttp();
		$request = $http->get($this->url);

		switch ($request->code)
		{
			case 200:
				return array(
					'success' => true,
					'result'  => false,
					'message' => JText::sprintf('COM_RSFIREWALL_GOOGLE_SAFE_BROWSER_INVALID', $request->body),
					'details' => JText::_('COM_RSFIREWALL_GOOGLE_SAFE_BROWSER_INVALID_DETAILS')
				);

			case 204:
				return array(
					'success' => true,
					'result'  => true,
					'message' => JText::_('COM_RSFIREWALL_GOOGLE_SAFE_BROWSER_VALID'),
					'details' => ''
				);

			case 400:
				return array(
					'success'   => false,
					'result'  => false,
					'message' => JText::_('COM_RSFIREWALL_GOOGLE_SAFE_BROWSER_BAD_REQUEST'),
					'details' => ''
				);

			case 403:
				return array(
					'success'   => false,
					'result'  => false,
					'message' => JText::_('COM_RSFIREWALL_GOOGLE_SAFE_BROWSER_BAD_API_KEY'),
					'details' =>  JText::_('COM_RSFIREWALL_GOOGLE_SAFE_BROWSER_HOW_TO_GET_KEY')
				);

			case 503:
				return array(
					'success'   => true,
					'result'  => false,
					'message' => JText::_('COM_RSFIREWALL_GOOGLE_SAFE_BROWSER_SERVICE_UNAVAILABLE'),
					'details' => ''
				);

		}
	}

}