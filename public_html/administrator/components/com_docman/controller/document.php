<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanControllerDocument extends ComKoowaControllerModel
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->addCommandCallback('after.save2new', '_setRedirect');
        $this->addCommandCallback('after.read', '_setDefaults');
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'behaviors' => array(
                'accessible',
                'thumbnailable',
                'findable',
                'organizable',
                'sortable',
                'com:tags.controller.behavior.taggable',
                'scannable'
            ),
        ));

        parent::_initialize($config);
    }

    public function getRequest()
    {
        $request = parent::getRequest();

        // This is used to circumvent the URL size exceeding 2k bytes problem for document counts in files view
        if ($request->query->view === 'documents' && $request->data->has('storage_path')) {
            $request->query->storage_path = $request->data->storage_path;
        }

        return $request;
    }

    /**
     * Preset some fields in the edit form from request variables
     *
     * @param KControllerContextInterface $context
     */
    protected function _setDefaults(KControllerContextInterface $context)
    {
        $request = $this->getRequest();
        $view = $this->getView();

        if ($context->result->isNew())
        {
            if ($request->getFormat() == 'html' && $view->getName() == 'document')
            {
                if (!empty($request->query->storage_path)) {
                    $context->result->storage_path = $request->query->storage_path;
                    $context->result->storage_type = 'file';
                }
            }

            if ($request->query->storage_type) {
                $context->result->storage_type = $request->query->storage_type;
            }

            if ($request->query->category) {
                $context->result->docman_category_id = $request->query->category;
            }
        }
    }

    /**
     * Redirect to the form with the last category preselected if it exists in the URL
     *
     * @param KControllerContextInterface $context
     * @return KModelEntityInterface
     */
    protected function _setRedirect(KControllerContextInterface $context)
    {
        $referrer = $this->getReferrer($context);

        if ($referrer)
        {
            $query = $referrer->getQuery(true);

            if (!empty($query['category']))
            {
                $identifier = $this->getIdentifier();
                $view       = KStringInflector::singularize($identifier->name);
                $url        = sprintf('index.php?option=com_%s&view=%s&category=%d', $identifier->package, $view, $query['category']);

                $context->response->setRedirect($this->getObject('lib:http.url',array('url' => $url)));
            }
        }
    }

    /**
     * Re-set the redirect when needed in the overridden getReferrer method
     *
     * @param KControllerContextInterface $context
     */
    protected function _afterSave(KControllerContextInterface $context)
    {
        if($context->result && $context->result->getStatus() !== KModelEntityInterface::STATUS_FAILED) {
            $context->response->setRedirect($this->getReferrer($context));
        }
    }

    /**
     * Re-set the redirect when needed in the overridden getReferrer method
     *
     * @param KControllerContextInterface $context
     */
    protected function _afterCancel(KControllerContextInterface $context)
    {
        $context->response->setRedirect($this->getReferrer($context));
    }

    protected function _actionCopy(KControllerContextInterface $context)
    {
        if(!$context->result instanceof KModelEntityInterface) {
            $entities = $this->getModel()->fetch();
        } else {
            $entities = $context->result;
        }

        if(count($entities))
        {
            foreach($entities as $entity)
            {
                unset($entity->id);
                unset($entity->uuid);
                $entity->setStatus(KDatabase::STATUS_DELETED);
                $entity->setProperties($context->request->data->toArray());
            }

            //Only throw an error if the action explicitly failed.
            if($entities->save() === false)
            {
                $error = $entities->getStatusMessage();
                throw new KControllerExceptionActionFailed($error ? $error : 'Copy Action Failed');
            }
            else $context->status = $entities->getStatus() === KDatabase::STATUS_CREATED ? KHttpResponse::CREATED : KHttpResponse::NO_CONTENT;
        }
        else throw new KControllerExceptionResourceNotFound('Resource could not be found');

        return $entities;
    }

    /**
     * Redirects batch edits to documents view.
     *
     * @param KControllerContextInterface $context
     * @return KObjectInterface
     */
    public function getReferrer(KControllerContextInterface $context)
    {
        $referrer = parent::getReferrer($context);

        if ($referrer instanceof KHttpUrl)
        {
            $query = $referrer->query;

            $is_docman = isset($query['option']) && $query['option'] == 'com_docman';
            $is_files  = isset($query['view']) && $query['view'] == 'files';
            $is_form   = isset($query['layout']) && $query['layout'] == 'form';

            if ($is_docman && $is_files && $is_form)
            {
                $referrer = $this->getObject('lib:http.url',array(
                    'url' => $this->getView()->getRoute(array('view' => 'documents'))
                ));
            }
        }

        return $referrer;
    }
}
