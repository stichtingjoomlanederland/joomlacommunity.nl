<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanControllerSubmit extends ComKoowaControllerModel
{
    /**
     * A reference to the uploaded file row
     * Used to delete the file if the add action fails
     */
    protected $_uploaded;

    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->addCommandCallback('before.add', '_uploadFile');
        $this->addCommandCallback('after.add' , '_cleanUp');
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'behaviors' => array('thumbnailable', 'findable', 'notifiable', 'editable'),
            'toolbars'  => array('submit'),
            'model'     => 'documents',
            'formats'   => ['json']
        ));

        parent::_initialize($config);
    }

    /**
     * Add the toolbar for non-authentic users too
     *
     * @param KControllerContextInterface $context
     */
    protected function _addToolbars(KControllerContextInterface $context)
    {
        if($this->getView() instanceof KViewHtml)
        {
            if($this->isDispatched())
            {
                foreach($context->toolbars as $toolbar) {
                    $this->addToolbar($toolbar);
                }

                if($toolbars = $this->getToolbars())
                {
                    $this->getView()
                        ->getTemplate()
                        ->addFilter('toolbar', array('toolbars' => $toolbars));
                };
            }
        }
    }

    protected function _actionSave(KControllerContextInterface $context)
    {
        $result = $this->execute('add', $context);

        $chunk  = $context->request->data->get('chunk', 'int');
        $chunks = $context->request->data->get('chunks', 'int');

        $upload_finished = (!$chunks || $chunk == $chunks - 1);

        if ($upload_finished && $context->getResponse()->getStatusCode() === KHttpResponse::CREATED)
        {
            $route = JRoute::_('index.php?option=com_docman&view=submit&layout=success&Itemid='.$this->getRequest()->query->Itemid, false);

            if ($context->getRequest()->getFormat() === 'html') {
                $context->response->setRedirect($route);
            } else {
                $context->response->setContent(json_encode([
                    'redirect' => $route
                ]), 'application/json');
            }
        }

        return $result;
    }

    protected function _setData(KControllerContextInterface $context)
    {
        $data       = $context->request->data;
        $page       = JFactory::getApplication()->getMenu()->getItem($this->getRequest()->query->Itemid);
        $translator = $this->getObject('translator');

        if (!$page) {
            throw new RuntimeException($translator->translate('Invalid menu item'));
        }

        foreach ($this->getModel()->getTable()->getColumns() as $key => $column) {
            if (!in_array($key, array('docman_category_id', 'storage_type', 'title', 'description'))) {
                unset($data->$key);
            }
        }

        $data->enabled = $page->params->get('auto_publish') ? 1 : 0;

        if (empty($data->storage_type)) {
            $data->storage_type = $data->storage_path_remote ? 'remote' : 'file';
        }

        $categories = (array) $page->params->get('category_id');

        if (!$data->docman_category_id) {
            $data->docman_category_id = count($categories) ? $categories[0] : 0;
        }

        if (!in_array($data->docman_category_id, $categories) && $page->params->get('category_children'))
        {
            $state = array(
                'id'           => $data->docman_category_id,
                'access'       => $context->user->getRoles(),
                'current_user' => $context->user->getId(),
                'parent_id'    => $categories
            );

            if (!$this->getObject('com://admin/docman.model.categories')->setState($state)->count()) {
                throw new KControllerExceptionRequestInvalid($translator->translate('You cannot submit documents on the selected category'));
            }
        }

        if ($data->storage_type === 'file')
        {
            $file = $context->request->files->file;

            if (empty($file) || empty($file['name'])) {
                throw new KControllerExceptionRequestInvalid($translator->translate('You did not select a file to be uploaded.'));
            }
        } else {
            $data->storage_path = $data->storage_path_remote;
        }

    }

    protected function _getFileController(KControllerContextInterface $context)
    {
        return $this->getObject('com:files.controller.file', [
            'behaviors' => [
                'permissible' => [
                    'permission' => 'com://site/docman.controller.permission.submit'
                ]
            ],
            'request' => clone $context->request
        ])->container('docman-files');
    }

    protected function _uploadFile(KControllerContextInterface $context)
    {
        $result = true;

        try
        {
            $this->_setData($context);

            $data = $context->request->data;

            if ($data->storage_type === 'file')
            {
                $file = $context->request->files->file;

                $controller = $this->_getFileController($context);
                $category   = $this->getObject('com://admin/docman.model.categories')->id($data->docman_category_id)->fetch();
                $folder     = $category->folder;

                $filename = $data->has('name') ? $data->name : $file['name'];
                $filename = $this->_getUniqueName($controller->getModel()->getContainer(), $folder, $filename);

                $this->_uploaded = $controller
                        ->add(array(
                            'file'   => $file['tmp_name'],
                            'name'   => $filename,
                            'folder' => $folder,
                            'chunk'  => $context->request->data->get('chunk', 'int'),
                            'chunks' => $context->request->data->get('chunks', 'int')
                        ));

                if ($this->_uploaded) {
                    $data->storage_path = $this->_uploaded->path;
                } else {
                    $result = false; // This happens when we upload just a chunk
                }
            }
        }
        catch (Exception $exception)
        {
            if ($context->getRequest()->getFormat() !== 'json') {
                $message = $exception->getMessage();
                $context->getResponse()->setRedirect($this->getRequest()->getReferrer(), $message, 'error');
                $context->getResponse()->send();

                $result = false;
            } else {
                $context->response->setContent(json_encode([
                    'status' => false,
                    'error' => $exception->getMessage()
                ]), 'application/json');

                $result = false;
            }
        }

        return $result;
    }

    /**
     * Find a unique name for the given container and folder by adding (1) (2) etc to the end of file name
     *
     * @param $container
     * @param $folder
     * @param $file
     * @return string
     */
    protected function _getUniqueName($container, $folder, $file)
    {
        $adapter   = $this->getObject('com:files.adapter.file');
        $folder    = $container->fullpath.(!empty($folder) ? '/'.$folder : '');
        $fileinfo  = pathinfo(' '.strtr($file, array('/' => '/ ')));
        $filename  = ltrim($fileinfo['filename']);
        $extension = $fileinfo['extension'];

        $adapter->setPath($folder.'/'.$file);

        $i = 1;
        while ($adapter->exists())
        {
            $file = sprintf('%s (%d).%s', $filename, $i, $extension);

            $adapter->setPath($folder.'/'.$file);
            $i++;
        }

        return $file;
    }

    protected function _cleanUp(KControllerContextInterface $context)
    {
        if ($context->getResponse()->getStatusCode() !== KHttpResponse::CREATED)
        {
            try
            {
                if ($this->_uploaded instanceof KModelEntityInterface) {
                    $this->_uploaded->delete();
                }

            } catch (Exception $e) {
                // Well, we tried
            }
        }
    }
}
