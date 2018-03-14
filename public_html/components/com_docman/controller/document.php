<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanControllerDocument extends ComKoowaControllerModel
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->addCommandCallback('after.save'  , '_setStatusMessage');
        $this->addCommandCallback('after.delete', '_setStatusMessage');

        $this->addCommandCallback('after.read'   , '_populateCategory');
        $this->addCommandCallback('before.render', '_checkDownloadLink');
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'formats'   => array('json'),
            'behaviors' => array(
                'thumbnailable',
                'findable',
                /*'filterable',*/
                'organizable',
                'com:tags.controller.behavior.taggable',
                'com://admin/docman.controller.behavior.scannable'
            )
        ));

        parent::_initialize($config);
    }

    /**
     * Load admin language file in forms since some of the layouts are shared
     *
     * @param KControllerContextInterface $context
     */
    protected function _beforeRender(KControllerContextInterface $context)
    {
        $request = $context->getRequest();

        if ($request->getFormat() === 'html' && $request->query->view === 'document'
            && $this->getView()->getLayout() === 'form')
        {
            $this->getObject('translator')->load('com://admin/docman');
        }
    }

    /**
     * Find the referrer based on the context
     *
     * @param KControllerContextInterface $context
     * @return KHttpUrl The referrer
     */
    public function findReferrer(KControllerContextInterface $context)
    {
        $menu = JFactory::getApplication()->getMenu()->getActive();

        if (isset($menu->query['view']) && $menu->query['view'] !== 'list' && $menu->query['view'] !== 'tree') {
            $redirect = sprintf('index.php?Itemid=%d', $this->getRequest()->query->Itemid);
        } else {
            $redirect = sprintf('index.php?view=list&slug=%s&Itemid=%d', $context->result->category_slug, $this->getRequest()->query->Itemid);
        }

        return $this->getObject('lib:http.url', array('url' => JRoute::_($redirect, false)));
    }

    /**
     * Check to see if we need to redirect to the download view or a remote URL
     *
     * @param KControllerContextInterface $context
     * @return bool|void
     */
    protected function _checkDownloadLink(KControllerContextInterface $context)
    {
        $entity = $this->getModel()->fetch();
        $query  = $this->getRequest()->query;

        // Redirect document view to download view if the title links are set as download in menu parameters
        if (!$entity->isNew() && $this->getRequest()->getFormat() === 'html'
            && $query->view === 'document' && $this->getView()->getLayout() === 'default')
        {
            $menu = JFactory::getApplication()->getMenu()->getActive();

            if ($menu->params->get('document_title_link') === 'download'
                    && in_array($menu->query['view'], array('tree', 'list', 'flat', 'document'))
            ) {
                $url = JRoute::_('index.php?option=com_docman&view=download&alias='.$entity->alias.'&category_slug='.$entity->category_slug.'&Itemid='.$menu->id, false);
                JFactory::getApplication()->redirect($url);
            }
        }

        return true;
    }

    /**
     * If the category slug is supplied in the URL, prepopulate it in the new document form
     *
     * @param KControllerContextInterface $context
     */
    protected function _populateCategory(KControllerContextInterface $context)
    {
        if ($context->result->isNew())
        {
            $query = $this->getRequest()->query;
            $view = $this->getView();

            if ($this->getRequest()->getFormat() === 'html' && $view->getName() == 'document')
            {
                $slug = $query->category_slug;

                if (empty($slug) && $query->path)
                {
                    $slug = explode('/', $query->path);
                    $slug = array_pop($slug);
                }

                if (empty($slug))
                {
                    $menu = JFactory::getApplication()->getMenu()->getActive();
                    if (($menu->query['view'] === 'list' || $menu->query['view'] === 'tree') && isset($menu->query['slug'])) {
                        $slug = $menu->query['slug'];
                    }
                }

                if (!empty($slug))
                {
                    $id = $this->getObject('com://site/docman.model.categories')->slug($slug)->fetch()->id;
                    $context->result->docman_category_id = $id;
                    $context->result->category_slug = $slug;
                }
            }
        }
    }

    /**
     * Set status messages after save and delete actions
     *
     * @param KControllerContextInterface $context
     */
    protected function _setStatusMessage(KControllerContextInterface $context)
    {
        $translator = $this->getObject('translator');
        $action     = $context->action;

        $failed  = false;
        foreach ($context->result as $entity)
        {
            if ($entity->getStatus() == KDatabase::STATUS_FAILED)
            {
                $type = 'error';

                if (!$entity->getStatusMessage())
                {
                    if ($action === 'delete') {
                        $message = $translator->translate('Unable to delete document');
                    } else {
                        $message = $translator->translate('Unable to save document');
                    }

                }
                else $message = $entity->getStatusMessage();

                $context->response->setRedirect($this->getRequest()->getReferrer(), $message, $type);

                $failed = true;
                break;
            }
        }

        if (!$failed && count($context->result) === 1)
        {
            if ($action === 'delete') {
                $message = $translator->translate('Document deleted');
            } else {
                $message = $translator->translate('Document saved');
            }

            $context->response->addMessage($message);
        }
    }
}
