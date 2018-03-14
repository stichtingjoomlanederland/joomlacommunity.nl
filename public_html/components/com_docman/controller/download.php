<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanControllerDownload extends ComDocmanControllerDocument
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'behaviors' => array('redirectable', 'previewable'),
            'model' => 'documents',
            'view'  => 'download'
        ));

        parent::_initialize($config);
    }

    public function execute($action, KControllerContextInterface $context)
    {
        try
        {
            return parent::execute($action, $context);
        }
        catch(KHttpExceptionForbidden $exception)
        {
            $request = $this->getRequest();

            if ($request->isSafe() && $request->getFormat() == 'html' && $context->getName() == 'before.render')
            {
                // Re-direct the user to the previous location (using the controller referrer).
                $url     = $this->getReferrer($context);
                $message = $this->getObject('translator')->translate('You are not authorized to download the selected file');

                $this->getResponse()->setRedirect(JRoute::_($url, false), $message, 'error')
                    ->send();
            }
            else throw $exception;
        }
    }

    public function getRequest()
    {
        $request = parent::getRequest();

        if($this->isDispatched())
        {
            //Set force download
            $params = JFactory::getApplication()->getMenu()->getActive()->params;

            if ($params->get('force_download')) {
                $request->query->set('force-download', 1);
            }
        }

        return $request;
    }

    protected function _actionRender(KControllerContextInterface $context)
    {
        //Execute the action
        $document = $this->execute('read', $context);

        if (!$document->isNew())
        {
            $schemes = $document->getSchemes(); //Get the schemes whitelist
            if(isset($schemes[$document->storage->scheme]) && $schemes[$document->storage->scheme] === true)
            {
                //Set mimetype
                $file     = $document->storage;

                if (file_exists($file->fullpath))
                {
                    //Increase document hit count
                    if ($document->isHittable() && !$context->request->isStreaming()) {
                        $document->hit();
                    }

                    //Set the data in the response
                    try
                    {
                        $this->getResponse()
                            ->attachTransport('stream')
                            ->setContent($file->fullpath, $document->mimetype ?: 'application/octet-stream');
                    }
                    catch (InvalidArgumentException $e) {
                        throw new KControllerExceptionResourceNotFound('File not found');
                    }
                }
                else  throw new KControllerExceptionResourceNotFound('File not found');
            }
            else throw new RuntimeException('Stream wrapper is missing');
        }
        else throw new KControllerExceptionResourceNotFound('Document not found');
    }
}
