<?php
/**
 * @package        RSFirewall!
 * @copyright  (c) 2009 - 2016 RSJoomla!
 * @link           https://www.rsjoomla.com
 * @license        GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class RSFirewallGoogleSafeBrowsing
{
	/**
	 * @var string
	 * @since 2.11.1
	 */
	private $api;
	/**
	 * @var string
	 * @since 2.11.1
	 */
	private $url;
	/**
	 * @var mixed|string
	 * @since 2.11.1
	 */
	private $data;

	/**
	 * RSFirewallGoogleSafeBrowsing constructor.
	 *
	 * @since 2.11.1
	 */
	public function __construct()
	{
		$config = RSFirewallConfig::getInstance();
		$api    = $config->get('google_safebrowsing_api_key');

		$this->api = trim($api);
		/**
		 * Example JSON to be sent over to google
			{
				"client": {
					"clientId":      "yourcompanyname",
					"clientVersion": "1.5.2"
				},
				"threatInfo": {
					"threatTypes":      ["MALWARE", "SOCIAL_ENGINEERING"],
					"platformTypes":    ["WINDOWS"],
					"threatEntryTypes": ["URL"],
					"threatEntries": [
						{"url": "http://www.urltocheck1.org/"},
						{"url": "http://www.urltocheck2.org/"},
						{"url": "http://www.urltocheck3.com/"}
					]
				}
			}
		 */
		$this->data = json_encode(array(
			'client'     => array(
				'clientId'      => 'RSFirewall!',
				'clientVersion' => (string) new RSFirewallVersion,
			),
			'threatInfo' => array(
				'threatTypes'      => array('MALWARE', 'SOCIAL_ENGINEERING', 'UNWANTED_SOFTWARE', 'POTENTIALLY_HARMFUL_APPLICATION', 'THREAT_TYPE_UNSPECIFIED'),
				'platformTypes'     => array('ANY_PLATFORM', 'PLATFORM_TYPE_UNSPECIFIED'),
				'threatEntryTypes' => array('URL'),
				'threatEntries'    => array(
					array('url' => urlencode(JUri::root())),
				)
			)
		));

		$this->url = $this->buildUrl();
	}

	/**
	 * @return string
	 * @since 2.11.1
	 */
	public function buildUrl()
	{
		return 'https://safebrowsing.googleapis.com/v4/threatMatches:find?key=' . $this->api;
	}

	/**
	 * @param string $api
	 *
	 * @since 2.11.1
	 * @return RSFirewallGoogleSafeBrowsing
	 */
	public static function getInstance($api = '')
	{
		static $inst;
		if (!$inst)
		{
			$inst = new RSFirewallGoogleSafeBrowsing;
		}

		return $inst;
	}

	/**
	 * @return mixed
	 * @since 2.11.1
	 */
	public static function getGoogleResponse()
	{
		$gsb = RSFirewallGoogleSafeBrowsing::getInstance();

		$headers = array(
			'POST ' . $gsb->url . ' HTTP/1.1',
			'Content-Type' => 'application/json'
		);

		$http    = JHttpFactory::getHttp();
		$request = $http->post(
			$gsb->url,
			$gsb->data,
			$headers
		);

		return $request;
	}

	/**
	 * @return array
	 * @since 2.11.1
	 */
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

		$cache = JFactory::getCache('com_rsfirewall');
		$cache->setCaching(true);
		$request = $cache->call(array('RSFirewallGoogleSafeBrowsing', 'getGoogleResponse'));

		return $this->parseRequest($request);

	}

	/**
	 * @return array
	 * @since 2.11.1
	 */
	public function parseRequest($request){
		$body = @json_decode($request->body);
		switch ($request->code)
		{
			case 200:
				$body = (array) $body;
				if (empty($body))
				{
					return array(
						'success' => true,
						'result'  => true,
						'message' => JText::_('COM_RSFIREWALL_GOOGLE_SAFE_BROWSER_VALID'),
						'details' => ''
					);
				}

				$reason = '';
				foreach ($body['matches'] as $match)
				{
					$reason .= $match->threatType . ' ';
				}

				return array(
					'success' => true,
					'result'  => false,
					'message' => JText::_('COM_RSFIREWALL_GOOGLE_SAFE_BROWSER_INVALID', $reason),
					'details' => JText::_('COM_RSFIREWALL_GOOGLE_SAFE_BROWSER_INVALID_DETAILS')
				);
				break;
			case 400:
				return array(
					'success' => false,
					'result'  => false,
					'message' => isset($body->error->message) ? $body->error->message : JText::_('COM_RSFIREWALL_GOOGLE_SAFE_BROWSER_BAD_REQUEST'),
					'details' => ''
				);
				break;
			case 403:
				return array(
					'success' => false,
					'result'  => false,
					'message' => isset($body->error->message) ? $body->error->message : JText::_('COM_RSFIREWALL_GOOGLE_SAFE_BROWSER_BAD_API_KEY'),
					'details' => JText::_('COM_RSFIREWALL_GOOGLE_SAFE_BROWSER_HOW_TO_GET_KEY')
				);
				break;
			case 500:
				return array(
					'success' => true,
					'result'  => false,
					'message' => isset($body->error->message) ? $body->error->message : JText::_('COM_RSFIREWALL_GOOGLE_SAFE_BROWSER_INTERNAL_SERVER_ERROR'),
					'details' => ''
				);
				break;
			case 503:
				return array(
					'success' => true,
					'result'  => false,
					'message' => isset($body->error->message) ? $body->error->message : JText::_('COM_RSFIREWALL_GOOGLE_SAFE_BROWSER_SERVICE_UNAVAILABLE'),
					'details' => ''
				);
				break;
			case 504:
				return array(
					'success' => true,
					'result'  => false,
					'message' => isset($body->error->message) ? $body->error->message : JText::_('COM_RSFIREWALL_GOOGLE_SAFE_BROWSER_TIMEOUT'),
					'details' => ''
				);
				break;
			default:
				return array(
					'success' => true,
					'result'  => false,
					'message' => isset($body->error->message) ? $body->error->message : JText::_('COM_RSIFREWALL_SOMETHING_WENT_WRONG'),
					'details' => ''
				);
				break;
		}
	}
}