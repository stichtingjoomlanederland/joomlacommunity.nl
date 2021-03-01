<?php
/**
 * @package     JoomlatoolsUpdater
 * @copyright   Copyright (C) 2021 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class PlgSystemJoomlatoolsupdaterLicense extends KObject implements KObjectSingleton
{
    /*const LICENSE_ENDPOINT = 'https://support.joomlatools.box/data/site';
    const PUBLIC_KEY_ENDPOINT = 'https://support.joomlatools.box/data/public-key';*/
    const LICENSE_ENDPOINT = 'https://api.joomlatools.com/license/site';
    const PUBLIC_KEY_ENDPOINT = 'https://api.joomlatools.com/license/public-key';

    const API_KEY_OPTION = 'foliolabs_api_key';
    const LICENSE_OPTION = 'foliolabs_license';
    const SITE_KEY_OPTION = 'foliolabs_site_key';
    const PUBLIC_KEY_OPTION = 'foliolabs_public_key';

    /** @var PlgSystemJoomlatoolsupdaterCryptoToken */
    protected $_token;

    /** @var null|string */
    protected $_error;

    protected $_parameters;

    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->_setParameters($config->parameters);
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append([
            'parameters' => []
        ]);

        if ($plugin = $this->_getPlugin()) {
            $config->append([
                'parameters' => $plugin->params
            ]);
        }

        parent::_initialize($config);
    }

    public function hasError()
    {
        return (bool) $this->_error;
    }

    public function getError()
    {
        return $this->_error;
    }

    protected function _validateToken($token)
    {
        if (!$token->verify($this->getPublicKey())) {
            throw new PlgSystemJoomlatoolsupdaterLicenseException('Cannot verify license');
        }

        if ($token->isExpired()) {
            throw new PlgSystemJoomlatoolsupdaterLicenseException('License is not valid anymore');
        }

        $site = $token->getClaim('site');

        if ($site['key'] !== $this->getSiteKey()) {
            throw new PlgSystemJoomlatoolsupdaterLicenseException('License is for a different site');
        }
    }

    public function load()
    {
        try {
            $license = $this->getLicense();

            if (!$license) {
                throw new PlgSystemJoomlatoolsupdaterLicenseException('Cannot find license');
            }

            if (!$this->getPublicKey()) {
                throw new PlgSystemJoomlatoolsupdaterLicenseException('Cannot find public key');
            }

            if (!$this->getSiteKey()) {
                throw new PlgSystemJoomlatoolsupdaterLicenseException('Cannot find site key');
            }

            $token = $this->decode($license);

            $this->_validateToken($token);

            $this->_token = $token;

            return true;
        }
        catch (PlgSystemJoomlatoolsupdaterLicenseException $e) {
            $this->_error = $e->getMessage();

            return false;
        }

    }

    public function getToken()
    {
        if (!$this->_token && !$this->hasError()) {
            $this->load();
        }

        return $this->_token;
    }

    public function isValid()
    {
        if (!$this->_token && !$this->hasError()) {
            $this->load();
        }

        if ($this->hasError() || time() > $this->getExpiry()) {
            return false;
        }

        return true;
    }

    public function hasFeature($feature)
    {
        if (!$this->_token && !$this->hasError()) {
            $this->load();
        }

        if ($this->hasError()) {
            return false;
        }

        if ($feature === 'connect') {
            $connect = $this->getToken()->getClaim('connect');

            return $connect && $connect['enabled'] === true;
        }

        return $this->isValid();
    }

    protected function _isAgency()
    {
        return (bool) array_intersect([6, 13], array_column($this->getSubscriptions(), 'id'));
    }

    protected function _isBusiness()
    {
        return in_array(5, array_column($this->getSubscriptions(), 'id'));
    }

    protected function _isBusinessOrHigher()
    {
        return (bool) array_intersect([5, 6, 13], array_column($this->getSubscriptions(), 'id'));
    }

    public function getSubscriptions()
    {
        return !$this->hasError() ? ($this->getToken()->getClaim('subscriptions') ?: []) : [];
    }

    public function getExpiry()
    {
        $end = 0;

        foreach ($this->getSubscriptions() as $subscription) {
            if ($subscription['end'] > $end) {
                $end = $subscription['end'];
            }
        }

        return $end;
    }

    public function getConnectKeys()
    {
        return $this->isValid() && $this->hasFeature('connect') ? $this->getToken()->getClaim('connect') : [];
    }

    public function getCustomer()
    {
        return $this->getToken()->getClaim('sub');
    }

    public function getLicense()
    {
        return $this->_parameters->license;
    }

    public function getPublicKey()
    {
        return $this->_parameters->public_key;
    }

    public function getApiKey()
    {
        return $this->_parameters->api_key;
    }

    public function getSiteKey()
    {
        return $this->_parameters->site_key;
    }

    public function setApiKey($api_key)
    {
        $this->_parameters->api_key = trim($api_key);

        $this->_saveParameters();
    }

    public function setLicense($license)
    {
        $this->_parameters->license = trim($license);

        $this->_saveParameters();
    }

    public function setPublicKey($public_key)
    {
        $this->_parameters->public_key = trim($public_key);

        $this->_saveParameters();
    }

    public function setSiteKey($site_key)
    {
        $this->_parameters->site_key = trim($site_key);

        $this->_saveParameters();
    }

    protected function _getPlugin()
    {
        $result = null;
        try {
            $query = /** @lang text */"SELECT extension_id, params, manifest_cache FROM #__extensions
                    WHERE type = 'plugin' AND element = 'joomlatoolsupdater' AND folder = 'system'
                    LIMIT 1
                    ";

            if ($result = JFactory::getDbo()->setQuery($query)->loadObject()) {
                $result->params = @json_decode($result->params, true);
                $result->version = null;

                $manifest = @json_decode($result->manifest_cache);
                unset($result->manifest_cache);
                if (is_object($manifest) && !empty($manifest->version)) {
                    $result->version = $manifest->version;
                }
            }
        }
        catch (\Exception $e) {}

        return $result;
    }

    protected function _saveParameters()
    {
        $plugin = $this->_getPlugin();

        if ($plugin) {
            $query = "UPDATE #__extensions SET params = '%s' WHERE extension_id = %d";
            $params = $this->_parameters->toArray();

            foreach ($params as $key => $value) {
                $params[$key] = trim(str_replace(['\r', '\n', "\r", "\n"], '', $value));
            }

            $result = JFactory::getDbo()->setQuery(sprintf($query, json_encode($params), $plugin->extension_id))->execute();

            $this->_refreshParametersFromDatabase();

            return $result;
        }

        return false;
    }

    protected function _setParameters($parameters) {
        if (is_array($parameters)) {
            $parameters = new KObjectConfig($parameters);
        }

        $this->_parameters = $parameters;
    }

    protected function _refreshParametersFromDatabase() {
        if ($plugin = $this->_getPlugin()) {
            $this->_setParameters($plugin->params);
        }
    }

    public function setApiKeyFromFile($file)
    {
        if (is_file($file)) {
            $api_key = file_get_contents($file);

            if ($api_key) {
                $this->setApiKey($api_key);
            }
        }
    }

    public function setPublicKeyFromFile($file)
    {
        if (is_file($file)) {
            $public_key = file_get_contents($file);

            if ($public_key) {
                $this->setPublicKey($public_key);
            }
        }
    }

    public function savePublicKey()
    {
        $public_key = $this->_sendRequest(static::PUBLIC_KEY_ENDPOINT);

        if ($public_key->status_code === 200) {
            $this->setPublicKey($public_key->body);

            return true;
        }

        return false;
    }

    public function saveLicenseToken()
    {
        $token_request = $this->_sendRequest(static::LICENSE_ENDPOINT, [
            'data' => $this->getSiteData(),
            'headers' => [
                'authorization: Bearer '.$this->getApiKey(),
                'accept: application/json',
                'content-type: application/json',
            ]
        ]);

        if ($token_request->status_code === 200) {
            $body = json_decode($token_request->body);

            if (!empty($body) && !empty($body->license)) {
                $this->setLicense($body->license);
            }
        }
    }

    public function saveSiteKey()
    {
        if (!$this->getSiteKey()) {
            $site_key = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0x0fff) | 0x4000,
                mt_rand(0, 0x3fff) | 0x8000,
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
            );

            $this->setSiteKey($site_key);
        }
    }

    public function getSiteData()
    {
        return [
            "key" => $this->getSiteKey(),
            "is_local" => $this->isLocal(),
            "is_valid" => $this->isValid(),
            "public_key" => $this->getPublicKey(),
            "api_key" => $this->getApiKey(),
            "url" => JURI::root(),
            "joomla" => JVERSION,
            "php" => phpversion(),
            "mysql" => JFactory::getDbo()->getVersion(),
            "extensions" => $this->_getExtensionVersions()
        ];
    }


    public function onInstall()
    {
        $this->refresh();
    }

    public function refresh()
    {
        $this->savePublicKey();
        $this->saveSiteKey();
        $this->saveLicenseToken();
    }

    /**
     * @param $string
     * @return PlgSystemJoomlatoolsupdaterCryptoToken
     */
    public function decode($string)
    {
        try {
            /** @var PlgSystemJoomlatoolsupdaterCryptoToken $token */
            $token = $this->getObject('plg:system.joomlatoolsupdater.crypto.token');
            $token->fromString($string);

            return $token;
        } catch (\Exception $e) {
            throw new PlgSystemJoomlatoolsupdaterLicenseException('Cannot create token from license', 0, $e);
        }
    }

    protected function _sendRequest($url, $options = array())
    {
        $curl = curl_init();

        if (isset($options['query'])) {
            if (is_array($options['query'])) {
                $options['query'] = http_build_query($options['query'], '', '&');
            }

            $url .= '?'.$options['query'];
        }

        $method = isset($options['method']) ? strtoupper($options['method']) : (isset($options['data']) ? 'POST' : 'GET');

        $headers = array_merge([
            'User-agent' => 'Foliolabs/License',

        ], (isset($options['headers']) ?$options['headers'] : []));

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => isset($options['data']) ? json_encode($options['data']) : null,
            CURLOPT_HTTPHEADER => $headers,
        ));

        if (isset($options['callback']) && is_callable($options['callback'])) {
            $callback = $options['callback'];
            $callback($curl, $url, $options);
        }

        $response = curl_exec($curl);

        if (curl_errno($curl) && (!isset($options['exception']) || $options['exception'] !== false)) {
            throw new \RuntimeException('Curl Error: '.curl_error($curl));
        }

        $status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if (isset($status_code) && ($status_code < 200 || $status_code >= 300)
            && (!isset($options['exception']) || $options['exception'] !== false)) {
            throw new \UnexpectedValueException('Problem in the request. Request returned '. $status_code . ' with response: '.$response, $status_code);
        }

        curl_close($curl);

        $result = new \stdClass();
        $result->status_code = $status_code;
        $result->body        = $response;

        return $result;
    }

    /**
     * Returns true if the site is running on localhost
     *
     * @return string
     */
    public function isLocal()
    {
        static $local_hosts = ['localhost', '127.0.0.1', '::1'];

        $url  = $this->getObject('request')->getUrl();
        $host = $url->host;

        if (in_array($host, $local_hosts)) {
            return true;
        }

        // Returns true if host is an IP address
        if (ip2long($host)) {
            return (filter_var($host, FILTER_VALIDATE_IP,
                    FILTER_FLAG_IPV4 |
                    FILTER_FLAG_IPV6 |
                    FILTER_FLAG_NO_PRIV_RANGE |
                    FILTER_FLAG_NO_RES_RANGE) === false);
        }
        else {
            // If no TLD is present, it's definitely local
            if (strpos($host, '.') === false) {
                return true;
            }

            return preg_match('/(?:\.)(local|localhost|test|example|invalid|dev|box|intern|internal)$/', $host) === 1;
        }
    }

    protected function _getExtensionVersions()
    {
        $extensions = [];

        foreach (static::$_extensions as $key => $extension) {
            if ($version = $this->_getExtensionVersion($extension)) {
                $extensions[$key] = $version;
            }
        }

        return $extensions;
    }

    protected static $_extensions = array(
        'docman' => array('type' => 'component', 'element' => 'com_docman'),
        'fileman' => array('type' => 'component', 'element' => 'com_fileman'),
        'leadman' => array('type' => 'component', 'element' => 'com_leadman'),
        'logman' => array('type' => 'component', 'element' => 'com_logman'),
        'textman' => array('type' => 'component', 'element' => 'com_textman'),
        'connect' => array('type' => 'plugin', 'element' => 'connect', 'folder' => 'koowa')
    );


    /**
     * Returns null if the extension does not exist, version number as string if it exists
     * or 'unknown' if extension exists but the version number cannot be determined
     * @param $extension
     * @return null|string
     */
    protected function _getExtensionVersion($extension)
    {
        $version = null;

        try {
            $folder  = isset($extension['folder']) ? $extension['folder'] : '';

            $query = /** @lang text */"SELECT manifest_cache FROM #__extensions
                    WHERE type = '{$extension['type']}' AND element = '{$extension['element']}' AND folder = '$folder'
                    LIMIT 1
                    ";

            if ($result = JFactory::getDbo()->setQuery($query)->loadResult()) {
                $manifest = @json_decode($result);

                if (is_object($manifest) && !empty($manifest->version)) {
                    $version = $manifest->version;
                }
            }

        }
        catch (Exception $e) {}

        return $version;
    }
}