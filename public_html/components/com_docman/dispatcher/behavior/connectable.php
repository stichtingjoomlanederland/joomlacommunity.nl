<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanDispatcherBehaviorConnectable extends KControllerBehaviorAbstract
{
    public function isSupported()
    {
        return $this->getObject('com://admin/docman.model.entity.config')->connectAvailable();
    }

    protected function _beforeDispatch(KControllerContextInterface $context)
    {
        /** @var KDispatcherRequest $request */
        $request = $this->getObject('request');
        $query   = $request->getQuery();

        if ($query->has('connect'))
        {
            if ($query->has('token'))
            {
                if (!PlgKoowaConnect::verifyToken($query->token)) {
                    throw new RuntimeException('Invalid JWT token');
                }

                if ($request->isGet() && $query->has('serve') && $query->has('id')) {
                    $this->_serveDocument($query->id);
                }
                elseif ($request->isPost())
                {
                    $result = array(
                        'result' => $this->_updateThumbnail()
                    );

                    $this->getObject('response')
                        ->setContent(json_encode($result), 'application/json')
                        ->send();
                }

                return false;
            }
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

        if ($document->isNew()) {
            throw new KControllerExceptionResourceNotFound('Document not found');
        }

        $file     = $document->storage;

        if ($file->isNew() || !is_file($file->fullpath)) {
            throw new KControllerExceptionResourceNotFound('File not found');
        }

        /** @var KDispatcherResponseAbstract $response */
        $response = $this->getObject('response');

        $response->attachTransport('stream')
            ->setContent($file->fullpath, $document->mimetype ?: 'application/octet-stream')
            ->getHeaders()->set('Content-Disposition', ['attachment' => ['filename' => '"file"']]);

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

        if ($document->isLocked()) {
            $document->locked_by = $document->locked_on = null;
            $document->save();
        }

        if ($scan->thumbnail && isset($data->thumbnail_url))
        {
            $controller = $this->getObject('com://admin/docman.controller.thumbnail');
            $context    = $controller->getContext();

            $context->setAttribute('entity', $document)
                ->setAttribute('thumbnail', $data->thumbnail_url);

            $controller->execute('save', $context);
        }

        if ($scan->ocr && isset($data->contents_url))
        {
            try {
                $file = $this->getObject('com:files.model.entity.url');
                $file->setProperties(array('file' => $data->contents_url));

                if ($file->contents) {
                    $document->contents = $file->contents;
                    $document->save();

                    JEventDispatcher::getInstance()->trigger('onFinderAfterSave', array('com_docman.document', $document, false));
                }
            }
            catch (Exception $e) {}
        }

        if (!empty($data->error)) {
            $scan->status = ComDocmanControllerBehaviorScannable::STATUS_FAILED;
            $scan->save();
        } else {
            $scan->delete();
        }


        return true;
    }
}
