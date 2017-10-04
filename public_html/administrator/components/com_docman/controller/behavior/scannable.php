<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanControllerBehaviorScannable extends KControllerBehaviorAbstract
{
    const STATUS_PENDING = 0;

    const STATUS_SENT = 1;

    const STATUS_FAILED = 2;

    const MAXIMUM_PENDING_SCANS = 6;

    const MAXIMUM_FILE_SIZE = 262144000; // 250 MB

    public static $thumbnail_extensions = [
        'pdf', 'doc', 'docx', 'odt', 'xls', 'xlsx', 'ods', 'ppt', 'pptx', 'odp',
        'bmp', 'gif', 'png', 'tif', 'tiff', 'ai', 'psd', 'svg', 'jpg', 'jpeg', 'html', 'txt'
    ];

    public static $ocr_extensions = [
        'pdf', 'doc', 'docx', 'odt', 'html', 'txt',
        'xls', 'xlsx', 'ods', 'ppt', 'pptx'
    ];

    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        if ($this->isSupported()) {
            $thumbnail_controller = 'com://admin/docman.controller.thumbnail';

            $this->getIdentifier($thumbnail_controller)->getConfig()->append(array(
                'supported_extensions' => static::$thumbnail_extensions
            ));

            $this->getObject($thumbnail_controller)->addCommandCallback('before.generate', function($context) {
                return $this->_beforeGenerate($context);
            });
        }

    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'priority'   => static::PRIORITY_LOW, // low priority so that thumbnailable runs first
        ));

        parent::_initialize($config);
    }

    public function isSupported()
    {
        return $this->getObject('com://admin/docman.model.entity.config')->connectAvailable();
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

        $now = gmdate('Y-m-d H:i:s');

        $query
            ->values('status = '.\ComDocmanControllerBehaviorScannable::STATUS_PENDING)
            ->table(array('tbl' => 'docman_scans'))
            ->where('status = '.\ComDocmanControllerBehaviorScannable::STATUS_SENT)
            ->where("GREATEST(created_on, modified_on) < DATE_SUB(:now, INTERVAL 1 HOUR)")
            ->bind(['now' => $now]);

        $this->getObject('com://admin/docman.database.table.scans')->getAdapter()->update($query);
    }

    public function sendPendingScan()
    {
        $scan = $this->_getScansModel()
            ->status(\ComDocmanControllerBehaviorScannable::STATUS_PENDING)
            ->limit(1)
            ->sort('created_on')->direction('desc')
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

    public function canScanDocument(KModelEntityInterface $document)
    {
        return $document->storage_type === 'file'
        && $document->size && $document->size < static::MAXIMUM_FILE_SIZE
        && (in_array($document->extension, static::$thumbnail_extensions)
            || in_array($document->extension, static::$ocr_extensions))
            ;
    }

    public function shouldScanDocument(KModelEntityInterface $document)
    {
        $result = false;

        if ($this->canScanDocument($document)) {
            if (in_array($document->extension, static::$thumbnail_extensions)) {
                if (!$document->image) {
                    $result = true;
                }
            }

            if (!$result && in_array($document->extension, static::$ocr_extensions)) {
                if ($document->isNew() || !$document->contents) {
                    $result = true;
                }
            }
        }

        return $result;
    }

    /**
     * Hooks into thumbnail controller and stops the default local thumbnail generation
     *
     * Returns false if the document is in queue to be scanned
     *
     * @param KControllerContextInterface $context
     * @return bool
     */
    protected function _beforeGenerate(KControllerContextInterface $context)
    {
        /** @var ComDocmanModelEntityDocument $document */
        $document = $context->getAttribute('entity');
        $in_queue = $this->_enqueueScan($document);

        return $in_queue ? false : true;
    }

    /**
     * Create a thumbnail for new files
     *
     * @param KControllerContextInterface $context
     */
    protected function _afterAdd(KControllerContextInterface $context)
    {
        if ($context->response->getStatusCode() === 201) {

            $scan = $this->_enqueueScan($context->result);

            if ($scan) {
                $this->_sendSynchronousScan($scan, $context->result, $context);
            }
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

        if (count($item) === 1 && $this->canScanDocument($item)) {
            $context->old_storage_path = $item->storage_path;
            $context->old_storage_type = $item->storage_type;
        }
    }

    protected function _afterEdit(KControllerContextInterface $context)
    {
        if (count($context->result) === 1 && $this->canScanDocument($context->result)) {
            $scan     = null;
            $data     = $context->request->data;
            $document = $context->result;

            $should_scan_document = $data->force_scan || $this->shouldScanDocument($document);

            if (!$should_scan_document) {
                // We might have a pending scan from before
                $pending_scan = $this->_getScan($document);

                if (!$pending_scan->isNew()) {
                    $should_scan_document = true;
                }

                if ((($context->old_storage_path && $context->old_storage_type)
                    && (($document->storage_path !== $context->old_storage_path)
                        || ($document->storage_type !== $context->old_storage_type))
                )) {
                    $should_scan_document = true;
                }
            }

            if ($should_scan_document) {
                if ($scan = $this->_enqueueScan($document)) {
                    $this->_sendSynchronousScan($scan, $document, $context);
                }
            }
        }
    }

    protected function _sendSynchronousScan($scan, $document = null, $context = null)
    {
        try
        {
            if ($this->_isLocal()) {
                $message = $this->getObject('translator')->translate('Document scan needs public server');
                $context->response->addMessage($message, KControllerResponse::FLASH_WARNING);

                return false;
            }

            if ($this->needsThrottling()) {
                $message = $this->getObject('translator')->translate('Document scan is throttled');
                $context->response->addMessage($message, KControllerResponse::FLASH_SUCCESS);

                return false;
            }

            $this->_sendScan($scan, $document);

            if ($scan->status == static::STATUS_SENT) {
                $message = $this->getObject('translator')->translate('Document scan is in progress');
                $context->response->addMessage($message, KControllerResponse::FLASH_SUCCESS);
            }

            if (JDEBUG && $scan->response) {
                $context->response->addMessage($scan->response, KControllerResponse::FLASH_NOTICE);
            }
        }
        catch (Exception $e) {
            if (JDEBUG) {
                $context->response->addMessage($e->getMessage(), KControllerResponse::FLASH_ERROR);
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

        if (!$this->_isLocal())
        {
            $data = array(
                'download_url' => (string)$this->_getDownloadUrl($document),
                'callback_url' => (string)$this->_getCallbackUrl(),
                'filename'     => ltrim(basename(' '.strtr($document->storage_path, array('/' => '/ ')))),
                'user_data'    => array(
                    'uuid' => $document->uuid
                )
            );

            $response = PlgKoowaConnect::sendRequest('scanner/start', ['data' => $data]);

            if ($response && $response->status_code == 200) {
                $scan->status = static::STATUS_SENT;
                $scan->response = $response->body;
                $scan->save();
            }
        }

        return $scan;
    }

    protected function _getScan(KModelEntityInterface $document)
    {
        $model = $this->_getScansModel();
        $scan  = $model->identifier($document->uuid)->fetch();

        if ($scan->isNew()) {
            $scan = $model->create();
            $scan->identifier = $document->uuid;
        }

        return $scan;
    }

    protected function _enqueueScan(KModelEntityInterface $document)
    {
        $result = false;

        if ($this->canScanDocument($document))
        {
            $scan = $this->_getScan($document);
            $scan->ocr = true;
            $scan->thumbnail = true;
            $scan->save();

            if (!$scan->isNew()) {
                $result = $scan;
            }
        }

        return $result;
    }

    protected function _isLocal()
    {
        return PlgKoowaConnect::isLocal();
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
            'connect' => 1,
            'token' => PlgKoowaConnect::generateToken()
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
            'connect' => 1,
            'id'     => $document->id,
            'token'  => PlgKoowaConnect::generateToken()
        );

        $url->setQuery($query);

        return $url;
    }
}