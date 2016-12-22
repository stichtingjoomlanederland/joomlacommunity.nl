<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanDispatcherBehaviorScannable extends KControllerBehaviorAbstract
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
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

    protected function _beforeDispatch(KControllerContextInterface $context)
    {
        /** @var KDispatcherRequest $request */
        $request = $this->getObject('request');
        $query   = $request->getQuery();
        $data    = $request->getData();

        if ($query->has('thumbnail') &&  $query->has('token'))
        {
            /** @var $token KHttpToken */
            $token  = $this->getObject('http.token');
            $secret = $this->getConfig()->secret_key;

            if (!$token->fromString($query->token)->verify($secret)) {
                throw new RuntimeException('Invalid JWT token');
            }

            if ($request->isGet() && $query->has('serve') && $query->has('id')) {
                $this->_serveDocument($query->id);
            }
            elseif ($request->isPost() && $data->has('thumbnails') && $data->has('status'))
            {
                $result = array(
                    'result' => $this->_updateThumbnail()
                );

                $this->getObject('response')->setContent(json_encode($result), 'application/json')->send();
            }

            return false;
        }

        return true;
    }

    /**
     * Serve a document for the consumption of the thumbnail service
     *
     * @param integer $id
     */
    protected function _serveDocument($id)
    {
        $document = $this->getObject('com://admin/docman.model.documents')->id($id)->fetch();
        $file     = $document->storage;
        /** @var KDispatcherResponseAbstract $response */
        $response = $this->getObject('response');

        $response->attachTransport('stream')
            ->setContent($file->fullpath, 'application/octet-stream')
            ->getHeaders()->set('Content-Disposition', array('attachment', 'filename="file"'));

        $response->send();
    }

    /**
     * Updates the document thumbnail from the request payload
     *
     * @return boolean
     */
    protected function _updateThumbnail()
    {
        /** @var KDispatcherRequest $request */
        $request = $this->getObject('request');
        $data = $request->getData();

        $user_data = $data->user_data;

        if (!isset($user_data['uuid'])) {
            throw new RuntimeException('Missing user data');
        }

        /** @var ComDocmanModelEntityDocument $document */
        $scan      = $this->getObject('com://admin/docman.model.scans')->identifier($user_data['uuid'])->fetch();
        $document  = $this->getObject('com://admin/docman.model.documents')->uuid($user_data['uuid'])->fetch();

        if ($document->isNew()) {
            throw new RuntimeException('Document not found');
        }

        if ($data->status === 'failure') {
            $scan->status = \ComDocmanControllerBehaviorScannable::STATUS_FAILED;
            $scan->save();

            return true;
        }

        if ($data->status !== 'success') {
            throw new RuntimeException('Failed');
        }

        if ($document->isLocked()) {
            $document->locked_by = $document->locked_on = null;
            $document->save();
        }

        if ($scan->thumbnail && (isset($data->thumbnails) && is_array($data->thumbnails) && count($data->thumbnails)))
        {
            $thumbnail  = $data->thumbnails[0]['url'];
            $controller = $this->getObject('com://admin/docman.controller.thumbnail');
            $context    = $controller->getContext();

            $context->setAttribute('entity', $document)
                ->setAttribute('thumbnail', $thumbnail);

            $controller->execute('save', $context);
        }

        if ($scan->ocr && isset($data->original_file) && isset($data->original_file['metadata'])
            && isset($data->original_file['metadata']['ocr'])
            && is_array($data->original_file['metadata']['ocr']) && count($data->original_file['metadata']['ocr'])
        ) {
            $ocr      = $data->original_file['metadata']['ocr'];
            $contents = '';

            foreach ($ocr as $page) {
                if (!empty($page['text'])) {
                    $contents .= preg_replace('#\s+#', ' ', $page['text'])."\n\n";
                }
            }
            $document->contents = $contents;
            $document->save();

            JEventDispatcher::getInstance()->trigger('onFinderAfterSave', array('com_docman.document', $document, false));
        }

        $scan->delete();

        return true;
    }
}
