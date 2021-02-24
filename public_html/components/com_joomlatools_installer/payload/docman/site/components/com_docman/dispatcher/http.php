<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanDispatcherHttp extends ComKoowaDispatcherHttp
{
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->addCommandCallback('before.get', '_setLimit');

        $this->getObject('event.publisher')->addListener('onException', array($this, 'onExceptionNotFound'));
    }

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'controller'     => 'list',
            'authenticators' => array('jwt'),
            'behaviors'      => array(
                'connectable',
                'com://admin/docman.dispatcher.behavior.routable'
            )
        ));

        parent::_initialize($config);
    }

    /**
     * Redirects guest users to login form on 404 errors if the entity can be accessed without the viewlevel filter
     *
     * @param KEventException $event
     */
    public function onExceptionNotFound(KEventException $event)
    {
        if ($event->getException()->getCode() === KHttpResponse::NOT_FOUND && !$this->getObject('user')->isAuthentic()
            && $event->getException()->getMessage() !== 'File not found')
        {
            $model = $this->getController()->getModel();
            $model->setState(array('access' => null));

            if (!$model->fetch()->isNew())
            {
                $message = $this->getObject('translator')->translate('You are not authorized to access this resource. Please login and try again.');
                $url = JRoute::_('index.php?option=com_users&view=login&return='.base64_encode((string) $this->getRequest()->getUrl()), false);

                $this->getResponse()->setRedirect($url, $message, 'error');
                $this->getResponse()->send();

                $event->stopPropagation();
            }
        }
    }

    /**
     * Sets and override default limit based on page settings parameters.
     *
     * @param KDispatcherContextInterface $context
     * @return KModelEntityInterface
     */
    protected function _setLimit(KDispatcherContextInterface $context)
    {
        $controller = $this->getController();

        if (in_array($controller->getIdentifier()->name, array('tree', 'list', 'flat')))
        {
            $params = JFactory::getApplication()->getMenu()->getActive()->params;

            if ($limit = $params->get('limit')) {
                $this->getConfig()->limit->default = $limit;
            }

            if (!$params->get('show_document_sort_limit'))
            {
                $this->getRequest()->getQuery()->limit = (int) $this->getConfig()->limit->default;
                $controller->getModel()->getState()->setProperty('limit', 'internal', true);
            }
        }
    }

    public function getRequest()
    {
        $request = parent::getRequest();

        $query = $request->query;

        if ($query->alias && !$query->slug)
        {
            $parts       = explode('-', $query->alias, 2);
            $query->slug = array_pop($parts);
        }

        $menu = JFactory::getApplication()->getMenu()->getActive();
        if ($menu && !in_array($query->view, array('doclink', 'documents'))) {
            $query->Itemid = $menu->id;
        }

        // Can't use executable behavior here as it calls getController which in turn calls this method
        if ($this->getObject('user')->authorise('core.manage', 'com_docman') !== true)
        {
            $query->enabled = 1;
            $query->status  = 'published';
        }


        $query->access = $this->getObject('user')->getRoles();
        $query->page   = $query->Itemid;
        $query->current_user = $this->getObject('user')->getId();

        // This cannot come from the query string on frontend
        unset($query->group);

        return $request;
    }
}
