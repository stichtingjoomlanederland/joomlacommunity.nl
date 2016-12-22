<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanControllerBehaviorScannable extends KControllerBehaviorAbstract
{
    const URL = "https://api.joomlatools.com/thumbnail/";

    const STATUS_PENDING = 0;

    const STATUS_SENT = 1;

    const STATUS_FAILED = 2;

    const MAXIMUM_PENDING_SCANS = 3;

    public static $thumbnail_extensions = array(
        'psd', 'ai', 'eps', 'pdf', 'png', 'jpg', 'jpeg', 'gif', 'bmp', 'svg', 'tif', 'tiff', 'pbm', 'pgm', 'ppm',
        'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'rtf',
        'odt', 'ott', 'ods', 'ott', 'odp', 'otp', 'odg', 'otg', 'odc', 'otc',
        'webm', 'avi', 'xvid', 'divx', 'mpg', 'mpeg', 'mpeg4', 'm4v', 'mp4', 'mov', 'mkv', 'wmv','html'
    );

    public static $ocr_extensions = array(
        'psd', 'ai', 'eps', 'pdf', 'png', 'jpg', 'jpeg', 'gif', 'bmp', 'svg', 'tif', 'tiff', 'pbm', 'pgm', 'ppm',
        'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'rtf',
        'odt', 'ott', 'ods', 'ott', 'odp', 'otp', 'odg', 'otg', 'odc', 'otc'
    );

    protected $_scan;

    protected $_document;

    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $thumbnail_controller = 'com://admin/docman.controller.thumbnail';

        $this->getIdentifier($thumbnail_controller)->getConfig()->append(array(
            'supported_extensions' => static::$thumbnail_extensions
        ));

        $this->getObject($thumbnail_controller)->addCommandCallback('before.generate', function($context) {
            return $this->_beforeGenerate($context);
        });
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'priority'   => static::PRIORITY_LOW, // low priority so that thumbnailable runs first
            'api_key'    => '',
            'secret_key' => ''
        ));

        if ($this->isSupported()) {
            $config->api_key    = PlgKoowaConnect::getInstance()->getApiKey();
            $config->secret_key = PlgKoowaConnect::getInstance()->getSecretKey();
        }

        parent::_initialize($config);
    }

    public function isSupported()
    {
        return class_exists('PlgKoowaConnect') && PlgKoowaConnect::isSupported();
    }

    public function purgeStaleScans()
    {
        /*
         * Remove scans for deleted documents
         */
        /** @var KDatabaseQueryDelete $query */
        $query = $this->getObject('database.query.delete');

        $query
            ->table(array('tbl' => 'docman_scans'))
            ->join(array('d' => 'docman_documents'), 'd.uuid = tbl.identifier')
            ->where('d.docman_document_id IS  NULL');

        $this->getObject('com://admin/docman.database.table.scans')->getAdapter()->delete($query);

        /*
         * Set status back to "not sent" for scans that did not receive a response for over an hour
         */
        /** @var KDatabaseQueryUpdate $query */
        $query = $this->getObject('database.query.update');

        $query
            ->values('status = '.\ComDocmanControllerBehaviorScannable::STATUS_PENDING)
            ->table(array('tbl' => 'docman_scans'))
            ->where('status = '.\ComDocmanControllerBehaviorScannable::STATUS_SENT)
            ->where("GREATEST(created_on, modified_on) < DATE_SUB(NOW(), INTERVAL 1 HOUR)");

        $this->getObject('com://admin/docman.database.table.scans')->getAdapter()->update($query);
    }

    public function sendPendingScan()
    {
        $scan = $this->_getScansModel()
            ->status(\ComDocmanControllerBehaviorScannable::STATUS_PENDING)
            ->limit(1)
            ->sort('created_on')->direction('asc')
            ->fetch();

        if (!$scan->isNew()) {
            $this->_sendScan($scan);
        }

        return $scan;
    }

    public function needsThrottling()
    {
        $count = $this->_getScansModel()->status(\ComDocmanControllerBehaviorScannable::STATUS_SENT)->count();

        return ($count >= static::MAXIMUM_PENDING_SCANS);
    }


    public function hasPendingScan()
    {
        return $this->_getScansModel()->status(\ComDocmanControllerBehaviorScannable::STATUS_PENDING)->count();
    }

    public function canSendScan()
    {
        $this->purgeStaleScans();

        return ($this->isSupported() && !$this->_isLocal() && $this->hasPendingScan() && !$this->needsThrottling());
    }

    /**
     * Create a thumbnail for new files
     *
     * @param KControllerContextInterface $context
     */
    protected function _afterAdd(KControllerContextInterface $context)
    {
        if ($context->response->getStatusCode() === 201) {
            $this->_enqueueScan($context->result, true);

            $this->_sendCurrentScan();
        }
    }

    /**
     * Figure out if the file has changed and if so regenerate the thumbnail on after save
     *
     * @param KControllerContextInterface $context
     */
    protected function _beforeEdit(KControllerContextInterface $context)
    {
        $item = $this->getModel()->fetch();
        $data = $context->request->data;

        // Force a re-generate if the document file changes.
        if ($data->storage_path && ($item->storage_path !== $data->storage_path)) {
            $context->file_changed = true;
        }
    }

    protected function _afterEdit(KControllerContextInterface $context)
    {
        if ($context->getResponse()->getStatusCode() < 300) {
            if ($context->file_changed) {
                foreach ($context->result as $entity) {
                    $this->_enqueueScan($entity, true);
                }
            }

            $this->_sendCurrentScan();
        }
    }

    /**
     * Returns false if the document is in queue to be scanned
     *
     * @param KControllerContextInterface $context
     * @return bool
     */
    protected function _beforeGenerate(KControllerContextInterface $context)
    {
        /** @var ComDocmanModelEntityDocument $document */
        $document = $context->getAttribute('entity');
        $in_queue = $this->_enqueueScan($document, false, true);

        return $in_queue ? false : true;
    }

    protected function _sendCurrentScan()
    {
        try
        {
            if ($this->needsThrottling()) {
                $message = $this->getObject('translator')->translate('Document scan is throttled');

                $this->getObject('response')->addMessage($message, KControllerResponse::FLASH_SUCCESS);

                return false;
            }

            if ($this->_scan && $this->_document) {
                $this->_sendScan($this->_scan, $this->_document);
            }
        }
        catch (Exception $e) {
            if (JDEBUG) {
                $this->getObject('response')->addMessage($e->getMessage(), KControllerResponse::FLASH_ERROR);
            }
        }
    }

    protected function _sendScan($scan, $document = null)
    {
        if ($document === null)
        {
            if (!$scan->identifier) {
                throw new UnexpectedValueException('Scan does not contain a document identifier');
            }

            $document = $this->getObject('com://admin/docman.model.documents')->uuid($scan->identifier)->fetch();

            if ($document->isNew()) {
                throw new UnexpectedValueException(sprintf('Document not found with the UUID %s', $scan->identifier));
            }
        }

        if (!$this->_isLocal()) {
            $size = $this->getObject('com://admin/docman.controller.thumbnail')->getThumbnailSize();
            $data = array(
                'url'    => (string)$this->_getDownloadUrl($document),
                'sizes'  => array($size['x'].'>'),
                'format' => 'jpg',
                'pages'  => 'all',
                'metadata' => array('ocr', 'checksum'),
                'data'   => array(
                    'uuid'     => $document->uuid,
                    'callback' => (string)$this->_getCallbackUrl()
                )
            );

            if ($this->_sendRequest(static::URL.'?jwt='.$this->_getToken(), $data)) {
                $scan->status = static::STATUS_SENT;
                $scan->save();
            }

            $message = $this->getObject('translator')->translate('Document scan is in progress');

            $this->getObject('response')->addMessage($message, KControllerResponse::FLASH_SUCCESS);
        }
        else {
            $message = $this->getObject('translator')->translate('Document scan needs public server');

            $this->getObject('response')->addMessage($message, KControllerResponse::FLASH_WARNING);
        }

        return $scan;
    }

    protected function _enqueueScan(ComDocmanModelEntityDocument $document, $ocr = false, $thumbnail = false)
    {
        $thumbnail = $thumbnail && in_array($document->extension, static::$thumbnail_extensions);
        $ocr       = $ocr && in_array($document->extension, static::$ocr_extensions) && $document->storage_type === 'file';

        if ($thumbnail || $ocr) {
            $model = $this->_getScansModel();
            $scan  = $model->identifier($document->uuid)->fetch();

                if ($scan->isNew()) {
                    $scan = $model->create();
                    $scan->identifier = $document->uuid;
                }

            if ($ocr) {
                $scan->ocr = $ocr;
            }

            if ($thumbnail) {
                $scan->thumbnail = $thumbnail;
            }

            if ($scan->save()) {
                $this->_document = $document;
                $this->_scan     = $scan;
            }

            return true;
        }

        return false;
    }
    protected function _isLocal()
    {
        static $local_hosts = array('localhost', '127.0.0.1', '::1');

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

    /**
     * Sends an HTTP request and returns the response
     *
     * @param  string $url Destination
     * @param  array $data Request data, will be encoded as JSON
     * @return string
     */
    protected function _sendRequest($url, $data)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                "Content-type: application/json"
            ),
        ));

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            throw new RuntimeException('Curl Error: '.curl_error($curl));
        }

        $info = curl_getinfo($curl);

        if (isset($info['http_code']) && ($info['http_code'] < 200 || $info['http_code'] >= 300)) {
            throw new RuntimeException('Problem in the request. Request returned '. $info['http_code']);
        }

        curl_close($curl);

        return $response;
    }

    protected function _getScansModel()
    {
        return $this->getObject('com://admin/docman.model.scans');
    }

    /**
     * Return a callback URL to the plugin with a JWT token
     *
     * @return KHttpUrlInterface
     */
    protected function _getCallbackUrl()
    {
        /** @var KHttpUrlInterface $url */
        $url       = clone $this->getObject('request')->getSiteUrl();
        $query     = array(
            'option' => 'com_docman',
            'view'   => 'documents',
            'format' => 'json',
            'thumbnail' => 1,
            'token' => $this->_getToken()
        );

        if (substr($url->getPath(), -1) !== '/') {
            $url->setPath($url->getPath().'/');
        }

        $url->setQuery($query);

        return $url;
    }

    /**
     * Return a download URL with a JWT token for the given document
     *
     * This will bypass all access checks to make sure thumbnail service can access the file
     *
     * @param  KModelEntityInterface $document
     * @return KHttpUrlInterface
     */
    protected function _getDownloadUrl(KModelEntityInterface $document)
    {
        /** @var KHttpUrlInterface $url */
        $url       = clone $this->getObject('request')->getSiteUrl();
        $query     = array(
            'option' => 'com_docman',
            'view'   => 'documents',
            'serve'  => 1,
            'thumbnail' => 1,
            'id'     => $document->id,
            'token'  => $this->_getToken()
        );

        $url->setQuery($query);

        return $url;
    }

    /**
     * Returns a signed JWT token for the current API key in plugin settings
     *
     * @return string
     */
    protected function _getToken()
    {
        /** @var KHttpTokenInterface $token */
        $token = $this->getObject('http.token');
        $date  = new DateTime('now');

        return $token
            ->setIssuer($this->getConfig()->api_key)
            ->setExpireTime($date->modify('+1 hours'))
            ->sign($this->getConfig()->secret_key);
    }
}